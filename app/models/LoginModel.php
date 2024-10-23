<?php

namespace App\Models;

use PDO;

class LoginModel
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function loginGoverno($usuario, $senha)
    {
        $query = $this->pdo->prepare("SELECT * FROM governo WHERE usuario = :usuario AND senha = :senha");
        $query->bindParam(":usuario", $usuario);
        $query->bindParam(":senha", $senha);
        $query->execute();
        return $query->fetch();
    }

    public function loginEmpresa($usuario, $senha)
    {
        $query = $this->pdo->prepare("SELECT * FROM empresas WHERE usuario = :usuario AND senha = :senha");
        $query->bindParam(":usuario", $usuario);
        $query->bindParam(":senha", $senha);
        $query->execute();
        return $query->fetch();
    }

    public function loginFamilia($usuario, $senha)
    {
        $query = $this->pdo->prepare("SELECT * FROM familias WHERE usuario = :usuario AND senha = :senha");
        $query->bindParam(":usuario", $usuario);
        $query->bindParam(":senha", $senha);
        $query->execute();
        return $query->fetch();
    }
}