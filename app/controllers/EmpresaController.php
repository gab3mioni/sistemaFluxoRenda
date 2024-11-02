<?php
namespace App\Controllers;

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

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->transacaoValidator = new TransacaoValidator();
        $this->empresaModel = new EmpresaModel($this->authService, $this->transacaoValidator);
    }

    public function index(): void
    {
        $id = $this->authService->isAuthenticated();

        $saldo = $this->empresaModel->getSaldo($id);
        $receita = $this->empresaModel->getReceita($id);
        $despesa = $this->empresaModel->getDespesa($id);
        $investimento = $this->empresaModel->getInvestimento($id);
        $impostos = $this->empresaModel->getImpostos($id);
        $beneficios = $this->empresaModel->getBeneficios($id);

        $this->view('empresa', [
            'saldo' => $saldo,
            'receita' => $receita,
            'despesa' => $despesa,
            'investimento' => $investimento,
            'impostos' => $impostos,
            'beneficios' => $beneficios
        ]);
    }

    public function logout(): void
    {
        $this->authService->logout();
    }

    public function newSalario(): void
    {
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            $id_familia = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_FLOAT);
            $tipo = "salario";


            if (!$id_familia || !$valor || $valor <= 0) {
                echo "Dados inválidos. Verifique e tente novamente.";
                return;
            }

            $result = $this->empresaModel->pagarSalario($id_familia, $valor, $tipo);

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
}