<?php

namespace App\Services\Validator;

class TransacaoValidator
{
    public function validateSaldo(float $saldoAtual): bool
    {
        if ( $saldoAtual > 0 ) {
            return true;
        }
        return false;
    }

    public function validateValorInserido(float $valorInserido): bool
    {
        if ( $valorInserido > 0 ) {
            return true;
        }
        return false;
    }
}