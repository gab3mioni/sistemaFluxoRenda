# Guia de Contribuição

## Sumário
1. [Configurando o ambiente](#configurando-o-ambiente)
2. [Fluxo de trabalho com Git](#fluxo-de-trabalho-com-git)
    - [Branch principal](#branch-principal)
    - [Criando uma nova branch](#criando-uma-nova-branch)
    - [Submetendo um Pull Request](#submetendo-um-pull-request)
3. [Estilo de Código](#estilo-de-código)
4. [Reportando Bugs](#reportando-bugs)
5. [Commits](#commits)
6. [Dicas Gerais](#dicas-gerais)

---

## Configurando o ambiente

Antes de começar a desenvolver, siga os passos abaixo para configurar seu ambiente:

1. Faça o clone do repositório:
    ```bash
    git clone https://github.com/gab3mioni/sistemaFluxoRenda.git
    ```

2. Acesse a pasta do projeto:
    ```bash
    cd sistemaFluxoRenda
    ```

3. Certifique-se de ter as dependências instaladas conforme a documentação do projeto.

---

## Fluxo de trabalho com Git

### Branch principal

- O projeto utiliza a branch **`develop`** como branch de desenvolvimento principal.
- A branch **`main`** é reservada apenas para versões estáveis.

### Criando uma nova branch

1. **Certifique-se de estar na branch `develop`:**
    ```bash
    git checkout develop
    ```

2. **Sincronize com as últimas mudanças:**
    ```bash
    git pull origin develop
    ```

3. **Crie uma nova branch com base na `develop`:**
   Utilize um nome descritivo e siga o padrão `feature/descricao` ou `bugfix/descricao`.
    ```bash
    git checkout -b feature/nome-da-feature
    ```

4. **Realize as alterações necessárias e commit:**
   Certifique-se de escrever mensagens de commit claras e objetivas.
    ```bash
    git add .
    git commit -m "Descrição do que foi feito"
    ```

5. **Suba sua branch para o GitHub:**
    ```bash
    git push origin feature/nome-da-feature
    ```

---

### Submetendo um Pull Request

Após finalizar o desenvolvimento na sua branch, siga os passos abaixo para abrir um **Pull Request**:

1. **Acesse o repositório no GitHub**.
2. Clique no botão **"New Pull Request"**.
3. Escolha a branch de origem (`feature/nome-da-feature`) e a branch de destino (`develop`).
4. Preencha o **Pull Request** seguindo o template abaixo:

#### Template de Pull Request

```markdown
### Descrição
Descreva de forma clara o que foi desenvolvido. Explique a lógica utilizada e os pontos principais de alteração.

### Issue relacionada
Referencie a issue que este PR resolve (se aplicável).
- Issue: #[número da issue]

### Testes realizados
Liste os testes manuais ou automáticos que foram realizados para validar o funcionamento das mudanças.

- [ ] Teste 1: Descrição do teste
- [ ] Teste 2: Descrição do teste

### Checklist de submissão
Certifique-se de que:
- [ ] O código foi testado localmente.
- [ ] Os commits seguem as boas práticas.
- [ ] O PR está sendo aberto para a branch `develop`.
```

---

## Estilo de Código

Para manter a consistência no projeto, siga os seguintes padrões de estilo:

1. **Indentação:** Use 4 espaços para indentação.
2. **Nomes de Variáveis:** Use camelCase para variáveis e funções. Exemplo: `minhaVariavel`.
3. **Nomes de Classes:** Utilize PascalCase para nomes de classes. Exemplo: `MinhaClasse`.
4. **Comentários:** Adicione comentários explicativos quando necessário, especialmente em trechos de código complexos.
5. **Limpeza de Código:** Remova trechos de código comentados ou não utilizados antes de abrir um pull request.
6. **Linhas Máximas:** Limite o número de caracteres por linha a 80, sempre que possível.
7. **Boas Práticas:** Siga as melhores práticas de programação (como evitar duplicação de código e otimização desnecessária).
8. **PHP Standards Recommendations:** Siga as PSR para padronização do código em PHP.

### Linters e Formatação

- Utilize as ferramentas de lint e formatação definidas no projeto. Elas serão aplicadas automaticamente na submissão de código.
- Antes de abrir um Pull Request, rode os linters localmente:
  ```bash
  npm run lint
  
---

## Reportando Bugs

Se encontrar um bug, siga as instruções abaixo para reportá-lo:

1. **Verifique se o bug já foi reportado**: Confira nas issues abertas se o problema já foi identificado.
2. **Abra uma nova issue**: Se o bug ainda não tiver sido reportado, crie uma nova issue e inclua as seguintes informações:
   - **Descrição clara do problema.**
   - **Passos para reproduzir o bug.**
   - **Resultado esperado e o resultado atual.**
   - **Versão do sistema** (se aplicável).
   - **Logs ou prints relevantes.**

### Exemplo de report

```markdown
**Descrição do bug**: O botão "Login" não responde ao clique.

**Passos para reproduzir**:
1. Acesse a tela de login.
2. Insira um usuário e senha válidos.
3. Clique no botão "Login".

**Resultado esperado**: O sistema deveria logar o usuário.
**Resultado atual**: Nada acontece ao clicar no botão.
```

---

## Commits

Ao realizar commits no projeto, siga as diretrizes abaixo para manter o histórico de alterações limpo, rastreável e compreensível para todos os colaboradores:

1. **Commits Pequenos e Objetivos**
   - Faça commits pequenos e frequentes, focados em uma única tarefa ou alteração. Isso facilita a revisão e a rastreabilidade do código.

2. **Padrão de Mensagens de Commit**
   - Siga o padrão `[tipo]: descrição`, usando verbos no imperativo para descrever a ação do commit. Utilize os tipos abaixo para cada situação:
      - **feat**: quando adicionar uma nova funcionalidade (ex: `feature: adicionando tela de login`)
      - **fix**: para correções de bugs (ex: `fix: ajustando validação do login`)
      - **refactor**: para refatorações sem alterar a funcionalidade (ex: `refactor: otimizando profile controller`)
      - **docs**: para atualizações na documentação (ex: `docs: atualizando exemplos de utilização da API`)
      - **style**: para alterações de formatação e estilo (ex: `style: aplicando estilo na tela de login`)
      - **test**: ao adicionar ou modificar testes (ex: `test: adicionando testes para validação do login`)
      - **chore**: para tarefas de manutenção ou configurações (ex: `chore: atualizando dependências`)

3. **Escreva Mensagens de Commit Claras**
   - No título (primeira linha) do commit, descreva a alteração em até 50 caracteres.
   - Se precisar de mais detalhes, deixe uma linha em branco e adicione uma descrição mais completa. Exemplo:
     ```plaintext
     feat: adicionando autenticação do usuário

     Implementado funcionalidade de login e logout do usuário, incluindo gerenciamento de 
     sessão. Esta atualização também inclui validação de formulário no front end.
     ```

4. **Estrutura de Commit**
   - Realize cada commit no contexto de uma branch específica. Use o formato `feature/nome-da-feature` ou `bugfix/nome-do-bug` para as branches, garantindo que cada commit reflita a tarefa da branch.

5. **Sincronize o Código com a Branch de Origem**
   - Antes de realizar commits, sincronize sua branch com a `develop`:
     ```bash
     git checkout develop
     git pull origin develop
     git checkout feature/nome-da-feature
     git rebase develop
     ```

6. **Commits para Testes e Debug**
   - Evite subir commits temporários de testes ou depuração. Use commits finais e organizados para melhorar a legibilidade e o histórico do projeto.

7. **Checklist de Commits Antes do PR**
   - Verifique antes de abrir o Pull Request:
      - [ ] O commit foi testado localmente.
      - [ ] A mensagem de commit segue o padrão.
      - [ ] O commit está relacionado apenas à funcionalidade descrita na branch.

---

## Dicas Gerais

- **Sincronize frequentemente:** Certifique-se de manter seu repositório local atualizado com a branch `develop` para evitar conflitos. Use os comandos a seguir para garantir que está trabalhando com o código mais recente:

  ```bash
  git checkout develop
  git pull origin develop

- **Revisão de código:** Sempre participe das revisões de Pull Requests de outros colaboradores. Isso ajuda a manter a consistência do código, identificar melhorias e compartilhar conhecimento.
- **Testes:** Teste todas as suas alterações localmente antes de enviar um Pull Request. Isso minimiza o risco de introduzir bugs e garante que o código está funcionando conforme esperado.
- **Documentação:** Se fizer alterações significativas em funcionalidades ou APIs, lembre-se de atualizar a documentação para refletir essas mudanças.
- **Comunicação:** Se tiver dúvidas ou sugestões, use os canais de comunicação definidos para o projeto (e-mail, Slack, etc.) para discutir com os outros colaboradores antes de implementar grandes mudanças.