<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Services\AuthService;

class GovernoModel
{
    private $pdo;
    private $authService;

    public function __construct(AuthService $authService)
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->authService = $authService;
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
        $tipoTransacao = 'beneficio';

        $sql = "INSERT INTO transacao_governo (id_familia, valor, tipo_transacao) VALUES (:id_familia, :valor, :tipo_transacao)";
        $query = $this->pdo->prepare($sql);
        $query->bindParam(":id_familia", $id, PDO::PARAM_INT);
        $query->bindParam(":valor", $valor, PDO::PARAM_STR);
        $query->bindParam(":tipo_transacao", $tipoTransacao, PDO::PARAM_STR);

        return $query->execute();
    }
}