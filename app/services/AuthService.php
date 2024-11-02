<?php

namespace App\Services;

use App\Helpers\UrlHelper;

class AuthService
{
    public function __construct()
    {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isAuthenticated(): ?int
    {
        return $_SESSION['usuario']['id'] ?? null;
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
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header('Location: ' . UrlHelper::base_url('login'));
        exit;
    }

}