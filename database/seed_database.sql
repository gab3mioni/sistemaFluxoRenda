USE sistemaFluxoRenda;

INSERT INTO governo (usuario, senha)
VALUES
    ('admin_governo', 'senha123');

INSERT INTO empresas (usuario, senha, nome, cnpj, saldo, receita, despesa, investimento, imposto, beneficio_governo)
VALUES
    ('empresaA', 'empresaA123', 'Empresa A', '12345678000195', 100000.00, 150000.00, 50000.00, 0.00, 0.00, 0.00),
    ('empresaB', 'empresaB123', 'Empresa B', '23456789000186', 100000.00, 200000.00, 100000.00, 0.00, 0.00, 0.00),
    ('empresaC', 'empresaC123', 'Empresa C', '34567890000177', 50000.00, 70000.00, 20000.00, 0.00, 0.00, 0.00),
    ('empresaD', 'empresaD123', 'Empresa D', '45678901000168', 100000.00, 150000.00, 50000.00, 0.00, 0.00, 0.00),
    ('empresaE', 'empresaE123', 'Empresa E', '56789012000159', 100000.00, 130000.00, 30000.00, 0.00, 0.00, 0.00);

INSERT INTO familias (usuario, senha, nome, cpf, saldo, renda, consumo, investimento, beneficio_governo)
VALUES
    ('silva', 'silva123', 'Família Silva', '12345678901', 2500.00, 3000.00, 500.00, 0.00, 0.00),
    ('souza', 'souza123', 'Família Souza', '23456789012', 3000.00, 4000.00, 1000.00, 0.00, 0.00),
    ('oliveira', 'oliveira123', 'Família Oliveira', '34567890123', 2800.00, 3500.00, 700.00, 0.00, 0.00),
    ('santos', 'santos123', 'Família Santos', '45678901234', 7500.00, 5000.00, 1500.00, 0.00, 0.00),
    ('pereira', 'pereira123', 'Família Pereira', '56789012345', 4000.00, 6000.00, 2000.00, 0.00, 0.00);
