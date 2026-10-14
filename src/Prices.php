<?php

declare(strict_types=1);

namespace PromptDiet;

/**
 * Public list-price snapshot, USD per 1,000,000 tokens.
 *
 * Compiled 2026-10-14 from provider pages mirrored by public price trackers
 * (input / output / cached input). Tiers, batch discounts, long-context
 * surcharges and promotions are ignored on purpose. Edit the table — do not
 * treat it as an invoice.
 */
final class Prices
{
    public const AS_OF = '2026-10-14';

    /**
     * @return array<string, array{provider:string, input:float, output:float, cached:float}>
     */
    public static function all(): array
    {
        return [
            'grok-4.7' => ['provider' => 'xAI', 'input' => 2.00, 'output' => 6.00, 'cached' => 0.50],
            'claude-haiku-5.5' => ['provider' => 'Anthropic', 'input' => 0.10, 'output' => 0.50, 'cached' => 0.01],
            'claude-sonnet-5.5' => ['provider' => 'Anthropic', 'input' => 2.00, 'output' => 10.00, 'cached' => 0.10],
            'claude-opus-5.5' => ['provider' => 'Anthropic', 'input' => 4.00, 'output' => 20.00, 'cached' => 0.20],
            'claude-fable-5.1' => ['provider' => 'Anthropic', 'input' => 10.00, 'output' => 50.00, 'cached' => 0.25],
            'gpt-6-luna' => ['provider' => 'OpenAI', 'input' => 0.10, 'output' => 0.50, 'cached' => 0.01],
            'gpt-6.1-sol' => ['provider' => 'OpenAI', 'input' => 2.00, 'output' => 10.00, 'cached' => 0.10],
            'gpt-6-astra' => ['provider' => 'OpenAI', 'input' => 10.00, 'output' => 50.00, 'cached' => 1.00],
            'gemini-3.5-flash-lite' => ['provider' => 'Google', 'input' => 0.30, 'output' => 2.50, 'cached' => 0.03],
            'gemini-3.8-flash' => ['provider' => 'Google', 'input' => 0.75, 'output' => 3.75, 'cached' => 0.075],
            'gemini-3.1-pro' => ['provider' => 'Google', 'input' => 2.00, 'output' => 12.00, 'cached' => 0.20],
        ];
    }

    /**
     * @return array{provider:string, input:float, output:float, cached:float}
     */
    public static function get(string $id): array
    {
        $all = self::all();
        if (!isset($all[$id])) {
            $known = implode(', ', array_keys($all));
            throw new \InvalidArgumentException("Unknown model \"{$id}\". Known: {$known}");
        }

        return $all[$id];
    }
}
