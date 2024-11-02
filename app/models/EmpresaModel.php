<?php

namespace App\Models;

use PDO;
use App\Services\AuthService;
use App\Services\Validator\TransacaoValidator;
use App\Models\FamiliaModel;
use PDOException;

class EmpresaModel
{
    private $pdo;
    private $authService;
    private $transacaoValidator;
    private $familiaModel;

    public function __construct(AuthService $authService, TransacaoValidator $transacaoValidator)
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->authService = $authService;
        $this->transacaoValidator = $transacaoValidator;
        $this->familiaModel = new FamiliaModel($this->authService, $this->transacaoValidator);
    }

    public function getSaldo(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT saldo FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getReceita(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT receita FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getDespesa(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT despesa FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getInvestimento(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT investimento FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getImpostos(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT imposto FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getBeneficios(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT beneficio_governo FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function pagarSalario(int $id_familia, float $valor, string $tipo_transacao): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_empresa = $this->authService->isAuthenticated();
            $saldoAtual = $this->getSaldo();

            if ($this->transacaoValidator->validateSaldo($saldoAtual)) {
                if ($this->transacaoValidator->validateValorInserido($valor)) {
                    $query = $this->pdo->prepare("INSERT INTO transacao_familia_empresa (id_familia, id_empresa, valor, tipo_transacao) VALUES (:id_familia, :id_empresa, :valor, :tipo_transacao)");
                    $query->bindParam(':id_familia', $id_familia);
                    $query->bindParam(':id_empresa', $id_empresa);
                    $query->bindParam(':valor', $valor);
                    $query->bindParam(':tipo_transacao', $tipo_transacao);
                    $result = $query->execute();

                    if ($result && $this->atualizarSaldoEmpresa($id_empresa, $valor) && $this->atualizarDespesasEmpresa($id_empresa, $valor)) {
                        if ($tipo_transacao == 'salario' && $this->atualizarSaldoFamilia($id_familia, $valor)) {
                            $this->pdo->commit();
                            return true;
                        }
                    }
                }
            }
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function atualizarSaldoEmpresa(int $id, float $valor): bool
    {
        try {
            $saldoAtual = $this->getSaldo();

            if ($this->transacaoValidator->validateSaldo($saldoAtual)) {
                $novoSaldo = $saldoAtual - $valor;

                if ($novoSaldo >= 0) {
                    $query = $this->pdo->prepare("UPDATE empresas SET saldo = :novoSaldo WHERE id = :id");
                    $query->bindParam(":novoSaldo", $novoSaldo);
                    $query->bindParam(":id", $id, PDO::PARAM_INT);
                    $query->execute();

                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }

    public function atualizarDespesasEmpresa(int $id, float $valor): bool
    {
        try {
            $despesasAtual = $this->getDespesa();

            $despesaNovo = $despesasAtual + $valor;

            $query = $this->pdo->prepare("UPDATE empresas SET despesa = :despesaNovo WHERE id = :id");
            $query->bindParam(":despesaNovo", $despesaNovo);
            $query->bindParam(":id", $id, PDO::PARAM_INT);
            $query->execute();

            return true;

        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }

    public function atualizarSaldoFamilia(int $id, float $valor): bool
    {
        try {
            $saldoAtual = $this->familiaModel->getSaldo();
            $novoSaldo = $saldoAtual + $valor;

            $query = $this->pdo->prepare("UPDATE familias SET saldo = :novoSaldo WHERE id = :id");
            $query->bindParam(":novoSaldo", $novoSaldo);
            $query->bindParam(":id", $id, PDO::PARAM_INT);
            $query->execute();

            return true;
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }
}