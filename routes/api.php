<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JWTController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
    
    Route::post('signup-email', 'RegisterController@signUpEmail');
    Route::post('register', 'RegisterController@register');
    Route::post('login', 'UserController@authenticate');
     //get users
   // Route::get('list-users', 'UserController@getUsers');

    Route::group(['middleware' => ['jwt.verify']], function() {

        // phases routes
        Route::post('setup-phases', 'PhasesController@setupPhases');
        Route::get('list-phases', 'PhasesController@listPhases');

        // contribution routes
        Route::post('setup-contributions', 'ContributionController@setupContribution');
        Route::get('list-contributions', 'ContributionController@listContributions');

        Route::post('make-contributions', 'ContributionController@makeContributions');
        Route::get('list-mycontributions', 'ContributionController@listMyContributions');

         // investment routes
        Route::post('setup-investments', 'InvestmentController@setupInvestment');
        Route::get('list-investments', 'InvestmentController@listInvestments');

        Route::post('make-investments', 'InvestmentController@makeInvestments');
        Route::get('list-myinvestments', 'InvestmentController@listMyInvestments');
        Route::post('choose-investments', 'InvestmentController@chooseInvestments');

        // users and admin dashboard
        Route::get('dashboard', 'HomeController@UserDashboard');
        Route::get('admin-dashboard', 'HomeController@AdminDashboard');

         // loan routes
         Route::post('setup-loan', 'LoanController@setupLoan');
         Route::get('list-loan', 'LoanController@listLoans');
 
         Route::post('apply-loan', 'LoanController@applyLoan');
         Route::get('list-my-loan', 'LoanController@listMyLoan');

        // confirm user loan
        Route::post('confirm-user-loan',    'LoanController@confirmUserLoan');

         // wallet routes
         Route::get('list-my-wallets', 'WalletController@listWallets');
         Route::get('wallets', 'WalletController@Wallet');

         // list contributors
         Route::get('list-contributors', 'HomeController@listContributors');

         // list contributors loan
        Route::get('list-loans', 'HomeController@listContributorsLoans');

        // list investment
        Route::get('list-all-investments', 'HomeController@listAllInvestment');

         // list wallet
        Route::get('list-wallets', 'HomeController@sumAllWallets');

        // generate code
        Route::post('generate-transaction-code', 'GenerateCodeController@generateTranCode');
        Route::get('list-transaction-code', 'GenerateCodeController@listTranCode');

        // banke
        Route::post('setup-bank', 'BankController@addBank');
        Route::get('list-bank-details', 'BankController@listBankInfo');

        // confirm user payment
        Route::post('confirm-user-payment', 'RegisterController@confirmUserPayment');

        // contribute
        Route::post('contribute-fund', 'HomeController@contributeFund');

        //list my contribution
        Route::get('my-contributions', 'HomeController@listMyContributions');
        Route::get('all-contributions', 'HomeController@listAllContributions');

        // confirm contribution payment
        Route::post('confirm-contribution-payment', 'HomeController@confirmContributionrPayment');

        // claim contribution
        Route::get('claim-fund', 'HomeController@claimContribution');


    });
