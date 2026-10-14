<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use PromptDiet\Cli;
use PromptDiet\Diet;
use PromptDiet\Prices;
use PromptDiet\Report;
use PromptDiet\Tokenizer;
use PromptDiet\Web;

$passed = 0;

function check(bool $condition, string $message): void
{
    global $passed;
    if (!$condition) {
        fwrite(STDERR, "FAIL {$message}\n");
        exit(1);
    }
    $passed++;
}

check(Tokenizer::estimate('') === 0, 'empty string is zero tokens');
check(Tokenizer::estimate('Hello') === 1, 'short word is one token');
$fox = Tokenizer::estimate('The quick brown fox jumps over the lazy dog.');
check($fox >= 9 && $fox <= 12, "fox sentence in range, got {$fox}");
check(Tokenizer::estimate("line\n\n\n\nline") === Tokenizer::estimate("line\nline"), 'whitespace is not billed on its own');

$million = Report::tokensCost(1000000, 2.0);
check(abs($million - 2.0) < 1e-9, 'one million tokens at $2 is $2');
check(Report::money(2.0) === '$2.00', 'money formats dollars');
check(Report::money(0.025) === '$0.0250', 'money formats sub-dollar');

$policy = 'You must not invent columns, routes, or environment variables. Quote the file you used before changing it.';
$duped = $policy . "\n\n" . $policy . "\n\nKeep the currency in XAF.\n";
$diet = Diet::apply($duped, false);
check(substr_count($diet['text'], 'You must not invent') === 1, 'duplicate paragraph kept once');
check(str_contains($diet['text'], 'Keep the currency in XAF.'), 'unique line survives');
$ids = array_column($diet['findings'], 'id');
check(in_array('duplicates', $ids, true), 'duplicate finding recorded');

$blob = str_repeat('QUJDREVG', 20);
$withBlob = "note\n{$blob}\nend\n";
$stripped = Diet::apply($withBlob, false);
check(!str_contains($stripped['text'], 'QUJDREVGQUJD'), 'base64 blob removed');
check(str_contains($stripped['text'], '[base64 omitted'), 'base64 marker present');
check(str_contains($stripped['text'], 'end'), 'neighbours kept');

$hex = str_repeat('ab', 40);
$hexDiet = Diet::apply("key {$hex} end", false);
check(str_contains($hexDiet['text'], '[hex omitted'), 'long hex removed');
check(!str_contains($hexDiet['text'], $hex), 'hex digits gone');

$frames = '';
for ($i = 0; $i < 8; $i++) {
    $frames .= "#{$i} /var/www/html/app/File{$i}.php(10): App\\Demo->run()\n";
}
$stack = Diet::apply("boom\n{$frames}done\n", false);
check(substr_count($stack['text'], '/var/www/html/app/File') === 3, 'three frames kept');
check(str_contains($stack['text'], '5 frames omitted'), 'omitted frame count');
check(str_contains($stack['text'], 'done'), 'text after stack kept');

$log = '';
for ($i = 0; $i < 10; $i++) {
    $log .= "2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo\n";
}
$logs = Diet::apply($log . "Please keep this sentence.\n", false);
check(substr_count($logs['text'], 'retrying webhook') === 2, 'two log lines kept');
check(str_contains($logs['text'], 'Please keep this sentence.'), 'prose after logs kept');

$code = "```php\n";
for ($i = 1; $i <= 50; $i++) {
    $code .= "echo {$i};\n";
}
$code .= "```\n";
$safe = Diet::apply($code, false);
check(str_contains($safe['text'], 'echo 50;'), 'default diet keeps unique code');
$aggressive = Diet::apply($code, true);
check(!str_contains($aggressive['text'], 'echo 30;'), 'aggressive diet drops the middle of a long fence');
check(str_contains($aggressive['text'], 'echo 1;'), 'aggressive diet keeps the head');
check(str_contains($aggressive['text'], 'echo 50;'), 'aggressive diet keeps the tail');
check(str_contains($aggressive['text'], 'fenced lines omitted'), 'crop marker present');

$padded = "alpha\n\n\n\n\nbeta\n";
$collapsed = Diet::apply($padded, false);
check($collapsed['text'] === "alpha\n\nbeta\n", 'blank padding collapsed');

$once = Diet::apply($withBlob . "\n\n" . $duped . $frames . $log, false);
$twice = Diet::apply($once['text'], false);
check($twice['text'] === $once['text'], 'diet is idempotent');

$saved = 0;
foreach ($once['findings'] as $finding) {
    $saved += $finding['tokens_saved'];
}
$before = Tokenizer::estimate($withBlob . "\n\n" . $duped . $frames . $log);
$after = Tokenizer::estimate($once['text']);
check($saved === $before - $after, "finding deltas sum to the token delta ({$saved} vs " . ($before - $after) . ')');

$report = Report::build($duped, 'grok-4.7', 10, 0, false);
check($report['runs'] === 10, 'runs preserved');
check($report['month_usd'] === $report['total_usd'] * 30, 'month is 30 days');
check(abs($report['total_usd'] - (10 * $report['per_turn'])) < 1e-9, 'daily total is runs times one turn');
check(count($report['compare']) === count(Prices::all()), 'every model is priced');

