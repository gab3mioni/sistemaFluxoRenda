<?php

namespace App\Controllers;

use Core\Controller;

class FamiliaController extends Controller {
    public function index(): void
    {
        $this->view('familia');
    }
}