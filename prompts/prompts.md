# Unidade 1

## Prompt 1 - `.gitignore`
```text
Contexto: Iniciando um projeto em PHP
Objetivo: Gere um arquivo .gitignore para PHP
Estilo: Organize por seções com comentários
Resposta: Gere um arquivo .gitignore
```

## Prompt 2 - README inicial
```text
Contexto: MVP de Gerador de senhas seguras em PHP, com chamada via linha de comando
Objetivo: Escrever um README inicial com objetivo, stack, como rodar localmente e roadmap de releases
Estilo: Markdown simples, direto e profissional
Resposta: Gere um arquivo README completo
```

## Prompt 3 - Endpoint de healthcheck
```text
Contexto: Projeto em PHP com chamadas via CLI
Objetivo: Criar um bin/status.php que retorne mensagem "ok" quando chamado via CLI
Estilo: Tipagem e código limpo
Resposta: Gere um arquivo bin/status.php
```

## Prompt 4 - Revisão crítica (utilizando o modo Plan)
```text
Contexto: Avaliação do código em bin/status.php
Objetivo: Analise o código gerado em status.php e responda:
    1. Quais os riscos existentes?
    2. O que pode quebrar em produção?
    3. Quais testes mínimos devo criar agora?
Estilo: Respostas curtas em checklist
Resposta: Resposta a cada uma das questões propostas
```

## Prompt 5 - Mensagem de commit
```text
Contexto: Criada a estrutura inicial, README, .gitignore e procedimento para verificação de status da aplicação
Objetivo: Gerar commit inicial no padrão Conventional Commits
Estilo: Apenas uma linha de commit
Resposta: Sugestão de mensagem para commit inicial
```
Chat: troque "scaffold" por "estrutura" na sugestão inicial
Chat: inicie o versionamento com git

# Unidade 2

## Prompt 1 - Escopo MVP
```text
Contexto: MVP de Gerador de senhas seguras em PHP, com chamada via linha de comando.
Objetivo: Gerar documento de escopo com objetivo, requisitos funcionais, não funcionais e fora de escopo.
Estilo: Linguagem técnica, direta, em Markdown.
Resposta: Gere o arquivo em docs/escopo.md.
Restrição: o escopo deve ser compatível com o roadmap da versão 0.1, descrita no @/README.md
```

## Prompt 2 - Backlog por releases
```text
Contexto: Releases conforme roadmap descrito no @/README.md
Objetivo: Criar backlog mínimo com IDs RF/RT e critérios de aceite.
Estilo: Checklist em Markdown.
Resposta: Gere o arquivo em docs/backlog.md.
```

## Prompt 3 - Diagrama `Mermaid`
```text
Contexto: Gerador de senhas seguras em PHP, com chamada via linha de comando. A função principal ainda será implementada. Utilize os arquivos de documentação @/README.md, @/docs/escopo.md para compreender o contexto. 
Objetivo: Crie um diagrama de componentes e fluxo de dados usando Mermaid.js, que mostre a interação entre o usuário (via terminal) e a aplicação
Estilo: Simples, legível e versionável.
Resposta: Gere o arquivo Mermaid na pasta docs
```

## Prompt 4 - Commit
```text
Contexto: Adicionados arquivos de documentação.
Objetivo: Sugerir mensagens de commit no padrão Conventional Commits
Estilo: Apenas uma linha de commit para cada arquivo alterado.
Resposta: Realizar o commit das alterações
```

## Prompt 5 - Revisão de planejamento (utilizando o modo Plan)
```text
Contexto: Avaliação de documentação de escopo e backlog
Objetivo: Analise @/docs/escopo.md e responda:
    1. O que está grande demais para a release inicial?
    2. O que está faltando para testabilidade?
    3. Quais 3 riscos técnicos devo mitigar antes da implementação?
Estilo: Respostas em bullets curtos
Resposta: Resposta a cada uma das questões propostas
```

Chat: Sim, montar plano de implementação recomendado
chat: Executar apenas a fase 0 (ajuste de documentação) do Plano de implementação

# Unidade 3

