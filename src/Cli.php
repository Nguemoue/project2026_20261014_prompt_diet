<?php

declare(strict_types=1);

namespace PromptDiet;

final class Cli
{
    public static function run(array $argv): int
    {
        $command = $argv[1] ?? 'help';
        if ($command === 'help' || $command === '--help' || $command === '-h') {
            self::help();

            return 0;
        }

        try {
            [$options, $positionals] = self::parse(array_slice($argv, 2));
        } catch (\InvalidArgumentException $exception) {
            fwrite(STDERR, $exception->getMessage() . "\n");

            return 1;
        }

        if ($command === 'models') {
            self::models($options['json']);

            return 0;
        }

        if (!in_array($command, ['estimate', 'diet'], true)) {
            fwrite(STDERR, "Unknown command \"{$command}\".\n\n");
            self::help();

            return 1;
        }

        try {
            $text = self::readSource($positionals[0] ?? null);
            $report = Report::build(
                $text,
                $options['model'],
                $options['runs'],
                $options['output_tokens'],
                $options['aggressive']
            );
        } catch (\InvalidArgumentException $exception) {
            fwrite(STDERR, $exception->getMessage() . "\n");

            return 1;
        } catch (\RuntimeException $exception) {
            fwrite(STDERR, $exception->getMessage() . "\n");

            return 1;
        }

        if ($options['out'] !== null) {
            if (file_put_contents($options['out'], $report['slim']) === false) {
                fwrite(STDERR, "Could not write {$options['out']}\n");

                return 1;
            }
        }

        if ($command === 'diet' && !$options['json']) {
            if ($options['out'] === null) {
                fwrite(STDOUT, $report['slim']);
                if ($report['slim'] !== '' && !str_ends_with($report['slim'], "\n")) {
                    fwrite(STDOUT, "\n");
                }
            }
            self::render($report, false, STDERR);
        } elseif ($options['json']) {
            $payload = $report;
            if (!$options['with_slim']) {
                unset($payload['slim']);
            }
            fwrite(STDOUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
        } else {
            self::render($report, !$options['no_color'] && self::isTty(STDOUT), STDOUT);
        }

        if ($options['max_tokens'] !== null && $report['tokens'] > $options['max_tokens']) {
            fwrite(STDERR, "Over budget: {$report['tokens']} tokens > {$options['max_tokens']}.\n");

            return 2;
        }

        return 0;
    }

    /**
     * @param array<int, string> $args
     * @return array{0: array<string, mixed>, 1: list<string>}
     */
    private static function parse(array $args): array
    {
        $options = [
            'model' => 'grok-4.7',
            'runs' => 200,
            'output_tokens' => 0,
            'json' => false,
            'aggressive' => false,
            'max_tokens' => null,
            'out' => null,
            'no_color' => false,
            'with_slim' => false,
        ];
        $positionals = [];

        for ($index = 0; $index < count($args); $index++) {
            $arg = $args[$index];
            $needs = static function (string $name) use (&$index, $args, $arg): string {
                if (str_contains($arg, '=')) {
                    return (string) substr($arg, strpos($arg, '=') + 1);
                }
                $index++;
                if (!isset($args[$index])) {
                    throw new \InvalidArgumentException("Missing value for {$name}.");
                }

                return $args[$index];
            };

            if ($arg === '--json') {
                $options['json'] = true;
            } elseif ($arg === '--aggressive') {
                $options['aggressive'] = true;
            } elseif ($arg === '--no-color') {
                $options['no_color'] = true;
            } elseif ($arg === '--with-slim') {
                $options['with_slim'] = true;
            } elseif ($arg === '--model' || str_starts_with($arg, '--model=')) {
                $options['model'] = $needs('--model');
            } elseif ($arg === '--runs' || str_starts_with($arg, '--runs=')) {
                $options['runs'] = self::positiveInt($needs('--runs'), '--runs');
            } elseif ($arg === '--output-tokens' || str_starts_with($arg, '--output-tokens=')) {
                $options['output_tokens'] = self::nonNegativeInt($needs('--output-tokens'), '--output-tokens');
            } elseif ($arg === '--max-tokens' || str_starts_with($arg, '--max-tokens=')) {
                $options['max_tokens'] = self::nonNegativeInt($needs('--max-tokens'), '--max-tokens');
            } elseif ($arg === '--out' || str_starts_with($arg, '--out=')) {
                $options['out'] = $needs('--out');
            } elseif ($arg === '--') {
                foreach (array_slice($args, $index + 1) as $rest) {
                    $positionals[] = $rest;
                }
                break;
            } elseif (str_starts_with($arg, '-') && $arg !== '-') {
                throw new \InvalidArgumentException("Unknown option {$arg}.");
            } else {
                $positionals[] = $arg;
            }
        }

        return [$options, $positionals];
    }

    private static function positiveInt(string $value, string $flag): int
    {
        $number = self::nonNegativeInt($value, $flag);
        if ($number < 1) {
            throw new \InvalidArgumentException("{$flag} must be >= 1.");
        }

        return $number;
    }

    private static function nonNegativeInt(string $value, string $flag): int
    {
        if (preg_match('/^\d+$/', $value) !== 1) {
            throw new \InvalidArgumentException("{$flag} expects an integer, got \"{$value}\".");
        }

        return (int) $value;
    }

    private static function readSource(?string $path): string
    {
        if ($path === null || $path === '-') {
            $data = stream_get_contents(STDIN);
            if ($data === false) {
                throw new \RuntimeException('Could not read stdin.');
            }

            return $data;
        }

        if (!is_file($path)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        $data = file_get_contents($path);
        if ($data === false) {
            throw new \RuntimeException("Could not read {$path}");
        }

        return $data;
    }

    private static function help(): void
    {
        $help = <<<TXT
Prompt Diet — what a bloated agent prompt actually costs.

Usage
  prompt-diet estimate [file|-] [options]
  prompt-diet diet [file|-] [options]
  prompt-diet models [--json]

Options
  --model <id>            Price row to highlight (default grok-4.7)
  --runs <n>              Turns per day in the projection (default 200)
  --output-tokens <n>     Expected completion size added to every turn
  --max-tokens <n>        Exit 2 when the original file is larger (CI)
  --aggressive            Also crop fenced blocks longer than 40 lines
  --out <file>            Write the slimmed prompt
  --json                  Machine-readable report on stdout
  --with-slim             Include the slimmed prompt in JSON
  --no-color

The estimate is a local heuristic (±20%), not a provider invoice.
Prices are a public list-price snapshot dated 2026-10-14. Nothing is uploaded.

TXT;
        fwrite(STDOUT, $help);
    }

    private static function models(bool $json): void
    {
        $rows = [];
        foreach (Prices::all() as $id => $price) {
            $rows[] = [
                'id' => $id,
                'provider' => $price['provider'],
                'input_per_million' => $price['input'],
                'output_per_million' => $price['output'],
                'cached_input_per_million' => $price['cached'],
            ];
        }

        if ($json) {
            fwrite(STDOUT, json_encode(['as_of' => Prices::AS_OF, 'models' => $rows], JSON_PRETTY_PRINT) . "\n");

            return;
        }

        fwrite(STDOUT, 'List prices USD / 1M tokens, snapshot ' . Prices::AS_OF . "\n\n");
        foreach ($rows as $row) {
            fwrite(STDOUT, sprintf(
                "  %-24s %-12s  in %7.2f   out %7.2f   cache %7.3f\n",
                $row['id'],
                $row['provider'],
                $row['input_per_million'],
                $row['output_per_million'],
                $row['cached_input_per_million']
            ));
        }
    }

    /**
     * @param array<string, mixed> $report
     * @param resource $out
     */
    private static function render(array $report, bool $color, $out): void
    {
        $paint = static function (string $code, string $text) use ($color): string {
            return $color ? "\033[{$code}m{$text}\033[0m" : $text;
        };

        $width = 52;
        $rule = static fn (): string => str_repeat('-', $width) . "\n";
        $row = static function (string $left, string $right) use ($width): string {
            $gap = $width - strlen($left) - strlen($right);

            return $gap < 1 ? $left . ' ' . $right . "\n" : $left . str_repeat(' ', $gap) . $right . "\n";
        };

        $saved = (int) $report['tokens_saved'];
        $ratio = Report::percent((float) $report['saved_ratio']);
        $after = number_format((int) $report['tokens_after']);
        $before = number_format((int) $report['tokens']);

        $buffer = "\n" . $paint('1', 'PROMPT DIET') . "  receipt  " . $report['as_of'] . "\n";
        $buffer .= $row($report['model_id'] . '  ' . $report['provider'], 'in $' . number_format($report['prices']['input'], 2) . ' / 1M');
        $buffer .= $rule();
        $buffer .= $row('tokens in the file', $before);
        $buffer .= $row('after diet', $after . '   -' . $ratio);
        $buffer .= $row('output tokens / turn', (string) $report['output_tokens']);
        $buffer .= $rule();

        foreach ($report['ladder'] as $step) {
            $mark = $step['runs'] === $report['runs'] ? '>' : ' ';
            $label = $mark . ' ' . $step['runs'] . ' turn' . ($step['runs'] === 1 ? '' : 's') . '/day';
            $buffer .= $row($label, Report::money((float) $step['total_usd']));
        }
        $buffer .= $row('30-day projection', $paint('1', Report::money((float) $report['month_usd'])));
        $buffer .= $row('30-day after diet', Report::money((float) $report['month_after_usd']));
        $buffer .= $row('30-day if prefix is cached', Report::money((float) $report['cached_month_usd']));
        $buffer .= $rule();

        if ($report['findings'] === []) {
            $buffer .= "No high-confidence waste. The file is already lean.\n";
        } else {
            foreach ($report['findings'] as $finding) {
                $buffer .= $row('- ' . $finding['title'], number_format($finding['tokens_saved']) . ' tok');
                $buffer .= '  ' . $finding['detail'] . "\n";
            }
        }

        $buffer .= $rule();
        $buffer .= $saved > 0
            ? 'Cut ' . $ratio . ' · ' . number_format($saved) . " tokens removed.\n"
            : "Nothing safe to cut. Pass --aggressive to crop long fences.\n";
        $buffer .= "Heuristic " . $report['estimate'] . ". Local only, list prices, not an invoice.\n\n";

        fwrite($out, $buffer);
    }

    /** @param resource $stream */
    private static function isTty($stream): bool
    {
        return function_exists('stream_isatty') && stream_isatty($stream);
    }
}
