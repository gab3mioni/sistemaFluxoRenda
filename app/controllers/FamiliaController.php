<?php

namespace App\Controllers;

use App\Services\DatabaseService;
use App\Services\EntityDataFetcher;
use App\Services\Updater\EmpresaUpdater;
use App\Services\Updater\FamiliaUpdater;
use PDO;
use Core\Controller;
use App\Models\FamiliaModel;
use App\Services\AuthService;
use App\Helpers\UrlHelper;
use App\Services\Transaction\TransacaoValidator;

class FamiliaController extends Controller
{
    private $familiaModel;
    private $authService;
    private $transacaoValidator;
    private $databaseService;
    private $empresaUpdater;
    private $familiaUpdater;
    private $entityDataFetcher;
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->authService = new AuthService();
        $this->transacaoValidator = new TransacaoValidator();
        $this->databaseService = new DatabaseService($this->pdo);
        $this->entityDataFetcher = new EntityDataFetcher($this->pdo);
        $this->empresaUpdater = new EmpresaUpdater($this->databaseService, $this->transacaoValidator);
        $this->familiaUpdater = new FamiliaUpdater($this->databaseService, $this->transacaoValidator);
        $this->familiaModel = new FamiliaModel($this->pdo, $this->authService, $this->transacaoValidator,
            $this->empresaUpdater, $this->familiaUpdater, $this->entityDataFetcher);
    }

    public function index(): void
    {
        $id = $this->authService->isAuthenticated();
        $tipo = 'familia';

        $saldo = $this->entityDataFetcher->getSaldo($id, $tipo);
        $renda = $this->familiaModel->getRenda($id);
        $consumo = $this->familiaModel->getConsumo($id);
        $investimento = $this->entityDataFetcher->getInvestimento($id, $tipo);
        $beneficio = $this->entityDataFetcher->getBeneficio($id, $tipo);
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

    public function logout(): void
    {
        $this->authService->logout();
    }

    public function newTransacao(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
            $tipo = "consumo";

            if (!$id || !$valor || $valor <= 0) {
                echo "Dados inválidos. Verifique e tente novamente.";
                return;
            }

            $result = $this->familiaModel->setConsumoFamilia($id, $valor, $tipo);

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
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
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