<?php

namespace App\Models;

use PDO;
use App\Services\AuthService;
use App\Services\Validator\TransacaoValidator;
use PDOException;

class FamiliaModel
{
    private $pdo;
    private $authService;
    private $transacaoValidator;

    public function __construct(AuthService $authService, TransacaoValidator $transacaoValidator)
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->authService = $authService;
        $this->transacaoValidator = $transacaoValidator;
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

    public function getHistoricoTransacoes(int $id_familia): array
    {
        $query = $this->pdo->prepare("SELECT id, id_empresa, valor, tipo_transacao, data_transacao
        FROM transacao_familia_empresa
        WHERE id_familia = :id_familia
        ORDER BY data_transacao DESC
    ");
        $query->bindParam(":id_familia", $id_familia);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setTransacaoFamiliaEmpresa(int $id_empresa, float $valor, string $tipo_transacao): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_familia = $this->authService->isAuthenticated();
            $saldoAtual = $this->getSaldo();

            if ($this->transacaoValidator->validateSaldo($saldoAtual)) {
                if ($this->transacaoValidator->validateValorInserido($valor)) {
                    $query = $this->pdo->prepare("INSERT INTO transacao_familia_empresa (id_familia, id_empresa, valor, tipo_transacao) VALUES (:id_familia, :id_empresa, :valor, :tipo_transacao)");
                    $query->bindParam(':id_familia', $id_familia);
                    $query->bindParam(':id_empresa', $id_empresa);
                    $query->bindParam(':valor', $valor);
                    $query->bindParam(':tipo_transacao', $tipo_transacao);
                    $result = $query->execute();

                    if($result && $this->atualizarSaldo($id_familia, $valor)) {
                        if($tipo_transacao == 'consumo' && $this->atualizarConsumo($id_familia, $valor)) {
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

    public function atualizarSaldo(int $id, float $valor): bool
    {
        try {
            $saldoAtual = $this->getSaldo();

            if($this->transacaoValidator->validateSaldo($saldoAtual)) {
                $novoSaldo = $saldoAtual - $valor;

                if($novoSaldo >= 0) {
                    $query = $this->pdo->prepare("UPDATE familias SET saldo = :novoSaldo WHERE id = :id");
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
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }

    public function atualizarConsumo(int $id, float $valor): bool
    {
        try {
            $consumoAtual = $this->getConsumo();

            $novoConsumo = $consumoAtual + $valor;

            $query = $this->pdo->prepare("UPDATE familias SET consumo = :novoConsumo WHERE id = :id");
            $query->bindParam(":novoConsumo", $novoConsumo);
            $query->bindParam(":id", $id, PDO::PARAM_INT);
            $query->execute();

            return true;
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }

    public function setInvestimentoFamilia(string $tipo_transacao, float $valor): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_familia = $this->authService->isAuthenticated();
            $origem = 'familia';
            $saldoAtual = $this->getSaldo();

            if($this->transacaoValidator->validateSaldo($saldoAtual)) {
                $destino = ($tipo_transacao === 'poupanca') ? 'governo' : 'setor_finaceiro';

                $query = $this->pdo->prepare("INSERT INTO setor_financeiro (id_familia, tipo_transacao, valor, origem, destino) VALUES (:id_familia, :tipo_transacao, :valor, :origem, :destino)");
                $query->bindParam(":id_familia", $id_familia);
                $query->bindParam(":tipo_transacao", $tipo_transacao);
                $query->bindParam(":valor", $valor);
                $query->bindParam(":origem", $origem);
                $query->bindParam(":destino", $destino);
                $result = $query->execute();

                if($result && $this->atualizarSaldo($id_familia, $valor) && $this->atualizarInvestimento($id_familia, $valor)) {
                    $this->pdo->commit();
                    return true;
                }
            }
            $this->pdo->rollBack();
            return false;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function atualizarInvestimento(int $id, float $valor): bool
    {
        try {
            $investimentoAtual = $this->getInvestimento();

            $novoInvesimento = $investimentoAtual + $valor;

            $query = $this->pdo->prepare("UPDATE familias SET investimento = :novoInvestimento WHERE id = :id");
            $query->bindParam(":novoInvestimento", $novoInvesimento);
            $query->bindParam(":id", $id, PDO::PARAM_INT);
            $query->execute();

            return true;
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }
}