<?php
namespace App\Controllers;

use Core\Controller;

class DashboardGovernoController extends Controller {
    public function index() {

        $this->view('dashboardGoverno');
    }
}