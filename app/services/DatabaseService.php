<?php

namespace App\Services;

use PDO;
use PDOException;

class DatabaseService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateField(string $table, string $field, float $value, int $id): bool
    {
        try {
            $query = $this->pdo->prepare("UPDATE {$table} SET {$field} = :value WHERE id = :id");
            $query->bindParam(":value", $value);
            $query->bindParam(":id", $id, PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function insertField(string $table, string $field, float $value, int $id): bool
    {
        try {
            $query = $this->pdo->prepare("INSERT INTO {$table} (id, {$field}) VALUES (:id, :value)");
            $query->bindParam(":value", $value);
            $query->bindParam(":id", $id, PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}