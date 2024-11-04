<?php

namespace App\Services;

class EntityDataFetcher
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSaldo(int $id, string $tipo): float
    {
        return $this->fetchSingleValue($id, $tipo, 'saldo');
    }

    public function getInvestimento(int $id, string $tipo): float
    {
        return $this->fetchSingleValue($id, $tipo, 'investimento');
    }

    public function getImposto(int $id, string $tipo): float
    {
        return $this->fetchSingleValue($id, $tipo, 'imposto');
    }

    public function getBeneficio(int $id, string $tipo): float
    {
        $column = 'beneficio_governo';
        return $this->fetchSingleValue($id, $tipo, $column);
    }

    public function getRendaReceita(int $id, string $tipo): float
    {
        $column = ( $tipo === 'familia') ? 'renda' : 'receita';
        return $this->fetchSingleValue($id, $tipo, $column);
    }

    private function fetchSingleValue(int $id, string $tipo, string $column): float
    {
        $table = $this->resolveTableName($tipo);
        $query = $this->pdo->prepare("SELECT {$column} FROM {$table} WHERE id = :id");
        $query->bindParam(':id', $id, \PDO::PARAM_INT);
        $query->execute();

        return (float) $query->fetchColumn();
    }

    private function resolveTableName(string $tipo): string
    {
        switch (strtolower($tipo)) {
            case 'empresa':
                return 'empresas';
            case 'familia':
                return 'familias';
            default:
                throw new \InvalidArgumentException("Tipo inválido: {$tipo}");
        }
    }
}
