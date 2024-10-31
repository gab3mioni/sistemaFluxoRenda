<?php
namespace App\Controllers;

use App\Models\GovernoModel;
use App\Services\AuthService;
use Core\Controller;

class GovernoController extends Controller {

    private $governoModel;
    private $authService;


    public function __construct()
    {
        $this->authService = new AuthService();
        $this->governoModel = new GovernoModel($this->authService);
    }
    public function index(): void
    {
        $impostoFamilias = $this->governoModel->getImpostoFamilia();
        $impostoEmpresas = $this->governoModel->getImpostoEmpresas();

        $somaImpostosFamilia = $this->somaImpostosFamilia($impostoFamilias);
        $somaImpostosEmpresa = $this->somaImpostosEmpresas($impostoEmpresas);

        $this->view('governo', ['somaImpostosFamilia' => $somaImpostosFamilia,  'somaImpostosEmpresa' => $somaImpostosEmpresa]);
    }

    public function somaImpostosFamilia(array $impostoFamilias): float
    {
        $soma = 0.0;

        foreach($impostoFamilias as $transacao) {
            $soma += (float)$transacao['valor'];
        }

        return $soma;
    }

    public function somaImpostosEmpresas(array $impostoEmpresas): float
    {
        $soma = 0.0;

        foreach($impostoEmpresas as $transacao) {
            $soma += (float)$transacao['valor'];
        }

        return $soma;
    }
}