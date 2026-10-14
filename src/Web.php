<?php

declare(strict_types=1);

namespace PromptDiet;

final class Web
{
    public static function examplePath(): string
    {
        return dirname(__DIR__) . '/examples/fat-agent.md';
    }

    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $post
     */
    public static function handle(array $query, array $post): string
    {
        $models = Prices::all();
        $model = is_string($post['model'] ?? null) ? $post['model'] : 'grok-4.7';
        $runsRaw = is_string($post['runs'] ?? null) ? $post['runs'] : '200';
        $outputRaw = is_string($post['output_tokens'] ?? null) ? $post['output_tokens'] : '0';
        $aggressive = isset($post['aggressive']);
        $text = is_string($post['text'] ?? null) ? $post['text'] : '';
        $error = null;
        $report = null;

        if ($text === '' && isset($query['sample'])) {
            $sample = file_get_contents(self::examplePath());
            $text = $sample === false ? '' : $sample;
        }

        $submitted = isset($post['text']) || isset($query['sample']);
        if ($submitted && $text !== '') {
            if (strlen($text) > 500000) {
                $error = 'Paste is over 500 KB. Split it, or run the CLI on the file.';
            } elseif (!isset($models[$model])) {
                $error = 'Unknown model. Pick one from the list.';
                $model = 'grok-4.7';
            } elseif (preg_match('/^\d+$/', $runsRaw) !== 1 || (int) $runsRaw < 1) {
                $error = 'Turns per day must be a positive integer.';
            } elseif (preg_match('/^\d+$/', $outputRaw) !== 1) {
                $error = 'Output tokens must be a non-negative integer.';
            } else {
                $report = Report::build($text, $model, (int) $runsRaw, (int) $outputRaw, $aggressive);
            }
        }

        return self::page($report, $error, $text, $model, $runsRaw, $outputRaw, $aggressive);
    }

