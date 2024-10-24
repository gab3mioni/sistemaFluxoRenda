USE sistemaFluxoRenda;

INSERT INTO governo (tipo_transacao, descricao, valor)
VALUES
    ('imposto', 'Imposto de Renda', 5000.00),
    ('imposto', 'IPTU', 1200.50),
    ('beneficio', 'Auxílio Emergencial', 600.00),
    ('beneficio', 'Seguro-Desemprego', 1200.00),
    ('imposto', 'Imposto de Importação', 3000.75),
    ('imposto', 'Contribuição Social', 450.80),
    ('beneficio', 'Aposentadoria', 1500.00),
    ('imposto', 'ISS', 900.40),
    ('beneficio', 'Bolsa Família', 350.00),
    ('imposto', 'ICMS', 700.00);

INSERT INTO empresas (nome, cnpj, receita, despesa, investimento, imposto, beneficio_governo)
VALUES
    ('Empresa A', '12345678000195', 100000.00, 50000.00, 15000.00, 10000.00, 5000.00),
    ('Empresa B', '23456789000186', 200000.00, 100000.00, 25000.00, 15000.00, 10000.00),
    ('Empresa C', '34567890000177', 150000.00, 70000.00, 20000.00, 12000.00, 8000.00),
    ('Empresa D', '45678901000168', 300000.00, 150000.00, 50000.00, 20000.00, 15000.00),
    ('Empresa E', '56789012000159', 250000.00, 130000.00, 30000.00, 18000.00, 12000.00);

INSERT INTO familias (nome, cpf, renda, consumo, investimento, beneficio_governo)
VALUES
    ('Família Silva', '12345678901', 5000.00, 3000.00, 500.00, 200.00),
    ('Família Souza', '23456789012', 8000.00, 4000.00, 1000.00, 300.00),
    ('Família Oliveira', '34567890123', 6000.00, 3500.00, 700.00, 400.00),
    ('Família Santos', '45678901234', 9000.00, 5000.00, 1500.00, 500.00),
    ('Família Pereira', '56789012345', 10000.00, 6000.00, 2000.00, 600.00);

INSERT INTO setor_externo (tipo_transacao, descricao, valor)
VALUES
    ('exportacao', 'Exportação de Soja', 50000.00),
    ('importacao', 'Importação de Eletrônicos', 40000.00),
    ('exportacao', 'Exportação de Carne', 30000.00),
    ('importacao', 'Importação de Petróleo', 100000.00),
    ('exportacao', 'Exportação de Minério de Ferro', 150000.00);

INSERT INTO setor_financeiro (tipo_transacao, valor, origem, destino)
VALUES
    ('investimento', 20000.00, 'empresa', 'setor_externo'),
    ('poupanca', 5000.00, 'familia', 'governo'),
    ('emprestimo', 30000.00, 'setor_externo', 'empresa'),
    ('investimento', 15000.00, 'governo', 'familia'),
    ('poupanca', 10000.00, 'familia', 'empresa');

INSERT INTO transacao_familia_empresa (id_familia, id_empresa, valor, tipo_transacao)
VALUES
    (1, 1, 2000.00, 'consumo'),
    (2, 2, 4000.00, 'consumo'),
    (3, 3, 3500.00, 'consumo'),
    (4, 4, 5000.00, 'consumo'),
    (5, 5, 6000.00, 'salario');

INSERT INTO transacao_governo (id_familia, id_empresa, valor, tipo_transacao)
VALUES
    (1, NULL, 200.00, 'beneficio'),
    (2, NULL, 300.00, 'beneficio'),
    (3, 1, 5000.00, 'imposto'),
    (NULL, 2, 10000.00, 'imposto'),
    (5, NULL, 600.00, 'beneficio');
