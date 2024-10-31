<?php

namespace Core;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        require_once "../app/views/$view.php";
    }
}