    /**
     * @param array<string, mixed>|null $report
     */
    private static function page(?array $report, ?string $error, string $text, string $model, string $runs, string $outputTokens, bool $aggressive): string
    {
        $models = Prices::all();
        $options = '';
        foreach ($models as $id => $price) {
            $selected = $id === $model ? ' selected' : '';
            $options .= '<option value="' . self::e($id) . '"' . $selected . '>'
                . self::e($id . ' · ' . $price['provider']) . '</option>';
        }

        $checked = $aggressive ? ' checked' : '';
        $body = $report === null ? self::emptyReceipt($error) : self::receipt($report);
        if ($error !== null && $report === null) {
            $body = '<p class="error">' . self::e($error) . '</p>' . $body;
        }

        $year = '2026';

        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Prompt Diet</title>
<style>
  :root { color-scheme: dark; }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    min-height: 100vh;
    background:
      radial-gradient(1200px 500px at 50% -10%, #3a3126 0%, transparent 55%),
      #14110e;
    color: #1b1712;
    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
  }
  .desk { max-width: 720px; margin: 0 auto; padding: 28px 16px 64px; }
  header.top { color: #f3ead7; margin-bottom: 18px; }
  header.top p { margin: 6px 0 0; color: #b7aa96; line-height: 1.45; }
  h1 { font-size: 15px; letter-spacing: 0.22em; font-weight: 600; margin: 0; }
  .paper {
    background: #f6f1e6;
    padding: 26px 22px 28px;
    box-shadow: 0 24px 70px rgba(0,0,0,.45);
    border-radius: 2px 2px 0 0;
  }
  .brand { display: flex; justify-content: space-between; gap: 12px; font-size: 12px; letter-spacing: 0.14em; }
  .rule { border: none; border-top: 1px dashed #1b1712; margin: 14px 0; }
  .due { font-family: Georgia, "Iowan Old Style", serif; font-size: clamp(42px, 10vw, 68px); line-height: 0.95; margin: 4px 0 2px; letter-spacing: -1px; }
  .sub { color: #5e564b; font-size: 12px; line-height: 1.45; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  td { padding: 4px 0; vertical-align: baseline; }
  td.num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
  tr.hot td { font-weight: 700; }
  .save { color: #1d6b34; }
  .finding { margin: 0 0 8px; }
  .finding b { font-weight: 650; }
  details { margin-top: 8px; }
  summary { cursor: pointer; }
  textarea, select, input[type="number"] {
    width: 100%;
    font: inherit;
    color: inherit;
    background: transparent;
    border: 1px solid #1b1712;
    border-radius: 0;
    padding: 8px;
  }
  textarea { min-height: 180px; resize: vertical; line-height: 1.4; }
  label { display: block; font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; margin: 12px 0 6px; }
  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .check { display: flex; gap: 8px; align-items: center; margin: 12px 0; font-size: 13px; text-transform: none; letter-spacing: 0; }
  button, .ghost {
    font: inherit;
    border: 1px solid #1b1712;
    background: #1b1712;
    color: #f6f1e6;
    padding: 10px 14px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
  }
  .ghost { background: transparent; color: #1b1712; }
  .actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
  .error { background: #fff1e8; border: 1px solid #9a3412; color: #9a3412; padding: 8px 10px; }
  pre.slim { white-space: pre-wrap; word-break: break-word; font: inherit; font-size: 12px; background: #efe8d8; padding: 10px; max-height: 280px; overflow: auto; }
  footer { color: #8d8272; font-size: 12px; margin-top: 14px; line-height: 1.45; }
  @media (max-width: 640px) {
    .grid { grid-template-columns: 1fr; }
    .paper { padding: 20px 14px 22px; }
    .actions a, .actions button { flex: 1 1 140px; text-align: center; }
  }
</style>
</head>
<body>
<div class="desk">
  <header class="top">
    <h1>PROMPT DIET</h1>
    <p>A local receipt for agent prompts. The text never leaves this machine. Prices are a ' . $year . '-10-14 list-price snapshot, not an invoice.</p>
  </header>
  <article class="paper">
    ' . $body . '
    <hr class="rule">
    <form method="post" action="?">
      <label for="text">Prompt, AGENTS.md, skill file, log</label>
      <textarea id="text" name="text" spellcheck="false">' . self::e($text) . '</textarea>
      <div class="grid">
        <div>
          <label for="model">Model</label>
          <select id="model" name="model">' . $options . '</select>
        </div>
        <div>
          <label for="runs">Turns per day</label>
          <input id="runs" name="runs" type="number" min="1" step="1" value="' . self::e($runs) . '">
        </div>
      </div>
      <label for="output_tokens">Output tokens expected each turn (0 = input only)</label>
      <input id="output_tokens" name="output_tokens" type="number" min="0" step="1" value="' . self::e($outputTokens) . '">
      <label class="check"><input type="checkbox" name="aggressive" value="1"' . $checked . '> Aggressive: also crop fenced blocks over 40 lines</label>
      <div class="actions">
        <button type="submit">Print receipt</button>
        <a class="ghost" href="?sample=1">Load the bloated sample</a>
      </div>
    </form>
  </article>
  <footer>Heuristic ' . self::e(Tokenizer::METHOD) . ', usually within 20% of a real tokenizer. Edit prices in src/Prices.php. CLI: <code>php bin/prompt-diet estimate examples/fat-agent.md</code></footer>
</div>
</body>
</html>';
    }

    private static function emptyReceipt(?string $error): string
    {
        if ($error !== null) {
            return '<div class="brand"><span>PROMPT DIET</span><span>NO SALE</span></div>
              <hr class="rule">
              <p class="due">$0.00</p>
              <p class="sub">Fix the ticket and print it again.</p>';
        }

        return '<div class="brand"><span>PROMPT DIET</span><span>WAITING</span></div>
          <hr class="rule">
          <p class="due">$—.——</p>
          <p class="sub">Paste a system prompt or open the sample. You will get tokens, a 30-day projection, and a cut list.</p>';
    }

    /**
     * @param array<string, mixed> $report
     */
    private static function receipt(array $report): string
    {
        $rows = '';
        foreach ($report['ladder'] as $step) {
            $class = $step['runs'] === $report['runs'] ? ' class="hot"' : '';
            $rows .= '<tr' . $class . '><td>' . (int) $step['runs'] . ' turns/day</td><td class="num">'
                . self::e(Report::money((float) $step['total_usd'])) . '</td></tr>';
        }

        $findings = '';
        if ($report['findings'] === []) {
            $findings = '<p class="sub">No high-confidence waste. Try aggressive mode for long code fences.</p>';
        } else {
            foreach ($report['findings'] as $finding) {
                $findings .= '<p class="finding"><b>' . self::e($finding['title']) . '</b> · '
                    . number_format((int) $finding['tokens_saved']) . ' tokens<br><span class="sub">'
                    . self::e($finding['detail']) . '</span></p>';
            }
        }

        $compare = '';
        foreach ($report['compare'] as $row) {
            $class = $row['selected'] ? ' class="hot"' : '';
            $compare .= '<tr' . $class . '><td>' . self::e($row['id']) . '</td><td class="num">'
                . self::e(Report::money((float) $row['total_usd'])) . '</td></tr>';
        }

        $ratio = Report::percent((float) $report['saved_ratio']);

        return '<div class="brand"><span>PROMPT DIET</span><span>' . self::e($report['as_of']) . '</span></div>
          <p class="sub">' . self::e($report['model_id']) . ' · ' . self::e($report['provider'])
            . ' · input $' . number_format((float) $report['prices']['input'], 2) . ' / 1M tokens</p>
          <hr class="rule">
          <p class="sub">30-day projection at ' . (int) $report['runs'] . ' turns/day'
            . ((int) $report['output_tokens'] === 0 ? ', input only' : ', plus ' . (int) $report['output_tokens'] . ' output tokens')
            . '</p>
          <p class="due">' . self::e(Report::money((float) $report['month_usd'])) . '</p>
          <p class="sub save">After diet: ' . self::e(Report::money((float) $report['month_after_usd']))
            . ' · cut ' . self::e($ratio) . ' (' . number_format((int) $report['tokens_saved']) . ' tokens)</p>
          <hr class="rule">
          <table>
            <tr><td>Tokens in</td><td class="num">' . number_format((int) $report['tokens']) . '</td></tr>
            <tr><td>Tokens after diet</td><td class="num">' . number_format((int) $report['tokens_after']) . '</td></tr>
            ' . $rows . '
            <tr><td>30-day if the prefix is cached</td><td class="num">' . self::e(Report::money((float) $report['cached_month_usd'])) . '</td></tr>
          </table>
          <hr class="rule">
          <p class="sub">CUT LIST</p>
          ' . $findings . '
          <hr class="rule">
          <p class="sub">SAME PROMPT, OTHER MODELS · ' . (int) $report['runs'] . ' TURNS/DAY</p>
          <table>' . $compare . '</table>
          <details>
            <summary>Slimmed prompt</summary>
            <pre class="slim">' . self::e((string) $report['slim']) . '</pre>
          </details>';
    }

    private static function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
