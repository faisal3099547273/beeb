<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/new', function () {
    return "OFF";
});
Route::namespace('Api')->name('api.')->group(function () {
//    Route::prefix('merchant')->middleware('merchant.auth:sanctum')->group(function () {
    Route::prefix('merchant')->middleware('auth:sanctum')->group(function () {
       Route::get('api/authorization', 'UserController@authorization')->name('authorization');
     Route::post('marchent/product', 'UserController@ApiProductstore');
            Route::post('category', 'UserController@varietyCreate');
            Route::get('product-bids/{id}', 'UserController@getProductBidsApi');
            Route::post('bid-winner', 'UserController@postBidWinnerApi');
            Route::get('bids', 'UserController@getBidsApi');
            Route::get('all-vehicle', 'UserController@getMerchantvehicle');
            Route::get('all-product', 'UserController@getMerchantProducts');
            Route::post('vehicle-product', 'UserController@Vehiclestore');
            Route::post('vehicle-update/{id}', 'UserController@UpdateVehiclestore');
});
});
Route::namespace('Api')->name('api.')->group(function(){
	Route::get('general-setting','BasicController@generalSetting');
	Route::get('unauthenticate','BasicController@unauthenticate')->name('unauthenticate');
	Route::get('languages','BasicController@languages');
	Route::get('language-data/{code}','BasicController@languageData');

	Route::namespace('Auth')->group(function(){
		Route::post('login', 'LoginController@login');
		Route::post('register', 'RegisterController@register');
		Route::post('seller/register', 'RegisterController@createMerchant');
		
		

	    Route::post('password/email', 'ForgotPasswordController@sendResetCodeEmail');
	    Route::post('password/verify-code', 'ForgotPasswordController@verifyCode');

	    Route::post('password/reset', 'ResetPasswordController@reset');
	});
// routes/api.php







//                             My Routes -----------------------------------



Route::post('languages','RegisterController@sellerregister');




















Route::get('/location', 'ProductController@show');


	Route::middleware('auth.api:sanctum')->name('user.')->prefix('user')->group(function(){
		Route::get('logout', 'Auth\LoginController@logout');
		Route::get('authorization', 'AuthorizationController@authorization')->name('authorization');
	    Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
	    Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
	    Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
	    Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

	    Route::middleware(['checkStatusApi'])->group(function(){
	    	Route::get('dashboard',function(){
	    		return auth()->user();
	    	});

            Route::post('profile-setting', 'UserController@submitProfile');
            Route::post('change-password', 'UserController@submitPassword');

            // Withdraw
            Route::get('withdraw/methods', 'UserController@withdrawMethods');
            Route::post('withdraw/store', 'UserController@withdrawStore');
            Route::post('withdraw/confirm', 'UserController@withdrawConfirm');
            Route::get('withdraw/history', 'UserController@withdrawLog');


            // Deposit
            Route::get('deposit/methods', 'PaymentController@depositMethods');
            Route::post('deposit/insert', 'PaymentController@depositInsert');
            Route::get('deposit/confirm', 'PaymentController@depositConfirm');

            Route::get('deposit/manual', 'PaymentController@manualDepositConfirm');
            Route::post('deposit/manual', 'PaymentController@manualDepositUpdate');

            Route::get('deposit/history', 'UserController@depositHistory');
           

            Route::get('transactions', 'UserController@transactions');

	    });
	});
});




Route::namespace('Merchant')->prefix('merchant')->name('merchant.')->group(function(){
    Route::get('vehicle/all', 'ProductController@Apiindex');
 
    Route::namespace('Auth')->group(function(){
        Route::post('seller/login', 'LoginController@Sellerlogin');
    });

    Route::middleware('merchant')->group(function(){

        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware('merchant.checkStatus')->group(function(){
            Route::get('dashboard', 'MerchantController@dashboard')->name('dashboard');
            Route::post('store-product', 'ProductController@ApiproductStore');
        });

    });
});