<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Família | Sistema de Fluxo de Renda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-white">
                <div class="card-body text-center">
                    <h3 class="card-title">Saldo Atual</h3>
                    <p class="display-5">
                        <!-- NÃO MEXER NO CÓDIGO ABAIXO -->
                        R$ <?= number_format($saldo, 2, ',', '.') ?>
                        <!-- NÃO MEXER NO CÓDIGO ACIMA -->
                    </p>
                </div>

                <div class="row text-center">
                    <div class="col-md-2">
                        <h4 class="card-title">Receita</h4>
                        <p>
                            <!-- NÃO MEXER NO CÓDIGO ABAIXO -->
                            R$ <?= number_format($receita, 2, ',', '.') ?>
                            <!-- NÃO MEXER NO CÓDIGO ACIMA -->
                        </p>

                    </div>

                    <div class="col-md-2">
                        <h4 class="card-title">Despesas</h4>
                        <p>
                            <!-- NÃO MEXER NO CÓDIGO ABAIXO -->
                            R$ <?= number_format($despesa, 2, ',', '.') ?>
                            <!-- NÃO MEXER NO CÓDIGO ACIMA -->
                        </p>
                    </div>

                    <div class="col-md-2">
                        <h4 class="card-title">Investimentos</h4>
                        <p>
                            <!-- NÃO MEXER NO CÓDIGO ABAIXO -->
                            R$ <?= number_format($investimento, 2, ',', '.') ?>
                            <!-- NÃO MEXER NO CÓDIGO ACIMA -->
                        </p>
                    </div>

                    <div class="col-md-2">
                        <h4 class="card-title">Impostos</h4>
                        <p>
                            <!-- NÃO MEXER NO CÓDIGO ABAIXO -->
                            R$ <?= number_format($impostos, 2, ',', '.') ?>
                            <!-- NÃO MEXER NO CÓDIGO ACIMA -->
                        </p>
                    </div>

                    <div class="col-md-2">
                        <h4 class="card-title">Benefícios</h4>
                        <p>
                            <!-- NÃO MEXER NO CÓDIGO ABAIXO -->
                            R$ <?= number_format($beneficios, 2, ',', '.') ?>
                            <!-- NÃO MEXER NO CÓDIGO ACIMA -->
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cash-stack me-2"></i>
                        Nova Transação
                    </h5>
                </div>
                <div class="card-body">
                    <!-- NÃO MEXER NO METHOD E ACTION DO FORM -->
                    <!-- NÃO MEXER NO NAME="" DOS INPUT -->
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
                            <i class="bi bi-send me-2"></i>
                            Pagar Salário
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        Novo Investimento
                    </h5>
                </div>
                <div class="card-body">
                    <!-- NÃO MEXER NO METHOD E ACTION DO FORM -->
                    <!-- NÃO MEXER NO NAME="" DOS INPUT -->
                    <form method="POST" action="familia/newInvestimento">
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

        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-header bg-warning ">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>
                        Impostos Pendentes
                    </h5>
                </div>
                <div class="card-body">
                    <div id="sem-impostos" class="text-center py-4" style="display: none;">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        <p class="mt-3 mb-0">Não há impostos pendentes para pagamento.</p>
                    </div>

                    <div id="com-impostos">
                        <!-- NÃO MEXER NO METHOD E ACTION DO FORM -->
                        <form method="POST" action="familia/pagarImposto">
                            <div class="mb-3">
                                <label class="form-label">Data da Cobrança</label>
                                <input type="text" class="form-control" id="dataCobranca">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" name="valorImposto" class="form-control">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-cash-coin me-2"></i>
                                Pagar Imposto
                            </button>
                        </form>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
