<?php

namespace Core;

class Controller
{
    protected function view($view, $data = [])
    {
        require_once "../app/views/$view.php";
    }
}