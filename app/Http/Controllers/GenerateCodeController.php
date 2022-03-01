<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenerateCodeController extends FunctionsController
{
    // generate transaction code
    public function generateTranCode() {

        $code = $this->generateTransactionCode();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Transaction code generated',
            'transaction_code' => $code,
        ]);
    }

    public function listTranCode() {

        $list = $this->listTransactionCode();

        return response()->json([
            'transaction_code' => $list,
        ]);
    }
}
