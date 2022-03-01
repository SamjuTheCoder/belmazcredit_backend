<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoanSetup;
use App\Models\Loan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

/**
 * @Author: Julius Fasema
 * Controller: LoanController
 * Description: Defines all the functions for setting up contribution and others
 * Date: 30-01-2022
 */

class LoanController extends FunctionsController
{
    
    // setup contribution amount
    public function setupLoan(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'phases' => 'required|numeric',
            'loan_amount' => 'required|numeric',
            'percentage' => 'required|numeric',
        ]);

        if($validator->fails()){

                return response()->json([
                    'success' => 'success',
                    'code'    => 'E002',
                    'message' => $validator->errors()->toJson()
                ]);
        }

        $user = LoanSetup::create([
            'phases' => $request->get('phases'),
            'loan_amount' => $request->get('loan_amount'),
            'percentage' => $request->get('percentage'),
        ]);

        //$token = JWTAuth::fromUser($user);
        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Record successfully added',
             //compact('user','token')
        
        ],201);
    }

    // list loan
    public function listLoans()
    {
        $loan = LoanSetup::leftjoin('phases','loan_setups.phases','=','phases.id')
        ->get();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Loan successfully listed',
            'data' => $loan,
        ],201);
    }

    // my loan
    public function listMyLoan()
    {
        $myloan = Loan::leftjoin('phases','loans.phases_id','=','phases.id')
                                    ->where('user_id',Auth::user()->id)
                                    ->get();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Loan successfully listed',
            'data' => $myloan,
        ], 201);
    }

    // make contribution amount
    public function applyLoan(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'loan_amount' => 'required|numeric',
        ]);

        if($validator->fails()){

                return response()->json([
                    
                    'success' => 'success',
                    'code' => 'E002',
                    'message' => $validator->errors()->toJson()
                ]);
        }

        if( LoanSetup::where('phases',Auth::user()->phases_id)->exists() ) {

            $data = LoanSetup::where('phases',Auth::user()->phases_id)->first();

              if( $request->get('loan_amount') > $data->loan_amount ) {

                    return response()->json([
                        'success' => 'error',
                        'code'    => 'E003',
                        'message' => 'Sorry! You cannot apply for more than '. $data->loan_amount. ' loan in this phase you are.',
                    
                    ],201);

              }
              else {

                    Loan::create([
                        'user_id' => Auth::user()->id,
                        'phases_id' => Auth::user()->phases_id,
                        'loan_amount' => $request->get('loan_amount'),
                        'interest' => $request->get('loan_amount') * ( $data->percentage / 100),
                    ]);
            
                    return response()->json([
                        'success' => 'success',
                        'code'    => '00',
                        'message' => 'Loan applied successfully!',
                    
                    ],201);
                }

              }

        }
        // end function for loan

        public function confirmUserLoan(Request $request) {

            $this->confirmLoanRequest($request->input('uid'));

            return response()->json([
                'success' => 'success',
                'code'    => '00',
                'message' => 'Successfully confirmed!',
            
            ]);

        }

}