## Prompt 1 - Estrutura
```text
Contexto: Após análise do projeto com a questão: "O que está faltando para testabilidade?" foram levantados os dois tópicos abaixo e gerado o plano de implementação registrado em @/docs/plano-implementacao.md:
- Estrutura de testes indefinida: sem `composer.json`/autoload, `phpunit.xml`, diretório `tests/` ou comando `composer test` (a DoD do backlog referencia `composer test`, mas o escopo prevê monolito sem isso).
- Arquitetura testável não especificada: `bin/generator.php` monolítico mistura parse, validação, geração e I/O — falta definir separação (ex.: `src/PasswordGenerator` pura + camada CLI) para testar sem invocar processo.
A fase 0 do plano já foi implementada.
Resposta: Implementar apenas a fase 1 (estrutura do projeto) do respectivo plano.
```

Chat: Eu mandei criar apenas a estrutura de testes, não é para gerar o código ainda.

Chat: Realizar commit único das alterações realizadas utilizando o padrão Conventional Commits

## Prompt 2 - Testes de status
```text
Contexto: O script @/bin/status.php realiza o healthcheck da aplicação.
Objetivo: Gerar suíte de testes unitários para o script @/bin/status.php usando o framework PHPUnit.
Inclua os testes para
[ ] Teste de saída exata — assert output = "ok\n" e exit_code = 0
[ ] Teste de contexto — assert que o script não roda (ou retorna error) quando php_sapi_name() !== 'cli'
[ ] Teste de stdout vs stderr — assert que a mensagem sai em stdout, não stderr
[ ] Teste de idempotência — executar N vezes consecutivas, assert saída sempre idêntica
[ ] Teste de exit code — assert exit_code === 0 em caso de sucesso e != 0 em caso de falência (ex: PHP fatal error)
[ ] Teste de ausência de saída extra — assert que não há warnings, notices ou byte extra no output
Estilo: Testes claros, nomes descritivos e fixtures simples.
Resposta: Gere os arquivos de teste na pasta /tests.
```

## Prompt 3 - Testes de core (invertida a ordem para seguir TDD)
```text
Contexto: O script @/bin/generator.php gera senhas seguras em PHP.
Objetivo: Gerar suíte de testes unitários para a "v0.1 — MVP" definida em @/docs/backlog.md usando o framework PHPUnit.
Inclua testes de cobertura para os critérios de aceite descritos em @/docs/escopo.md. 
Estilo: Testes claros, nomes descritivos e fixtures simples.
Resposta: Gere os arquivos de teste na pasta /tests.
```

## Prompt 4 - Commit de testes
```text
Contexto: Adicionados arquivos de teste.
Objetivo: Sugerir mensagem de commit no padrão Conventional Commits
Estilo: Apenas uma linha de commit.
Resposta: Realizar o commit das alterações pendentes.
```

Chat: No @/docs/backlog.md , O RT-108 está em duplicidade com o RT-101. Remover o RT-108 e renumerar os testes RT-109 a RT-115. Após a alteração, revisar o arquivo @/tests/Unit/GeneratorTest.php e ajustar a os comentários para corresponder à nova numeração.

## Prompt 5 - Testes de classes internas
```text
Contexto: O código está organizado assim:
- src/PasswordGenerator.php — classe pura: generate(int $length, array $options): string; sem I/O, sem exit; lança InvalidArgumentException em erro.
- src/OptionParser.php — parse das flags → objeto/arrays; sem I/O.
- bin/generator.php — fino: SAPI guard (como status.php), parser, loop, fwrite(STDOUT), exit codes.
Objetivo: Gerar suíte de testes unitários para as classes @/src/OptionParser.php e @/src/PasswordGenerator.php definidos na Fase 2 do @/docs/plano-implementacao.md usando o framework PHPUnit.
Estilo: Testes claros, nomes descritivos e fixtures simples.
Resposta: Gere os arquivos de teste na pasta /tests.
Restrições: estamos utilizando a metodologia TDD. Apenas os testes devem ser implementados. A implementação das classes será realizada em etapa posterior.
```

## Prompt 6 - Revisão da documentação dos Testes
```text
Contexto: Foram acrescentados os arquivos de testes @/tests/Unit/OptionParserTest.php e @/tests/Unit/PasswordGeneratorTest.php, não previstos inicialmente no @/docs/backlog.md.
Objetivo: Revisar o arquivo @/docs/backlog.md, acrescentando os testes RT-01 à RT-07 implementados no bloco v0.1 — MVP.
Estilo: Checklist em Markdown.
Resposta: arquivo docs/backlog.md atualizado.
```

Chat: Realizar commit único das alterações realizadas utilizando o padrão Conventional Commits

# Unidade 4

