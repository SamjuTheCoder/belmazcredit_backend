<?php

namespace App\Http\Controllers;

use App\Models\Phases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Exception;

/**
 * @Author: Julius Fasema
 * Controller: PhasesController
 * Description: Defines all the functions for setting up phases and others
 * Date: 30-01-2022
 */

class PhasesController extends Controller
{
    // setup phases
    public function setupPhases(Request $request)
    {
        

            $validator = Validator::make($request->all(), [
                'phases' => 'required|string|max:50|unique:phases',
                'user_number' => 'required|numeric|max:10',
            ]);
    
            if($validator->fails()){

                    return response()->json([
                        
                        'success' => 'success',
                        'code' => 'E001',
                        'message' => $validator->errors()->toJson()
                    
                    ]);
            }
            
            Phases::create([
                'phases' => $request->get('phases'),
                'user_number' => $request->get('user_number'),
            ]);
    
            return response()->json([
                'success' => 'success',
                'code'    => '00',
                'message' => 'Phase successfully added',        
            ],201);

                   
    }

    // list phases
    public function listPhases()
    {
        $phases = Phases::all();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Phase successfully added',
            'data' => $phases,
        ],201);
    }

    //edit phases
    public function editPhases(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'phases' => 'required|string|max:50',
            'user_number' => 'required|numeric|max:10',
        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }

        Phases::where('id',$request->get('id'))->update([
            'phases' => $request->get('phases'),
            'user_number' => $request->get('user_number'),
        ]);

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Phase successfully updated',        
        ],201);
    }
}
