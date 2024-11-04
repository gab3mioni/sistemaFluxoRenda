<?php

namespace App\Services\Validator;

class TransacaoValidator
{
    public function validateValorInserido(float $valor): bool
    {
        if ( $valor > 0 ) {
            return true;
        }
        return false;
    }

    public function validateTransacao(float $saldoAtual, float $valorInserido): bool
    {
        if(!$this->validateValorInserido($valorInserido)){
            return false;
        }

        if( $valorInserido > $saldoAtual ) {
            return false;
        }

        return true;
    }
}