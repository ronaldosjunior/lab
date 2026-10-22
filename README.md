# Gerador de Senhas Seguras (PHP)

Gere senhas aleatórias e criptograficamente seguras direto do terminal.

---

## Objetivo

Ferramenta de linha de comando (CLI) para gerar senhas robustas com controle sobre:

- **Tamanho** da senha
- **Inclusão de caracteres** (maiúsculas, minúsculas, números, símbolos)

O foco é **simplicidade, segurança e zero dependências externas** — tudo em PHP puro, usando a fonte CSPRNG nativa do PHP (`random_bytes()` / `random_int()`) como fonte de entropia.

---

## Stack

| Camada       | Tecnologia       |
|--------------|-----------------|
| Linguagem    | PHP >= 8.1      |
| Execução     | CLI (terminal)  |
| Randomness   | `random_bytes()` (CSPRNG nativo do PHP) |
| Dep. externas | Nenhuma (MVP)   |

---

## Como Rodar Localmente

### Pré-requisitos

- PHP 8.1+ instalado e disponível no `PATH`

### Passos

```bash
# 1. Clone o repositório
git clone <url-do-repo>
cd <nome-do-repo>

# 2. (Opcional) Instale dependências — no MVP não há, mas para o futuro:
composer install

# 3. Gere sua primeira senha
php bin/generator.php --length=20
```

### Exemplos de uso

```bash
# Senha de 16 caracteres (padrão)
php bin/generator.php

# Senha de 24 caracteres
php bin/generator.php --length=24

# 32 caracteres, somente alfanuméricas
php bin/generator.php --length=32 --no-symbols
```

### Flags disponíveis (MVP)

| Flag           | Default | Descrição                              |
|----------------|---------|----------------------------------------|
| `--length=N`   | `16`    | Tamanho da senha (min 8, max 64)       |
| `--no-symbols` | `off`   | Excluir símbolos                       |
| `--no-lower`   | `off`   | Excluir minúsculas                     |
| `--no-upper`   | `off`   | Excluir maiúsculas                     |
| `--no-digits`  | `off`   | Excluir números                        |
| `--help`       | —       | Mostrar ajuda                          |

---

## Roadmap

### v0.1 — MVP (atual — escopo realinhado em 21/10/2026, ver `docs/escopo.md`)
- [ ] Geração de senha com CSPRNG nativo do PHP, sem viés de módulo
- [ ] Controle de tamanho (`--length`, 8–64) e conjuntos de caracteres
- [ ] Output simples no stdout
- [ ] Testes unitários (PHPUnit)
- [ ] `--help`
- [ ] Validação de entrada com mensagens de erro padrãoizadas
- [ ] Tag de release

### v0.2 — Melhoria de UX
- [ ] Saída em formato `--format=json|csv`
- [ ] Geração em lote (`--count`)
- [ ] Barra de progresso para lotes grandes
- [ ] Cópia para clipboard (`--copy`)

### v0.3 — Segurança Avançada
- [ ] Cálculo de entropia da senha gerada (bits)
- [ ] Modo "passphrase" (estilo Diceware)
- [ ] Whitelist/blacklist de caracteres (ex: evitar caracteres ambíguos `0/O`, `1/l`)
- [ ] Auditoria: verificação de bias via chi-quadrado

### v1.0 — Estável
- [ ] Documentação completa
- [ ] CI/CD (GitHub Actions)
- [ ] Pacote publicável via Packagist (opcional)

---

## Contribuindo

1. Fork o repositório
2. Crie uma branch: `git checkout -b feature/nome-da-feature`
3. Faça o commit e abra um Pull Request

---

## Licença

MIT — veja [LICENSE](LICENSE) para detalhes.
