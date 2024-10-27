CREATE DATABASE sistemaFluxoRenda;
USE sistemaFluxoRenda;

CREATE TABLE IF NOT EXISTS governo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    cnpj VARCHAR(14) NOT NULL,
    receita DECIMAL(15, 2),
    despesa DECIMAL(15, 2),
    investimento DECIMAL(15, 2),
    imposto DECIMAL(15, 2),
    beneficio_governo DECIMAL(15, 2),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS familias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(11) NOT NULL,
    renda DECIMAL(15, 2),
    consumo DECIMAL(15, 2),
    investimento DECIMAL(15, 2),
    beneficio_governo DECIMAL(15, 2),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

CREATE TABLE IF NOT EXISTS setor_externo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_transacao ENUM('exportacao', 'importacao') NOT NULL,
    descricao VARCHAR(255),
    valor DECIMAL(15, 2) NOT NULL,
    data_transacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS setor_financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_transacao ENUM('investimento', 'poupanca', 'emprestimo') NOT NULL,
    valor DECIMAL(15, 2) NOT NULL,
    origem ENUM('familia', 'empresa', 'governo', 'setor_externo') NOT NULL,
    destino ENUM('familia', 'empresa', 'governo', 'setor_externo') NOT NULL,
    data_transacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transacao_familia_empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_familia INT,
    id_empresa INT,
    valor DECIMAL(15, 2) NOT NULL,
    tipo_transacao ENUM('consumo', 'salario') NOT NULL,
    data_transacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_familia) REFERENCES familias(id) ON DELETE CASCADE,
    FOREIGN KEY (id_empresa) REFERENCES empresas(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS transacao_governo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_familia INT NULL,
    id_empresa INT NULL,
    valor DECIMAL(15, 2) NOT NULL,
    tipo_transacao ENUM('imposto', 'beneficio') NOT NULL,
    data_transacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_familia) REFERENCES familias(id) ON DELETE SET NULL,
    FOREIGN KEY (id_empresa) REFERENCES empresas(id) ON DELETE SET NULL
);