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
<nav class="navbar navbar-expand-lg navbar-dark bg-custom-primary">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="bi bi-currency-exchange me-2"></i>
        </a>
    </div>
</nav>

<div class="container py-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-white">
                <div class="card-body text-center">
                    <h3 class="card-title">Saldo Atual</h3>
                    <h2 class="display-4" id="currentBalance">R$ 1.000,00</h2>
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
                    <form id="transactionForm">
                        <div class="mb-3">
                            <label class="form-label">Para qual grupo você irá transferir?</label>
                            <select class="form-select" id="transactionType" required>
                                <option value="familia">Família</option>
                                <option value="empresa">Empresa</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qual o nome da família/empresa?</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" class="form-control" id="transactionAmount" step="0.01" required>
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

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Histórico de Transações
                    </h5>
                </div>
                <div class="card-body">
                    <div id="transactionHistory" class="transaction-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
