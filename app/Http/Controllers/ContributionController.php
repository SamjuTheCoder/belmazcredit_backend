<?php

namespace App\Http\Controllers;

use App\Models\ContributionSetup;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\Auth;

/**
 * @Author: Julius Fasema
 * Controller: ContributionController
 * Description: Defines all the functions for setting up contribution and others
 * Date: 30-01-2022
 */

class ContributionController extends Controller
{
    // setup contribution amount
    public function setupContribution(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'phases' => 'required|numeric',
            'contributed_amount' => 'required|numeric',
            'receive' => 'required|numeric',
            'withdrawal' => 'required|numeric',
        ]);

        if($validator->fails()){

                return response()->json([
                    'success' => 'success',
                    'code'    => 'E002',
                    'message' => $validator->errors()->toJson()
                ]);
        }

        $user = ContributionSetup::create([
            'phases' => $request->get('phases'),
            'contributed_amount' => $request->get('contributed_amount'),
            'receive' => $request->get('receive'),
            'withdrawal' => $request->get('withdrawal'),
        ]);

        //$token = JWTAuth::fromUser($user);
        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Record successfully added',
             //compact('user','token')
        
        ],201);
    }

    // list contribution
    public function listContributions()
    {
        $contribution = ContributionSetup::leftjoin('phases','contribution_setups.phases','=','phases.id')->get();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Contribution successfully listed',
            'data' => $contribution,
        ],201);
    }

    // my contribution
    public function listMyContributions()
    {
        $mycontribution = Contribution::leftjoin('phases','contributions.phases_id','=','phases.id')
                                    ->where('user_id',Auth::user()->id)
                                    ->get();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Contribution successfully listed',
            'data' => $mycontribution,
        ],201);
    }

    // make contribution amount
    public function makeContributions(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'contribution_amount' => 'required|numeric',
        ]);

        if($validator->fails()){

                return response()->json([
                    
                    'success' => 'success',
                    'code' => 'E002',
                    'message' => $validator->errors()->toJson()
                ]);
        }

        if( ContributionSetup::where('phases',Auth::user()->phases_id)->exists() ) {

            $data = ContributionSetup::where('phases',Auth::user()->phases_id)->first();

              if( $request->get('contribution_amount') > $data->contributed_amount ) {

                    return response()->json([
                        'success' => 'error',
                        'code'    => 'E003',
                        'message' => 'Sorry! You cannot contribute more than '. $data->contributed_amount. ' in this phase you are.',
                    
                    ],201);

              }
              else {

                    Contribution::create([
                        'user_id' => Auth::user()->id,
                        'phases_id' => Auth::user()->phases_id,
                        'contribution_amount' => $request->get('contribution_amount'),
                    ]);
            
                    return response()->json([
                        'success' => 'success',
                        'code'    => '00',
                        'message' => 'Amount contributed successfully!',
                    
                    ],201);
                }

              }

        }

        // function for contribution



}
