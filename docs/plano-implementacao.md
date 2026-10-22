# Plano de Implementação — Gerador de Senhas v0.1 (sugerido após revisão da unidade 2)

## Fase 0 — Decisões e alinhamento de docs (pré-condição) - Implementado
- **Escopo v0.1 final:** RF-01 (geração CSPRNG), RF-02 (`--length` 8–64), RF-03 (conjuntos `--no-*`), RF-05 (stdout puro), RF-06 (`--help`), RF-07 (validação), RF-08 (PHPUnit), RF-09 (executabilidade). **Remove:** RF-04 (`--count`) → v0.2; RNF-06 (benchmark) → v1.0.
- **Corrigir 3 docs:** `escopo.md` (remover `--count`/benchmark, mover p/ fora de escopo), `README.md` (mesmo), `backlog.md` (adicionar seção **v0.1** com RTs mapeados — hoje ela não existe).
- **Padronizar mensagens de erro** (texto exato p/ asserts), ex.:
  - `Erro: --length deve estar entre 8 e 64.` (exit 1, stderr)
  - `Erro: ao menos um conjunto de caracteres deve permanecer ativo.` (exit 1, stderr)
  - `Erro: flag desconhecida --foo` (exit 1, stderr)
- **Decisão técnica de aleatoriedade (mitiga viés):** um `random_bytes(strlen($pool))` por senha + `random_int()` como reserva documentada; ou, mais simples e auditável: **`random_int(0, $len-1)` por caractere** (CSPRNG, zero viés de módulo) — documentar em `docs/security.md` resumido no README.

## Fase 1 — Estrutura de projeto e testabilidade
- `composer.json` → PSR-4 `Genpass\` → `src/`; `require-dev: phpunit/phpunit ^10`; script `test: "phpunit"`.
- `phpunit.xml.dist` (não `phpunit.xml`!) + **remover `phpunit.xml` do `.gitignore`** (trocar por `phpunit.xml` local).
- `tests/_bootstrap.php` (se necessário) e diretório `tests/Unit/`.
- **Arquitetura testável (2 camadas):**
  - `src/PasswordGenerator.php` — classe pura: `generate(int $length, array $options): string`; sem I/O, sem `exit`; lança `InvalidArgumentException` em erro.
  - `src/OptionParser.php` — parse das flags → objeto/arrays; sem I/O.
  - `bin/generator.php` — fino: SAPI guard (como `status.php`), parser, loop, `fwrite(STDOUT)`, exit codes.

## Fase 2 — Testes primeiro (TDD — os RTs da v0.1)
- `tests/Unit/PasswordGeneratorTest.php`:
  - RT-01: tamanho exato (`--length` 8, 16, 64)
  - RT-02: default = 16
  - RT-03: `--no-upper/--no-lower/--no-digits/--no-symbols` excluem apenas o conjunto indicado
  - RT-04: todos os conjuntos desligados → exceção
  - RT-05: `length < 8` ou `> 64` → exceção
  - RT-06: **distribuição** — 10k amostras, cada caractere do pool presente (teste fraco contra viés, determinístico o suficiente)
- `tests/Unit/OptionParserTest.php`:
  - RT-07: flags válidas, `--length=abc` → erro, flag desconhecida → erro, `--help`
- **Meta de cobertura:** 100% das classes de `src/` (são pequenas — factível).
- `tests/Integration/CommandLineTest.php` (opcional, se ambiente cooperar): executa `php bin/generator.php --help` e valida exit code 0 / stderr em erro.

## Fase 3 — Implementação
- `src/PasswordGenerator.php` + `src/OptionParser.php` conforme testes.
- `bin/generator.php` (convenção de `status.php`: strict types, SAPI guard, docblock).
- Conjuntos fixos ASCII: `A-Z a-z 0-9 !@#$%^&*()-_=+[]{}<>?/` (evita issues de codepage Windows).

## Fase 4 — Verificação e release
- `composer install && composer test` → suíte **OK**, cobertura reportada.
- Checklist DoD do escopo (ajustado): geração default, flags, `--help` exit 0, erro → stderr + exit ≠ 0, suíte OK, sem `require` runtime, tag `v0.1.0`.
- Commit message + tag git (verificar `git config` usuário — o relatório cita que foi configurado).

---

## Ordem de execução (resumo)
1. Fase 0 (docs — ~15 min) → 2. Fase 1 (esqueleto) → 3. Fase 2 (testes) → 4. Fase 3 (código) → 5. Fase 4 (validação + tag).

**Pré-requisito a verificar em Act mode:** `php -v` e `composer -V` disponíveis no PATH (o `php -v` não respondeu agora — se não houver PHP local, ajustamos a Fase 4 para validar por outro meio).

---

Posso seguir com essa ordem. Quando estiver satisfeito(a), **toggle para Act mode** e eu executo as 5 fases, validando a suíte de testes ao final. Se quiser ajustar algo antes (ex.: manter `--length` até 128, ou pular a integração via `proc_open`), me diga.