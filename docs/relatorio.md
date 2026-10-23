## Stack utilizada

Após discussões a respeito de funcionalidades e segurança com os modelos, optei por avaliar um LLM local com a seguinte configuração:
- IDE: VS Code
- Plugins: 
    1. Continue (autocompletar, chat, edição) - Utilizado na Unidade 1
    2. Cline (agente para tarefas mais complexas) - Utilizado na Unidade 2
- Servidor LLM local: Ollama
- LLMs: 
    - qwen2.5-coder:7b - todo em VRAM, para tarefas que exigem velocidade (configurado para autocompletar [modo edit] do Continue) - Testado na unidade 1, mas sem capacidade de gerar arquivos
    - qwen3.8:27b - mais completo, para tarefas complexas (configurado para o modo chat, plan e agente do Continue e modo Plan e Act do Cline) - funcionou bem para a Unidade 1, execução muita lenta na Unidade 2 
    - qwen3-coder:30b - focado em código, apresentou maior performance que o qwen3.8:27b. Utilizado à partir da Unidade 3, foi o que apresentou melhor relação perfomance x recursos.
    - Microsoft 365 Copilot - utilizada para questões mais genéricas, que não envolviam diretamente a codificação, como instruções sobre ferramentas a serem instaladas

## Impressões

- O plugin do Cline para VS Code exibe a utilização da janela de contexto à medida que o chat vai se desenvolvendo, o que é bastante interessante.

- A utilização do Continue no modo Agent (com o llm qwen3.8:27b) ficou relativamente lento, mas com respostas satisfatórias para a Unidade 1.

- Com o Cline, o Thinking é feito todo em inglês, com tradução para português da resposta final.

- Notei que havia alguma dificuldade com o Power Shell (não sei se com o Cline ou com o LLM local). Por várias vezes o agente executava alguns comandos no shell, não obtinha a resposta esperada e precisava refazer o comando com uma sintaxe diferente.

- Teste com diferentes configurações:
Unidade 1 - Continue - Única janela de contexto de 256K, com execução satisfatória
Unidade 2 - Cline - Contexto de 32K, cada etapa com uma nova janela de contexto, muita lentidão, mas alcançado resultado esperado.
Unidade 3 - Cline - Contexto de 32K, 

## Revisões manuais:

### Readme inicial

- Reduzido o escopo dentro do objetivo: removido item de controle de Força/entropia proposto
- Alteração de ordem dos requisitos: inclusão de testes e tags no MVP, Geração em lote deixado para versão posterior.

### Commit inicial

- Executado os comandos para configuração de usuário e e-mail do git, seguindo sugestão do próprio modelo.

### Commit de revisão

- Commit manual após revisão da unidade 2.

### Readme final

- Revisão dos modelos de IA utilizados (modelo gerou informações inverídicas)
- Revisão de Ferramentas de Desenvolvimento
- Pré-requisitos: inclusão de links

### Testes finais

- Revisão dos testes, exclusão de duplicidades

### Commit final

## Dificuldades encontradas:

`Resumo`: As principais dificuldades encontradas foram em relação à estrutura, uma vez que tinha como objetivo conseguir completar a atividade utilizando apenas modelos locais, rodando totalmente offline. Durante o processo, foram testados 4 modelos diferentes e diversas configurações.    
	
1. Durante a preparação do ambiente e escolha das ferramentas, utilizei modelos de IA para realizar levantamentos de informações sobre características de algumas ferramentas.
Notei que diferentes modelos traziam respostas divergentes para algumas perguntas objetivas.
Essa situação trouxe uma pequena insegurança em seguir orientações dos modelos para tomada de decisões mais críticas.
Desta forma, optei por realizar a atividades em diferentes modelos para comparar o processo de cada um.

Exemplo: estava rodando localmente o modelo qwen3.8 e após algumas respostas sobre características de alguns plugins do VS Code, fiz o questionamento abaixo:

---

*Modelo:* **qwen3.8**

*Prompt* 

`A extensão Cline pode ser integrada ao Ollama sem necessitar de uma conta online?`

*Resposta*
 ```
Cline é uma extensão do VS Code que oferece suporte a sugestões de código em tempo real, mas ela depende de uma conexão com a internet para funcionar...
```

---

Para validar a informação, abri uma nova conversa com o Microsoft Copilot Chat (incluso no pacote MS365) e realizei pergunta similar:

*Modelo:* **Copilot Chat (Basic)**

*Prompt* 

`A extensão Cline do VS Code precisa de conexão com a internet para funcionar ou pode ser integrada ao Ollama para trabalhar em um ambiente local offline?`

