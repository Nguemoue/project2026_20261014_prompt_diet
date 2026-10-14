<?php

declare(strict_types=1);

namespace PromptDiet;

final class Report
{
    /**
     * @return array<string, mixed>
     */
    public static function build(string $text, string $modelId, int $runs, int $outputTokens, bool $aggressive): array
    {
        $model = Prices::get($modelId);
        $runs = max(1, $runs);
        $outputTokens = max(0, $outputTokens);

        $diet = Diet::apply($text, $aggressive);
        $tokens = Tokenizer::estimate($text);
        $tokensAfter = Tokenizer::estimate($diet['text']);

        $ladderRuns = array_values(array_unique([1, 50, 200, $runs]));
        sort($ladderRuns);

        $ladder = [];
        foreach ($ladderRuns as $rowRuns) {
            $ladder[] = self::costRow($rowRuns, $tokens, $outputTokens, $model);
        }

        $highlighted = self::costRow($runs, $tokens, $outputTokens, $model);
        $after = self::costRow($runs, $tokensAfter, $outputTokens, $model);

        $compare = [];
        foreach (Prices::all() as $id => $prices) {
            $row = self::costRow($runs, $tokens, $outputTokens, $prices);
            $compare[] = [
                'id' => $id,
                'provider' => $prices['provider'],
                'per_turn' => self::once($tokens, $outputTokens, $prices),
                'total_usd' => $row['total_usd'],
                'cached_total_usd' => $row['cached_total_usd'],
                'selected' => $id === $modelId,
            ];
        }
        usort($compare, static fn (array $a, array $b): int => $a['total_usd'] <=> $b['total_usd']);

        return [
            'estimate' => Tokenizer::METHOD,
            'as_of' => Prices::AS_OF,
            'model_id' => $modelId,
            'provider' => $model['provider'],
            'prices' => $model,
            'bytes' => strlen($text),
            'tokens' => $tokens,
            'tokens_after' => $tokensAfter,
            'tokens_saved' => max(0, $tokens - $tokensAfter),
            'saved_ratio' => $tokens === 0 ? 0.0 : ($tokens - $tokensAfter) / $tokens,
            'runs' => $runs,
            'output_tokens' => $outputTokens,
            'aggressive' => $aggressive,
            'findings' => $diet['findings'],
            'slim' => $diet['text'],
            'ladder' => $ladder,
            'per_turn' => $highlighted['per_turn'],
            'total_usd' => $highlighted['total_usd'],
            'cached_total_usd' => $highlighted['cached_total_usd'],
            'month_usd' => $highlighted['total_usd'] * 30,
            'month_after_usd' => $after['total_usd'] * 30,
            'cached_month_usd' => $highlighted['cached_total_usd'] * 30,
            'compare' => $compare,
        ];
    }

    /**
     * @param array{provider:string, input:float, output:float, cached:float} $prices
     * @return array{runs:int, per_turn:float, total_usd:float, cached_total_usd:float}
     */
    private static function costRow(int $runs, int $inputTokens, int $outputTokens, array $prices): array
    {
        $perTurn = self::once($inputTokens, $outputTokens, $prices);
        $input = self::tokensCost($inputTokens, $prices['input']);
        $cached = self::tokensCost($inputTokens, $prices['cached']);
        $output = self::tokensCost($outputTokens, $prices['output']);

        return [
            'runs' => $runs,
            'per_turn' => $perTurn,
            'total_usd' => $runs * $perTurn,
            'cached_total_usd' => $input + ($runs - 1) * $cached + $runs * $output,
        ];
    }

    /**
     * @param array{provider:string, input:float, output:float, cached:float} $prices
     */
    private static function once(int $inputTokens, int $outputTokens, array $prices): float
    {
        return self::tokensCost($inputTokens, $prices['input']) + self::tokensCost($outputTokens, $prices['output']);
    }

    public static function tokensCost(int $tokens, float $perMillion): float
    {
        return ($tokens / 1000000) * $perMillion;
    }

    public static function money(float $usd): string
    {
        $abs = abs($usd);
        if ($abs < 0.0000005) {
            return '$0.00';
        }
        if ($abs < 0.0001) {
            return '$' . number_format($usd, 6, '.', ',');
        }
        if ($abs < 1) {
            return '$' . number_format($usd, 4, '.', ',');
        }

        return '$' . number_format($usd, 2, '.', ',');
    }

    public static function percent(float $ratio): string
    {
        return number_format(max(0, $ratio) * 100, 0) . '%';
    }
}
