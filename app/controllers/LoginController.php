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

    public function authenticate($usuario, $senha, $tipo)
    {
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
                include __DIR__ . '../views/login.php';
        }
    }

    public function loginGoverno($usuario, $senha)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

            if(!empty($usuario) && !empty($senha)) { // Validação para que não receba vazio
                $loginModel = new LoginModel();
                $user = $loginModel->loginGoverno($usuario, $senha);

                if($user) {
                    if(passsword_verify($senha, $user['senha'])) {
                        session_start(); // Inicia a sessão do usuário
                        $_SESSION['usuario'] = $user; // Armazena informações para uso posterior
                        $_SESSION['usuario']['tipo'] = $user['tipo']; // Armazena tipo para uso posterior
                        $_SESSION['usuario']['id'] = $user['id']; // Armazena id para uso posterior
                        $_SESSION['usuario']['nome'] = $user['nome']; // Armazena nome para uso posterior

                        header('Location: dashboardGoverno'); // Redireciona para a view dashboardGoverno.php
                        exit;
                    } else {
                        $errorMessage = 'Senha incorreta. Tente novamente';
                        require_once __DIR__ . '../views/login.php';
                    }
                } else {
                    $errorMessage = 'Usuario não encontrado. Tente novamente';
                    require_once __DIR__ . '../views/login.php';
                }
            } else {
                $errorMessage = 'Por favor, preencha todos os campos.';
                require_once __DIR__ . '../views/login.php';
            }
        } else {
            require_once __DIR__ . '../views/login.php';
        }
    }

    public function loginEmpresa($usuario, $senha)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

            if(!empty($usuario) && !empty($senha)) { // Validação para que não receba vazio
                $loginModel = new LoginModel();
                $user = $loginModel->loginEmpresa($usuario, $senha);

                if($user) {
                    if(passsword_verify($senha, $user['senha'])) {
                        session_start();
                        $_SESSION['usuario'] = $user; // Armazena informações para uso posterior
                        $_SESSION['usuario']['tipo'] = $user['tipo']; // Armazena tipo para uso posterior
                        $_SESSION['usuario']['id'] = $user['id']; // Armazena id para uso posterior
                        $_SESSION['usuario']['nome'] = $user['nome']; // Armazena nome para uso posterior

                        header('Location: dashboardEmpresa'); // Redireciona para a view dashboardEmpresa.php
                        exit;
                    } else {
                        $errorMessage = 'Senha incorreta. Tente novamente';
                        require_once __DIR__ . '../views/login.php';
                    }
                } else {
                    $errorMessage = 'Usuario não encontrado. Tente novamente';
                    require_once __DIR__ . '../views/login.php';
                }
            } else {
                $errorMessage = 'Por favor, preencha todos os campos.';
                require_once __DIR__ . '../views/login.php';
            }
        } else {
            require_once __DIR__ . '../views/login.php';
        }
    }

    public function loginFamilia($usuario, $senha)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

            if(!empty($usuario) && !empty($senha)) { // Validação para que não receba vazio
                $loginModel = new LoginModel();
                $user = $loginModel->loginFamilia($usuario, $senha);

                if($user) {
                    if(passsword_verify($senha, $user['senha'])) {
                        session_start();
                        $_SESSION['usuario'] = $user; // Armazena informações para uso posterior
                        $_SESSION['usuario']['tipo'] = $user['tipo']; // Armazena tipo para uso posterior
                        $_SESSION['usuario']['id'] = $user['id']; // Armazena id para uso posterior
                        $_SESSION['usuario']['nome'] = $user['nome']; // Armazena nome para uso posterior

                        header('Location: dashboardFamilia'); // Redireciona para a view dashboardFamilia.php
                        exit;
                    } else {
                        $errorMessage = 'Senha incorreta. Tente novamente';
                        require_once __DIR__ . '../views/login.php';
                    }
                } else {
                    $errorMessage = 'Usuario não encontrado. Tente novamente';
                    require_once __DIR__ . '../views/login.php';
                }
            } else {
                $errorMessage = 'Por favor, preencha todos os campos.';
                require_once __DIR__ . '../views/login.php';
            }
        } else {
            require_once __DIR__ . '../views/login.php';
        }
    }
}