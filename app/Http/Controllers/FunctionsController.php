<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Phase1;
use App\Models\Phase2;
use App\Models\Phase3;
use App\Models\Phase4;
use App\Models\ContributionSetup;
use App\Models\Investment;
use App\Models\InvestmentSetup;
use App\Models\LoanSetup;
use App\Models\Loan;
use App\Models\Bonus;
use App\Models\Wallet;
use App\Models\BankAccount;
use App\Models\Contribution;
use App\Models\ContributionCheck;
use App\Models\EmailSignup;
use App\Models\TransactionCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;

/*
 * @Author: Julius Fasema
 * Controller: FunctionsController
 * Description: Defines all the reuseable functions
 * Date: 10-02-2022
 */

class FunctionsController extends Controller
{
    
    // function to generate random numbers
    public function generateRandomNumber($length) {
        $characters = '01234567890302050781598452630918735620';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // count users an assign orderid
    public function OrderID() {

        $count = User::where('role_id',3)->count();
    
        return $count;
        }

    // count users an assign orderid
    public function getOrderID($groupid) {

    $count = User::where('role_id',3)->where('group_id',$groupid)->count();

    return $count;
    }

    // count users for assigning groupid
    public function groupOrderID() {

        $count = User::where('role_id',3)->where('sponsor_id',null)->count();
    
        return $count;
        }

    // check if user has made payment
    public function checkForPayment($user_id) {
    
        if (User::where('id', $user_id)->where('payment_status',1)->exists() ) {

                return true;
        } else {

                return false;
        }
    }

    // return next user to be paid
    // public function nextUserForPayment($user_id, $phase) {

    //     $user_phase = $this->checkUserPhase($user_id, $phase);

    //     if( $user_phase == 1) {

    //             $phase_status = 'phase1_status';
    //             $min_value = $this->getMinValue($phase, $phase_status); // get the order id based on user phase
    //             $users = User::where('role_id',3)->where('phases_id', $user_phase)->where($phase_status, 0)->limit(10)->orderby('order_id','asc')->get();
                
    //             foreach($users as $user) {

    //                     if( $user->order_id == $min_value ) 
    //                     {
    //                             return $user;
    //                     }
                        
    //             }

                
    //     }
    //     if( $user_phase == 2) {

    //             $phase_status = 'phase2_status';
    //             $min_value = $this->getMinValue($phase, $phase_status); // get the order id based on user phase
    //             $users = User::where('role_id',3)->where('phases_id', $user_phase)->where($phase_status, 0)->limit(10)->orderby('order_id','asc')->get();
                
    //             foreach($users as $user) {

    //                     if( $user->order_id == $min_value ) 
    //                     {
    //                             return $user;
    //                     }
                        
    //             }

                
    //     }
    //     if( $user_phase == 3) {

    //             $phase_status = 'phase3_status';
    //             $min_value = $this->getMinValue($phase, $phase_status); // get the order id based on user phase
    //             $users = User::where('role_id',3)->where('phases_id', $user_phase)->where($phase_status, 0)->limit(10)->orderby('order_id','asc')->get();
                
    //             foreach($users as $user) {

    //                     if( $user->order_id == $min_value ) 
    //                     {
    //                             return $user;
    //                     }
                        
    //             }

                
    //     }
    //     if( $user_phase == 4) {

    //             $phase_status = 'phase4_status';
    //             $min_value = $this->getMinValue($phase, $phase_status); // get the order id based on user phase
    //             $users = User::where('role_id',3)->where('phases_id', $user_phase)->where($phase_status, 0)->limit(10)->orderby('order_id','asc')->get();
                
    //             foreach($users as $user) {

    //                     if( $user->order_id == $min_value ) 
    //                     {
    //                             return $user;
    //                     }
                        
    //             }

                
    //     }
        
    // }

    // check user phase
    public function checkUserPhase($user_id, $phase) {

        $user_phase = User::where('id', $user_id)->where('phases_id', $phase)->first();

        return $user_phase->phases_id;
        
    }

    // return min order id for phase1_status
    public function getMinValue($user_phase, $phase_status) {

        $min_value = User::where('role_id',3)->where('phases_id', $user_phase)->where($phase_status, 0)->limit(10)->orderby('order_id','asc')->min('order_id');
        
        return $min_value;
    }

    // validate number of users i.e must be ten(10)
    public function validateNumberOfUsers($sponsor_id) {

        $count = User::where('sponsor_id', $sponsor_id)->count();
                            
        return $count;
        
    }

    // check if each user during registration completed its 10
    public function checkIfUserCompletedTen($sponsor_id) {
    
        $count = $this->validateNumberOfUsers($sponsor_id);

        if( $count < 10 ) {

            return false;
        }
        elseif( $count == 10 )  {

            return true;
        }

    }

    // check if any user during registration completed its phase 1 10
    public function registerNewUser($REFERRAL_ID, $name, $email, $phone, $pass, $transaction_code, $sponsor_id) {

        if($sponsor_id == null){
            $groupid = $this->groupOrderID() + 1;
        }else {
            $groupid =  User::where('referral_id',$sponsor_id)->value('group_id');
        }

        User::create([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => Hash::make($pass),
        'role_id' => 3,
        'phases_id' => 1,
        'transaction_code'  => $transaction_code,
        'referral_id' => $REFERRAL_ID, 
        'sponsor_id'  => $sponsor_id,
        'order_id' => $this->getOrderID($groupid) + 1, //order user in ascending order
        'group_id' => $groupid, //group user in ascending order
        ]);

        return $this->getOrderID($groupid) + 1;

    }

    // validate number of users in phase 1 - 10)
    public function validateNumberOfUsersPhase1($sponsor_id) {

        $count = Phase1::where('sponsor_id', $sponsor_id)->count();
                            
        return $count;
        
    }

    // get all users in phase 1
    public function UsersPhase1($sponsor_id) {

        $users = Phase1::where('sponsor_id', $sponsor_id)->get();
                            
        return $users;
        
    }

    // insert into phase 1
    public function addUsersPhase1($referral_id, $sponsor_id) {

        if( Phase2::where('referral_id',$referral_id)->where('sponsor_id',$sponsor_id)->exists()) {

        }else {
            Phase1::create(['referral_id'=>$referral_id, 'sponsor_id'=> $sponsor_id]);
        }       
    }

    // validate number of users in phase 2 - 100)
    public function validateNumberOfUsersPhase2($sponsor_id) {

        $count = Phase2::where('sponsor_id', $sponsor_id)->count();
                            
        return $count;
        
    }

    // get all users in phase 2
    public function UsersPhase2($sponsor_id) {

        $users = Phase2::where('sponsor_id', $sponsor_id)->get();
                            
        return $users;
        
    }

    // insert into phase 2
    public function addUsersPhase2($referral_id, $sponsor_id) {

        if( Phase2::where('referral_id',$referral_id)->where('sponsor_id',$sponsor_id)->exists()) {

        }else {
            Phase2::create(['referral_id'=>$referral_id, 'sponsor_id'=> $sponsor_id]);
        }
                
    }

    // validate number of users in phase 3 - 1000)
    public function validateNumberOfUsersPhase3($sponsor_id) {

        $count = Phase3::where('sponsor_id', $sponsor_id)->count();
                            
        return $count;
        
    }

    // get all users in phase 3
    public function UsersPhase3($sponsor_id) {

        $users = Phase3::where('sponsor_id', $sponsor_id)->get();
                            
        return $users;
        
    }

    // insert into phase 3
    public function addUsersPhase3($referral_id, $sponsor_id) {

        if( Phase3::where('referral_id',$referral_id)->where('sponsor_id',$sponsor_id)->exists()) {

        }else {
            Phase3::create(['referral_id'=>$referral_id, 'sponsor_id'=> $sponsor_id]);
        }         
    }

    // validate number of users in phase 4 - 10000)
    public function validateNumberOfUsersPhase4($sponsor_id) {

        $count = Phase4::where('sponsor_id', $sponsor_id)->count();
                            
        return $count;
        
    }

    // get all users in phase 4
    public function UsersPhase4($sponsor_id) {

        $users = Phase4::where('sponsor_id', $sponsor_id)->get();
                            
        return $users;
        
    }

    // insert into phase 4
    public function addUsersPhase4($referral_id, $sponsor_id) {

        if( Phase4::where('referral_id',$referral_id)->where('sponsor_id',$sponsor_id)->exists()) {

        }else {
            Phase4::create(['referral_id'=>$referral_id, 'sponsor_id'=> $sponsor_id]);
        }
                
    }

    // update user phase
    public function updateUserPhase($phase, $user_referralid) {

        User::where('referral_id', $user_referralid)->update(['phases_id' => $phase]);

    }

    // get user phase
    public function getMyUser($user_referralid) {

        return User::where('sponsor_id', $user_referralid)->get();

    }

    // stages contribution amount
    public function getPhaseAmount($user_id) {

        $amount = ContributionSetup::leftjoin('users','contribution_setups.phases','=','users.phases_id')
                ->where('users.id', $user_id)->first();

        return [
                'amount' => $amount->contributed_amount,
                'receive' => $amount->receive,
        ];
    }

     // stages investment amount
     public function getInvestmentAmount($user_id, $invest_id) {

        if(InvestmentSetup::leftjoin('users','investment_setups.id','=','users.investment_id')
        ->where('users.id', $user_id)->where('users.investment_id', $invest_id)->exists()){

            $amount = InvestmentSetup::leftjoin('users','investment_setups.id','=','users.investment_id')
            ->where('users.id', $user_id)->where('investment_setups.id', $invest_id)->first();

            return [
                    'min' => $amount->min_amount,
                    'max' => $amount->max_amount,
                    'per' => $amount->per,
            ];
        }
        else {

            return [
                'min' => 0.00,
                'max' => 0.00,
                'per' => 0,
        ];
        }
    }

        // total investment amount
        public function getTotalInvestmentAmount($user_id) {

            if(Investment::where('user_id', $user_id)->exists()){
    
                $amount = Investment::where('user_id', $user_id)->orderby('id','desc')->first();
    
                return [
                        'amount' => $amount->investment_amount,
                ];
            }
            else {
    
                return [
                    'amount' => 0.00,
                ];
            }
        }

    // stages loan amount
    public function getLoanAmount($phases_id) {

        if(LoanSetup::where('phases', $phases_id)->exists()){

            $amount = LoanSetup::where('phases', $phases_id)->first();

            return [
                    'loan_amount' => $amount->loan_amount,
                    'percentage' => $amount->percentage,
            ];
        }
        else {

            return [
                'loan_amount' => 0.00,
                'percentage' => 0,
            ];
        }
    }

    // stages loan amount
    public function getMyLoanAmount($phase, $user_id) {

        if(Loan::where('user_id', $user_id)->exists()) {

            if($phase <= 1) {

                return [
                    'loan_amount' => 0.00,
                    'status' => 'E001',
                ];

            }elseif($phase > 1){

                $amount = Loan::where('user_id', $user_id)->orderby('id','desc')->first();
                return [
                    
                        'loan_amount' => $amount->loan_amount,
                        'status' => $amount->status,
                ];
            }
        }
        else {

            return [
                'loan_amount' => 0.00,
                'status' => 0,
        ];

        }
    }

    // insert into bonuses
    public function addBonus($referral_id, $sponsor_id, $amount) {

        Bonus::create(['referral_id'=>$referral_id, 'sponsor_id'=> $sponsor_id, 'bonus_amount'=>$amount]);       
    }

    // get referral count
    public function getBonusCount($referral_id) {

           $referral_count =  Bonus::where('referral_id',$referral_id)->count();

           return $referral_count;
                
    }

    // get bonuses
    public function getBonus($referral_id) {

           $totalbonus =  Bonus::where('referral_id',$referral_id)->sum('bonus_amount');

           return $totalbonus;
                    
    }

    // get user phase
    public function getUserPhase($referral_id) {

        $user_phase = User::where('referral_id', $referral_id)->first();
        $phase_amount = ContributionSetup::where('phases', $user_phase->phases_id)->first();

        return 10000;
        
    }

    // insert into wallet
    public function addToWallet($userid, $transactiontype, $amount) {

        Wallet::create(['user_id'=> $userid, 'earning_type'=> $transactiontype, 'wallet_amount'=>$amount]);

    }

    // sum wallet amount
    public function sumWallet($userid) {

        $amount = Wallet::where('user_id', $userid)->sum('wallet_amount');

        return $amount;

    }

    // sum wallet amount
    public function withdrawalWallet($userid) {

        $amount = Wallet::where('user_id', $userid)->sum('withdrawal');

        return $amount;

    }

    // get login user role
    public function getUserRole($userid) {

        $role = User::where('id', $userid)->first();

        return $role->role_id;

    }

    // total contributions
     public function totalContributions() {

        $count = User::where('phases_id', 1)->where('role_id', 3)->count();
        $count2 = User::where('phases_id', 2)->where('role_id', 3)->count();
        $count3 = User::where('phases_id', 3)->where('role_id', 3)->count();
        $count4 = User::where('phases_id', 4)->where('role_id', 3)->count();

        $amount = $count * 10000;
        $amount2 = $count2 * 50000;
        $amount3 = $count3 * 300000;
        $amount4 = $count4 * 1500000;

        return $amount+$amount2+$amount3+$amount4;

    }

    // total contributors
    public function totalContributors() {

        $count = User::where('role_id', 3)->count();

        return $count;

    }

     // total referral
     public function totalReferral() {

        $count = Wallet::where('earning_type', 'REFERRAL_BONUS')->count();

        return $count;

    }

     // total referrals
     public function totalReferralAmount() {

        $amount = Wallet::where('earning_type', 'REFERRAL_BONUS')->sum('wallet_amount');

        return $amount;

    }

     // total investment
     public function totalInvestment() {

        $amount = Investment::sum('investment_amount');

        return $amount;

    }

    // total loans
    public function totalLoans() {

        $amount = Loan::sum('loan_amount');

        return $amount;

    }

    // total loans interest
    public function totalLoansInterest() {

        $amount = Loan::sum('interest');

        return $amount;

    }

    // list of contributors
    public function listContributor() {

        $users = User::where('role_id',3)
        ->leftjoin('phases','users.phases_id','=','phases.id')
        ->leftjoin('investments','users.investment_id','=','investments.id')
        ->leftjoin('bank_accounts','users.id','=','bank_accounts.user_id')
        ->select('*','users.id as uid','users.name as uname', 'users.phases_id as pid','bank_accounts.name as bankname')
        ->get();

        return $users;

    }

    // list of contributors for loan
    public function listContributorLoan() {

        $users = Loan::where('users.role_id',3)
        ->leftjoin('phases','loans.phases_id','=','phases.id')
        ->leftjoin('users','loans.user_id','=','users.id')
        ->select('*','loans.id as uid','loans.status as lstatus')
        ->get();

        return $users;

    }

     // list of investment
     public function listAllInvestments() {

        $users = Investment::where('users.role_id',3)
        ->leftjoin('phases','investments.phases_id','=','phases.id')
        ->leftjoin('users','investments.user_id','=','users.id')
        ->leftjoin('investment_setups','investments.investment_id','=','investment_setups.id')
        ->select('*','investments.id as uid')
        ->get();

        return $users;

    }

    // sum all wallet amount
    public function sumAllWallet() {

        $amount = Wallet::leftjoin('users','wallets.user_id','=','users.referral_id')
        ->get();

        return $amount;

    }

    // add bank details
    public function addBankDetails($userid, $name, $bankname, $accountnumber) {

        $bank = BankAccount::create(['user_id' => $userid, 'name' => $name, 'bank_name' => $bankname, 'account_number' => $accountnumber ]);

        if($bank) {

            return true;

        }else {

            return false;
        }
        

    }

     // genrate transaction code
     public function generateTransactionCode() {

        $code =  $this->generateCode(7);
        TransactionCode::create(['code' => $code]);

        return $code;

     }

     // function to generate transaction code
    public function generateCode($length) {
        $characters = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // list transaction code
    public function listTransactionCode() {

        return TransactionCode::all();

     }

     // create bank information
     public function createBank($userid, $name, $bank, $bankaccount) {

        return BankAccount::create(['user_id' =>$userid, 'name' => $name, 'bank_name'=> $bank, 'account_number'=> $bankaccount ]);

     }

     // list bank information
     public function listBank($userid) {

        return BankAccount::where('user_id',$userid)->get();

     }

    // validate email
     public function validateEmail($email) {

        if(EmailSignup::where('email', $email)->where('status',1)->exists()) {

            return 1;
        }
        else {
            return 0;
        }

     }
    
     // validate transaction code
     public function validateCode($code) {

        if(TransactionCode::where('code', $code)->where('status',1)->exists()) {

            return 1;
        }
        else {
            return 0;
        }

     }

    // update transaction code
    public function updateTransactionCode($transaction_code) {

        $resp = TransactionCode::where('code', $transaction_code)->update(['status' => 0 ]);

        if($resp){
            
            return 1;
        }
        else {
            return 0;
        }
    }

    // update email
    public function updateEmail($email) {

        $resp = EmailSignup::where('email', $email)->update(['status' => 0 ]);

        if($resp){
            
            return 1;
        }
        else {
            return 0;
        }
    }

    //activate users after payment is confirmed
    public function userStatus($uid) {

        $resp = User::where('id', $uid)->update(['status' => 1]);

        if($resp){
            
            return 1;
        }
        else {
            return 0;
        }
    }

    // contribute fund
    public function contribute($phases,$phaseid, $groupid, $amount,$referralid) {

        $user = User::where('group_id', $groupid)->where('phases_id', $phaseid)->orderby('order_id','asc')->get();

        foreach ($user as $key => $value) {

            $count = $this->countPerUserContribution($value->referral_id, $phaseid);

            if($count == 10) {
                Contribution::where('referral_id', $value->referral_id)->update(['receive_status'=>1]);
            }
            elseif ($count < 10) {
                Contribution::create(['user_id'=> Auth::user()->id, 'phases_id'=>$phaseid, 'referral_id'=>$value->referral_id, 'sponsor_id'=>$referralid,'contribution_amount'=>$amount, 'group_id'=>$groupid]);
                return 1;
            }
          
        }

    }

     // fetch all contributors to a user. check and update user phase
     public function fetchContributors($referralid, $phaseid) {

        $count_users = [];
        $sum = 0;

        if($phaseid == 1) {

            $count = $this->countContributors($referralid, $phaseid);
            if($count == 10) {
                $this->updateUserPhase(2,$referralid); 
            }

        }elseif($phaseid == 2) {
           
            $users = Contribution::where('referral_id', $referralid)->where('phases_id', 1)->get();

            foreach ($users as $key => $value) {
                $count = $this->countContributors($value->sponsor_id, $value->phases_id);
                if ($count == 10) {
                    array_push($count_users, $count);
                }
            }
                                
            if(array_sum($count_users) == 100) {
                $this->updateUserPhase(3,$referralid); 
            }
        }
        
    }

    // count cntributors
    public function countContributors($referralid, $phaseid) {
        $count = Contribution::where('referral_id', $referralid)->where('phases_id', $phaseid)->count();             
        return $count;
    }

    // determine the next user to earn contribution
    public function nextToEarn($userid, $phaseid) {

        $user = User::where('id', $userid)->first();

            $count = $this->countPerUserContribution($user->referral_id, $phaseid);

            if($count == 10) {

                return [

                    "status" => "You are qualified to earn",
                    "status_code" => "2",
                ];
            }
            elseif (( $count > 5) && ($count < 10)) {
               
                return [

                    "status" => "You are next inline to earn, please be patient.",
                    "status_code" => "1",
                ];

            }
            elseif($count <= 5) {
               
                return [

                    "status" => "You are inline to earn, please be patient.",
                    "status_code" => "0",
                ];

            }

    }

    //count per user contributions
    public function countPerUserContribution($referralid, $phaseid) {
        $count = Contribution::where('referral_id', $referralid)->where('phases_id', $phaseid)->count();
        return $count;
    }

    // check if record exists in contribution table
    public function isContributionExists($userid,$phaseid, $amount, $referralid) {

        $res = Contribution::where('user_id', $userid)->where('phases_id',$phaseid)->where('sponsor_id',$referralid)->where('contribution_amount',$amount)->exists();
        if($res) { return 1; }
    }

    //confirm users after payment
    public function confirmContributionStatus($uid) {

        $resp = Contribution::where('id', $uid)->update(['status' => 1]);

        if($resp){
            return 1;
        }
        else {
            return 0;
        }
    }

      //confirm users loan request
      public function confirmLoanRequest($uid) {

        $resp = Loan::where('id', $uid)->update(['status' => 1]);

        if($resp){
            
            return 1;
        }
        else {
            return 0;
        }
    }

    // recursive function
    // public function callUsers($user_referral_id) {

    //     if( User::where('referral_id', $user_referral_id)->where('sponsor_id', null)->exists() ) {

    //         $user = User::where('sponsor_id', $user_referral_id)->get();

    //         foreach( $user as $key => $value ) {
    //             ContributionCheck ::create(['referral_id' => $value->referral_id, 'sponsor_id' => $value->sponsor_id ]);
    //             $this->callUsers($value->referral_id);
    //         }

    //         return ContributionCheck::all();

    //     } else {

    //         $user = User::where('referral_id', $user_referral_id)->first();
            
    //             while(( $user->referral_id != null) && ($user->sponsor_id == null)) {
                    
    //                break;
    //             } 
    //             ContributionCheck::create(['referral_id' => $user->referral_id, 'sponsor_id' => $user->sponsor_id]);
    //             $this->callUsers($user->sponsor_id);
               
    //         }
            
    //         return ContributionCheck::all();  
    // }


}
