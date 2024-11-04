<?php

namespace App\Controllers;

use App\Services\EntityDataFetcher;
use App\Services\Updater\FamiliaUpdater;
use PDO;
use App\Services\DatabaseService;
use App\Services\Updater\EmpresaUpdater;
use App\Models\EmpresaModel;
use App\Services\AuthService;
use App\Services\Validator\TransacaoValidator;
use App\Helpers\UrlHelper;
use Core\Controller;

class EmpresaController extends Controller
{
    private $empresaModel;
    private $authService;
    private $transacaoValidator;
    private $empresaUpdater;
    private $databaseService;
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

        $this->empresaModel = new EmpresaModel($this->pdo, $this->authService, $this->transacaoValidator,
            $this->empresaUpdater, $this->familiaUpdater, $this->entityDataFetcher);
    }

    public function index(): void
    {
        $id = $this->authService->isAuthenticated();
        $tipo = 'empresa';

        $saldo = $this->entityDataFetcher->getSaldo($id, $tipo);
        $receita = $this->empresaModel->getReceita($id);
        $despesa = $this->empresaModel->getDespesa($id);
        $investimento = $this->entityDataFetcher->getInvestimento($id, $tipo);
        $impostos = $this->empresaModel->getImpostos($id);
        $beneficios = $this->entityDataFetcher->getBeneficio($id, $tipo);
        $historicoTransacoes = $this->empresaModel->getHistoricoTransacoes($id);

        $this->view('empresa', [
            'saldo' => $saldo,
            'receita' => $receita,
            'despesa' => $despesa,
            'investimento' => $investimento,
            'impostos' => $impostos,
            'beneficios' => $beneficios,
            'historicoTransacoes' => $historicoTransacoes
        ]);
    }

    public function logout(): void
    {
        $this->authService->logout();
    }

    public function newSalario(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $id_familia = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
            $tipo = "salario";


            if (!$id_familia || !$valor || $valor <= 0) {
                echo "Dados inválidos. Verifique e tente novamente.";
                return;
            }

            $result = $this->empresaModel->setSalario($id_familia, $valor, $tipo);

            if ($result) {
                echo "Transação realizada com sucesso!";
                header('Location: ' . UrlHelper::base_url('empresa'));
            } else {
                echo "Falha na transação. Verifique os dados e tente novamente.";
                header('Location: ' . UrlHelper::base_url('empresa'));
            }
        } else {
            echo "Método não permitido.";
            header('Location: ' . UrlHelper::base_url('empresa'));
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

            $result = $this->empresaModel->setInvestimentoEmpresa($tipo, $valor);

            if ($result) {
                echo "Investimento realizado com sucesso!";
                header('Location: ' . UrlHelper::base_url('empresa'));
            } else {
                echo "Falha no investimento. Verifique os dados e tente novamente.";
                header('Location: ' . UrlHelper::base_url('empresa'));
            }
        } else {
            echo "Método não permitido.";
            header('Location: ' . UrlHelper::base_url('empresa'));
        }
    }

    public function newExterno(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
            $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS);


            if (!$tipo || !$valor || $valor <= 0) {
                echo "Dados inválidos. Verifique e tente novamente.";
                return;
            }

            if($tipo === 'importacao') {
                $result = $this->empresaModel->setImportacao($valor);
            } else if ($tipo === 'exportacao') {
                $result = $this->empresaModel->setExportacao($valor);
            }

            if ($result) {
                echo "Investimento realizado com sucesso!";
                header('Location: ' . UrlHelper::base_url('empresa'));
            } else {
                echo "Falha no investimento. Verifique os dados e tente novamente.";
                header('Location: ' . UrlHelper::base_url('empresa'));
            }
        } else {
            echo "Método não permitido.";
            header('Location: ' . UrlHelper::base_url('empresa'));
        }
    }
}