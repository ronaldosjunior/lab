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

## Tecnologias Utilizadas

### Linguagens e Frameworks
- **PHP 8.1+** - Linguagem principal para implementação
- **PHPUnit** - Para testes unitários

### Modelos de IA e Assistentes de Código
- **qwen3.8:27b** - utilizado com as extensões Continue e Cline do VS Code
- **qwen3-coder:30b** - substituindo o **qwen3.8**, apresentou melhor relação perfomance x recursos.
- **Microsoft 365 Copilot** - sugestão de ferramentas e modelos locais

### Ferramentas de Desenvolvimento
- **Composer** - Gerenciamento de dependências PHP
- **Git** - Controle de versão

---

## Como Rodar Localmente

### Pré-requisitos

- PHP 8.1+ instalado e disponível no `PATH` 
    (baixar [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou outro servidor PHP)
- [Composer](https://getcomposer.org/download/) instalado e disponível no `PATH`
- [Git](https://git-scm.com/install/windows) instalado (para clonar o repositório)

### Passos

```bash
# 1. Clone o repositório
git clone <url-do-repo>
cd <nome-do-repo>

# 2. Instale as dependências
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

# Senha sem números e símbolos (apenas letras)
php bin/generator.php --length=20 --no-digits --no-symbols
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

## Limitações e Próximos Passos

### Limitações Atuais (v0.1 - MVP)
- **Somente geração de senha única** - não há suporte para geração em lote
- **Formato de saída limitado** - apenas texto simples no stdout
- **Sem funcionalidades avançadas de segurança** - cálculo de entropia, modo passphrase, auditoria de viés
- **Interface minimalista** - sem barra de progresso ou cópia automática para clipboard

### Próximos Passos (Roadmap)

#### v0.2 — Melhoria de UX
- [ ] Saída em formato `--format=json|csv`
- [ ] Geração em lote (`--count`)
- [ ] Barra de progresso para lotes grandes
- [ ] Cópia para clipboard (`--copy`)

#### v0.3 — Segurança Avançada
- [ ] Cálculo de entropia da senha gerada (bits)
- [ ] Modo "passphrase" (estilo Diceware)
- [ ] Whitelist/blacklist de caracteres (ex: evitar caracteres ambíguos `0/O`, `1/l`)
- [ ] Auditoria: verificação de bias via chi-quadrado

#### v1.0 — Estável
- [ ] Documentação completa
- [ ] CI/CD (GitHub Actions)
- [ ] Pacote publicável via Packagist (opcional)

---

## Contribuindo

1. Fork o repositório
2. Crie uma branch: `git checkout -b feature/nome-da-feature`
3. Faça o commit e abra um Pull Request

---

## Testes

O projeto inclui testes unitários com PHPUnit:

```bash
# Executar todos os testes
composer test

# Ou diretamente com PHPUnit
vendor/bin/phpunit
```

Os testes cobrem:
- Geração de senhas com diferentes tamanhos
- Validação de entrada
- Controle de conjuntos de caracteres
- Mensagens de erro padrão

---

## Créditos

Este projeto foi desenvolvido como parte do **Laboratório Introdutório Construindo um Mini-projeto com Inteligência Artificial Generativa**.

---

## Licença

MIT — veja [LICENSE](LICENSE) para detalhes.
