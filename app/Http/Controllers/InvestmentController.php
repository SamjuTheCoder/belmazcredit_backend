<?php

namespace App\Http\Controllers;

use App\Models\InvestmentSetup;
use App\Models\Investment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * @Author: Julius Fasema
 * Controller: InvestmentController
 * Description: Defines all the functions for setting up investment and others
 * Date: 30-01-2022
 */

class InvestmentController extends Controller
{
    // setup investment
    public function setupInvestment(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'min_amount' => 'required|numeric',
            'max_amount' => 'required|numeric',
            'per' => 'required|numeric|max:100',
        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }

        $user = InvestmentSetup::create([
            'min_amount' => $request->get('min_amount'),
            'max_amount' => $request->get('max_amount'),
            'per' => $request->get('per'),
        ]);

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Investment successfully added',        
        ],201);
    }

    // list investment
    public function listInvestments()
    {
        $investment = InvestmentSetup::all();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Investment successfully listed',
            'data' => $investment,
        ],201);
    }

    // my investment
    public function listMyInvestments()
    {
        $myinvestment = Investment::leftjoin('phases','investments.phases_id','=','phases.id')
                                    ->leftjoin('investment_setups','investments.investment_id','=','investment_setups.id')
                                    ->where('user_id',Auth::user()->id)
                                    ->get();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Investment successfully listed',
            'data' => $myinvestment,
        ],201);
    }

    // make investment amount
    public function chooseInvestments(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'investment_type' => 'required|numeric',
        ]);

        if($validator->fails()){

                return response()->json([
                    
                    'success' => 'error',
                    'code' => 'E002',
                    'message' => $validator->errors()->toJson()
                ]);
        }


        User::where('id',Auth::user()->id)->update([
               
                'investment_id' => $request->get('investment_type'),
            ]);
    
            return response()->json([
                'success' => 'success',
                'code'    => '00',
                'message' => 'Investment type selected successfully!',
            
            ],201);

    }

    // make investment amount
    public function makeInvestments(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'investment_amount' => 'required|numeric',
        ]);

        if($validator->fails()){

                return response()->json([
                    
                    'success' => 'Error',
                    'code' => 'E005',
                    'message' => $validator->errors()->toJson()
                ]);
        }

        if( InvestmentSetup::where('id',Auth::user()->investment_id)->exists() ) {

            $data = InvestmentSetup::where('id',Auth::user()->investment_id)->first();

              if(( $request->get('investment_amount') >= $data->min_amount ) && ($request->get('investment_amount') <= $data->max_amount )) {

                Investment::create([
                    'user_id' => Auth::user()->id,
                    'phases_id' => Auth::user()->phases_id,
                    'investment_id' => Auth::user()->phases_id,
                    'investment_amount' => $request->get('investment_amount'),
                ]);
        
                return response()->json([
                    'success' => 'success',
                    'code'    => '00',
                    'message' => 'Amount invested successfully!',
                
                ],201);
                   

              }
              else {

                return response()->json([
                    'success' => 'error',
                    'code'    => 'E003',
                    'message' => 'Sorry! You can only invest within '. $data->min_amount. ' - ' .$data->max_amount,
                
                ],201);

                }

              }

        }
}
