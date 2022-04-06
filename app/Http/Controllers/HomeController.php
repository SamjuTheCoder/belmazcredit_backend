<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Phase1;
use App\Models\Phase2;
use App\Models\Phase3;
use App\Models\Phase4;
use Illuminate\Support\Facades\Auth;

/*
 * @Author: Julius Fasema
 * Controller: HomeController
 * Description: Defines all the functions for registration.
 * Date: 30-01-2022
 */

class HomeController extends FunctionsController
{
    // user dashboard
     public function UserDashboard() {
        
        $data = $this->home(Auth::user()->phases_id, Auth::user()->referral_id); // call user dashboard function

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'User Dashboard successfully loaded!',
            'data' => $data,
        ]);
    }

    // contribute fund
    public function contributeFund(Request $request) {

        if($request->input('phase_id') == 1){
            
            $phases = 'phase1s';
            $amount = 10000;

            $res1 = $this->isContributionExists(Auth::user()->id,1,$amount,Auth::user()->referral_id);

            if($res1 == 1) {

                return response()->json([
                    'success' => 'ERROR',
                    'code'    => 'E00',
                    'message' => 'Amount already contributed',
                ]);

                exit;
            }
           
            $res = $this->contribute($phases,1,Auth::user()->group_id,$amount,Auth::user()->referral_id);
            if($res == 1){
                return response()->json([
                    'success' => 'success',
                    'code'    => '00',
                    'message' => 'Amount contributed successfully',
                ]);
            }
    
        }
        elseif($request->input('phase_id') == 2){
            
            $phases = 'phase2s';
            $amount = 50000;

            $res1 = $this->isContributionExists(Auth::user()->id,2,$amount,Auth::user()->referral_id);

            if($res1 == 1) {

                return response()->json([
                    'success' => 'ERROR',
                    'code'    => 'E00',
                    'message' => 'Amount already contributed',
                ]);

                exit;
            }

            $res = $this->contribute($phases,2,Auth::user()->group_id,$amount, Auth::user()->referral_id);
            if($res == 1){
                return response()->json([
                    'success' => 'success',
                    'code'    => '00',
                    'message' => 'Amount contributed successfully',
                ]);
            }
    
        }
        elseif($request->input('phase_id') == 3){
            
            $phases = 'phase3s';
            $amount = 3000000;

            $res1 = $this->isContributionExists(Auth::user()->id,3,$amount,Auth::user()->referral_id);

            if($res1 == 1) {

                return response()->json([
                    'success' => 'ERROR',
                    'code'    => 'E00',
                    'message' => 'Amount already contributed',
                ]);

                exit;
            }

            $res = $this->contribute($phases,3, Auth::user()->group_id, $amount,Auth::user()->referral_id);
            if($res == 1){
                return response()->json([
                    'success' => 'success',
                    'code'    => '00',
                    'message' => 'Amount contributed successfully',
                ]);
            
            }
        }
        elseif($request->input('phase_id') == 4){
            
            $phases = 'phase4s';
            $amount = 1500000;

            $res1 = $this->isContributionExists(Auth::user()->id,4,$amount,Auth::user()->referral_id);

            if($res1 == 1) {

                return response()->json([
                    'success' => 'ERROR',
                    'code'    => 'E00',
                    'message' => 'Amount already contributed',
                ]);

                exit;
            }

            $res = $this->contribute($phases,4, Auth::user()->group_id, $amount,Auth::user()->referral_id);
            if($res == 1){
                return response()->json([
                    'success' => 'success',
                    'code'    => '00',
                    'message' => 'Amount contributed successfully',
                ]);
            }    
        }

    }

    // list my contributions
    public function listMyContributions() {

        $data = Contribution::where('referral_id',Auth::user()->referral_id)
        ->leftjoin('phases','contributions.phases_id','=','phases.id')->get();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Successfully loaded!',
            'data' => $data,
        ]);

    }

     // activare user after payment is confirmed
     public function confirmContributionrPayment(Request $request) {

        $res =  $this->confirmContributionStatus($request->input('uid'));
        
        if($res == 1) {
 
                 return response()->json([
                                 
                         'success' => 'success',
                         'code'    => '00',
                         'message' => 'Payment Confirmed',
                         'data' => 1,
                 ]);
         }
         else {
                 exit;
         }
     }

    // list all contributions
    public function listAllContributions() {

        $data = Contribution::leftjoin('users','contributions.user_id','=','users.id')
        ->leftjoin('phases','contributions.phases_id','=','phases.id')
        ->select('*','contributions.id as uid','contributions.status as cstatus','users.name as uname')
        ->orderby('contributions.id','desc')
        ->get();

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Successfully loaded!',
            'contributors' => $data,
        ]);

    }

    // admin dashboard
    public function AdminDashboard() {
        
        $data = $this->adminHome(Auth::user()->id ); // call user dashboard function

        return response()->json([
            'success' => 'success',
            'code'    => '00',
            'message' => 'Admin Dashboard successfully loaded!',
            'data' => $data,
        ]);
    }

    // // call function callUsers
    // public function callUsersRecursively() {

    //     $data = $this->callUsers(Auth::user()->referral_id);

    //     return response()->json([
    //         'success' => 'success',
    //         'code'    => '00',
    //         'message' => 'List of downlines',
    //         'data' => $data,
    //     ]);
    // }

    // users dashboard
    public function home($user_phase, $user_referralid) {

        $count = 0;
        $phase2users = [];

        $user_role = $this->getUserRole(Auth::user()->id);
        
        $users = $this->getMyUser($user_referralid);
        $users2 = $this->UsersPhase2($user_referralid);
        $users3 = $this->UsersPhase3($user_referralid);
        $users4 = $this->UsersPhase4($user_referralid);

        //count users in each stage
        $count1 = $this->validateNumberOfUsersPhase1($user_referralid);
        $count2 = $this->validateNumberOfUsersPhase2($user_referralid);
        $count3 = $this->validateNumberOfUsersPhase3($user_referralid);
        $count4 = $this->validateNumberOfUsersPhase4($user_referralid);

        // call users stage amount
        $phase_amount = $this->getPhaseAmount(Auth::user()->id);

        // call investment setup
        $investment = $this->getInvestmentAmount(Auth::user()->id, Auth::user()->investment_id );

        // get loan amount
        $loan = $this->getLoanAmount(Auth::user()->phases_id);

        // call my loan amount function
        $myloan = $this->getMyLoanAmount(Auth::user()->phases_id, Auth::user()->id);
        
        // get refferal count
        $referral_count = $this->getBonusCount(Auth::user()->referral_id);

        // get total referral amount
        $total_referral_amount = $this->getBonus(Auth::user()->referral_id);

        // get total investment amount
        $investment_amount = $this->getTotalInvestmentAmount(Auth::user()->id);

        // determine the next user inline to earn
        $earning_status = $this->nextToEarn(Auth::user()->id, Auth::user()->phases_id);

        // sum user contribution
        $user_contribution = $this->sumWalletContribution(Auth::user()->referral_id);

        $this->fetchContributors($user_referralid, $user_phase);
        $count = $this->countContributors($user_referralid, $user_phase);
        $walletsum = $this->sumWalletReferral(Auth::user()->referral_id);

        // sum referral withdrawal
        $referral_withdrawal = $this->sumWithdrawalReferral(Auth::user()->referral_id);

        // sum contribution withdrawal 
        $contribution_withdrawal = $this->sumWithdrawalContribution(Auth::user()->referral_id);

            return [
                "stage_users" => $count,
                "user_phase"=> Auth::user()->phases_id,
                "user_referral_id"=>Auth::user()->referral_id,
                'phase_amount' => $phase_amount,
                'investment' => $investment,
                'loan' => $loan,
                'myloan' => $myloan,
                'referral_count' => $referral_count,
                'total_referral_amount' => $total_referral_amount,
                'investment_amount' => $investment_amount,
                'user_role' => $user_role,
                'earning_status' => $earning_status,
                'wallet' =>  $walletsum,
                'user_contribution' => $user_contribution,
                'referral_withdrawal' => $referral_withdrawal,
                'contribution_withdrawal' => $contribution_withdrawal
                
                ];

        // check if user is in phase 2- 100
        // elseif( $user_phase == 2 ) {

        //     foreach( $users2 as $user ) {

        //         $users = $this->getMyUser($user->referral_id);
        //         array_push($phase2users, $users);
                   
        //             foreach($users as $pusers) {

        //                 $count ++; 

        //                 if ( $count == 100 ) { 
        //                 $this->updateUserPhase(3,$user_referralid); 
        //                 $this->addUsersPhase3($pusers->referral_id, $user_referralid);
        //             }
        //         }

        //     }

        //     return [
        //         "stage_users" => $count2,
        //         "user_phase"=>Auth::user()->phases_id,
        //         "user_referral_id"=>Auth::user()->referral_id,
        //         'phase_amount' => $phase_amount, 
        //         'investment' => $investment,
        //         'loan' => $loan,
        //         'myloan' => $myloan,
        //         'referral_count' => $referral_count,
        //         'total_referral_amount' => $total_referral_amount,
        //         'investment_amount' => $investment_amount,
        //         'user_role' => $user_role,
        //         'earning_status' => $earning_status

        //         ];

        // } // end phase 2 check

        // // check if user is in phase 3 - 1000
        // elseif( $user_phase == 3 ) {

        //     foreach( $users3 as $user ) {

        //         $users = $this->getMyUser($user->referral_id);

        //         array_push($phase2users, $users);
                   
        //             foreach($users as $pusers) {

        //                 $count ++; 

        //                 if ( $count == 1000 ) { 
        //                 $this->updateUserPhase(4,$user_referralid); 
        //                 $this->addUsersPhase4($pusers->referral_id, $user_referralid);
        //             }
        //         }

        //     }

        //     return [
        //         "stage_users" => $count3,
        //         "user_phase"=>Auth::user()->phases_id,
        //         "user_referral_id"=>Auth::user()->referral_id,
        //         'phase_amount' => $phase_amount,
        //         'investment' => $investment,
        //         'loan' => $loan,
        //         'myloan' => $myloan,
        //         'referral_count' => $referral_count,
        //         'total_referral_amount' => $total_referral_amount,
        //         'investment_amount' => $investment_amount,
        //         'user_role' => $user_role,
        //         'earning_status' => $earning_status

        //         ];

        // } // end phase 3 check

        // // check if user is in stage 4 - 10000
        // elseif( $user_phase == 4 ) {

        //     foreach( $users4 as $user ) {

        //         $users = $this->getMyUser($user->referral_id);
        //         array_push($phase2users, $users);
                   
        //             foreach($users as $pusers) {

        //                 $count ++; 

        //                 if ( $count == 10000 ) { 
        //                 $this->updateUserPhase(1,$user_referralid); 
        //             }
        //         }

        //     }

        //     return [
        //         "stage_users" => $count4,
        //         "user_phase"=> Auth::user()->phases_id,
        //         "user_referral_id"=> Auth::user()->referral_id,
        //         'phase_amount' => $phase_amount,
        //         'investment' => $investment,
        //         'loan' => $loan, 
        //         'myloan' => $myloan,
        //         'referral_count' => $referral_count,
        //         'total_referral_amount' => $total_referral_amount,
        //         'investment_amount' => $investment_amount,
        //         'user_role' => $user_role,
        //         'earning_status' => $earning_status
                
        //         ];

        // } // end phase 4 check

    } // end users dashboard


    // admin dashboard
    public function adminHome($user_id) {

        $count = 0;
        $phase2users = [];

        // call total contributors
        $total_contributors = $this->totalContributors();
        
        // call total contributions
        $total_contributions = $this->totalContributions();

        // call total referral
        $total_referral = $this->totalReferral();

        // call referral amount
        $total_referral_amount = $this->totalReferralAmount();

        // call total investment
        $total_investment = $this->totalInvestment();
        
        // get refferal count
        $total_loans = $this->totalLoans();

        // get total referral amount
        $total_loan_interest = $this->totalLoansInterest();

      
        return [
            "total_contributors" => $total_contributors,
            "total_contributions"=> $total_contributions,
            "total_referral"=> $total_referral,
            'total_referral_amount' => $total_referral_amount,
            'total_investment' => $total_investment,
            'total_loans' => $total_loans,
            'total_loan_interest' => $total_loan_interest,
            ];

        
    }// end admin dashboard

    //list contributors
    public function listContributors() {

        $list_contributors = $this->listContributor();


        return [
            'list_contributors' => $list_contributors
            ];
    }

     //list contributors loan 
     public function listContributorsLoans() {

        $list_contributors_loan = $this->listContributorLoan();


        return [
            'list_contributors_loan' => $list_contributors_loan
            ];
    }

     //list all investment 
     public function listAllInvestment() {

        $list_investment = $this->listAllInvestments();


        return [
            'list_investment' => $list_investment
            ];
    }

    //list all transactions 
    public function sumAllWallets() {

        $list_all = $this->sumAllWallet();


        return [
            'list_wallets' => $list_all
            ];
    }

     //list all withdrawal transaction 
     public function sumAllWalletsWithdrawal() {

        $list_all = $this->sumAllWalletWithdrawal();


        return [
            'list_wallets_withdrawal' => $list_all
            ];
    }

    public function claimContribution(Request $request) {

        if($request->input('phase_id') == 1){
                return response()->json([
                    'success' => 'Error',
                    'code'    => 'E00',
                    'message' => 'Sorry you are not yet qualify to earn',
                ]);

        }
        elseif($request->input('phase_id') == 2){
            $EARNING = 40000;
            $exists = $this->isExistsWallet(Auth::user()->referral_id, 'CONTRIBUTION EARNING', $EARNING);

            if($exists == 1) {
                return response()->json([
                    'success' => 'Error',
                    'code'    => 'E00',
                    'message' => 'Fund already claimed!',
                ]);

            }elseif($exists == 0 ){
            $this->addToWallet(Auth::user()->referral_id, 'CONTRIBUTION EARNING', $EARNING); // add earning into wallet
            $this->confirmContributionClaim(Auth::user()->referral_id, Auth::user()->phases_id); // confirm user has claim fund

            return response()->json([
                'success' => 'success',
                'code'    => '00',
                'message' => 'Fund successfully claimed!',
            ]);
            }
        }
        elseif($request->input('phase_id') == 3){
            $EARNING = 150000;
            $this->addToWallet(Auth::user()->referral_id, 'CONTRIBUTION EARNING', $EARNING); // add earning into wallet
            $this->confirmContributionClaim(Auth::user()->referral_id, Auth::user()->phases_id); // confirm user has claim fund

            return response()->json([
                'success' => 'success',
                'code'    => '00',
                'message' => 'Fund successfully claimed!',
            ]);
        }
        elseif($request->input('phase_id') == 4){
            $EARNING = 1400000;
            $this->addToWallet(Auth::user()->referral_id, 'CONTRIBUTION EARNING', $EARNING); // add earning into wallet
            $this->confirmContributionClaim(Auth::user()->referral_id, Auth::user()->phases_id); // confirm user has claim fund

            return response()->json([
                'success' => 'success',
                'code'    => '00',
                'message' => 'Fund successfully claimed!',
            ]);
        }
        elseif(Auth::user()->phases_id == 4){
            $EARNING = 10000000;
            $this->addToWallet(Auth::user()->referral_id, 'CONTRIBUTION EARNING', $EARNING); // add earning into wallet
            $this->confirmContributionClaim(Auth::user()->referral_id, Auth::user()->phases_id); // confirm user has claim fund

            return response()->json([
                'success' => 'success',
                'code'    => '00',
                'message' => 'Fund successfully claimed!',
            ]);
        }
    }

    public function referralWithdraw(Request $request) {

       $amount =  $this->sumWalletReferral(Auth::user()->referral_id);

    //    if($request->input('amount') == 1000)
    //    {
    //     $input_amount = 1000;
    //    }

    if($request->input('amount') == null){

        return response()->json([
            'success' => 'Error',
            'code'    => 'E00',
            'message' => 'Please enter amount to withdraw',
        ]);

    }elseif( $request->input('amount') > $amount ){

            return response()->json([
                'success' => 'Error',
                'code'    => 'E00',
                'message' => 'Sorry you can not place withdrawal. Insufficient Fund.',
            ]);

       }else{

                $bal = $amount - $request->input('amount');

                if( $bal <= 1000 ){

                    return response()->json([
                        'success' => 'Error',
                        'code'    => 'E00',
                        'message' => 'Sorry you can not withdraw minimum balance of 1,000',
                    ]);

                }
                else {

                    $this->withdrawWallet(Auth::user()->referral_id, 'REFERRAL BONUS', $request->input('amount'));
            
                    return response()->json([
                        'success' => 'success',
                        'code'    => '00',
                        'message' => 'Successfull',
                    ]);
                }

                
            }
      
    
    }

    public function contributionWithdraw(Request $request) {

        $amount =  $this->sumWalletContribution(Auth::user()->referral_id);
 
        if($request->input('amount') == null){
 
         return response()->json([
             'success' => 'Error',
             'code'    => 'E00',
             'message' => 'Please enter amount to withdraw',
         ]);
 
        }
        elseif($request->input('amount') == 0) {
         
         return response()->json([
             'success' => 'Error',
             'code'    => 'E00',
             'message' => 'Sorry you can not withdraw this amount',
         ]);
 
         }  
        elseif( $request->input('amount') > $amount ){
 
             return response()->json([
                 'success' => 'Error',
                 'code'    => 'E00',
                 'message' => 'Sorry! You have insufficient balance',
             ]);
 
        }
         else{  
             
             $this->withdrawWallet(Auth::user()->referral_id, 'CONTRIBUTION EARNING', $request->input('amount'));
 
             return response()->json([
                 'success' => 'success',
                 'code'    => '00',
                 'message' => 'Successfull',
             ]);
         }
     
     }

    
}

