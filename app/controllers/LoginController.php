<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\LoginModel;

class LoginController extends Controller
{
    public function index()
    {
        $this->view('login'); // Carrega a view login.php
    }

    public function base_url($path = '')
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/sistemaFluxoRenda/public/' . ltrim($path, '/');
    }

    public function authenticate()
    {
        $usuario = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_SPECIAL_CHARS);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);

        switch ($tipo) { // Dependendo do tipo selecionado, chama a função de login especifica
            case 'governo':
                $this->loginGoverno($usuario, $senha);
                break;
            case 'empresa':
                $this->loginEmpresa($usuario, $senha);
                break;
            case 'familia':
                $this->loginFamilia($usuario, $senha);
                break;
            default:
                $errorMessage = "Tipo de login inválido";
                include __DIR__ . '/../views/login.php';
        }
    }

    public function loginGoverno($usuario, $senha)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $usuario = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_SPECIAL_CHARS);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!empty($usuario) && !empty($senha)) { // Validação para que não receba vazio
                $loginModel = new LoginModel();
                $user = $loginModel->loginGoverno($usuario, $senha);

                if ($user) {
                    session_start(); // Inicia a sessão do usuário
                    if ( isset($user) && isset($user['tipo'])) {
                        $_SESSION['usuario'] = $user; // Armazena informações para uso posterior
                        $_SESSION['usuario']['tipo'] = $user['tipo']; // Armazena tipo para uso posterior
                        $_SESSION['usuario']['id'] = $user['id']; // Armazena id para uso posterior
                        $_SESSION['usuario']['nome'] = $user['nome']; // Armazena nome para uso posterior
                    } else {
                        $_SESSION['usuario'] = [];
                        $_SESSION['usuario']['tipo'] = 'governo';
                    }

                    header('Location: ' . $this->base_url('dashboardGoverno')); // Redireciona para a view dashboardGoverno.php
                    exit;
                } else {
                    $errorMessage = 'Usuario não encontrado. Tente novamente';
                    include __DIR__ . '/../views/login.php';
                }
            } else {
                $errorMessage = 'Por favor, preencha todos os campos.';
                include __DIR__ . '/../views/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/login.php';
        }
    }

    public function loginEmpresa($usuario, $senha)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $usuario = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_SPECIAL_CHARS);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!empty($usuario) && !empty($senha)) { // Validação para que não receba vazio
                $loginModel = new LoginModel();
                $user = $loginModel->loginEmpresa($usuario, $senha);

                if ($user) {
                    session_start(); // Inicia a sessão do usuário
                    if ( isset($user) && isset($user['tipo'])) {
                        $_SESSION['usuario'] = $user; // Armazena informações para uso posterior
                        $_SESSION['usuario']['tipo'] = $user['tipo']; // Armazena tipo para uso posterior
                        $_SESSION['usuario']['id'] = $user['id']; // Armazena id para uso posterior
                        $_SESSION['usuario']['nome'] = $user['nome']; // Armazena nome para uso posterior
                    } else {
                        $_SESSION['usuario'] = [];
                        $_SESSION['usuario']['tipo'] = 'empresa';
                    }


                    header('Location: ' . $this->base_url('dashboardEmpresa')); // Redireciona para a view dashboardEmpresa.php
                    exit;
                } else {
                    $errorMessage = 'Usuario não encontrado. Tente novamente';
                    include __DIR__ . '/../views/login.php';
                }
            } else {
                $errorMessage = 'Por favor, preencha todos os campos.';
                include __DIR__ . '/../views/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/login.php';
        }
    }

    public function loginFamilia($usuario, $senha)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $usuario = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_SPECIAL_CHARS);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!empty($usuario) && !empty($senha)) { // Validação para que não receba vazio
                $loginModel = new LoginModel();
                $user = $loginModel->loginFamilia($usuario, $senha);

                if ($user) {
                    session_start(); // Inicia a sessão do usuário
                    if ( isset($user) && isset($user['tipo'])) {
                        $_SESSION['usuario'] = $user; // Armazena informações para uso posterior
                        $_SESSION['usuario']['tipo'] = $user['tipo']; // Armazena tipo para uso posterior
                        $_SESSION['usuario']['id'] = $user['id']; // Armazena id para uso posterior
                        $_SESSION['usuario']['nome'] = $user['nome']; // Armazena nome para uso posterior
                    } else {
                        $_SESSION['usuario'] = [];
                        $_SESSION['usuario']['tipo'] = 'familia';
                    }


                    header('Location: ' . $this->base_url('dashboardFamilia')); // Redireciona para a view dashboardFamilia.php
                    exit;
                } else {
                    $errorMessage = 'Usuario não encontrado. Tente novamente';
                    include __DIR__ . '/../views/login.php';
                }
            } else {
                $errorMessage = 'Por favor, preencha todos os campos.';
                include __DIR__ . '/../views/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/login.php';
        }
    }
}