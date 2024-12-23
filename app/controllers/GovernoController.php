<?php

namespace App\Controllers;

use App\Helpers\UrlHelper;
use App\Models\GovernoModel;
use App\Services\AuthService;
use App\Services\DatabaseService;
use App\Services\EntityDataFetcher;
use App\Services\Updater\EmpresaUpdater;
use App\Services\Updater\FamiliaUpdater;
use App\Services\Transaction\TransacaoValidator;
use Core\Controller;

class GovernoController extends Controller
{

    private $pdo;
    private $governoModel;
    private $authService;
    private $transacaoValidator;
    private $databaseService;
    private $entityDataFetcher;
    private $empresaUpdater;
    private $familiaUpdater;


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
        $this->governoModel = new GovernoModel($this->pdo, $this->authService, $this->transacaoValidator,
            $this->empresaUpdater, $this->familiaUpdater, $this->entityDataFetcher);
    }

    public function index(): void
    {
        $impostoFamilias = $this->governoModel->getImpostoFamilia();
        $impostoEmpresas = $this->governoModel->getImpostoEmpresas();

        $beneficiosFamilia = $this->governoModel->showBeneficios('familia');
        $beneficiosEmpresa = $this->governoModel->showBeneficios('empresa');

        $impostoFamilia = $this->governoModel->showImpostos('familia');
        $impostoEmpresa = $this->governoModel->showImpostos('empresa');

        $somaImpostosFamilia = $this->somaImpostosFamilia($impostoFamilias);
        $somaImpostosEmpresa = $this->somaImpostosEmpresas($impostoEmpresas);


        $this->view('governo', [
            'somaImpostosFamilia' => $somaImpostosFamilia,
            'somaImpostosEmpresa' => $somaImpostosEmpresa,
            'beneficiosFamilia' => $beneficiosFamilia,
            'beneficiosEmpresa' => $beneficiosEmpresa,
            'impostosFamilia' => $impostoFamilia,
            'impostosEmpresas' => $impostoEmpresa,
        ]);
    }

    public function logout(): void
    {
        $this->authService->logout();
    }

    public function somaImpostosFamilia(array $impostoFamilias): float
    {
        $soma = 0.0;

        foreach ($impostoFamilias as $transacao) {
            $soma += (float)$transacao['valor'];
        }

        return $soma;
    }

    public function somaImpostosEmpresas(array $impostoEmpresas): float
    {
        $soma = 0.0;

        foreach ($impostoEmpresas as $transacao) {
            $soma += (float)$transacao['valor'];
        }

        return $soma;
    }

    public function newBeneficio(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $destinatario = htmlspecialchars(trim($_POST['destinatario'] ?? ''), ENT_QUOTES, 'UTF-8');
            $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);

            if (!$id || !$destinatario || !$valor) {
                echo "Dados inválidos. Verifique e tente novamente";
                return;
            }

            $result = $this->governoModel->setBeneficio($id, $destinatario, $valor);

            if ($result) {
                echo "Transação realizada com sucesso!";
                header('Location: ' . UrlHelper::base_url('governo'));
            } else {
                echo "Falha na transação. Verifique os dados e tente novamente";
                header('Location: ' . UrlHelper::base_url('governo'));
            }
        } else {
            echo "Método não permitido.";
            header('Location: ' . UrlHelper::base_url('governo'));
        }
    }

    public function newImposto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $tipo = htmlspecialchars(trim($_POST['tipo'] ?? ''), ENT_QUOTES, 'UTF-8');
            $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);

            if (!$id || !$tipo || !$valor) {
                echo "Dados inválidos. Verifique e tente novamente";
                return;
            }

            $result = $this->governoModel->setImposto($id, $tipo, $valor);

            if ($result) {
                echo "Transação realizada com sucesso!";
                header('Location: ' . UrlHelper::base_url('governo'));
            } else {
                echo "Falha na transação. Verifique os dados e tente novamente";
                header('Location: ' . UrlHelper::base_url('governo'));
            }
        } else {
            echo "Método não permitido.";
            header('Location: ' . UrlHelper::base_url('governo'));
        }
    }
}