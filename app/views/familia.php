<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Família | Sistema de Fluxo de Renda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container py-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-white">
                <div class="card-body text-center">
                    <h3 class="card-title">Saldo Atual</h3>
                    <p class="display-5">R$ <?= number_format($saldo, 2, ',', '.') ?></p>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <h4 class="card-title">Renda</h4>
                        <p>R$ <?= number_format($renda, 2, ',', '.') ?></p>
                    </div>

                    <div class="col-md-3">
                        <h4 class="card-title">Consumo</h4>
                        <p>R$ <?= number_format($consumo, 2, ',', '.') ?></p>
                    </div>

                    <div class="col-md-3">
                        <h4 class="card-title">Investimentos</h4>
                        <p>R$ <?= number_format($investimento, 2, ',', '.') ?></p>
                    </div>

                    <div class="col-md-3">
                        <h4 class="card-title">Benefícios</h4>
                        <p>R$ <?= number_format($beneficio, 2, ',', '.') ?></p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card h-100">
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
                        <div id="transactionMessage" class="mt-3"></div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Investimentos -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Novo Investimento
                    </h5>
                </div>
                <div class="card-body">
                    <form id="investmentForm">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Investimento</label>
                            <select class="form-select" id="investmentType" required>
                                <option value="P">Poupança</option>
                                <option value="I">Investimento</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="investmentAmount" step="0.01" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-piggy-bank me-2"></i>
                            Realizar Investimento
                        </button>
                        <div id="investmentMessage" class="mt-3"></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gift me-2"></i>
                        Solicitar Benefício
                    </h5>
                </div>
                <div class="card-body">
                    <form id="benefitForm">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Benefício</label>
                            <select class="form-select" id="benefitType" required>
                                <option value="BF">Bolsa Família</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor Solicitado</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="benefitAmount" step="0.01" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info text-white w-100">
                            <i class="bi bi-check-circle me-2"></i>
                            Solicitar Benefício
                        </button>
                        <div id="benefitMessage" class="mt-3"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-light mb-4">
        <div class="card-header">
            <h4>Histórico de Transações</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Valor (R$)</th>
                    <th>Empresa</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($historicoTransacoes)): ?>
                    <?php foreach ($historicoTransacoes as $transacao): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($transacao['data_transacao'])) ?></td>
                            <td><?= ucfirst($transacao['tipo_transacao']) ?></td>
                            <td><?= number_format($transacao['valor'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($transacao['id_empresa']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Nenhuma transação encontrada.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
