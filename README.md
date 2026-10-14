# Prompt Diet

**En bref.** Petit outil PHP, zéro dépendance, qui répond à une question devenue chère en 2026 : *combien coûte ce prompt d’agent, et qu’est-ce qui est juste du gras ?* Collez un `AGENTS.md`, un skill file ou un log collé dans le contexte. Vous obtenez un ticket de caisse, puis une version allégée. Rien n’est envoyé nulle part.

---

Agents re-send the same fat context on every turn: duplicated policies, a stack trace, a base64 payload, the same webhook line 180 times. Prompt Diet prices that file against a dated list-price table and cuts only the waste it can defend.

On the bundled sample (`examples/fat-agent.md`, about 7,100 estimated tokens):

```text
PROMPT DIET  receipt  2026-10-14
grok-4.7  xAI                          in $2.00 / 1M
tokens in the file                             7,094
after diet                                458   -94%
> 200 turns/day                                $2.84
30-day projection                             $85.13
30-day after diet                              $5.50
30-day if prefix is cached                    $21.60
```

Same file, same 200 turns/day, input only: about **$170 / 30 days** on Claude Opus 5.5, about **$426** on Claude Fable 5.1. Those are list-price projections, not an invoice.

## Run it

Requires PHP 8.1+ with the PCRE extension (the default build). No Composer install is required.

```bash
git clone https://github.com/Nguemoue/project2026_20261014_prompt_diet.git
cd project2026_20261014_prompt_diet

php bin/prompt-diet estimate examples/fat-agent.md
php bin/prompt-diet estimate AGENTS.md --model claude-sonnet-5.5 --runs 80
php bin/prompt-diet diet examples/fat-agent.md > lean.md
php bin/prompt-diet models
```

`diet` writes the slimmed prompt to stdout and the receipt to stderr, so this keeps the receipt on screen:

```bash
php bin/prompt-diet diet AGENTS.md > AGENTS.lean.md
```

Local receipt page (still no upload):

```bash
php -S 127.0.0.1:8765 -t public
```

Open the site, choose **Load the bloated sample**, then **Print receipt**.

## CI gate

Fail a pull request when a committed prompt crosses a budget. The check uses the original file, not the slimmed copy. Exit code `2` means over budget.

```yaml
- name: Prompt budget
  run: php bin/prompt-diet estimate AGENTS.md --max-tokens 4000 --json
```

`--json` prints the report. Add `--with-slim` if you also want the cleaned text in the payload.

## What it cuts

Default mode is conservative. It will:

- drop duplicate paragraphs (and the heading that only introduced the copy)
- collapse stack traces to the first two frames and the last
- drop repeated log lines after the second copy
- replace long base64 / hex blobs and `data:` URIs with a short marker
- trim trailing whitespace and padded blank lines

It will not delete a unique sentence or a unique source line. Paths and URLs are left alone: base64’s alphabet contains `/`, and a Laravel path is not a secret.

`--aggressive` also crops fenced blocks longer than 40 lines, keeping the head and the tail.

## What the number means

`heuristic-v1` is a local estimate, not tiktoken and not the provider’s tokenizer. Short words count as one token, longer words as roughly one token per four characters, numbers per three digits, symbols and CJK characters as one. Whitespace is not billed on its own. Expect about **±20%** against a real byte-pair encoder. Use it to compare two versions of a prompt, not to reconcile an API bill.

Prices live in [`src/Prices.php`](src/Prices.php). The snapshot is **2026-10-14**, USD per million tokens (input, output, cached input), compiled from public list prices. Tiers, batch discounts, long-context surcharges and promos are ignored. Edit the table when a provider changes a price.

The 30-day figure is `turns per day × 30 × price`. It assumes the whole file is re-sent as input on every turn. Cached-prefix cost is shown beside it: first turn at the full input price, later turns at the cached input price.

## Tests

```bash
php tests/run.php
```

GitHub Actions runs the same file on PHP 8.1, 8.2, 8.3 and 8.4.

## Layout

```text
bin/prompt-diet          CLI
public/index.php         receipt page
src/                     tokenizer, prices, diet, report
examples/fat-agent.md    synthetic bloated prompt (no real secrets)
tests/run.php            assertions, no PHPUnit
```

## License

MIT. Copyright 2026 Luc (Nguemoue).