## Prompt 1 - Parse de flags
```text
Contexto: O script @/src/OptionParser.php será responsável pelo parse das flags, sem I/O. 
As flags disponíveis estão listadas em @/README.md.
Conjuntos fixos ASCII: `A-Z a-z 0-9 !@#$%^&*()-_=+[]{}<>?/` (evita issues de codepage Windows).
Objetivo: A implementação deverá passar pelos testes definidos em @/tests/Unit/OptionParserTest.php.
Estilo: Código limpo e comentários curtos no formato PHPDoc.
Resposta: Implementar o arquivo @/src/OptionParser.php.
```

## Prompt 2 - Gerador de senhas
```text
Contexto: O script @/src/PasswordGenerator.php será responsável pela geração de senhas utilizando uma classe pura: `generate(int $length, array $options): string`; sem I/O, sem `exit`; lança `InvalidArgumentException` em erro. 
As flags disponíveis estão listadas em @/README.md
Conjuntos fixos ASCII: `A-Z a-z 0-9 !@#$%^&*()-_=+[]{}<>?/` (evita issues de codepage Windows).
Objetivo: A implementação deverá passar pelos testes definidos em @/tests/Unit/PasswordGeneratorTest.php.
Estilo: Código limpo e comentários curtos no formato PHPDoc.
Resposta: Implementar o arquivo @/src/PasswordGenerator.php.
```

## Prompt 3 - Core
```text
Contexto: O script @/bin/generator.php será a chamada principal para o MVP de Gerador de senhas, com chamada via linha de comando. Deve seguir as convenções de @/bin/status.php: strict types, SAPI guard, docblock. Fará uso das classes @/src/OptionParser.php (parser de flags) e @/src/PasswordGenerator.php (gera a senha) 
Conjuntos fixos ASCII: `A-Z a-z 0-9 !@#$%^&*()-_=+[]{}<>?/` (evita issues de codepage Windows).
Objetivo: A implementação deverá passar pelos testes definidos em @/tests/Unit/GeneratorTest.php.
Estilo: Código limpo e comentários curtos no formato PHPDoc.
Resposta: Implementar o arquivo @/bin/generator.php.
```

## Prompt 4 - Revisão técnica (utilizando o modo Plan)
```text
Contexto: Avaliação do código
Objetivo: Analise arquivos das pastas @/bin e @/src e responda:
    1. Quais pontos de acoplamento estão altos?
    2. Onde faltam validações?
Estilo: Respostas em checklist
Resposta: Resposta a cada uma das questões propostas
```

## Prompt 5 - Commit de código
```text
Contexto: Adicionados arquivos de código.
Objetivo: Sugerir mensagens de commit no padrão Conventional Commits
Estilo: Apenas uma linha de commit.
Resposta: Realizar o commit das alterações
```

Chat: Troque a mensagem para o idioma português

# Unidade 5

Chat: Rode uma bateria de testes completa.

Chat: Revise o @/docs/diagrama-componentes.mmd para incluir os componentes @/src/PasswordGenerator.php e @/src/OptionParser.php

## Prompt 1 - README final
```text
Contexto: MVP de Gerador de senhas seguras em PHP, com chamada via linha de comando.
Objetivo: Revise o arquivo @README.md para atualizar/complementar as informações utilizadas no projeto. O arquivo deve conter as seções:
- Título e breve descrição do projeto
- Instruções detalhadas como configurar o ambiente e rodar o projeto localmente
- Exemplos de uso ou como interagir com a aplicação
- Lista de tecnologias utilizadas, inclusive os modelos de IA e assistentes de código
- Seção de "Limitações e Próximos Passos"
- Créditos e Licença
Estilo: Markdown simples, direto e profissional
Resposta: Complementar o arquivo @/README
```

## Prompt 2 - Revisão final de qualidade (utilizando o modo Plan)
```text
Contexto: Avaliação de qualidade do código gerado
Objetivo: Analise arquivos nas pastas @/bin/ e @/docs/ e identifique:
    1. Riscos técnicos restantes
    2. Gaps de cobertura de teste
    3. Melhorias prioritárias para a próxima release
Estilo: Respostas curtas em checklist
Resposta: Resposta a cada uma das questões propostas
```

## Prompt 3 - Teste de integração

Chat: Implementar tests/Integration/CommandLineTest.php, conforme definição na Fase 2 do @/docs/plano-implementacao.md

```text
Contexto: Adicionado teste de integração.
Objetivo: Sugerir mensagem de commit no padrão Conventional Commits
Estilo: Apenas uma linha de commit.
Resposta: Realizar o commit de @/tests/Integration/ComandLineTest.php
```
