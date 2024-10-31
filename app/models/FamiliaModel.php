<?php

namespace App\Models;

use PDO;
use App\Services\AuthService;
use App\Services\Validator\TransacaoValidator;

class FamiliaModel
{
    private $pdo;
    private $authService;

    public function __construct(AuthService $authService)
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->authService = $authService;
    }

    public function getSaldo(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT saldo FROM familias WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getRenda(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT renda FROM familias WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getConsumo(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT consumo FROM familias WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getInvestimento(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT investimento FROM familias WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getBeneficio(): float
    {
        $id = $this->authService->isAuthenticated();
        $query = $this->pdo->prepare("SELECT beneficio_governo FROM familias WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function setTransacao(int $id_empresa, float $valor, string $tipo_transacao): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_familia = $this->authService->isAuthenticated();
            $saldoAtual = $this->getSaldo();
            $transacaoValidator = new TransacaoValidator();

            if ($transacaoValidator->validateSaldo($saldoAtual)) {
                if ($transacaoValidator->validateValorInserido($valor)) {
                    $query = $this->pdo->prepare("INSERT INTO transacao_familia_empresa (id_familia, id_empresa, valor, tipo_transacao) VALUES (:id_familia, :id_empresa, :valor, :tipo_transacao)");
                    $query->bindParam(':id_familia', $id_familia);
                    $query->bindParam(':id_empresa', $id_empresa);
                    $query->bindParam(':valor', $valor);
                    $query->bindParam(':tipo_transacao', $tipo_transacao);
                    $result = $query->execute();

                    $this->pdo->commit();

                    return $result;
                }
            }
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

}