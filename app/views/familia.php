<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Família | Sistema de Fluxo de Renda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/familia/familia.css" rel="stylesheet">
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <a class="navbar-brand ms-3" href="#">Dashboard da Família</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link active" href="#" onclick="showSection('renda', this)">Renda</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showSection('consumo', this)">Consumo</a>
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


    <div class="hidden-section card mb-4 " id="renda">
        <div class="card-header">
        <h5>Saldo Atual</h5>
        </div>

        <div class="card-body">
        <p>
            <!-- NÃO MEXER NO CÓDIGO ABAIXO -->
             R$ <?= number_format($renda, 2, ',', '.') ?>
            <!-- NÃO MEXER NO CÓDIGO ACIMA -->
        </p>
    </div>
    </div>


    <div class="hidden-section card mb-4 " id="consumo">
        <div class="card-header">
        <h5>Consumo</h>
        </div>

        <div class="card-body">
        <p>
                         
            R$ <?= number_format($consumo, 2, ',', '.') ?>
                   
        </p>

    </div>
    </div>

    <div class="hidden-section card mb-4 " id="investimento">
    <div class="card-header">
        <h5>Investimentos</h>
    </div>
    
    <div class="card-body">

    <p>
    <td>Total investido: </td>
      R$ <?= number_format($investimento, 2, ',', '.') ?>                  
    </p>

        <div class="col-8 container justify-content-center">
           <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Novo Investimento
                    </h5>
                </div>
                <div class="card-body">
                    
                    <form method="POST" action="familia/newInvestimento">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Investimento</label>
                            <select class="form-select" name="tipo" required>
                                <option value="poupanca">Poupança</option>
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
    </div>


    <div class="hidden-section card mb-4 " id="beneficios">
        <div class="card-header">
        <h5>Benefícios</h5>
        </div>

        <div class="card-body">
        <td>Saldo recebido: </td>
        <p>
                            
            R$ <?= number_format($beneficio, 2, ',', '.') ?>
                           
        </p>

    </div>
    </div>

    <div class="hidden-section card mb-4 " id="transacao">

        <div class="card-header">
        <h5>Transação</h5>
        </div>

        <div class=" mt-3 mb-3 col-8 container justify-content-center">
           <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cash-stack me-2"></i>
                        Nova Transação
                    </h5>
                </div>
                <div class="card-body">
                  
                    <form method="POST" action="familia/newTransacao">
                        <div class="mb-3">
                            <label class="form-label">Qual o ID da empresa que você irá transferir?</label>
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
                            <i class="bi bi-send me-2"></i>
                            Realizar Transação
                        </button>
                    </form>
                </div>
            </div>
    </div>
</div>
    


    <div class="hidden-section card mb-4" id="historicoTransacoes">
    <h5 class="card-header">Histórico de Transações</h5>
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
                <!-- NÃO MEXER NO CÓDIGO A SEGUIR -->
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
                        <td colspan="4" class="text-center">Nenhuma transação encontrada.</td>
                    </tr>
                <?php endif; ?>
                <!-- NÃO MEXER NO CÓDIGO ACIMA -->
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="assets/js/familia/familia.js"></script>

</body>
</html>
