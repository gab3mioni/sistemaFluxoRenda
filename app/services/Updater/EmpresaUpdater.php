<?php

namespace App\Services\Updater;

use App\Services\Validator\TransacaoValidator;
use App\Services\DatabaseService;

class EmpresaUpdater
{
    private $databaseService;
    private $transacaoValidator;

    public function __construct(DatabaseService $databaseService, TransacaoValidator $transacaoValidator)
    {
        $this->databaseService = $databaseService;
        $this->transacaoValidator = $transacaoValidator;
    }

    public function atualizarSaldoEmpresa(int $id, string $tipo, float $valor, float $saldoAtual): bool
    {
        $novoSaldo = 0.0;

        if(!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        if ($tipo === 'investimento' || $tipo === 'despesa' || $tipo === 'imposto' || $tipo === 'salario') {
            $novoSaldo = $saldoAtual - $valor;
        } else if ($tipo === 'beneficio' || $tipo === 'consumo') {
            $novoSaldo = $saldoAtual + $valor;
        }

        if (!$this->transacaoValidator->validateValorInserido($novoSaldo)) {
            return false;
        }

        return $this->databaseService->updateField('empresas', 'saldo', $novoSaldo, $id);
    }

    public function atualizarReceitaEmpresa(int $id, float $valor, float $saldoReceitaAtual): bool
    {
        if(!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        $novoSaldoReceita = $saldoReceitaAtual + $valor;

        if (!$this->transacaoValidator->validateValorInserido($novoSaldoReceita)) {
            return false;
        }

        return $this->databaseService->updateField('empresas', 'receita', $novoSaldoReceita, $id);
    }

    public function atualizarDespesasEmpresa(int $id, float $valor, float $saldoDespesasAtual): bool
    {
        if (!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        $novoSaldoDespesa = $saldoDespesasAtual + $valor;

        if(!$this->transacaoValidator->validateValorInserido($novoSaldoDespesa)) {
            return false;
        }

        return $this->databaseService->updateField('empresas', 'despesa', $novoSaldoDespesa, $id);
    }

    public function atualizarInvestimentoEmpresa(int $id, float $valor, float $saldoInvestimentoAtual): bool
    {
        if(!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        $novoSaldoInvestimento = $saldoInvestimentoAtual + $valor;

        if (!$this->transacaoValidator->validateValorInserido($novoSaldoInvestimento)) {
            return false;
        }

        return $this->databaseService->updateField('empresas', 'investimento', $novoSaldoInvestimento, $id);
    }

    public function atualizarImposto(int $id, float $valor, float $saldoImpostoAtual): bool
    {
        if (!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        $novoSaldoImposto = $saldoImpostoAtual + $valor;

        if (!$this->transacaoValidator->validateValorInserido($novoSaldoImposto)) {
            return false;
        }

        return $this->databaseService->updateField('empresas', 'imposto', $novoSaldoImposto, $id);
    }

    public function atualizarBeneficio(int $id, float $valor, float $saldoBeneficioAtual): bool
    {
        if (!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        $novoSaldoBeneficio = $saldoBeneficioAtual + $valor;

        if (!$this->transacaoValidator->validateValorInserido($novoSaldoBeneficio)) {
            return false;
        }

        return $this->databaseService->updateField('empresas', 'beneficio_governo', $novoSaldoBeneficio, $id);
    }
}