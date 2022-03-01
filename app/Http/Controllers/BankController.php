<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
 * @Author: Julius Fasema
 * Controller: BankController
 * Description: Defines all the functions for creating user bank
 * Date: 10-02-2022
 */
class BankController extends FunctionsController
{
    // generate transaction code
    public function addBank(Request $request) {

        $code = $this->createBank(Auth::user()->id, $request->get('name'), $request->get('bank_name'), $request->get('account_name') );

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Bank created!',
            'bank_detail' => $code,
        ]);
    }

    public function listBankInfo() {

        $list = $this->listBank(Auth::user()->id);

        return response()->json([
            'bank_detail' => $list,
        ]);
    }
}
