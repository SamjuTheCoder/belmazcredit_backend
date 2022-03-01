<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

/*
 * @Author: Julius Fasema
 * Controller: UserController
 * Description: Defines all the functions for creating and login. Its also generate JWT for authentication
 * Date: 10-02-2022
 */

class UserController extends FunctionsController
{

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if ( !$token = JWTAuth::attempt($credentials)) {
                    
                return response()->json([
                        'success' => 'error',
                        'code' => 'E201',
                        'message' => 'Invalid credentials',
                
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => 'success',
                'code' => '00',    
                'message' => $e->getMessage(),
        ], 500);
        }

        $data = [];
        Session::put('USER_ID', Auth::user()->id);
        Session::put('ROLE_ID', Auth::user()->role_id);
        Session::put('PHASES_ID', Auth::user()->phases_id);

        array_push($data, Auth::user()->role_id);
        array_push($data, $token);

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Login successful',
            'data' => compact('token'),
            'userrole' => Auth::user()->role_id,
            'status' => Auth::user()->status
        ]);
    }

    public function getAuthenticatedUser()
        {
                try {

                        if (! $user = JWTAuth::parseToken()->authenticate()) {
                                return response()->json(['user_not_found'], 404);
                        }

                } catch (JWTException $e) {

                        return response()->json(['token_expired'], $e->getMessage());

                } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                        return response()->json(['token_invalid'], $e->getStatusCode());

                } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                        return response()->json(['token_absent'], $e->getStatusCode());

                }

                return response()->json(compact('user'));
        }
       
        
}