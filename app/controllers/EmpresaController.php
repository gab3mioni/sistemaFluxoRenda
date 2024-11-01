<?php
namespace App\Controllers;

use Core\Controller;

class EmpresaController extends Controller
{
    public function index(): void
    {
        $this->view('empresa');
    }

}