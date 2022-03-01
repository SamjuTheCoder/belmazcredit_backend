<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;

/**
 * @Author: Julius Fasema
 * Controller: WalletController
 * Description: Defines all the functions for wallet transactions
 * Date: 30-01-2022
 */

class WalletController extends FunctionsController
{
    
    public function Wallet() {
        
        $data = $this->home(Auth::user()->referral_id); // call user wallet function

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Wallet successfully loaded!',
            'data' => $data,
        ]);
    }

    public function home($userid) {

        // call the summation function
         $sum = $this->sumWallet($userid);
         $withdrawal = $this->withdrawalWallet($userid);

         return [
            "wallet_sum" => $sum,
            "withdrawal" => $withdrawal,
            ];

    }

     // get user wallet details
    public function listWallets() {

        $lists = Wallet::where('user_id', Auth::user()->referral_id)->get();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Successfully listed',
            'data' => $lists,
        ], 201);
    }
}
