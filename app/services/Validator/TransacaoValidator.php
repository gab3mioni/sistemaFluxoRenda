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

    public function validateTransacao(float $saldoAtual, float $valor): bool
    {
        if(!$this->validateValorInserido($valor)){
            return false;
        }

        if( $valor > $saldoAtual ) {
            return false;
        }

        return true;
    }
}