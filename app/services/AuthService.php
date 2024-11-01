<?php

namespace App\Services;

class AuthService
{
    public function __construct()
    {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['usuario']['id']);
    }

    public function login($user): void
    {
        if (!empty($user) && isset($user['id'])) {
            $_SESSION['usuario'] = [
                'id' => $user['id']
            ];
        }
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: /sistemaFluxoRenda/public/');
    }
}