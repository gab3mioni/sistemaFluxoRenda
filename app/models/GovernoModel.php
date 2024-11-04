<?php

namespace App\Models;

use App\Services\EntityDataFetcher;
use App\Services\Updater\EmpresaUpdater;
use App\Services\Updater\FamiliaUpdater;
use InvalidArgumentException;
use PDO;
use PDOException;
use App\Services\AuthService;
use App\Services\Validator\TransacaoValidator;

class GovernoModel extends BaseModel
{
    private $authService;
    private $transacaoValidator;
    private $entityDataFetcher;
    private $familiaUpdater;
    private $empresaUpdater;


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

    public function setBeneficio(int $id, string $destinatario, float $valor): bool
    {
        try {
            $this->pdo->beginTransaction();

            $tipoTransacao = 'beneficio';
            $colunaDestinatario = ($destinatario === 'familia') ? 'id_familia' : 'id_empresa';

            if (!$this->transacaoValidator->validateValorInserido($valor)) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->executeBeneficio($id, $destinatario, $colunaDestinatario, $valor, $tipoTransacao)) {
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

    public function setImposto(int $id, string $tipo, float $valor): bool
    {
        try {
            $this->pdo->beginTransaction();

            $tipoTransacao = 'imposto';

            if(!$this->transacaoValidator->validateValorInserido($valor)) {
                $this->pdo->rollBack();
                return false;
            }

            if(!$this->executeImposto($id, $valor, $tipo, $tipoTransacao)) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;

            /*if ($this->transacaoValidator->validateValorInserido($valor)) {
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
            } */
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    private function executeImposto(int $id, float $valor, string $tipo, string $tipoTransacao): bool
    {
        try {
            $saldoAtualEmpresa = $this->entityDataFetcher->getSaldo($id, 'empresa');

            $saldoAtualImposto = $this->entityDataFetcher->getImposto($id, 'empresa');

            $query = $this->executeQuery("
            INSERT INTO transacao_governo (id_empresa, valor, tipo_transacao, tipo_imposto)
            VALUES (:id_empresa, :valor, :tipo_transacao, :tipo_imposto)
            ", [
                ":id_empresa" => $id,
                ":valor" => $valor,
                ":tipo_transacao" => $tipoTransacao,
                ":tipo_imposto" => $tipo
            ]);

            if(!$query) {
                return false;
            }

            return $this->empresaUpdater->atualizarSaldoEmpresa($id, $tipoTransacao, $valor, $saldoAtualEmpresa) &&
                $this->empresaUpdater->atualizarImposto($id, $valor, $saldoAtualImposto);
        } catch (PDOException $e) {
            return false;
        }
    }

    private function executeBeneficio(int $id_destinatario, string $destinatario, string $colunaDestinatario,
                                      float $valor, string $tipoTransacao): bool
    {
        try {
            $saldoAtualFamilia = $this->entityDataFetcher->getSaldo($id_destinatario, 'familia');
            $saldoAtualEmpresa = $this->entityDataFetcher->getSaldo($id_destinatario, 'empresa');

            $beneficioAtualFamilia = $this->entityDataFetcher->getBeneficio($id_destinatario, 'familia');
            $beneficioAtualEmpresa = $this->entityDataFetcher->getBeneficio($id_destinatario, 'empresa');

            $query = $this->executeQuery("
             INSERT INTO transacao_governo ($colunaDestinatario, valor, tipo_transacao)
             VALUES (:id_destinatario, :valor, :tipoTransacao)
             ", [
                ":id_destinatario" => $id_destinatario,
                ":valor" => $valor,
                ":tipoTransacao" => $tipoTransacao
            ]);

            if (!$query) {
                return false;
            }

            if ($destinatario === 'familia') {
                $bool = $this->familiaUpdater->atualizarSaldo($id_destinatario, $tipoTransacao, $valor,
                        $saldoAtualFamilia) && $this->familiaUpdater->atualizarBeneficio($id_destinatario, $valor,
                        $beneficioAtualFamilia);
            }

            if ($destinatario === 'empresa') {
                $bool = $this->empresaUpdater->atualizarSaldoEmpresa($id_destinatario, $tipoTransacao, $valor,
                        $saldoAtualEmpresa) && $this->empresaUpdater->atualizarBeneficio($id_destinatario, $valor,
                        $beneficioAtualEmpresa);
            }

            return $bool;
        } catch (PDOException $e) {
            return false;
        }
    }
}