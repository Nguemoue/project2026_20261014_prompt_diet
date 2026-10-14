<?php

declare(strict_types=1);

namespace PromptDiet;

/**
 * High-confidence prompt slimming. Default mode never deletes unique prose
 * or unique source lines. Pass $aggressive to also crop huge fenced blocks.
 */
final class Diet
{
    /**
     * @return array{text:string, findings:list<array{id:string, title:string, detail:string, tokens_saved:int}>}
     */
    public static function apply(string $text, bool $aggressive = false): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", Tokenizer::utf8($text));
        $findings = [];

        $text = self::step($text, $findings, 'trailing-space', 'Trailing whitespace', [self::class, 'stripTrailingSpace']);
        $text = self::step($text, $findings, 'base64', 'Base64 blobs', [self::class, 'stripBlobs']);
        $text = self::step($text, $findings, 'stack', 'Stack traces', [self::class, 'collapseStacks']);
        $text = self::step($text, $findings, 'logs', 'Repeated log lines', [self::class, 'dedupeLogs']);
        $text = self::step($text, $findings, 'duplicates', 'Duplicate paragraphs', [self::class, 'dedupeParagraphs']);
        if ($aggressive) {
            $text = self::step($text, $findings, 'fences', 'Long fenced blocks', [self::class, 'cropFences']);
        }
        $text = self::step($text, $findings, 'blank-lines', 'Blank-line padding', [self::class, 'collapseBlankLines']);