*Resposta* 
```
Sim. **O Cline pode ser integrado ao Ollama e usar um modelo executado localmente**, sem depender de APIs da OpenAI, Anthropic ou outros provedores cloud. A configuração usual é o Cline apontar para o servidor local do Ollama, normalmente em http://localhost:11434.

... < inclui detalhamento sobre o fluxo de trabalho, explicações sobre a necessidade da internet para instalação das ferramentas, que pode haver necessidade de desabilitar a telemetria para garantir o isolamento completo, 
	restrições quanto a requisitos e hardware para rodar o modelo localmente e conclui com > ...

**Portanto, resposta curta:** sim, é perfeitamente viável usar **Cline + Ollama localmente e offline.**
```

---

Retornei ao modelo local e refiz a mesma pergunta, utilizando os mesmos termos:

*Modelo:* **qwen3.8**

*Prompt* 

`A extensão Cline do VS Code precisa de conexão com a internet para funcionar ou pode ser integrada ao Ollama para trabalhar em um ambiente local offline?`

*Resposta* 
```
**Sim, o Cline pode funcionar totalmente offline** quando integrado ao Ollama. Não é obrigatório usar a internet.

... < inclui descrição de características do plugin, instruções de instalação, pontos de atenção e conclui > ...

> O Cline **não exige internet** para gerar código. Com Ollama rodando localmente e o provedor configurado para `localhost`, você tem um loop completo de **plan → edit → terminal → teste** 100 % offline.
A única necessidade de rede seria se você quisesse usar um modelo da nuem *além* do local, mas isso é opcional.
```

2. Após realizada as instalações e configurações recomendadas para rodar uma LLM local, tentei utilizar o Cline no VS Code.

Logo na primeira tarefa, não consegui fazer com que o modelo criasse o arquivo.

No modo Act, dizia que o arquivo estava sendo criado, mas o arquivo não aparecia no projeto.
No modo Plan, o modelo identificava que o arquivo não havia sido criado, dizia que tentou utilizar uma ferramenta não disponível e que faria a criação de outra maneira, mas que deveria ser feito no modo Act, visto que o modo Plan não tinha permissão de criar arquivos.
Voltando ao modo Act, novamente informava que o arquivo havia sido criado, sem efetivamente criar. 

Existe a possibilidade de a restrição estar relacionada ao modelo, visto que o modo Plan estava utilizando um modelo maior e o modo Act estava com um modelo menor (mais rápido), conforme a sugestões de configuração.

Na documentação da Extensão, dizia que deveria aparecer botões para aprovação deste tipo de alteração (edição de arquivos), mas os botões não apareciam. Também tentei liberar a auto aprovação para criação de arquivos e execução de comandos (o modo Plan dizia que a criação de arquivo seria feita via comando do PowerShell), mas sem sucesso.

Após várias tentativas, mudei para o plugin Continue, onde consegui criar o arquivo (utilizando o modelo local mais completo).

3. Ao iniciar a unidade 2, o modelo qwen3.8:27b parou de responder. Alternando para o modelo menor (qwen2.5-coder:7b) voltou a responder, mas não gerava novos arquivos, indicando que a limitação ocorrida no Cline estava relacionada ao modelo.

A partir desse ponto, voltei a utilizar a extensão Cline, com o modelo local mais completo.

4. Com o modelo local utilizando janela de contexto de 256K as respostas estavam excessivamente lentas (vários minutos para dar algum retorno e dezenas de minutos para concluir uma única tarefa). Identificado que o modelo era reiniciado por várias vezes durante a tarefa. 
Foi necessário reduzir a a janela de contexto (32k), ser mais específico (fazer referências explícitas a determinados arquivos) e aumentar o tempo limite de resposta do Cline para que o modelo conseguisse concluir as tarefas da unidade 2. 

As respostas voltavam a ficar lentas quando o contexto utilizado excedia os 15k, não conseguindo concluir as tarefas da unidade 3.

5. A partir da Unidade 3, substitui o modelo pelo qwen3-coder:30b. Apesar de mais antigo, acredito que por ser focado em codificação, apresentou melhor performance.

No primeiro promptd da unidade, mesmo sendo explícito que seria apenas para criar a estrutura de testes, o modelo insistia em já implementar o código principal. Foi necessário rejeitar a permissão de escrita do arquivo e "dar uma bronca" para ele fazer apenas o que foi pedido.

6. Para utilizar a metodologia TDD, com revisão a cada passo, foi necessário "conter a proatividade" do modelo, que tentava avançar para as etapas seguintes, mesmo com a ordem explícita de gerar apenas os testes.