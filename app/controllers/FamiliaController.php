<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\FamiliaModel;
use App\Services\AuthService;
use App\Helpers\UrlHelper;
use App\Services\Validator\TransacaoValidator;

class FamiliaController extends Controller
{
    private $familiaModel;
    private $authService;
    private $transacaoValidator;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->transacaoValidator = new TransacaoValidator();
        $this->familiaModel = new FamiliaModel($this->authService, $this->transacaoValidator);
    }
    public function index(): void
    {
        $id = $this->authService->isAuthenticated();

        $saldo = $this->familiaModel->getSaldo();
        $renda = $this->familiaModel->getRenda();
        $consumo = $this->familiaModel->getConsumo();
        $investimento = $this->familiaModel->getInvestimento();
        $beneficio = $this->familiaModel->getBeneficio();
        $historicoTransacoes = $this->familiaModel->getHistoricoTransacoes($id);

        $this->view('familia', [
            'saldo' => $saldo,
            'renda' => $renda,
            'consumo' => $consumo,
            'investimento' => $investimento,
            'beneficio' => $beneficio,
            'historicoTransacoes' => $historicoTransacoes
        ]);
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

            $result = $this->familiaModel->setTransacaoFamiliaEmpresa($id, $valor, $tipo);

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

    public function newInvestimento(): void
    {
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
            $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!$tipo || !$valor || $valor <= 0) {
                echo "Dados inválidos. Verifique e tente novamente.";
                return;
            }

            $result = $this->familiaModel->setInvestimentoFamilia($tipo, $valor);

            if ($result) {
                echo "Investimento realizado com sucesso!";
                header('Location: ' . UrlHelper::base_url('familia'));
            } else {
                echo "Falha no investimento. Verifique os dados e tente novamente.";
                header('Location: ' . UrlHelper::base_url('familia'));
            }
        } else {
            echo "Método não permitido.";
            header('Location: ' . UrlHelper::base_url('familia'));
        }
    }
}