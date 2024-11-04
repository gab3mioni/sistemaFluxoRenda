<?php

namespace App\Helpers;

use PDO;

class HistoricoHelper
{
    public static function getTransacoesFamiliaEmpresa(PDO $pdo, string $tipo, int $id): array
    {
        $column = $tipo === 'familia' ? 'id_familia' : 'id_empresa';
        $query = $pdo->prepare("
            SELECT id, valor, tipo_transacao, data_transacao
            FROM transacao_familia_empresa
            WHERE $column = :id
            ");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTransacoesSetorExterno(PDO $pdo, string $tipo, int $id): array
    {
        $column = $tipo === 'familia' ? 'id_familia' : 'id_empresa';
        $query = $pdo->prepare("
        SELECT id AS id, NULL AS id_{$tipo}, tipo_transacao, descricao, valor, data_transacao
        FROM setor_externo
        WHERE $column = :id
    ");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTransacoesSetorFinanceiro(PDO $pdo, string $tipo, int $id): array
    {
        $column = $tipo === 'familia' ? 'id_familia' : 'id_empresa';
        $query = $pdo->prepare("
                    SELECT id AS id, NULL AS id_{$tipo}, valor, tipo_transacao, data_transacao
            FROM setor_financeiro
            WHERE $column = :id
        ");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTransacoesGoverno(PDO $pdo, string $tipo, int $id): array
    {
        $column = $tipo === 'familia' ? 'id_familia' : 'id_empresa';
        $query = $pdo->prepare("
            SELECT id AS id, NULL AS id_{$tipo}, valor, tipo_transacao, data_transacao
            FROM transacao_governo
            WHERE $column = :id
        ");
        $query->bindParam(":id", $id);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function combinarEOrdenarTransacoes(array $transacoesFamiliaEmpresa,
                                                      array $transacoesSetorFinanceiro, array $transacoesGoverno, array $transacoesExterno): array
    {
        $combinedResults = array_merge($transacoesFamiliaEmpresa, $transacoesSetorFinanceiro,
            $transacoesGoverno, $transacoesExterno);

        usort($combinedResults, function ($a, $b) {
            return strtotime($b['data_transacao']) - strtotime($a['data_transacao']);
        });

        return $combinedResults;
    }


}