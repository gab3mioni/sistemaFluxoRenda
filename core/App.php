<?php

namespace Core;

class App
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function run()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $this->router->dispatch($url);
    }
}