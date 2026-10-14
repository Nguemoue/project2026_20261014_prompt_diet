<?php

declare(strict_types=1);

namespace PromptDiet;

/**
 * Offline token estimate. Not a vendor BPE encoder.
 *
 * Latin words of 1–6 letters count as one token, longer words as one token
 * per four characters. Numbers split about every three digits. Each symbol
 * and each CJK character counts as one. Whitespace is not billed on its own
 * (real encoders usually glue it to the next piece).
 *
 * Expect roughly ±20% versus cl100k/o200k on prose and code. Use it to
 * compare prompts, not to invoice a provider.
 */
final class Tokenizer
{
    public const METHOD = 'heuristic-v1';

    public static function estimate(string $text): int
    {
        if ($text === '') {
            return 0;
        }

        $text = self::utf8($text);
        $pattern = '/[A-Za-z]+(?:\'[A-Za-z]+)?|\d+(?:[.,]\d+)*|[\x{4e00}-\x{9fff}\x{3040}-\x{30ff}\x{ac00}-\x{d7af}]|[^\s]/u';
        if (preg_match_all($pattern, $text, $matches) !== 1 && $matches[0] === []) {
            return 0;
        }

        $tokens = 0;
        foreach ($matches[0] as $piece) {
            $length = strlen($piece);
            if (preg_match('/^[A-Za-z]/', $piece) === 1) {
                $tokens += $length <= 6 ? 1 : (int) ceil($length / 4);
                continue;
            }
            if (preg_match('/^\d/', $piece) === 1) {
                $tokens += max(1, (int) ceil($length / 3));
                continue;
            }
            $tokens += 1;
        }

        return $tokens;
    }

    public static function utf8(string $text): string
    {
        if (preg_match('//u', $text) === 1) {
            return $text;
        }

        $clean = iconv('UTF-8', 'UTF-8//IGNORE', $text);

        return $clean === false ? '' : $clean;
    }
}
