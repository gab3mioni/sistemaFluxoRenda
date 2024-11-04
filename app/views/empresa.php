<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Empresa | Sistema de Fluxo de Renda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/empresa/empresa.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <a class="navbar-brand ms-3" href="#">Dashboard da Empresa</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="#" onclick="showSection('renda', this)">Saldo</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('consumo', this)">Dados Gerais</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('beneficios', this)">Benefícios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('transacao', this)">Transação</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('investimento', this)">Investimento</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('historicoTransacoes', this)">Histórico de Transações</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="governo/logout">Sair</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container py-4">

    <!-- Seções correspondentes a cada página da Navbar -->
    <div class="hidden-section card mb-4 " id="renda">
        <h5 class="card-header">Saldo Atual</h5>
        <p>
            R$ <?= number_format($saldo, 2, ',', '.') ?>
        </p>
    </div>



    <div class="hidden-section card mb-4 " id="consumo">

    <div class="card-header">
    <h5>Dados Gerais</h5>
    </div>

    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-2">
                <h4 class="card-title">Receita</h4>
                <p>R$ <?= number_format($receita, 2, ',', '.') ?></p>
            </div>
            <div class="col-md-2">
                <h4 class="card-title">Despesas</h4>
                <p>R$ <?= number_format($despesa, 2, ',', '.') ?></p>
            </div>
            <div class="col-md-2">
                <h4 class="card-title">Investimentos</h4>
                <p>R$ <?= number_format($investimento, 2, ',', '.') ?></p>
            </div>
            <div class="col-md-2">
                <h4 class="card-title">Impostos</h4>
                <p>R$ <?= number_format($impostos, 2, ',', '.') ?></p>
            </div>
        </div>
    </div>
</div>



    <div class="hidden-section card mb-4 " id="beneficios" style="display: none;">
        <h5 class="card-header">Benefícios</h5>
         <div class="card-body">
         <td>Saldo recebido: </td>
                        <p>
                            <!-- NÃO MEXER NO CÓDIGO ABAIXO -->
                            R$ <?= number_format($beneficios, 2, ',', '.') ?>
                            <!-- NÃO MEXER NO CÓDIGO ACIMA -->
                        </p>
                    </div>
    </div>



    <div class="hidden-section card mb-4 " id="transacao" style="display: none;">
    <h5 class="card-header">Transação</h6>
    <div class="container d-flex justify-content-center mt-3 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-cash-stack me-2"></i>Nova Transação</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="empresa/newSalario">
                    <div class="mb-3">
                        <label class="form-label">Qual o ID da família que você irá pagar o salário?</label>
                        <input type="number" name="id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" name="valor" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send me-2"></i>Pagar Salário
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>



    <div class="hidden-section card mb-4" id="investimento" style="display: none;">
        
    <div class="card-header">
        <h5>Investimento</h>
    </div>
        <div class="container d-flex justify-content-center mt-3 mb-3">
        <div class="card">
                <div class="card-body bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Novo Investimento
                    </h5>
                </div>
                <div class="card-body">
                    <!-- NÃO MEXER NO METHOD E ACTION DO FORM -->
                    <!-- NÃO MEXER NO NAME="" DOS INPUT -->
                    <form method="POST" action="empresa/newInvestimento">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Investimento</label>
                            <select class="form-select" name="tipo" required>
                                <option value="investimento">Investimento</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" name="valor" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-piggy-bank me-2"></i>
                            Realizar Investimento
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="hidden-section card mb-4" id="historicoTransacoes">
    <h5 class="card-header">Histórico de Transações</h6>
    <div class="container d-flex justify-content-center">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Valor (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($historicoTransacoes)): ?>
                    <?php foreach ($historicoTransacoes as $transacao): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($transacao['data_transacao'])) ?></td>
                            <td><?= ucfirst($transacao['tipo_transacao']) ?></td>
                            <td><?= number_format($transacao['valor'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Nenhuma transação encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="assets/js/empresa/empresa.js"></script>
</body>
</html>
