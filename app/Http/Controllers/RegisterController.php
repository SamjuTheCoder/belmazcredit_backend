<?php

namespace App\Http\Controllers;

use App\Models\EmailSignup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\SignUp;
use Illuminate\Support\Facades\Mail;

/*
 * @Author: Julius Fasema
 * Controller: RegisterController
 * Description: Defines all the functions for registration.
 * Date: 10-02-2022
 */

class RegisterController extends FunctionsController
{
    
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                //'transaction_code'=> 'required|string',
        ]);

        if($validator->fails()){

            return response()->json($validator->errors()->toJson(), 400);
        }

        // validate email
        $res = $this->validateEmail($request->get('email'));
        
        if($res == 0) {

                return response()->json([
                        
                        'success' => 'error',
                        'code'    => 'E002',
                        'message' => 'Invalid email',
                ]);

                exit;
        }

        // validate transaction code
        $res1 = $this->validateCode($request->get('transaction_code'));
        
        if($res1 == 0) {

                return response()->json([
                        
                        'success' => 'error',
                        'code'    => 'E003',
                        'message' => 'Invalid tranasction code',
                ]);

                exit;
        }

        // call the generate function concatenate the ordering function
        $REFERRAL_ID =  'BCN'.$this->generateRandomNumber(5).($this->OrderID() + 1);

                // $data = $this->checkIfUserCompletedTen($request->get('sponsor_id'));

                // if($data == false) {

                                $user = $this->registerNewUser($REFERRAL_ID, $request->get('name'), $request->get('email'), $request->get('phone'), $request->get('password'),$request->get('transaction_code'), $request->get('sponsor_id'));
                                //$this->addUsersPhase1($REFERRAL_ID,  $request->get('sponsor_id'));
                                
                                // update email and transaction code
                                $this->updateTransactionCode($request->get('transaction_code'));
                                $this->updateEmail($request->get('email'));
                                
                                
                                // if ($request->get('sponsor_id') == null ) {

                                if ($request->get('sponsor_id') != null) {
                                       
                                        $user = User::where('referral_id', $request->get('sponsor_id'))->value('sponsor_id');
                                        if($user == null) {

                                            // DIRECT REFERRAL 40%
                                           $DIRECT_REFERRAL_BONUS =  (40/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                                           $this->addBonus($request->get('sponsor_id'),"", $DIRECT_REFERRAL_BONUS);
                                           $this->addToWallet($request->get('sponsor_id'), 'REFERRAL BONUS', $DIRECT_REFERRAL_BONUS); // add earning into wallet

                                        }
                                        else {
                                                $user1 = User::where('referral_id',  $user)->value('sponsor_id');

                                                if($user1==null){

                                                        // FIRST INDIRECT REFERRAL 10%
                                                        $FIRST_INDIRECT_REFERRAL_BONUS =  (10/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                                                        $this->addBonus($user,"", $FIRST_INDIRECT_REFERRAL_BONUS);
                                                        $this->addToWallet($user, 'REFERRAL BONUS', $FIRST_INDIRECT_REFERRAL_BONUS); // add earning into wallet

                                                        $DIRECT_REFERRAL_BONUS =  (40/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                                                        $this->addBonus($request->get('sponsor_id'),"", $DIRECT_REFERRAL_BONUS);
                                                        $this->addToWallet($request->get('sponsor_id'), 'REFERRAL BONUS', $DIRECT_REFERRAL_BONUS); // add earning into wallet
                                                }else {
                                                        // SECOND INDIRECT REFERRAL 5%
                                                        $SECOND_INDIRECT_REFERRAL_BONUS =  (5/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                                                        $this->addBonus($user1,"", $SECOND_INDIRECT_REFERRAL_BONUS);
                                                        $this->addToWallet($user1, 'REFERRAL BONUS', $SECOND_INDIRECT_REFERRAL_BONUS); // add earning into wallet

                                                        $SECOND_INDIRECT_REFERRAL_BONUS =  (10/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                                                        $this->addBonus($user,"", $SECOND_INDIRECT_REFERRAL_BONUS);
                                                        $this->addToWallet($user, 'REFERRAL BONUS', $SECOND_INDIRECT_REFERRAL_BONUS); // add earning into wallet

                                                        $DIRECT_REFERRAL_BONUS =  (40/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                                                        $this->addBonus($request->get('sponsor_id'),"", $DIRECT_REFERRAL_BONUS);
                                                        $this->addToWallet($request->get('sponsor_id'), 'REFERRAL BONUS', $DIRECT_REFERRAL_BONUS); // add earning into wallet
                                                }
                                        }
                        
                                }
                               
                                return response()->json([
                                        
                                        'success' => 'success',
                                        'code'    => '00',
                                        'message' => 'Registration successful',
                                        'data' => $user,
                                ]);

                // }
                // elseif($data == true) {

                //         $users_1 = $this->UsersPhase1($request->get('sponsor_id'));
                //         $users_2 = $this->UsersPhase2($request->get('sponsor_id'));
                //         $users_3 = $this->UsersPhase3($request->get('sponsor_id'));
                //         $users_4 = $this->UsersPhase4($request->get('sponsor_id'));

                //         foreach( $users_1 as $users ) {

                //               $count = $this->validateNumberOfUsersPhase1($users->referral_id);  

                //               if($count < 10 ){

                //                         $user = $this->registerNewUser($REFERRAL_ID, $request->get('name'), $request->get('email'), $request->get('phone'), $request->get('password'), $request->get('transaction_code'),$users->referral_id);
                //                         $this->addUsersPhase1($REFERRAL_ID,  $users->referral_id);

                //                         // update email and transaction code
                //                         $this->updateTransactionCode($request->get('transaction_code'));
                //                         $this->updateEmail($request->get('email'));
                                        
                //                          // indirect referral bonus
                //                         $INDIRECT_REFERRAL_BONUS =  (10/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                //                         $this->addBonus($REFERRAL_ID,  $request->get('sponsor_id'), $INDIRECT_REFERRAL_BONUS);
                //                         $this->addToWallet($request->get('sponsor_id'), 'REFERRAL BONUS', $INDIRECT_REFERRAL_BONUS); // add earning into wallet

                //                         return response()->json([
                //                                 'success' => 'success',
                //                                 'code'    => '00',
                //                                 'message' => 'Registration successful',
                //                                 'data' => $user,
                //                         ]);
                                        
                //                         exit;

                //               }elseif( $count == 10 ) {

                //                         // iterate through phase 2
                //                         foreach( $users_2 as $users ) {

                //                                 $count = $this->validateNumberOfUsersPhase2($users->referral_id);  
                  
                //                                 if($count < 10 ){
                  
                //                                           $user = $this->registerNewUser($REFERRAL_ID, $request->get('name'), $request->get('email'), $request->get('phone'), $request->get('password'), $request->get('transaction_code'), $users->referral_id);
                //                                           $this->addUsersPhase2($REFERRAL_ID,  $users->referral_id);

                //                                           // update email and transaction code
                //                                          $this->updateTransactionCode($request->get('transaction_code'));
                //                                          $this->updateEmail($request->get('email'));
                                                          
                //                                            // indirect referral bonus
                //                                           $INDIRECT_REFERRAL_BONUS =  (10/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                //                                           $this->addBonus($REFERRAL_ID,  $request->get('sponsor_id'), $INDIRECT_REFERRAL_BONUS);
                //                                           $this->addToWallet($request->get('sponsor_id'), 'REFERRAL BONUS', $INDIRECT_REFERRAL_BONUS); // add earning into wallet
                                                         
                //                                           return response()->json([
                //                                                   'success' => 'success',
                //                                                   'code'    => '00',
                //                                                   'message' => 'Registration successful',
                //                                                   'data' => $user,
                //                                           ]);
                                                          
                //                                           exit;
                  
                //                                 }
                //                                 elseif( $count == 10 ) {

                //                                         // iterate through phase 3
                //                                         foreach( $users_3 as $users ) {

                //                                                 $count = $this->validateNumberOfUsersPhase3($users->referral_id);  
                                  
                //                                                 if($count < 10 ){
                                  
                //                                                           $user = $this->registerNewUser($REFERRAL_ID, $request->get('name'), $request->get('email'), $request->get('phone'), $request->get('password'), $request->get('transaction_code'), $users->referral_id);
                //                                                           $this->addUsersPhase3($REFERRAL_ID,  $users->referral_id);

                //                                                           // update email and transaction code
                //                                                           $this->updateTransactionCode($request->get('transaction_code'));
                //                                                           $this->updateEmail($request->get('email'));
                                                                           
                //                                                           // indirect referral bonus
                //                                                           $INDIRECT_REFERRAL_BONUS =  (10/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                //                                                           $this->addBonus($REFERRAL_ID,  $request->get('sponsor_id'), $INDIRECT_REFERRAL_BONUS);
                //                                                           $this->addToWallet($request->get('sponsor_id'), 'REFERRAL BONUS', $INDIRECT_REFERRAL_BONUS); // add earning into wallet
                //                                                           return response()->json([
                //                                                                   'success' => 'success',
                //                                                                   'code'    => '00',
                //                                                                   'message' => 'Registration successful',
                //                                                                   'data' => $user,
                //                                                           ]);
                                                                          
                //                                                           exit;
                                  
                //                                                 }
                //                                                 elseif( $count == 10 ) {

                //                                                         // iterate through phase 4
                //                                                         foreach( $users_4 as $users ) {

                //                                                                 $count = $this->validateNumberOfUsersPhase4($users->referral_id);  
                                                  
                //                                                                 if($count < 10 ){
                                                  
                //                                                                           $user = $this->registerNewUser($REFERRAL_ID, $request->get('name'), $request->get('email'), $request->get('phone'), $request->get('password'), $request->get('transaction_code'), $users->referral_id);
                //                                                                           $this->addUsersPhase4($REFERRAL_ID,  $users->referral_id);

                //                                                                           // update email and transaction code
                //                                                                           $this->updateTransactionCode($request->get('transaction_code'));
                //                                                                           $this->updateEmail($request->get('email'));
                                                                                          
                //                                                                           // indirect referral bonus
                //                                                                           $INDIRECT_REFERRAL_BONUS =  (10/100 ) * $this->getUserPhase($request->get('sponsor_id'));
                //                                                                           $this->addBonus($REFERRAL_ID,  $request->get('sponsor_id'), $INDIRECT_REFERRAL_BONUS);
                //                                                                           $this->addToWallet($request->get('sponsor_id'), 'REFERRAL BONUS', $INDIRECT_REFERRAL_BONUS); // add earning into wallet
                //                                                                           return response()->json([
                //                                                                                   'success' => 'success',
                //                                                                                   'code'    => '00',
                //                                                                                   'message' => 'Registration successful',
                //                                                                                   'data' => $user,
                //                                                                           ]);
                                                                                          
                //                                                                           exit;
                                                  
                //                                                                 }
                //                                                                 elseif( $count == 10 ) {
                
                //                                                                         exit;
                
                //                                                                 }
                
                //                                                         } // end iterate through phase 4

                //                                                 }

                //                                         } // end iterate through phase 3 
                //                                 }

                //                         } // end iterate through phase 2 
                //                }

                //         } // end iterate through phase 1

                // }
    }

    // user signup and triggers email to users with the instruction to complete registration
    public function signUpEmail(Request $request) {

        try {
                
                $this->validate($request, [

                'email' => 'required|unique:email_signups|email|string',
                ]);     

                $transaction_code = $this->generateTransactionCode();
                
               $resp = Mail::to($request->input('email'))->send(new SignUp($request->input('email'), $transaction_code));

                //if($resp) {

                        $data = EmailSignup::create(['email' => $request->input('email')]);

                        return response()->json([
                                
                                'success' => 'success',
                                'code'    => '00',
                                'message' => 'Thank you for signing up, check your email for further instruction on how to complete your registration',
                                'data' => $data,
                                'transction_code' => $transaction_code,
                        ]);
        // }
        } catch (\Throwable $th) {
               
                throw $th;
        }
        
    }

    // activare user after payment is confirmed
    public function confirmUserPayment(Request $request) {

       $res =  $this->userStatus($request->input('uid'));
       
       if($res == 1) {

                return response()->json([
                                
                        'success' => 'success',
                        'code'    => '00',
                        'message' => 'User Activated',
                        'data' => 1,
                ]);
        }
        else {
                exit;
        }
    }


}
