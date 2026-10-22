# Escopo — Gerador de Senhas Seguras (MVP v0.1)

**Documento:** Documento de Escopo
**Versão-alvo:** 0.1 (MVP)
**Data:** 21/10/2026
**Status:** Aprovado (escopo realinhado em 21/10/2026 — ver §9, Histórico de Alterações)

---

## 1. Objetivo

Desenvolver uma ferramenta de linha de comando (CLI) em PHP puro que gere senhas aleatórias e criptograficamente seguras, com controle sobre tamanho e conjuntos de caracteres. A implementação deve priorizar **simplicidade, segurança e zero dependências externas**, utilizando o CSPRNG nativo do PHP (`random_bytes()`) como fonte de entropia.

---

## 2. Requisitos Funcionais

| ID | Requisito | Detalhe |
|----|-----------|---------|
| RF-01 | Geração de senha aleatória | A senha deve ser gerada exclusivamente com fonte CSPRNG nativa do PHP (`random_bytes()` / `random_int()`). Proibido o uso de `rand()`, `mt_rand()` ou qualquer PRNG determinístico. A seleção de caracteres não deve introduzir viés (ver §8). |
| RF-02 | Controle de tamanho | A flag `--length=N` define o tamanho da senha. Valores válidos: 8–64. Default: `16`. |
| RF-03 | Controle de conjuntos de caracteres | Flags para inclusão/exclusão de: maiúsculas (`--no-upper`), minúsculas (`--no-lower`), dígitos (`--no-digits`), símbolos (`--no-symbols`). Ao menos um conjunto deve permanecer ativo. |
| RF-04 | Saída no stdout | Output textual, uma senha por linha. Sem formatação adicional (sem JSON, CSV, cores ou barras de progresso). |
| RF-05 | Flag `--help` | Exibe a lista de flags disponíveis, valores padrão e exemplo de uso. |
| RF-06 | Validação de entrada | Parâmetros fora do intervalo (ex: `--length=4`) devem gerar erro de uso (exit code ≠ 0) com mensagem clara no stderr (mensagens padronizadas em §7 — Mensagens de Erro Padrão). |
| RF-07 | Testes unitários | Cobertura via PHPUnit para: geração de senha, validação de tamanho, validação de conjuntos de caracteres e mensagem de erro. |
| RF-08 | Executabilidade | O script `bin/generator.php` deve ser executável via `php bin/generator.php` em qualquer ambiente com PHP ≥ 8.1 no `PATH`. |

> **Nota (21/10/2026):** a antiga RF-04 "Geração em lote (`--count`)" foi **removida** da v0.1 e realocada para a **v0.2** (concordância com o `backlog.md` e o `relatorio.md`). Os IDs subsequentes foram renumerados (antiga RF-05 → RF-04 … RF-09 → RF-08).

---

## 3. Requisitos Não Funcionais

| ID | Requisito | Critério |
|----|-----------|----------|
| RNF-01 | Linguagem e runtime | PHP ≥ 8.1, sem extensões além do núcleo padrão (não requer `ext-gmp`, `ext-sodium`, etc.). |
| RNF-02 | Dependências | Zero dependências externas em runtime. PHPUnit é dependência de **dev** apenas. |
| RNF-03 | Segurança — Entropia | A fonte aleatória deve ser `random_bytes()`. Não deve haver seeding manual nem reutilização de estado entre gerações. |
| RNF-04 | Segurança — Sem estado | A ferramenta não grava senhas em disco, log, clipboard ou memória persistente. Toda senha é efêmera (stdout → stdout). |
| RNF-05 | Portabilidade | Funciona em Linux, macOS e Windows (via WSL ou nativo) sem configuração adicional. |
| RNF-06 | Segurança de código | Nenhum `eval()`, `include` dinâmico ou interação com filesystem além de leitura do próprio script. |
| RNF-07 | Reprodutibilidade de testes | Suíte PHPUnit deve rodar determinística (`vendor/bin/phpunit`) sem dependência de rede. |

> **Nota (21/10/2026):** a antiga RNF-06 "Performance (benchmark)" foi **removida** da v0.1 e realocada para a **v1.0**. IDs subsequentes renumerados.

---

## 4. Fora de Escopo (v0.1)

Os itens abaixo **não** fazem parte do MVP e estão agendados para versões futuras:

| Item | Versão-alvo |
|------|-------------|
| Saída em formato estruturado (`--format=json\|csv`) | v0.2 |
| Geração em lote (`--count=N`, max 100) — antiga RF-04 | v0.2 |
| Barra de progresso para lotes | v0.2 |
| Cópia para clipboard (`--copy`) | v0.2 |
| Cálculo de entropia (bits) | v0.3 |
| Modo "passphrase" (Diceware) | v0.3 |
| Whitelist/blacklist de caracteres ambíguos (`0/O`, `1/l`) | v0.3 |
| Auditoria de viés (teste qui-quadrado) | v0.3 |
| Benchmark de performance (antiga RNF-06: 100 senhas de 64 caracteres em < 1 s) | v1.0 |
| Documentação completa (guia de referência, arquitetura) | v1.0 |
| Pipeline de CI/CD (GitHub Actions) | v1.0 |
| Publicação em Packagist | v1.0 |
| Interface gráfica (TUI, web, GUI) | — |
| Persistência ou histórico de senhas | — |
| Autenticação / controle de acesso | — |

---

## 5. Critérios de Aceite (Definição de Pronto — v0.1)

> Os itens abaixo estão **pendentes**: a implementação ainda não foi desenvolvida (21/10/2026).

- [ ] `php bin/generator.php` gera uma senha de 16 caracteres no stdout.
- [ ] Todas as flags da tabela RF-02 a RF-05 funcionam conforme especificado.
- [ ] `php bin/generator.php --help` exibe a ajuda e retorna exit code 0.
- [ ] Entrada inválida retorna exit code ≠ 0 e mensagem exata no stderr (ver §7).
- [ ] `vendor/bin/phpunit` executa a suíte completa com status **OK**.
- [ ] Nenhum `composer.json` require de runtime (apenas `require-dev`).
- [ ] Tag `v0.1.0` criada no repositório.

---

## 6. Restrições e Premissas

- O ambiente alvo possui PHP 8.1+ disponível via `PATH`.
- O operador não requer persistência; senhas são descartáveis por design.
- Não há exigência de compatibilidade com PHP 8.0 ou inferior.
- O repositório utiliza o padrão de layout PSR-4 apenas se necessário; o MVP pode ser monolítico (`bin/generator.php` + `src/`).

---

## 7. Mensagens de Erro Padrão (RF-06)

Todas as mensagens de erro saem **somente no stderr**, com exit code `1`, no formato `Erro: <mensagem>`. Estes textos são contratos de teste — os asserts do PHPUnit devem compará-los exatamente:

| Caso | Mensagem exata | Exit code |
|------|----------------|-----------|
| `--length` fora do intervalo (ex.: `--length=4`, `--length=65`) | `Erro: --length deve estar entre 8 e 64.` | 1 |
| `--length` não numérico (ex.: `--length=abc`) | `Erro: --length deve ser um inteiro entre 8 e 64.` | 1 |
| Todos os conjuntos desativados | `Erro: ao menos um conjunto de caracteres deve permanecer ativo.` | 1 |
| Flag desconhecida (ex.: `--foo`) | `Erro: flag desconhecida --foo` | 1 |

---

## 8. Decisão Técnica — Aleatoriedade sem Viés (RF-01)

- Conversão de `random_bytes()` diretamente para índices de caractere **por módulo** (`byte % strlen($pool)`) introduz **viés de módulo** quando `strlen($pool)` não divide 256 — **não é aceitável** como critério de projeto.
- Critério adotado: seleção de caractere via **`random_int(0, strlen($pool) - 1)`** por posição (CSPRNG do PHP, isento de viés de módulo), ou, alternativamente, amostragem de `random_bytes()` com **rejeição** (rejection sampling). A escolha final deve ser documentada no código.
- `rand()`, `mt_rand()` e seeding manual seguem proibidos (RF-01 / RNF-03).

---

## 9. Histórico de Alterações

| Data | Alteração |
|------|-----------|
| 19/10/2026 | Versão inicial aprovada. |
| 21/10/2026 | Realinhamento de escopo: `--count` (antiga RF-04) realocado para a v0.2; benchmark (antiga RNF-06) realocado para a v1.0; intervalo de `--length` fixado em 8–64; mensagens de erro padronizadas (§7); decisão de aleatoriedade sem viés registrada (§8); DoD (§5) desmarcado por ainda não haver implementação. |