$threw = false;
try {
    Report::build('x', 'not-a-model', 1, 0, false);
} catch (InvalidArgumentException) {
    $threw = true;
}
check($threw, 'unknown model throws');

$dirty = "spaced    \nword\n";
check(Diet::apply($dirty, false)['text'] === "spaced\nword\n", 'trailing space stripped');

$path = '/var/www/html/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php';
check(str_contains(Diet::apply("see {$path} please", false)['text'], $path), 'unix paths are not treated as blobs');

$headed = "## Policy\n\n{$policy}\n\n## Policy reminder\n\n{$policy}\n\n## Keep\n\nThe billing currency is XAF. Do not convert it, and do not round it.\n";
$headedDiet = Diet::apply($headed, false);
check(substr_count($headedDiet['text'], '## Policy') === 1, 'heading of a repeated section is dropped');
check(str_contains($headedDiet['text'], '## Keep'), 'unique heading survives');

$html = Web::handle(['sample' => '1'], []);
check(str_contains($html, 'PROMPT DIET'), 'sample page renders');
check(str_contains($html, 'The billing currency is XAF'), 'sample content loaded');
check(str_contains($html, '30-day projection'), 'sample prints a receipt');
check(str_contains($html, '[base64 omitted'), 'sample receipt shows the blob was cut');

$payload = '<script>alert(1)</script>';
$xss = Web::handle([], [
    'text' => $payload,
    'model' => 'grok-4.7',
    'runs' => '3',
    'output_tokens' => '0',
]);
$escaped = htmlspecialchars($payload, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
check(!str_contains($xss, $payload), 'raw script tag is not echoed');
check(str_contains($xss, $escaped), 'script tag is escaped');

$bad = Web::handle([], [
    'text' => 'hello',
    'model' => '<b>nope</b>',
    'runs' => '1',
    'output_tokens' => '0',
]);
check(str_contains($bad, 'Unknown model'), 'bad model is rejected');
check(!str_contains($bad, '<b>nope</b>'), 'bad model id is not injected as html');

$root = dirname(__DIR__);
$run = static function (array $args) use ($root): array {
    $command = escapeshellarg($root . '/bin/prompt-diet');
    $php = escapeshellarg(PHP_BINARY);
    $parts = [$php, $command];
    foreach ($args as $arg) {
        $parts[] = escapeshellarg($arg);
    }
    $spec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open(implode(' ', $parts), $spec, $pipes, $root);
    if (!is_resource($process)) {
        fwrite(STDERR, "FAIL could not start cli\n");
        exit(1);
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($process);

    return ['code' => $code, 'out' => (string) $stdout, 'err' => (string) $stderr];
};

$estimate = $run(['estimate', 'examples/fat-agent.md', '--no-color', '--runs', '200']);
check($estimate['code'] === 0, 'estimate exits 0, got ' . $estimate['code'] . ' ' . $estimate['err']);
check(str_contains($estimate['out'], 'PROMPT DIET'), 'estimate prints a receipt');
check(str_contains($estimate['out'], 'XAF') === false, 'receipt does not dump the prompt body');
check(str_contains($estimate['out'], 'Duplicate paragraphs') || str_contains($estimate['out'], 'Base64'), 'estimate reports waste');

$budget = $run(['estimate', 'examples/fat-agent.md', '--max-tokens', '10', '--json']);
check($budget['code'] === 2, 'over budget exits 2, got ' . $budget['code']);
$decoded = json_decode($budget['out'], true);
check(is_array($decoded) && ($decoded['tokens'] ?? 0) > 10, 'json report includes tokens');
check(!array_key_exists('slim', $decoded), 'json omits slim text unless asked');

$models = $run(['models']);
check($models['code'] === 0 && str_contains($models['out'], 'grok-4.7'), 'models lists grok');

$unknown = $run(['estimate', 'examples/fat-agent.md', '--model', 'nope']);
check($unknown['code'] === 1, 'unknown model exits 1');

$dietOut = $run(['diet', 'examples/fat-agent.md']);
check($dietOut['code'] === 0, 'diet exits 0');
check(str_contains($dietOut['out'], 'The billing currency is XAF. Do not convert it.'), 'diet stdout keeps the rule');
check(substr_count($dietOut['out'], 'You must not invent columns') === 1, 'diet stdout dedupes policy');

$syntaxFiles = [
    'bin/prompt-diet',
    'public/index.php',
    'src/bootstrap.php',
    'src/Tokenizer.php',
    'src/Prices.php',
    'src/Diet.php',
    'src/Report.php',
    'src/Cli.php',
    'src/Web.php',
];
foreach ($syntaxFiles as $file) {
    $php = escapeshellarg(PHP_BINARY);
    $target = escapeshellarg($root . '/' . $file);
    exec("{$php} -l {$target}", $lintOut, $lintCode);
    check($lintCode === 0, "syntax {$file}");
}

fwrite(STDOUT, "OK {$passed} checks\n");
