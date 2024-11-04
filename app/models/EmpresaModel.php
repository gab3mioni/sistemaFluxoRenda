<?php

namespace App\Models;

use App\Services\EntityDataFetcher;
use PDO;
use App\Helpers\HistoricoHelper;
use App\Services\Updater\EmpresaUpdater;
use App\Services\Updater\FamiliaUpdater;
use App\Services\AuthService;
use App\Services\Transaction\TransacaoValidator;
use PDOException;

class EmpresaModel extends BaseModel
{
    private $authService;
    private $transacaoValidator;
    private $empresaUpdater;
    private $familiaUpdater;
    private $entityDataFetcher;
    private $id;

    public function __construct(PDO $pdo, AuthService $authService, TransacaoValidator $transacaoValidator,
                                EmpresaUpdater $empresaUpdater, FamiliaUpdater $familiaUpdater,
                                EntityDataFetcher  $entityDataFetcher)
    {
        parent::__construct($pdo);
        $this->authService = $authService;
        $this->transacaoValidator = $transacaoValidator;
        $this->empresaUpdater = $empresaUpdater;
        $this->familiaUpdater = $familiaUpdater;
        $this->entityDataFetcher = $entityDataFetcher;

        $this->id = $this->authService->isAuthenticated();
    }

    public function getReceita($id): float
    {
        return $this->fetchSingleValue("SELECT receita FROM empresas WHERE id = :id", ["id" => $id]);
    }

    public function getDespesa($id): float
    {
        return $this->fetchSingleValue("SELECT despesa FROM empresas WHERE id = :id", ["id" => $id]);
    }

    public function getImpostos($id): float
    {
        return $this->fetchSingleValue("SELECT imposto FROM empresas WHERE id = :id", ["id" => $id]);
    }

    public function getHistoricoTransacoes(int $id_empresa): array
    {
        $transacoesFamiliaEmpresa = HistoricoHelper::getTransacoesFamiliaEmpresa($this->pdo, 'empresa', $id_empresa);
        $transacoesSetorFinanceiro = HistoricoHelper::getTransacoesSetorFinanceiro($this->pdo, 'empresa', $id_empresa);
        $transacoesExterno = HistoricoHelper::getTransacoesSetorExterno($this->pdo, 'empresa', $id_empresa);
        $transacoesGoverno = HistoricoHelper::getTransacoesGoverno($this->pdo, 'empresa', $id_empresa);


        return HistoricoHelper::combinarEOrdenarTransacoes($transacoesFamiliaEmpresa, $transacoesSetorFinanceiro,
            $transacoesGoverno, $transacoesExterno);
    }

    public function setSalario(int $id_familia, float $valor, string $tipo_transacao): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_empresa = $this->authService->isAuthenticated();
            $saldoAtual = $this->entityDataFetcher->getSaldo($id_empresa, 'empresa');

