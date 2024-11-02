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
    private $id;

    public function __construct(AuthService $authService, TransacaoValidator $transacaoValidator)
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->authService = $authService;
        $this->transacaoValidator = $transacaoValidator;
        $this->familiaModel = new FamiliaModel($this->authService, $this->transacaoValidator);

        $this->id = $this->authService->isAuthenticated();
    }

    public function getSaldo($id): float
    {
        $query = $this->pdo->prepare("SELECT saldo FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getReceita($id): float
    {
        $query = $this->pdo->prepare("SELECT receita FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getDespesa($id): float
    {
        $query = $this->pdo->prepare("SELECT despesa FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getInvestimento($id): float
    {
        $query = $this->pdo->prepare("SELECT investimento FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getImpostos($id): float
    {
        $query = $this->pdo->prepare("SELECT imposto FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    public function getBeneficios($id): float
    {
        $query = $this->pdo->prepare("SELECT beneficio_governo FROM empresas WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchColumn();
    }

    // HISTÓRICO DE TRANSAÇÕES

    private function getTransacoesFamiliaEmpresa(int $id_empresa): array
    {
        $query = $this->pdo->prepare("
        SELECT id, valor, tipo_transacao, data_transacao
        FROM transacao_familia_empresa
        WHERE id_empresa = :id_empresa
    ");
        $query->bindParam(":id_empresa", $id_empresa);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTransacoesSetorFinanceiro(int $id_empresa): array
    {
        $query = $this->pdo->prepare("
        SELECT id AS id, NULL AS id_familia, valor, tipo_transacao, data_transacao
        FROM setor_financeiro
        WHERE id_empresa = :id_empresa
    ");
        $query->bindParam(":id_empresa", $id_empresa);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTransacoesGoverno(int $id_empresa): array
    {
        $query = $this->pdo->prepare("
        SELECT id AS id, NULL as id_familia, valor, tipo_transacao, data_transacao
        FROM transacao_governo
        WHERE id_empresa = :id_empresa
    ");
        $query->bindParam(":id_empresa", $id_empresa);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHistoricoTransacoes(int $id_empresa): array
    {
        $transacoesFamiliaEmpresa = $this->getTransacoesFamiliaEmpresa($id_empresa);
        $transacoesSetorFinanceiro = $this->getTransacoesSetorFinanceiro($id_empresa);
        $transacoesGoverno = $this->getTransacoesGoverno($id_empresa);

        return $this->combinarEOrdenarTransacoes($transacoesFamiliaEmpresa, $transacoesSetorFinanceiro, $transacoesGoverno);
    }

    private function combinarEOrdenarTransacoes(array $transacoesFamiliaEmpresa, array $transacoesSetorFinanceiro, array $transacoesGoverno): array
    {
        $combinedResults = array_merge($transacoesFamiliaEmpresa, $transacoesSetorFinanceiro, $transacoesGoverno);

        usort($combinedResults, function ($a, $b) {
            return strtotime($b['data_transacao']) - strtotime($a['data_transacao']);
        });

        return $combinedResults;
    }

    // METÓDO PARA PAGAR SALÁRIO A FAMILIA

    public function pagarSalario(int $id_familia, float $valor, string $tipo_transacao): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_empresa = $this->authService->isAuthenticated();
            $saldoAtual = $this->getSaldo($id_empresa);

            if ($this->transacaoValidator->validateSaldo($saldoAtual)) {
                if ($this->transacaoValidator->validateValorInserido($valor)) {
                    $query = $this->pdo->prepare("INSERT INTO transacao_familia_empresa (id_familia, id_empresa, valor, tipo_transacao) VALUES (:id_familia, :id_empresa, :valor, :tipo_transacao)");
                    $query->bindParam(':id_familia', $id_familia);
                    $query->bindParam(':id_empresa', $id_empresa);
                    $query->bindParam(':valor', $valor);
                    $query->bindParam(':tipo_transacao', $tipo_transacao);
                    $result = $query->execute();

                    if ($result && $this->atualizarSaldoEmpresa($id_empresa, $valor) && $this->atualizarDespesasEmpresa($id_empresa, $valor)) {
                        if ($tipo_transacao == 'salario' && $this->atualizarSaldoFamilia($id_familia, $valor) && $this->atualizarRendaFamilia($id_familia, $valor)) {
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

    // METODO PARA INVESTIMENTO DA EMPRESA

    public function setInvestimentoEmpresa(string $tipo_transacao, float $valor): bool
    {
        try {
            $this->pdo->beginTransaction();

            $id_empresa = $this->authService->isAuthenticated();
            $origem = 'empresa';
            $saldoAtual = $this->getSaldo($id_empresa);

            if ($this->transacaoValidator->validateSaldo($saldoAtual)) {
                $destino = ($tipo_transacao === 'poupanca') ? 'governo' : 'setor_finaceiro';

                $query = $this->pdo->prepare("INSERT INTO setor_financeiro (id_empresa, tipo_transacao, valor, origem, destino) VALUES (:id_empresa, :tipo_transacao, :valor, :origem, :destino)");
                $query->bindParam(":id_empresa", $id_empresa);
                $query->bindParam(":tipo_transacao", $tipo_transacao);
                $query->bindParam(":valor", $valor);
                $query->bindParam(":origem", $origem);
                $query->bindParam(":destino", $destino);
                $result = $query->execute();

                if ($result && $this->atualizarSaldoEmpresa($id_empresa, $valor) && $this->atualizarInvestimentoEmpresa($id_empresa, $valor)) {
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

    public function atualizarSaldoEmpresa(int $id, float $valor): bool
    {
        try {
            $saldoAtual = $this->getSaldo($id);

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
            $despesasAtual = $this->getDespesa($id);

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

    public function atualizarInvestimentoEmpresa(int $id, float $valor): bool
    {
        try {
            $investimentoAtual = $this->getInvestimento($id);

            $novoInvesimento = $investimentoAtual + $valor;

            $query = $this->pdo->prepare("UPDATE empresas SET investimento = :novoInvestimento WHERE id = :id");
            $query->bindParam(":novoInvestimento", $novoInvesimento);
            $query->bindParam(":id", $id, PDO::PARAM_INT);
            $query->execute();

            return true;
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }

    public function atualizarSaldoFamilia(int $id, float $valor): bool
    {
        try {
            $saldoAtual = $this->familiaModel->getSaldo($id);
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

    public function atualizarRendaFamilia(int $id, float $valor): bool
    {
        try {
            $rendaAtual = $this->familiaModel->getRenda($id);
            $novaRenda = $rendaAtual + $valor;

            $query = $this->pdo->prepare("UPDATE familias SET renda = :novaRenda WHERE id = :id");
            $query->bindParam(":novaRenda", $novaRenda);
            $query->bindParam(":id", $id, PDO::PARAM_INT);
            $query->execute();

            return true;
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
            return false;
        }
    }
}