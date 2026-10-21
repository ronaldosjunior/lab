# Backlog — Gerador de Senhas Seguras (PHP)

Backlog mínimo derivado do [Roadmap](../README.md#roadmap). Cada item funcional (`RF`) tem critérios de aceite testáveis e um ou mais requisitos de teste (`RT`) que devem passar antes do item ser considerado pronto.

**Legenda**

| Prefixo | Significado |
|---------|-------------|
| `RF` | Requisito Funcional |
| `RT` | Requisito de Teste (critério automatizável) |

**Definição de Pronto (DoD) por item**

- [ ] Código em PHP puro, sem dependências externas
- [ ] Testes (`RT`) escritos e passando no `composer test` (PHPUnit)
- [ ] Sem regressão nos testes existentes
- [ ] README atualizado (flags/uso) se houver mudança de interface

---

## v0.2 — Melhoria de UX

### RF-01 — Saída em formato JSON/CSV

Aceitar a flag `--format=json|csv` e emitir a saída no formato escolhido.

- [ ] `--format=json` produz array JSON válido de objetos `{ "password": "<senha>" }`
- [ ] `--format=csv` produz linhas `<senha>` com cabeçalho `password`
- [ ] Valor inválido em `--format` exibe erro no `stderr` e exit code ≠ 0
- [ ] Sem a flag, comportamento padrão (texto puro no stdout) permanece inalterado
- [ ] Saída é compatível com pipes (sem cor/emoji/spinner no stdout)

**Critérios de aceite**

1. `php bin/generator.php --length=16 --format=json | jq .` retorna array válido
2. `php bin/generator.php --length=16 --format=csv` retorna 2 linhas: `password` + a senha
3. `php bin/generator.php --format=xml` exibe `Erro: formato inválido...` no stderr

**Testes**

- [ ] `RT-01` — Given `--format=json --count=3`, When gerado, Then stdout parseia como JSON com 3 itens, cada um com senha de 16 chars
- [ ] `RT-02` — Given `--format=csv`, Then stdout contém exatamente a linha `password` na 1ª linha e senhas nas demais
- [ ] `RT-03` — Given `--format=xml`, Then exit code é `1` e stderr contém mensagem de formato inválido
- [ ] `RT-04` — Given sem flag `--format`, Then stdout é igual ao comportamento v0.1 (uma senha por linha)

### RF-02 — Geração em lote (`--count`)

Aceitar `--count=N` (máx. 100) para gerar N senhas independentes.

- [ ] `--count` entre 1 e 100 gera exatamente N senhas
- [ ] `--count=0` ou `--count>100` ou valor não numérico → erro no stderr, exit code ≠ 0
- [ ] Cada senha do lote é estatisticamente independente (sem repetição garantida, mas sem estado compartilhado)
- [ ] Lotes combinam corretamente com `--format` e demais flags de conjuntos

**Critérios de aceite**

1. `php bin/generator.php --count=5 --length=12` exibe exatamente 5 senhas de 12 caracteres
2. `php bin/generator.php --count=0` → stderr: `--count deve estar entre 1 e 100`
3. `php bin/generator.php --count=101` → mesmo erro
4. `php bin/generator.php --count=abc` → mesmo erro

**Testes**

- [ ] `RT-05` — Given `--count=10`, Then exatamente 10 senhas no stdout
- [ ] `RT-06` — Given `--count=0`, Then exit code ≠ 0 e mensagem de validação no stderr
- [ ] `RT-07` — Given `--count=101`, Then exit code ≠ 0 e mensagem de validação no stderr
- [ ] `RT-08` — Given `--count=5 --no-digits`, Then nenhuma das 5 senhas contém dígitos
- [ ] `RT-09` — Given `--count=3 --format=json`, Then 3 objetos JSON e cada um possui `password` de tamanho correto

### RF-03 — Barra de progresso para lotes grandes

Exibir progresso no `stderr` quando `--count >= 10`.

- [ ] Progresso visível apenas quando lote >= 10 (evita ruído em lotes pequenos)
- [ ] Saída vai para `stderr`, nunca para `stdout` (preserva pipes)
- [ ] Em terminal não-TTY (pipe), progresso é desabilitado automaticamente

**Critérios de aceite**

1. `php bin/generator.php --count=20 | tee out.txt` → `out.txt` contém apenas as 20 senhas
2. Rodando sem pipe, o terminal mostra `[10/20]`, `[20/20]`
3. Rodando em TTY falso, nenhum caractere de barra de progresso no stderr

**Testes**

- [ ] `RT-10` — Given `--count=20` em contexto TTY simulado, Then stderr contém marcador `[20/20]`
- [ ] `RT-11` — Given `--count=20` sem TTY, Then stderr NÃO contém marcadores de progresso
- [ ] `RT-12` — Given `--count=5`, Then nenhum marcador de progresso (abaixo do limiar)
- [ ] `RT-13` — Given stdout em qualquer cenário, Then stdout contém apenas as senhas (sem progress)

### RF-04 — Cópia para clipboard (`--copy`)

Copiar a última (ou única) senha gerada para a área de transferência.

- [ ] `--copy` com 1 senha: copia a senha gerada
- [ ] `--copy` com `--count>1`: copia todas as senhas separadas por `\n`
- [ ] Sem suporte a clipboard no ambiente (ex.: CI) → aviso no stderr, exit code continua 0
- [ ] Não usar dependências externas (usar `OSC 52` do terminal quando possível, senão avisar)

**Critérios de aceite**

1. Em terminal com suporte a OSC 52, `php bin/generator.php --copy` coloca a senha no clipboard
2. Em ambiente sem suporte, `php bin/generator.php --copy` exibe `Aviso: clipboard não disponível` no stderr e exit code 0
3. Senha não aparece em double-output (não imprime duplicado)

**Testes**

- [ ] `RT-14` — Given `--copy` em TTY sem suporte a OSC 52, Then stderr contém aviso e exit code é 0
- [ ] `RT-15` — Given `--copy --count=3`, Then clipboard contém 3 senhas (3 linhas)
- [ ] `RT-16` — Given `--copy` com stdout direcionado a arquivo, Then arquivo contém apenas as senhas (sem escape OSC)

---

## v0.3 — Segurança Avançada

### RF-05 — Cálculo de entropia (bits)

Exibir a entropia estimada de cada senha gerada em bits: `H = length × log2(|charset|)`.

- [ ] `--entropy` (ou sempre, se `--format=json`) exibe `H` em bits ao lado da senha
- [ ] Cálculo usa o tamanho real do charset efetivo (após exclusões via `--no-*`)
- [ ] Exemplo: charset de 87 símbolos, 16 chars → `16 × log2(87) ≈ 107.5 bits`

**Critérios de aceite**

1. `php bin/generator.php --length=16 --entropy` exibe `≈ 107 bits` (charset completo de 87 símbolos)
2. `php bin/generator.php --length=16 --no-symbols --entropy` → `16 × log2(62) ≈ 96.6 bits`
3. Em `--format=json`, cada objeto possui campo `"entropy_bits"` (número)

**Testes**

- [ ] `RT-17` — Given `--length=16 --entropy`, Then stdout contém `107` (tolerância ±1)
- [ ] `RT-18` — Given `--length=16 --no-symbols --entropy`, Then stdout contém `96` ou `97`
- [ ] `RT-19` — Given `--format=json`, Then cada item JSON tem `entropy_bits` como número
- [ ] `RT-20` — Given charset reduzido a 0 (todas as classes removidas), Then erro claro: `Charset vazio`

### RF-06 — Modo passphrase (Diceware)

Gerar passphrase legível a partir de lista de palavras (4–8 palavras separadas por `-` ou espaço).

- [ ] `--diceware` ativa o modo passphrase
- [ ] `--words=N` define quantidade de palavras (default 4, máx 10)
- [ ] Lista de palavras embarcada no repositório (`data/words.txt`), ~2000 palavras
- [ ] Cada palavra selecionada com `random_int(1, $total)` (CSPRNG, sem bias modulo)
- [ ] Erro ao combinar `--diceware` com `--length` (conflicto de flags)

**Critérios de aceite**

1. `php bin/generator.php --diceware` exibe 4 palavras separadas por `-`
2. `php bin/generator.php --diceware --words=8` exibe 8 palavras
3. `php bin/generator.php --diceware --length=20` → erro: `--diceware e --length são incompatíveis`
4. `php bin/generator.php --diceware --format=json` → array com `password` (passphrase) e `entropy_bits`

**Testes**

- [ ] `RT-21` — Given `--diceware`, Then stdout contém exatamente 3 separadores `-` (4 palavras)
- [ ] `RT-22` — Given `--diceware --words=2`, Then stdout contém 1 separador
- [ ] `RT-23` — Given `--diceware --length=20`, Then exit code ≠ 0 e mensagem de conflito no stderr
- [ ] `RT-24` — Given `--diceware` com N execuções, Then cada palavra gerada pertence à `data/words.txt`
- [ ] `RT-25` — Given `--diceware --words=11`, Then erro: `--words deve estar entre 2 e 10`

### RF-07 — Whitelist/Blacklist de caracteres (ambiguidade)

Evitar caracteres visualmente ambíguos (`0/O`, `1/l/I`) via flag opt-in.

- [ ] `--ambiguous` adiciona `0`, `1`, `O`, `I`, `l` à blacklist
- [ ] Whitelist livre: `--charset="..."` (opcional nesta release)
- [ ] Erro claro se a blacklist esvaziar o charset

**Critérios de aceite**

1. `php bin/generator.php --length=64 --ambiguous` → nenhuma senha contém `0`, `1`, `O`, `I`, `l`
2. `php bin/generator.php --no-symbols --no-digits --no-lower --no-upper` (todas removidas) → erro claro

**Testes**

- [ ] `RT-26` — Given `--length=128 --ambiguous`, Then stdout não contém `0`, `1`, `O`, `I`, `l`
- [ ] `RT-27` — Given todas as classes removidas, Then erro: `charset vazio`
- [ ] `RT-28` — Given `--length=16` (sem `--ambiguous`), Then caracteres ambíguos são permitidos (não obrigatórios)

### RF-08 — Auditoria de bias via Chi-Quadrado

Modo `--audit` que mede a distribuição de frequências dos caracteres gerados.

- [ ] `--audit N` (ex.: `--audit 10000`) gera N senhas e calcula χ² sobre o charset efetivo
- [ ] Exibe χ², graus de liberdade, p-valor e veredito (`OK` / `BIAS SUSPEITO`)
- [ ] Usar `random_bytes()`/`random_int()` (já CSPRNG; o teste é documentação e QA)
- [ ] Limiar: p-valor < 0.001 → `BIAS SUSPEITO`

**Critérios de aceite**

1. `php bin/generator.php --audit 10000 --length=16` exibe χ² ≈ df (±20%) com p-valor > 0.001
2. `--audit 10000 --no-digits` → charset efetivo é 62 e df = 61
3. Rodar em CI com `--seed` fixo para reprodutibilidade (exemplo de uso)

**Testes**

- [ ] `RT-29` — Given `--audit 10000`, Then stdout contém χ², df, p-valor e veredito
- [ ] `RT-30` — Given `--audit 1000 --no-digits`, Then df exibido é 61
- [ ] `RT-31` — Given `--audit 100` (mínimo sugerido), Then mensagem de aviso: `amostra pequena`
- [ ] `RT-32` — Given `--audit 10`, Then erro: `--audit requer N >= 100`

---

## v1.0 — Estável / Meta

### RF-09 — Documentação completa

- [ ] `README.md` com todas as flags, exemplos de saída e troubleshooting
- [ ] `docs/architecture.md` descrevendo o pipeline (parse → validate → generate → format → output)
- [ ] `docs/security.md` explicando uso de CSPRNG e cálculo de entropia
- [ ] `CHANGELOG.md` seguindo Keep a Changelog

**Critérios de aceite**

1. `composer install && composer test && composer audit` passam em PHP 8.1, 8.2 e 8.3
2. Novo dev consegue gerar senha em < 2 min seguindo apenas o README
3. Todas as flags do `--help` têm seção correspondente no README

**Testes**

- [ ] `RT-33` — Given `--help`, Then saída lista todas as flags implementadas (sem omissões)
- [ ] `RT-34` — Given cada flag do `--help`, Then README tem uma linha de descrição correspondente
- [ ] `RT-35` — Given `composer test` em PHP 8.1/8.2/8.3, Then 100% dos testes passam

### RF-10 — CI/CD (GitHub Actions)

Pipeline mínimo com lint, teste e tag de release.

- [ ] `composer validate && composer audit` no CI
- [ ] `composer test` com cobertura (pct report)
- [ ] Tag de release semântica (`v1.0.0`) gerando artefato
- [ ] PHP 8.1 / 8.2 / 8.3 em matriz

**Critérios de aceite**

1. Push em `main` dispara workflow e passa
2. PR com teste falho → CI vermelho
3. Tag `v1.0.0` gera release com changelog

**Testes**

- [ ] `RT-36` — Given push em `main` com código válido, Then workflow `ci.yml` passa
- [ ] `RT-37` — Given PR com teste falho, Then workflow `ci.yml` falha com log claro
- [ ] `RT-38` — Given tag `vX.Y.Z`, Then GitHub Release é criada automaticamente

### RF-11 — Publicação no Packagist (opcional)

- [ ] `composer.json` com `name`, `description`, `license`, `keywords`
- [ ] `bin` declarado no `composer.json` (autocomando `genpass`)
- [ ] Versão semântica e tags no git

**Critérios de aceite**

1. `composer require vendor/genpass` instala e expõe o binário `genpass`
2. `genpass --length=16` gera senha
3. `composer audit` limpo

**Testes**

- [ ] `RT-39` — Given projeto consumidor `composer require`, Then `genpass` está no `vendor/bin`
- [ ] `RT-40` — Given `genpass --help`, Then exit code 0 e lista de flags

---

## Resumo de IDs

| ID | Descrição | Release |
|----|-----------|---------|
| RF-01 | Saída JSON/CSV | v0.2 |
| RF-02 | Geração em lote (`--count`) | v0.2 |
| RF-03 | Barra de progresso | v0.2 |
| RF-04 | Clipboard (`--copy`) | v0.2 |
| RF-05 | Entropia (bits) | v0.3 |
| RF-06 | Modo passphrase (Diceware) | v0.3 |
| RF-07 | Whitelist/Blacklist (ambiguos) | v0.3 |
| RF-08 | Auditoria Chi-Quadrado | v0.3 |
| RF-09 | Documentação completa | v1.0 |
| RF-10 | CI/CD GitHub Actions | v1.0 |
| RF-11 | Publicação no Packagist | v1.0 |

| RT | RF associado | Tipo |
|----|--------------|------|
| RT-01..04 | RF-01 | Auto |
| RT-05..09 | RF-02 | Auto |
| RT-10..13 | RF-03 | Auto/TTY |
| RT-14..16 | RF-04 | Auto/TTY |
| RT-17..20 | RF-05 | Auto |
| RT-21..25 | RF-06 | Auto |
| RT-26..28 | RF-07 | Auto |
| RT-29..32 | RF-08 | Auto |
| RT-33..35 | RF-09 | Auto |
| RT-36..38 | RF-10 | CI |
| RT-39..40 | RF-11 | Auto |

---

## Como usar este backlog

1. **Sprint**: puxe RFs na ordem (v0.2 → v0.3 → v1.0)
2. **Story-ready**: cada RF já traz critérios de aceite + RTs — pode virar uma task
3. **Pronto**: um RF só é considerado *done* quando todos os RTs associados passam e a DoD é checklist completo

> ⚠️ Este backlog é derivado do `README.md`. Se o README mudar, atualize este arquivo.