            if(!$id_empresa) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->transacaoValidator->validateTransacao($saldoAtual, $valor)) {
                $this->pdo->rollBack();
                return false;
            }

            if(!$this->executeSalario($id_empresa, $id_familia, $tipo_transacao, $valor)) {
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

    public function setInvestimentoEmpresa(string $tipo_transacao, float $valor): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_empresa = $this->authService->isAuthenticated();
            $saldoAtual = $this->entityDataFetcher->getSaldo($id_empresa, 'empresa');

            if (!$id_empresa) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->transacaoValidator->validateTransacao($saldoAtual, $valor)) {
                $this->pdo->rollBack();
                return false;
            }

            if(!$this->executeInvestimento($id_empresa, $tipo_transacao, $valor)) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function setImportacao(float $valor)
    {
        try {
            $this->pdo->beginTransaction();

            $id_empresa = $this->authService->isAuthenticated();
            $saldoAtual = $this->entityDataFetcher->getSaldo($id_empresa, 'empresa');
            $despesasAtual = $this->getDespesa($id_empresa);

            if(!$id_empresa) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->transacaoValidator->validateTransacao($saldoAtual, $valor)) {
                $this->pdo->rollBack();
                return false;
            }

            if(!$this->executeImportacao($id_empresa, $valor, $saldoAtual, $despesasAtual)) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function setExportacao(float $valor)
    {
        try {
            $this->pdo->beginTransaction();

            $id_empresa = $this->authService->isAuthenticated();
            $saldoAtual = $this->entityDataFetcher->getSaldo($id_empresa, 'empresa');
            $receitaAtual = $this->entityDataFetcher->getRendaReceita($id_empresa, 'empresa');

            if(!$id_empresa) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->transacaoValidator->validateValorInserido($valor)) {
                $this->pdo->rollBack();
                return false;
            }

            if(!$this->executeExportacao($id_empresa, $valor, $saldoAtual, $receitaAtual)) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    private function executeSalario(int $id_empresa, int $id_familia, $tipo_transacao, $valor): bool
    {
        try {
            $saldoAtualEmpresa = $this->entityDataFetcher->getSaldo($id_empresa, 'empresa');
            $saldoDespesasEmpresa = $this->getDespesa($id_empresa);
            $saldoAtualFamilia = $this->entityDataFetcher->getSaldo($id_familia, 'familia');
            $saldoAtualRendaFamilia = $this->entityDataFetcher->getRendaReceita($id_familia, 'familia');

            $query = $this->executeQuery("
            INSERT INTO transacao_familia_empresa (id_familia, id_empresa, valor, tipo_transacao)
            VALUES (:id_familia, :id_empresa, :valor, :tipo_transacao)
            ", [
                ':id_familia' => $id_familia,
                ':id_empresa' => $id_empresa,
                ':valor' => $valor,
                ':tipo_transacao' => $tipo_transacao
            ]);

            if(!$query) {
                return false;
            }

            return $this->empresaUpdater->atualizarSaldoEmpresa($id_empresa, $tipo_transacao, $valor, $saldoAtualEmpresa) &&
                $this->empresaUpdater->atualizarDespesasEmpresa($id_familia, $valor, $saldoDespesasEmpresa) &&
                $this->familiaUpdater->atualizarSaldo($id_familia, $tipo_transacao, $valor, $saldoAtualFamilia) &&
                $this->familiaUpdater->atualizarRenda($id_familia, $valor, $saldoAtualRendaFamilia);
        } catch (PDOException $e) {
            return false;
        }
    }

    private function executeInvestimento(int $id_empresa, string $tipo_transacao, float $valor): bool
    {
        try {
            $saldoAtual = $this->entityDataFetcher->getSaldo($id_empresa, 'empresa');
            $investimentoAtual = $this->entityDataFetcher->getInvestimento($id_empresa, 'empresa');
            $destino = ($tipo_transacao === 'poupanca') ? 'governo' : 'setor_financeiro';
            $origem = 'empresa';

            $query = $this->executeQuery("
            INSERT INTO setor_financeiro (id_empresa, tipo_transacao, valor, origem, destino) 
            VALUES (:id_empresa, :tipo_transacao, :valor, :origem, :destino)
            ", [
                ':id_empresa' => $id_empresa,
                ':tipo_transacao' => $tipo_transacao,
                ':valor' => $valor,
                ':origem' => $origem,
                ':destino' => $destino
            ]);

            if(!$query) {
                return false;
            }

            return $this->empresaUpdater->atualizarSaldoEmpresa($id_empresa, $tipo_transacao, $valor, $saldoAtual)
                && $this->empresaUpdater->atualizarInvestimentoEmpresa($id_empresa, $valor, $investimentoAtual);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    private function executeImportacao(int $id_empresa, float $valor, float $saldoAtual, float $despesasAtual): bool
    {
        try {
            $tipo_transacao = 'importacao';

            $query = $this->executeQuery("
            INSERT INTO setor_externo (id_empresa, tipo_transacao, valor) 
            VALUES (:id_empresa, :tipo_transacao, :valor)
            ", [
                ':id_empresa' => $id_empresa,
                ':tipo_transacao' => $tipo_transacao,
                ':valor' => $valor,
            ]);

            if(!$query) {
                return false;
            }

            return $this->empresaUpdater->atualizarSaldoEmpresa($id_empresa, $tipo_transacao, $valor, $saldoAtual) &&
                $this->empresaUpdater->atualizarDespesasEmpresa($id_empresa, $valor, $despesasAtual);

        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    private function executeExportacao(int $id_empresa, float $valor, float $saldoAtual, float $receitaAtual): bool
    {
        try {
            $tipo_transacao = 'exportacao';

            $query = $this->executeQuery("
            INSERT INTO setor_externo (id_empresa, tipo_transacao, valor) 
            VALUES (:id_empresa, :tipo_transacao, :valor)
            ", [
                ':id_empresa' => $id_empresa,
                ':tipo_transacao' => $tipo_transacao,
                ':valor' => $valor,
            ]);

            if(!$query) {
                return false;
            }

            return $this->empresaUpdater->atualizarSaldoEmpresa($id_empresa, $tipo_transacao, $valor, $saldoAtual) &&
                $this->empresaUpdater->atualizarReceitaEmpresa($id_empresa, $valor, $receitaAtual);

        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}