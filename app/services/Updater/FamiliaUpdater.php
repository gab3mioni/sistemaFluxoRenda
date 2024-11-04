<?php

namespace App\Services\Updater;

use App\Services\Transaction\TransacaoValidator;
use App\Services\DatabaseService;

class FamiliaUpdater
{
    private $transacaoValidator;
    private $databaseService;

    public function __construct(DatabaseService $databaseService, TransacaoValidator $transacaoValidator)
    {
        $this->transacaoValidator = $transacaoValidator;
        $this->databaseService = $databaseService;
    }

    public function atualizarSaldo(int $id, string $tipo, float $valor, float $saldoAtual): bool
    {
        $novoSaldo = 0.0;

        if (!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        if ($tipo === 'investimento' || $tipo === 'poupanca' || $tipo === 'consumo' || $tipo === 'imposto') {
            $novoSaldo = $saldoAtual - $valor;
        } else if ($tipo === 'beneficio' || $tipo === 'salario') {
            $novoSaldo = $saldoAtual + $valor;
        }

        if (!$this->transacaoValidator->validateValorInserido($novoSaldo)) {
            return false;
        }

        return $this->databaseService->updateField('familias', 'saldo', $novoSaldo, $id);
    }


    public function atualizarRenda(int $id, float $valor, float $saldoRendaAtual): bool
    {
        if (!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        $novoSaldoRenda = $saldoRendaAtual + $valor;

        if(!$this->transacaoValidator->validateValorInserido($novoSaldoRenda)) {
            return false;
        }

        return $this->databaseService->updateField('familias', 'renda', $novoSaldoRenda, $id);
    }

    public function atualizarConsumo(int $id, float $valor, float $saldoConsumoAtual): bool
    {
        if (!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        $novoSaldoConsumo = $saldoConsumoAtual + $valor;

        if (!$this->transacaoValidator->validateValorInserido($novoSaldoConsumo)) {
            return false;
        }

        return $this->databaseService->updateField('familias', 'consumo', $novoSaldoConsumo, $id);
    }

    public function atualizarInvestimento(int $id, float $valor, float $saldoInvestimentoAtual): bool
    {
        if (!$this->transacaoValidator->validateValorInserido($valor)) {
            return false;
        }

        $novoSaldoInvestimento = $saldoInvestimentoAtual + $valor;

        if (!$this->transacaoValidator->validateValorInserido($novoSaldoInvestimento)) {
            return false;
        }

        return $this->databaseService->updateField('familias', 'investimento', $novoSaldoInvestimento, $id);
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

        return $this->databaseService->updateField('familias', 'beneficio_governo', $novoSaldoBeneficio, $id);
    }
}