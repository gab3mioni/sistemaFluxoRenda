<?php
namespace App\Controllers;

use Core\Controller;

class GovernoController extends Controller {
    public function index(): void
    {
        $this->view('governo');
    }
}