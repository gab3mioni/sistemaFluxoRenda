<?php

namespace App\Helpers;

class UrlHelper
{
    public static function base_url($path = ''): string
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/sistemaFluxoRenda/public/' . ltrim($path, '/');
    }
}