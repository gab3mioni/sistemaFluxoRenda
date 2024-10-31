<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\FamiliaModel;
use App\Services\AuthService;
use App\Helpers\UrlHelper;

class FamiliaController extends Controller
{
    private $familiaModel;
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->familiaModel = new FamiliaModel($this->authService);
    }
    public function index(): void
    {
        $id = $this->authService->isAuthenticated();

        $saldo = $this->familiaModel->getSaldo();
        $renda = $this->familiaModel->getRenda();
        $consumo = $this->familiaModel->getConsumo();
        $investimento = $this->familiaModel->getInvestimento();
        $beneficio = $this->familiaModel->getBeneficio();

        $this->view('familia', ['saldo' => $saldo, 'renda' => $renda, 'consumo' => $consumo, 'investimento' => $investimento, 'beneficio' => $beneficio]);
    }

    public function newTransacao(): void
    {
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
            $tipo = "consumo";

            if (!$id || !$valor || $valor <= 0) {
                echo "Dados inválidos. Verifique e tente novamente.";
                return;
            }

            $result = $this->familiaModel->setTransacao($id, $valor, $tipo);

            if ($result) {
                echo "Transação realizada com sucesso!";
                header('Location: ' . UrlHelper::base_url('familia'));
            } else {
                echo "Falha na transação. Verifique os dados e tente novamente.";
                header('Location: ' . UrlHelper::base_url('familia'));
            }
        } else {
            echo "Método não permitido.";
            header('Location: ' . UrlHelper::base_url('familia'));
        }
    }
}