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

    public function __construct(AuthService $authService, TransacaoValidator $transacaoValidator)
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->authService = $authService;
        $this->transacaoValidator = $transacaoValidator;
        $this->familiaModel = new FamiliaModel($this->authService, $this->transacaoValidator);
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

                if ($result && $destinatario === 'familia') {
                    $atualizado = $this->atualizarBeneficio($id, $valor);
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

    public function atualizarBeneficio(int $id, float $valor): bool
    {
        try {
            $saldoBeneficioAtual = $this->familiaModel->getBeneficio($id);

            if ($this->transacaoValidator->validateSaldo($valor)) {
                $novoSaldoBeneficio = $saldoBeneficioAtual + $valor;

                var_dump($novoSaldoBeneficio);

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

    public function showBeneficios(): array
    {
        try {
            $sql = "
        SELECT 
            t.valor, 
            t.data_transacao, 
            COALESCE(f.nome, e.nome) AS destinatario_nome
        FROM 
            transacao_governo t
        LEFT JOIN 
            familias f ON t.id_familia = f.id
        LEFT JOIN 
            empresas e ON t.id_empresa = e.id
        WHERE 
            t.tipo_transacao = 'beneficio'
        ORDER BY 
            t.data_transacao DESC";

            $query = $this->pdo->prepare($sql);
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
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

                if ($result) { // adicionar inserção de imposto para empresa com atualizarImposto
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
}