        return ['text' => $text, 'findings' => $findings];
    }

    /**
     * @param list<array{id:string, title:string, detail:string, tokens_saved:int}> $findings
     * @param callable(string): array{0:string, 1:?string} $transform
     */
    private static function step(string $text, array &$findings, string $id, string $title, callable $transform): string
    {
        [$next, $detail] = $transform($text);
        if ($detail === null || $next === $text) {
            return $text;
        }

        $saved = Tokenizer::estimate($text) - Tokenizer::estimate($next);
        if ($saved > 0) {
            $findings[] = [
                'id' => $id,
                'title' => $title,
                'detail' => $detail,
                'tokens_saved' => $saved,
            ];
        }

        return $next;
    }

    /**
     * @return array{0:string, 1:?string}
     */
    private static function stripTrailingSpace(string $text): array
    {
        $lines = explode("\n", $text);
        $trimmed = 0;
        foreach ($lines as $index => $line) {
            $clean = rtrim($line, " \t");
            if ($clean !== $line) {
                $trimmed++;
                $lines[$index] = $clean;
            }
        }

        if ($trimmed === 0) {
            return [$text, null];
        }

        return [implode("\n", $lines), $trimmed . ' line' . ($trimmed === 1 ? '' : 's') . ' trimmed'];
    }

    /**
     * @return array{0:string, 1:?string}
     */
    private static function stripBlobs(string $text): array
    {
        $count = 0;
        $chars = 0;
        $pattern = '~(?:data:[a-zA-Z0-9.+-]+/[a-zA-Z0-9.+-]+;base64,)([A-Za-z0-9+/=]{40,})|(?<![A-Za-z0-9+/=])([A-Za-z0-9+/=]{80,})(?![A-Za-z0-9+/=])|(?<![A-Fa-f0-9])(?:0x)?([A-Fa-f0-9]{64,})(?![A-Fa-f0-9])~';
        $next = preg_replace_callback(
            $pattern,
            static function (array $match) use (&$count, &$chars): string {
                if ($match[1] !== '') {
                    $blob = $match[1];
                    $kind = 'base64';
                } elseif ($match[2] !== '') {
                    if (!self::looksLikeBase64($match[2])) {
                        return $match[0];
                    }
                    $blob = $match[2];
                    $kind = preg_match('/^[A-Fa-f0-9]+$/', $blob) === 1 ? 'hex' : 'base64';
                } else {
                    $blob = $match[3];
                    $kind = 'hex';
                }

                $count++;
                $chars += strlen($blob);

                return '[' . $kind . ' omitted ' . strlen($blob) . ' chars]';
            },
            $text
        );

        if ($next === null || $count === 0) {
            return [$text, null];
        }

        return [$next, $count . ' blob' . ($count === 1 ? '' : 's') . ', ' . number_format($chars) . ' chars'];
    }

    /**
     * Base64's alphabet contains "/", so a long Unix path looks like a blob.
     * Keep paths and URLs. Cut tokens that are unbroken, or that carry "+" / "=".
     */
    private static function looksLikeBase64(string $blob): bool
    {
        if (str_contains($blob, '+') || str_contains($blob, '=')) {
            return true;
        }
        if (!str_contains($blob, '/')) {
            return true;
        }

        foreach (explode('/', $blob) as $segment) {
            if (strlen($segment) >= 48) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{0:string, 1:?string}
     */
    private static function collapseStacks(string $text): array
    {
        $omitted = 0;
        $next = $text;
        foreach (['#\d+\s+[^\n]+', 'at\s+[^\n]+'] as $line) {
            $replaced = preg_replace_callback(
                '/(?:^|\n)((?:' . $line . '\n){4,})/',
                static function (array $match) use (&$omitted): string {
                    $lines = explode("\n", rtrim($match[1], "\n"));
                    $count = count($lines);
                    if ($count < 4) {
                        return $match[0];
                    }
                    $drop = $count - 3;
                    $omitted += $drop;
                    $kept = [
                        $lines[0],
                        $lines[1],
                        '[prompt-diet] ' . $drop . ' frames omitted',
                        $lines[$count - 1],
                    ];
                    $prefix = str_starts_with($match[0], "\n") ? "\n" : '';

                    return $prefix . implode("\n", $kept) . "\n";
                },
                $next
            );
            if ($replaced !== null) {
                $next = $replaced;
            }
        }

        if ($omitted === 0) {
            return [$text, null];
        }

        return [$next, $omitted . ' frames dropped, head and tail kept'];
    }

    /**
     * @return array{0:string, 1:?string}
     */
    private static function dedupeLogs(string $text): array
    {
        $lines = explode("\n", $text);
        $totals = [];
        foreach ($lines as $line) {
            if (self::isLogLine($line)) {
                $totals[$line] = ($totals[$line] ?? 0) + 1;
            }
        }

        $seen = [];
        $kept = [];
        $removed = 0;
        foreach ($lines as $line) {
            if (self::isLogLine($line) && ($totals[$line] ?? 0) > 2) {
                $seen[$line] = ($seen[$line] ?? 0) + 1;
                if ($seen[$line] > 2) {
                    $removed++;
                    continue;
                }
            }
            $kept[] = $line;
        }

        if ($removed === 0) {
            return [$text, null];
        }

        return [implode("\n", $kept), $removed . ' repeated log lines dropped, 2 kept'];
    }

    private static function isLogLine(string $line): bool
    {
        $trim = ltrim($line);

        return preg_match('/^(?:INFO|DEBUG|WARN|WARNING|ERROR|TRACE)\b|^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}/', $trim) === 1;
    }

    /**
     * @return array{0:string, 1:?string}
     */
    private static function dedupeParagraphs(string $text): array
    {
        $parts = preg_split("/\n{2,}/", $text);
        if ($parts === false) {
            return [$text, null];
        }

        $seen = [];
        $kept = [];
        $dropped = 0;
        foreach ($parts as $part) {
            $key = strtolower((string) preg_replace('/\s+/', ' ', trim($part)));
            if (strlen($key) >= 80 && isset($seen[$key])) {
                $dropped++;
                if ($kept !== []) {
                    $previous = trim($kept[count($kept) - 1]);
                    if (preg_match('/^#{1,6} \S[^\n]*$/', $previous) === 1) {
                        array_pop($kept);
                    }
                }
                continue;
            }
            if (strlen($key) >= 80) {
                $seen[$key] = true;
            }
            $kept[] = $part;
        }

        if ($dropped === 0) {
            return [$text, null];
        }

        return [implode("\n\n", $kept), $dropped . ' repeated paragraph' . ($dropped === 1 ? '' : 's') . ' dropped'];
    }

    /**
     * @return array{0:string, 1:?string}
     */
    private static function cropFences(string $text): array
    {
        $cropped = 0;
        $next = preg_replace_callback(
            '/```[^\n]*\n(.*?)\n?```/s',
            static function (array $match) use (&$cropped): string {
                $body = $match[1];
                $lines = explode("\n", $body);
                if (count($lines) <= 40) {
                    return $match[0];
                }
                $cropped++;
                $omit = count($lines) - 18;
                $head = array_slice($lines, 0, 12);
                $tail = array_slice($lines, -6);
                $fence = substr($match[0], 0, (int) strpos($match[0], "\n"));

                return $fence . "\n" . implode("\n", $head)
                    . "\n[prompt-diet] " . $omit . " fenced lines omitted\n"
                    . implode("\n", $tail) . "\n```";
            },
            $text
        );

        if ($next === null || $cropped === 0) {
            return [$text, null];
        }

        return [$next, $cropped . ' fenced block' . ($cropped === 1 ? '' : 's') . ' cropped to head and tail'];
    }

    /**
     * @return array{0:string, 1:?string}
     */
    private static function collapseBlankLines(string $text): array
    {
        $next = (string) preg_replace("/\n{3,}/", "\n\n", $text);
        if ($next === $text) {
            return [$text, null];
        }

        $before = preg_match_all("/\n{3,}/", $text);

        return [$next, ($before === false ? 0 : $before) . ' padded gap' . ($before === 1 ? '' : 's') . ' collapsed'];
    }
}
