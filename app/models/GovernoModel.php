<?php

namespace App\Models;

use InvalidArgumentException;
use PDO;
use PDOException;
use App\Services\AuthService;
use App\Services\Validator\TransacaoValidator;
use App\Models\FamiliaModel;

class GovernoModel
{
    private $pdo;
    private $authService;
    private $transacaoValidator;
    private $familiaModel;
    private $empresaModel;

    public function __construct(AuthService $authService, TransacaoValidator $transacaoValidator)
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->authService = $authService;
        $this->transacaoValidator = $transacaoValidator;
        $this->familiaModel = new FamiliaModel($this->authService, $this->transacaoValidator);
        $this->empresaModel = new EmpresaModel($this->authService, $this->transacaoValidator);
    }

    public function getImpostoFamilia(): array
    {
        $tipo_transacao = 'imposto';

        $query = $this->pdo->prepare("
        SELECT id, id_familia, id_empresa, valor, tipo_transacao, data_transacao
        FROM transacao_governo
        WHERE tipo_transacao = :tipo_transacao
        AND id_familia IS NOT NULL
        AND id_empresa IS NULL
        ORDER BY data_transacao DESC
    ");

        $query->bindParam(":tipo_transacao", $tipo_transacao, PDO::PARAM_STR);

        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getImpostoEmpresas(): array
    {
        $tipo_transacao = 'imposto';

        $query = $this->pdo->prepare("
        SELECT id, id_familia, id_empresa, valor, tipo_transacao, data_transacao
        FROM transacao_governo
        WHERE tipo_transacao = :tipo_transacao
        AND id_familia IS NULL
        AND id_empresa IS NOT NULL
        ORDER BY data_transacao DESC
    ");

        $query->bindParam(":tipo_transacao", $tipo_transacao, PDO::PARAM_STR);

        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showImpostos($tipo): array
    {
        $query = $this->pdo->prepare("
        SELECT 
            COALESCE(f.nome, e.nome) AS destinatario_nome,
            tg.valor,
            tg.tipo_imposto,
            tg.data_transacao,
            CASE
                WHEN tg.id_familia IS NOT NULL THEN 'familia'
                WHEN tg.id_empresa IS NOT NULL THEN 'empresa'
            END AS tipo_destinatario
        FROM transacao_governo tg
        LEFT JOIN familias f ON tg.id_familia = f.id
        LEFT JOIN empresas e ON tg.id_empresa = e.id
        WHERE tg.tipo_transacao = 'imposto'
    ");

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setBeneficio(int $id, string $destinatario, float $valor): bool
    {
        try {
            $this->pdo->beginTransaction();

            $tipoTransacao = 'beneficio';
            $colunaDestinatario = ($destinatario === 'familia') ? 'id_familia' : 'id_empresa';

            if ($this->transacaoValidator->validateValorInserido($valor)) {
                $sql = "INSERT INTO transacao_governo ($colunaDestinatario, valor, tipo_transacao) VALUES (:id_destinatario, :valor, :tipo_transacao)";
                $query = $this->pdo->prepare($sql);
                $query->bindParam(":id_destinatario", $id, PDO::PARAM_INT);
                $query->bindParam(":valor", $valor, PDO::PARAM_STR);
                $query->bindParam(":tipo_transacao", $tipoTransacao, PDO::PARAM_STR);

                $result = $query->execute();

                if ($result) {

                    $atualizado = false;

                    if ($destinatario === 'familia') {
                        $atualizado = $this->atualizarBeneficioFamilia($id, $valor) && $this->atualizarSaldoFamilia($id, $valor);
                    }

                    if ($destinatario === 'empresa') {
                        if ($this->atualizarBeneficioEmpresa($id, $valor) && $this->atualizarSaldoEmpresa($id, $tipoTransacao, $valor)) {
                            $atualizado = true;
                        }
                    }

                    if (!$atualizado) {
                        $this->pdo->rollBack();
                        return false;
                    }

                    $this->pdo->commit();
                    return true;
                }
            }
            $this->pdo->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function atualizarBeneficioFamilia(int $id, float $valor): bool
    {
        try {
            $saldoBeneficioAtual = $this->familiaModel->getBeneficio($id);

            if ($this->transacaoValidator->validateSaldo($valor)) {
                $novoSaldoBeneficio = $saldoBeneficioAtual + $valor;

                $query = $this->pdo->prepare("UPDATE familias SET beneficio_governo = :novoBeneficio WHERE id = :id");
                $query->bindParam(":novoBeneficio", $novoSaldoBeneficio, PDO::PARAM_STR);
                $query->bindParam(":id", $id, PDO::PARAM_INT);

                return $query->execute();
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function atualizarBeneficioEmpresa(int $id, float $valor): bool
    {
        try {
            $saldoBeneficioAtual = $this->empresaModel->getBeneficios($id);

            if ($this->transacaoValidator->validateSaldo($valor)) {
                $novoSaldoBeneficio = $saldoBeneficioAtual + $valor;

                $query = $this->pdo->prepare("UPDATE empresas SET beneficio_governo = :novoBeneficio WHERE id = :id");
                $query->bindParam(":novoBeneficio", $novoSaldoBeneficio, PDO::PARAM_STR);
                $query->bindParam(":id", $id, PDO::PARAM_INT);

                return $query->execute();
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function showBeneficios($tipo): array
    {
        $query = $this->pdo->prepare("
        SELECT 
            COALESCE(f.nome, e.nome) AS destinatario_nome,
            tg.valor,
            tg.data_transacao,
            CASE
                WHEN tg.id_familia IS NOT NULL THEN 'familia'
                WHEN tg.id_empresa IS NOT NULL THEN 'empresa'
            END AS tipo_destinatario
        FROM transacao_governo tg
        LEFT JOIN familias f ON tg.id_familia = f.id
        LEFT JOIN empresas e ON tg.id_empresa = e.id
        WHERE tg.tipo_transacao = 'beneficio'
    ");

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setImposto(int $id, string $tipo, float $valor): bool
    {
        try {
            $this->pdo->beginTransaction();

            $tipoTransacao = 'imposto';

            if ($tipoTransacao != 'imposto' || empty($tipo)) {
                $this->pdo->rollBack();
                throw new InvalidArgumentException("Tipo de imposto é obrigatório para transações do tipo 'imposto'.");
            }

            if ($this->transacaoValidator->validateValorInserido($valor)) {
                $sql = "INSERT INTO transacao_governo (id_empresa, valor, tipo_transacao, tipo_imposto) 
                    VALUES (:id_empresa, :valor, :tipo_transacao, :tipo_imposto)";
                $query = $this->pdo->prepare($sql);
                $query->bindParam(":id_empresa", $id, PDO::PARAM_INT);
                $query->bindParam(":valor", $valor, PDO::PARAM_STR);
                $query->bindParam(":tipo_transacao", $tipoTransacao, PDO::PARAM_STR);
                $query->bindParam(":tipo_imposto", $tipo, PDO::PARAM_STR);
                $result = $query->execute();

                if ($result && $this->atualizarImpostosEmpresa($id, $valor) && $this->atualizarSaldoEmpresa($id, $tipoTransacao, $valor)) {
                    $this->pdo->commit();
                    return true;
                }
            }
            $this->pdo->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    public function atualizarImpostosEmpresa(int $id, float $valor): bool
    {
        try {
            $impostoAtual = $this->empresaModel->getImpostos($id);

            if ($this->transacaoValidator->validateSaldo($valor)) {
                $novoImposto = $impostoAtual + $valor;

                $query = $this->pdo->prepare("UPDATE empresas SET imposto = :novoImposto WHERE id = :id");
                $query->bindParam(":novoImposto", $novoImposto, PDO::PARAM_STR);
                $query->bindParam(":id", $id, PDO::PARAM_INT);

                return $query->execute();
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function atualizarSaldoEmpresa(int $id, string $tipo, float $valor): bool
    {
        try {
            $empresaModel = new EmpresaModel($this->authService, $this->transacaoValidator);
            $saldoAtual = $empresaModel->getSaldo($id);

            if ($tipo === 'imposto') {
                $novoSaldo = $saldoAtual - $valor;
            } else if ($tipo === 'beneficio') {
                $novoSaldo = $saldoAtual + $valor;
            }

            $query = $this->pdo->prepare("UPDATE empresas SET saldo = :novoSaldo WHERE id = :id");
            $query->bindParam(":novoSaldo", $novoSaldo);
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

            if ($this->transacaoValidator->validateSaldo($saldoAtual)) {

                $novoSaldo = $saldoAtual + $valor;

                if ($novoSaldo >= 0) {
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
}