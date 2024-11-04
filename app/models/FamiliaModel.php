<?php

namespace App\Models;

use App\Services\EntityDataFetcher;
use App\Services\Updater\EmpresaUpdater;
use App\Services\Updater\FamiliaUpdater;
use PDO;
use App\Helpers\HistoricoHelper;
use App\Services\AuthService;
use App\Services\Validator\TransacaoValidator;
use PDOException;

class FamiliaModel extends BaseModel
{
    private $authService;
    private $transacaoValidator;
    private $empresaUpdater;
    private $familiaUpdater;
    private $entityDataFetcher;

    public function __construct(PDO               $pdo, AuthService $authService, TransacaoValidator $transacaoValidator,
                                EmpresaUpdater    $empresaUpdater, FamiliaUpdater $familiaUpdater,
                                EntityDataFetcher $entityDataFetcher)
    {
        parent::__construct($pdo);
        $this->authService = $authService;
        $this->transacaoValidator = $transacaoValidator;
        $this->empresaUpdater = $empresaUpdater;
        $this->entityDataFetcher = $entityDataFetcher;
        $this->familiaUpdater = $familiaUpdater;
    }

    // Getters

    public function getRenda($id): float
    {
        return $this->fetchSingleValue("SELECT renda FROM familias WHERE id = :id", [':id' => $id]);
    }

    public function getConsumo($id): float
    {
        return $this->fetchSingleValue("SELECT consumo FROM familias WHERE id = :id", [':id' => $id]);
    }

    public function getHistoricoTransacoes(int $id_familia): array
    {
        $transacoesFamiliaEmpresa = HistoricoHelper::getTransacoesFamiliaEmpresa($this->pdo, 'familia', $id_familia);
        $transacoesSetorFinanceiro = HistoricoHelper::getTransacoesSetorFinanceiro($this->pdo, 'familia', $id_familia);
        $transacoesGoverno = HistoricoHelper::getTransacoesGoverno($this->pdo, 'familia', $id_familia);
        $transacoesExterno = HistoricoHelper::getTransacoesSetorExterno($this->pdo, 'familia', $id_familia);

        return HistoricoHelper::combinarEOrdenarTransacoes($transacoesFamiliaEmpresa, $transacoesSetorFinanceiro,
            $transacoesGoverno, $transacoesExterno);
    }

    // Setters

    public function setConsumoFamilia(int $id_empresa, float $valor, string $tipo_transacao): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_familia = $this->authService->isAuthenticated();
            $saldoAtual = $this->entityDataFetcher->getSaldo($id_familia, 'familia');

            if (!$id_familia) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->transacaoValidator->validateTransacao($saldoAtual, $valor)) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->executeConsumo($id_empresa, $id_familia, $tipo_transacao, $valor)) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function setInvestimentoFamilia(string $tipo_transacao, float $valor): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_familia = $this->authService->isAuthenticated();
            $saldoAtual = $this->entityDataFetcher->getSaldo($id_familia, 'familia');
            $origem = 'familia';
            $destino = ($tipo_transacao === 'poupanca') ? 'governo' : 'setor_finaceiro';

            if (!$id_familia) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->transacaoValidator->validateTransacao($saldoAtual, $valor)) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->executeInvestimento($id_familia, $saldoAtual, $tipo_transacao, $valor, $origem, $destino)) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // Funções execute
    private function executeConsumo(int $id_empresa, int $id_familia, string $tipo_transacao, float $valor): bool
    {
        try {
            $saldoAtual = $this->entityDataFetcher->getSaldo($id_familia, 'familia');
            $saldoConsumoAtual = $this->getConsumo($id_familia);

            $saldoAtualReceitaEmpresa = $this->entityDataFetcher->getRendaReceita($id_empresa, 'empresa');
            $saldoAtualEmpresa = $this->entityDataFetcher->getSaldo($id_empresa, 'empresa');

            $query = $this->executeQuery("
            INSERT INTO transacao_familia_empresa (id_familia, id_empresa, valor, tipo_transacao)
            VALUES (:id_familia, :id_empresa, :valor, :tipo_transacao)
            ", [
                ':id_familia' => $id_familia,
                ':id_empresa' => $id_empresa,
                ':valor' => $valor,
                ':tipo_transacao' => $tipo_transacao
            ]);

            if (!$query) {
                return false;
            }

            return $this->familiaUpdater->atualizarSaldo($id_familia, $tipo_transacao, $valor, $saldoAtual) &&
                $this->familiaUpdater->atualizarConsumo($id_familia, $valor, $saldoConsumoAtual) &&
                $this->empresaUpdater->atualizarSaldoEmpresa($id_empresa, $tipo_transacao, $valor, $saldoAtualEmpresa) &&
                $this->empresaUpdater->atualizarReceitaEmpresa($id_empresa, $valor, $saldoAtualReceitaEmpresa);
        } catch (PDOException $e) {
            return false;
        }
    }

    private function executeInvestimento(int $id_familia, float $saldoAtual, string $tipo_transacao, float $valor, string $origem, string $destino): bool
    {
        try {
            $saldoInvestimentoAtual = $this->entityDataFetcher->getInvestimento($id_familia, 'familia');

            $query = $this->executeQuery("
            INSERT INTO setor_financeiro (id_familia, tipo_transacao, valor, origem, destino) 
            VALUES (:id_familia, :tipo_transacao, :valor, :origem, :destino)
            ", [
                ':id_familia' => $id_familia,
                ':tipo_transacao' => $tipo_transacao,
                ':valor' => $valor,
                ':origem' => $origem,
                ':destino' => $destino
            ]);

            if (!$query) {
                return false;
            }

            return $this->familiaUpdater->atualizarSaldo($id_familia, $tipo_transacao, $valor, $saldoAtual) &&
                $this->familiaUpdater->atualizarInvestimento($id_familia, $valor, $saldoInvestimentoAtual);
        } catch (PDOException $e) {
            return false;
        }
    }
}