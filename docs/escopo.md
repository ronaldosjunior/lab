# Escopo — Gerador de Senhas Seguras (MVP v0.1)

**Documento:** Documento de Escopo
**Versão-alvo:** 0.1 (MVP)
**Data:** 19/10/2026
**Status:** Aprovado

---

## 1. Objetivo

Desenvolver uma ferramenta de linha de comando (CLI) em PHP puro que gere senhas aleatórias e criptograficamente seguras, com controle sobre tamanho, conjuntos de caracteres e quantidade. A implementação deve priorizar **simplicidade, segurança e zero dependências externas**, utilizando o CSPRNG nativo do PHP (`random_bytes()`) como fonte de entropia.

---

## 2. Requisitos Funcionais

| ID | Requisito | Detalhe |
|----|-----------|---------|
| RF-01 | Geração de senha aleatória | A senha deve ser gerada exclusivamente via `random_bytes()` (CSPRNG). Proibido o uso de `rand()`, `mt_rand()` ou qualquer PRNG determinístico. |
| RF-02 | Controle de tamanho | A flag `--length=N` define o tamanho da senha. Valores válidos: 8–128. Default: `16`. |
| RF-03 | Controle de conjuntos de caracteres | Flags para inclusão/exclusão de: maiúsculas (`--no-upper`), minúsculas (`--no-lower`), dígitos (`--no-digits`), símbolos (`--no-symbols`). Ao menos um conjunto deve permanecer ativo. |
| RF-04 | Geração em lote (básica) | A flag `--count=N` gera N senhas (max 100). Default: `1`. Cada senha é emitida em uma linha no stdout. |
| RF-05 | Saída no stdout | Output textual, uma senha por linha. Sem formatação adicional (sem JSON, CSV, cores ou barras de progresso). |
| RF-06 | Flag `--help` | Exibe a lista de flags disponíveis, valores padrão e exemplo de uso. |
| RF-07 | Validação de entrada | Parâmetros fora do intervalo (ex: `--length=4`) devem gerar erro de uso (exit code ≠ 0) com mensagem clara no stderr. |
| RF-08 | Testes unitários | Cobertura via PHPUnit para: geração de senha, validação de tamanho, validação de conjuntos de caracteres, limites de `--count` e mensagem de erro. |
| RF-09 | Executabilidade | O script `bin/generator.php` deve ser executável via `php bin/generator.php` em qualquer ambiente com PHP ≥ 8.1 no `PATH`. |

---

## 3. Requisitos Não Funcionais

| ID | Requisito | Critério |
|----|-----------|----------|
| RNF-01 | Linguagem e runtime | PHP ≥ 8.1, sem extensões além do núcleo padrão (não requer `ext-gmp`, `ext-sodium`, etc.). |
| RNF-02 | Dependências | Zero dependências externas em runtime. PHPUnit é dependência de **dev** apenas. |
| RNF-03 | Segurança — Entropia | A fonte aleatória deve ser `random_bytes()`. Não deve haver seeding manual nem reutilização de estado entre gerações. |
| RNF-04 | Segurança — Sem estado | A ferramenta não grava senhas em disco, log, clipboard ou memória persistente. Toda senha é efêmera (stdout → stdout). |
| RNF-05 | Portabilidade | Funciona em Linux, macOS e Windows (via WSL ou nativo) sem configuração adicional. |
| RNF-06 | Performance | Geração de 100 senhas de 128 caracteres em < 1 s em hardware commodity. |
| RNF-07 | Segurança de código | Nenhum `eval()`, `include` dinâmico ou interação com filesystem além de leitura do próprio script. |
| RNF-08 | Reprodutibilidade de testes | Suíte PHPUnit deve rodar determinística (`vendor/bin/phpunit`) sem dependência de rede. |

---

## 4. Fora de Escopo (v0.1)

Os itens abaixo **não** fazem parte do MVP e estão agendados para versões futuras:

| Item | Versão-alvo |
|------|-------------|
| Saída em formato estruturado (`--format=json\|csv`) | v0.2 |
| Barra de progresso para lotes | v0.2 |
| Cópia para clipboard (`--copy`) | v0.2 |
| Cálculo de entropia (bits) | v0.3 |
| Modo "passphrase" (Diceware) | v0.3 |
| Whitelist/blacklist de caracteres ambíguos (`0/O`, `1/l`) | v0.3 |
| Auditoria de viés (teste qui-quadrado) | v0.3 |
| Documentação completa (guia de referência, arquitetura) | v1.0 |
| Pipeline de CI/CD (GitHub Actions) | v1.0 |
| Publicação em Packagist | v1.0 |
| Interface gráfica (TUI, web, GUI) | — |
| Persistência ou histórico de senhas | — |
| Autenticação / controle de acesso | — |

---

## 5. Critérios de Aceite (Definicion de Done — v0.1)

- [x] `php bin/generator.php` gera uma senha de 16 caracteres no stdout.
- [x] Todas as flags da tabela RF-02 a RF-06 funcionam conforme especificado.
- [x] `php bin/generator.php --help` exibe a ajuda e retorna exit code 0.
- [x] Entrada inválida retorna exit code ≠ 0 e mensagem no stderr.
- [x] `vendor/bin/phpunit` executa a suíte completa com status **OK**.
- [x] Nenhum `composer.json` require de runtime (apenas `require-dev`).
- [x] Tag `v0.1.0` criada no repositório.

---

## 6. Restrições e Premissas

- O ambiente alvo possui PHP 8.1+ disponível via `PATH`.
- O operador não requer persistência; senhas são descartáveis por design.
- Não há exigência de compatibilidade com PHP 8.0 ou inferior.
- O repositório utiliza o padrão de layout PSR-4 apenas se necessário; o MVP pode ser monolítico (`bin/generator.php` + `src/`).
