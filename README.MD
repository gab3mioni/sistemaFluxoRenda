# Sistema Fluxo Renda

Projeto desenvolvido para a matéria de Economia e Finanças, do 3°ADS/24, na FATEC Guaratinguetá - Prof. João Mod. 
O sistema deve simular o Fluxo Circular de Renda. Os grupos poderão interagir com os outros, simulando transações econômicas.

## 🚀 Começando

Essas instruções permitirão que você obtenha uma cópia do projeto em operação na sua máquina local para fins de desenvolvimento e teste.

Consulte **[Implantação](#-implantação)** para saber como implantar o projeto.

### 📋 Pré-requisitos

Antes de começar, você precisará ter os seguintes itens instalados na sua máquina:

- **WampServer** ou **XAMPP** para rodar o servidor PHP.
- **Composer** para gerenciamento de dependências.

### 🔧 Instalação

Siga os passos abaixo para configurar o ambiente de desenvolvimento:

1. Navegue até o diretório do WampServer ou XAMPP, se instalou com as configurações padrões, provavelmente será:
   ```bash
   cd C:\wamp64\www 
   ```
   ou
   ```bash
   cd C:\xampp\htdocs
   ```

2. Clone o repositório:
   ```bash
   git clone https://github.com/gab3mioni/sistemaFluxoRenda.git
   ```

3. Navegue até o diretório do projeto:
   ```bash
   cd sistemaFluxoRenda
   ```

4. Instale as dependências com Composer:
   ```bash
   composer install
   ```

5. Configure o banco de dados no arquivo `config/config.php` com suas credenciais.

6. Execute o arquivo `init_database.sql` para criar a estrutura do banco de dados:
   ```sql
   -- Execute as instruções contidas no arquivo init_database.sql
   ```

7. Execute o arquivo `seed_database.sql` para semear o banco de dados com dados fictícios:
   ```sql
   -- Execute as instruções contidas no arquivo seed_database.sql
   ```

7. Inicie o WampServer ou XAMPP e acesse o projeto pelo navegador:
   ```
   http://localhost/sistemaFluxoRenda/public/
   ```

Agora você deve ser capaz de acessar a aplicação e interagir com as funcionalidades do sistema.

## ⚙️ Executando os testes

Atualmente, a aplicação não possui testes automatizados implementados. Recomenda-se implementar testes de unidade e de integração para garantir a estabilidade do sistema.

### ⌨️ Testes de estilo de codificação

Os testes de estilo de codificação podem ser realizados utilizando ferramentas como PHP CodeSniffer ou PHPCS. Exemplos:

```bash
# Executar a análise de estilo
vendor/bin/phpcs --standard=PSR2 src/
```

## 📦 Implantação

Para implantar a aplicação em um servidor de produção, siga as instruções para configurar o ambiente e as variáveis de ambiente necessárias. Lembre-se de garantir que as credenciais do banco de dados estejam adequadas para o ambiente de produção.

## 🛠️ Construído com

* [HTML5](https://developer.mozilla.org/pt-BR/docs/Web/HTML) - Linguagem de marcação
* [CSS3](https://developer.mozilla.org/pt-BR/docs/Web/CSS) - Linguagem de estilo
* [Bootstrap 5.3](https://getbootstrap.com/) - Framework CSS para layout responsivo
* [PHP 8.2.18](https://www.php.net/) - Linguagem de programação
* [Composer 2.7.9](https://getcomposer.org/) - Gerenciador de dependências PHP
* [MySQL 8.3.0](https://dev.mysql.com/) - Banco de dados

## 🖇️ Colaborando

Por favor, leia o [CONTRIBUTING.md](https://github.com/gab3mioni/sistemaFluxoRenda/blob/main/CONTRIBUTING.MD) para obter detalhes sobre o nosso código de conduta e o processo para nos enviar pedidos de solicitação.

## 📌 Versão

Nós usamos [Git](https://git-scm.com/) para controle de versão. Para as versões disponíveis, observe as [tags neste repositório](https://github.com/gab3mioni/sistemaFluxoRenda/tags).

## ✒️ Autores

* **Alexandre Ferreira** - *Desenvolvedor Frontend* - [aleferreiras](https://github.com/ale-ferreiras)
* **Gabriel de Sousa Castro** - *Desenvolvedor Frontend* - [castro-bit](https://github.com/castro-bit)
* **Gabriel Mioni Bastos** - *Desenvolvedor Backend* - [gab3mioni](https://github.com/gab3mioni)
* **Melissa Cristine de Souza Máximo** - *UX/UI Designer & Frontend* - [MelissaMaximo](https://github.com/MelissaMaximo)
