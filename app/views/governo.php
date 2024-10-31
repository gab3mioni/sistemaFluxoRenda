<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Governo</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/governo/governo.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <a class="navbar-brand" href="#">Dashboard do Governo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#arrecadacao" onclick="showSection('arrecadacao')">Arrecadação</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#beneficios" onclick="showSection('beneficios')">Benefícios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#legislacoes" onclick="showSection('legislacoes')">Legislações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#taxas" onclick="showSection('taxas')">Taxas de Imposto</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">

        <div id="arrecadacao" class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Arrecadação de Impostos</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Fonte</th>
                        <th>Valor Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Famílias</td>
                        <td>R$ <?= number_format($somaImpostosFamilia, 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Empresas</td>
                        <td>R$ <?= number_format($somaImpostosEmpresa, 2, ',', '.') ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>



        <div id="beneficios" class="card hidden-section mb-4">
            <div class="card-header">
                <h5 class="mb-0">Distribuição e Gerenciamento de Benefícios</h5>
            </div>
            <div class="card-body">
                <h6>Adicionar Benefício</h6>
                <form method="POST" action="governo/newBeneficio">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="valor-beneficio">Valor (R$)</label>
                            <input type="number" name="valor" class="form-control" placeholder="Valor" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="destinatario-beneficio">Destinatário</label>
                            <select name="destinatario" class="form-control">
                                <option value="familia">Família</option>
                                <option value="empresa">Empresa</option>
                            </select>
                        </div>
                        <div class="form-group col-md4">
                            <label for="id">ID</label>
                            <input type="number" name="id" class="form-control" placeholder="ID" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Salvar Benefício</button>
                </form>

                <hr>
                <h6>Benefícios Ativos</h6>
                <div id="benefitsList" class="list-group">
                 
                </div>
            </div>
        </div>

       
        <div id="legislacoes" class="card hidden-section mb-4">
            <div class="card-header">
                <h5 class="mb-0">Legislações Implementadas</h5>
            </div>
            <div class="card-body">
                <h6>Adicionar Legislação</h6>
                <form id="addLegislationForm">
                    <div class="form-group">
                        <label for="tipo-legislacao">Tipo de Legislação</label>
                        <select id="tipo-legislacao" class="form-control" required>
                            <option value="" disabled selected>Selecione o tipo</option>
                            <option value="Família">Família</option>
                            <option value="Empresa">Empresa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="texto-legislacao">Descrição da Legislação</label>
                        <input type="text" id="texto-legislacao" class="form-control" placeholder="Descreva a legislação" required>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" onclick="addLegislation()">Salvar Legislação</button>
                </form>
                <hr>
                <h6>Legislações Ativas</h6>
                <div id="legislationList" class="list-group">
                 
                </div>
            </div>
        </div>

        <div class="container">
     
        <div id="taxas" class="card hidden-section mb-4">
            <div class="card-header">
                <h5 class="mb-0">Taxas de Imposto</h5>
            </div>
            <div class="card-body">
                <h6>Adicionar Taxa</h6>
                <form id="addTaxForm">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="destinatario-taxa">Destinatário</label>
                            <select id="destinatario-taxa" class="form-control" required>
                                <option value="" disabled selected>Selecione</option>
                                <option value="Família">Família</option>
                                <option value="Empresa">Empresa</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nome-taxa">Nome da Taxa</label>
                            <input type="text" id="nome-taxa" class="form-control" placeholder="Nome da taxa" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="preco-taxa">Valor (R$)</label>
                            <input type="number" id="preco-taxa" class="form-control" placeholder="Valor da taxa" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="descricao-taxa">Descrição</label>
                            <input type="text" id="descricao-taxa" class="form-control" placeholder="Descrição da taxa">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" onclick="addTax()">Salvar Taxa</button>
                </form>
                <hr>
                <h6>Taxas Ativas</h6>
                <div id="taxList" class="list-group">
                  
                </div>
            </div>
        </div>
    </div>

   
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/governo.js"></script>
</body>
</html>
