<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::post('/payment-return-first-call', 'GujratPayController@payment_return_handle_gujrat');
Route::post('/payment-gujrat-verification', 'GujratPayController@payment_gujrat_verification');
Route::get('pay-ver', 'GujratPayController@payment_verification_Gujrat');
Route::get('pay-ver-hp', 'HpPaymentController@payment_verification_hp');
Route::get('pay-ver-tri', 'TriPaymentController@payment_verification_tri');
Route::get('pay-ver-meg', 'MegPaymentController@payment_verification_meg');
Route::get('pay-ver-nag', 'MegPaymentController@payment_verification_nag');
Route::get('pay-ver-kar', 'KarPaymentController@payment_verification_kar');
Route::get('pay-ver-mp', 'MpPaymentController@payment_verification_mp');

///////////////////////////////////////////////////////////////////////////////////////////////

###################################### Payment Response West Bengol ######################
Route::any('/payment-return-handle-wb', 'WbPaymentController@payment_return_handle');
Route::any('/payment-verification-wb', 'WbPaymentController@payment_verification');

###################################### Payment Response Pudduchery ##############################
Route::any('/payment-return-handle-pd', 'PdPaymentController@payment_return_handle');
Route::any('/payment-verification-pd', 'PdPaymentController@payment_verification');

###################################### Payment Response Kerla ##############################
Route::any('/payment-return-handle-ke', 'KePaymentController@payment_return_handle');
Route::any('/payment-verification-ke', 'KeWbPaymentController@payment_verification');

###################################### Payment Response Tamilnadu ##############################
Route::any('/payment-return-handle-tm', 'TmPaymentController@payment_return_handle');
Route::any('/payment-verification-tm', 'TmPaymentController@payment_verification');

###################################### Payment Response Aasam ##############################
Route::any('/payment-return-handle-aa', 'AaPaymentController@payment_return_handle');
Route::any('/payment-verification-aa', 'AaPaymentController@payment_verification');
Route::get('/payment-verification-aa-cin', 'AaPaymentController@payment_verification_cin');

###################################### Payment Response Uttar Pradesh ##############################
Route::any('/payment-return-handle-up', 'UpPaymentController@payment_return_handle');
Route::any('/payment-verification-up', 'UpPaymentController@payment_verification');

###################################### Payment Response Manipur ##############################
Route::any('/payment-return-handle-man', 'ManipurPaymentController@payment_return_handle');
Route::any('/payment-verification-man', 'ManipurPaymentController@payment_verification');


###################################### Payment Response Punjab ##############################
Route::any('/payment-return-handle-pun', 'PunPaymentController@payment_return_handle');
Route::any('/payment-verification-pun', 'PunPaymentController@payment_verification');

###################################### Payment Response Goa ##############################
Route::any('/payment-return-handle-goa', 'GoaPaymentController@payment_return_handle');
Route::any('/payment-verification-goa', 'GoaPaymentController@payment_verification');

###################################### Payment Response Uttrakhand ##############################
Route::any('/payment-return-handle-uk', 'UkPaymentController@payment_return_handle');
Route::any('/payment-verification-uk', 'UkPaymentController@payment_verification');

###################################### Payment Response Himachal Pradesh ##############################
Route::any('/payment-return-handle-hp', 'HpPaymentController@payment_return_handle');
Route::any('/payment-verification-hp', 'HpPaymentController@payment_verification');


###################################### Payment Response Tripura ##############################
Route::any('/payment-return-handle-tri', 'TriPaymentController@payment_return_handle');
Route::any('/payment-verification-tri', 'TriPaymentController@payment_verification');
Route::get('/payment-verification-tri-print/{GRN}', 'TriPaymentController@GenerateURLCode');

###################################### Payment Response Meghalaya ##############################
Route::any('/payment-return-handle-meg', 'MegPaymentController@payment_return_handle');
Route::any('/payment-verification-meg', 'MegPaymentController@payment_verification');
Route::get('/payment-verification-meg-cin', 'MegPaymentController@payment_verification_cin');

###################################### Payment Response Nagaland ##############################
Route::any('/payment-return-handle-nag', 'NagPaymentController@payment_return_handle');
Route::any('/payment-verification-nag', 'NagPaymentController@payment_verification');

###################################### Payment Response Karnataka ##############################
Route::any('/payment-return-handle-kar', 'KarPaymentController@payment_return_handle');
Route::any('/payment-verification-kar', 'KarPaymentController@payment_verification');

###################################### Payment Response Madhya Pradesh ##############################
Route::any('/payment-return-handle-mp', 'MpPaymentController@payment_return_handle');
Route::any('/payment-verification-mp', 'MpPaymentController@payment_verification');

###################################### Payment Response Odisha ##############################
Route::any('/payment-return-handle-odi', 'OdiPaymentController@payment_return_handle');
Route::any('/payment-verification-odi', 'OdiPaymentController@double_verification')->name('payment-verification-odi');
Route::any('/payment-scroll-recieved', 'OdiPaymentController@scroll_recieved');


Route::get('locale/{locale}', function ($locale) {
  Session::put('locale', $locale);
  return redirect()->back();
});

Route::any('/checkdata', 'PaymentController@checkdata');
Route::get('search-by-epic-cdac-new', 'Admin\Common\CommonController@search_by_epic_cdac');
Route::any('/payment-return-handle', 'PaymentController@payment_return_handle');
Route::any('/payment-verification', 'PaymentController@payment_verification');

Route::get('locale/{locale}', function ($locale) {
  Session::put('locale', $locale);
  return redirect()->back();
});

Route::post('/change-database', 'Admin\AdminController@change_database');
Route::get('/change-database', 'Admin\AdminController@change_database');
Route::get('/clear-sleep', 'Admin\Common\CommonController@index');
Route::get('/profile/pin', 'Admin\Profile\TwoStepPinController@index');
Route::post('/profile/pin/update_via_web', 'Admin\Profile\TwoStepPinController@update_via_web');
Route::post('/profile/pin/update', 'Admin\Profile\TwoStepPinController@update');
Route::post('/profile/old_password/update', 'Admin\Profile\PasswordController@update_password');
Route::get('/profile/password', 'Admin\Profile\PasswordController@index');
Route::post('/profile/password/update', 'Admin\Profile\PasswordController@update');

Route::get('/candidate-test-login', 'Admin\AdminController@candidate_login');


//    start online nomination 
Route::get('search-by-epic-cdac', 'Admin\Common\CommonController@search_by_epic_cdac');
Route::group(['prefix' => 'common', 'middleware' => ['auth:admin', 'auth']], function () {
  Route::post('send-otp', 'Admin\Common\CommonController@send_otp');
  Route::post('verify_otp', 'Admin\Common\CommonController@verify_otp');
});


Route::get('/clear-cache', function () {
  Artisan::call('cache:clear');
  Artisan::call('view:clear');
  Artisan::call('config:cache');
  return "Cache is cleared";
});


//Online Nomination ROUTES STARTS 
include_once('onlineacnomination.php');
//Online Nomination ROUTES ENDS

Route::get('/db-connection', 'Db_Connectivity\ConnectionDb@index');
//sachchidanand   turnout  out of login
// Route::get('/change-database', function () {
//   return redirect("login");
// });


//Route::post('/change-database', 'UserController@change_database');
//Route::get('/change-database', 'UserController@change_database');

// Route::post('/change-database', 'Admin\AdminController@change_database');
// Route::get('/change-database', 'Admin\AdminController@change_database');


Route::get('/clear-sleep', 'Admin\Common\CommonController@index');

Auth::routes();
Route::get('/', 'HomeController@index');

//ROUTES FOR CANDIDATE LOGIN STARTS
Route::get('/get_captcha/{config?}', function (\Mews\Captcha\Captcha $captcha, $config = 'default') {
  //return $captcha->src($config);
})->middleware('clean_url');

//CANDIDATE LOGIN ROUTS STARTS
Route::POST('/user-postlogin', 'UserController@postlogin');
//otp page show
Route::get('/mobileotp/{mobile}', 'UserController@mobileotp')->name('otp');
//CUSTOM LOGIN
Route::post('/customlogin', 'UserController@customlogin')->middleware('EncryptDecrypt');
//Resend Mobile otp 
Route::post('resendotp', 'UserController@resendotp'); 
Route::post('fgpassword', 'UserController@Forgotpassword');
Route::post('setpassword', 'UserController@setpassword');
Route::post('verifyotpfirst', 'UserController@Verifyotpfirst');

Route::get('/refresh_captcha', 'Admin\HomeController1@refreshCaptcha');

Route::post('/RemoveDummyUser', 'HomeController@RemoveDummyUser');
Route::get('/deleteuser', 'HomeController@deleteuser');

//home
Route::get('/home', 'HomeController@userhome');

//Nomination candidate routes start
Route::get('/candidate-login', 'TempUserController@dummy_user_login');
Route::POST('/candidate-postlogin', 'TempUserController@postlogin');
Route::get('/candidate-mobileotp/{mobile}', 'TempUserController@mobileotp')->name('otp');
Route::post('/candidate-customlogin', 'TempUserController@customlogin');
Route::post('candidate-resendotp', 'TempUserController@resendotp');

Route::get('/candidate-home', 'TempHomeController@userhome');

Route::get('/candidate-roletype', 'TempHomeController@roletype');
Route::post('/candidate-permissionrole', 'TempHomeController@permissionrole');
Route::get('/profile', 'TempHomeController@profile');
Route::get('candidate-logout', 'TempHomeController@logout');

Route::group(['middleware' => ['auth:web', 'auth', 'usersession']], function () {
  Route::get('dashboard-nomination-new', 'TempHomeController@dashboard');
  Route::get('/first-login-user-view', 'TempHomeController@first_login_user_view');
});
//aditya chaturvedi
Route::group(['middleware' => 'usersession'], function () {   // check session here  usersession sachchida

  Route::group(['prefix' => '', 'as' => '', 'middleware' => ['auth:web', 'auth']], function () {
    Route::get('permission', ['as' => 'permission', 'uses' => 'Politicalparty\permissionController@index']);
    Route::get('total', ['as' => 'total', 'uses' => 'Politicalparty\permissionController@totalacceptpermission']);
    Route::get('rejected', ['as' => 'rejected', 'uses' => 'Politicalparty\permissionController@rejectedpermission']);
    Route::get('pending', ['as' => 'pending', 'uses' => 'Politicalparty\permissionController@pendingpermission']);
    Route::get('applied', ['as' => 'applied', 'uses' => 'Politicalparty\permissionController@appliedpermission']);
    Route::get('detaildata/{data}', 'Politicalparty\permissionController@detaildata');
    Route::get('view&update', 'Politicalparty\permissionController@viewupdate');
    Route::get('district/{state_id}', 'Politicalparty\permissionController@getdistrict');
    Route::get('ac/{state_id}/{dis_id}', 'Politicalparty\permissionController@getac');
    Route::get('policestation/{state_id}/{ac_id}', 'Politicalparty\permissionController@getpolicestation');
    Route::get('location/{state_id}/{dis_id}/{ac_id}', 'Politicalparty\permissionController@getlocation');
    Route::get('editcreate/{insert_id}', 'Politicalparty\permissionController@preview');

    Route::get('create', 'Politicalparty\permissionController@create');
    Route::post('getSelectDetails', 'Politicalparty\permissionController@getSelectDetails');

    Route::post('Applypermission', 'Politicalparty\permissionController@store');
    Route::Post('update', 'Politicalparty\permissionController@update');
    // Route::get('Applypermission', 'Politicalparty\permissionController@check');//duplicate
    Route::get('Receiptper/{id}', 'Politicalparty\permissionController@getprintreciept'); //aditya

    Route::get('politicalparty/getlatlong', 'Politicalparty\permissionController@getlatlongs');
    Route::get('mapindex', 'Politicalparty\mapController@mapindex');

    Route::get('/politicalparty/getAcs', 'Politicalparty\permissionController@getACList');
    Route::get('politicalparty/getlocations', 'Politicalparty\permissionController@getlocationList');
 

  
    Route::get('/update profile', 'Politicalparty\permissionController@updateprofile');
    Route::get('candidatelogout', 'HomeController@logout');
    // route end by aditya
    // route start by aditya
    Route::get('/roletype', 'Politicalparty\permissionController@roletype');
    Route::post('/permissionrole', 'Politicalparty\permissionController@permissionrole');
    Route::get('/profile', 'Politicalparty\permissionController@profile');
    Route::get('/getDistrictsval', 'Politicalparty\permissionController@getDistrictsval');
    Route::get('/getACListsval', 'Politicalparty\permissionController@getACListsval');
    Route::post('/addprofile', 'Politicalparty\permissionController@addprofile');
    Route::get('/getpermissiondetails/{id}/{status}/{location}', 'Politicalparty\permissionController@getpermissiondetails');

    // Extra Pages for Candidate 
    Route::get('/Privacy Policy', 'Politicalparty\permissionController@Privacy');
    Route::get('/Content Copyright', 'Politicalparty\permissionController@Content');
    Route::get('/Terms Condition', 'Politicalparty\permissionController@Terms');
    Route::get('/Abbreviations', 'Politicalparty\permissionController@Abbreviations');

    Route::get('/permissiondistrict/{st}', 'Politicalparty\permissionController@permissiondistrict');
    Route::get('/permissionAC/{stateID}/{districtID}', 'Politicalparty\permissionController@permissionAC');
    Route::get('/policeAC/{stateID}/{acID}', 'Politicalparty\permissionController@policeAC');
    Route::get('/Download_Permission/{status}/{id}/{location}', 'Politicalparty\permissionController@downloadprint');
    Route::get('/getpc/{sid}/{acic}/{distno}', 'Politicalparty\permissionController@getpconac');
    Route::get('/datevalidation/{StateId}', 'Politicalparty\permissionController@statedatevalidation');

    
    Route::post('/getrole_iddetails', 'Politicalparty\permissionController@getrole_iddetails');
    Route::get('/getpolldayss/{std_code}', 'Politicalparty\permissionController@getpolldayss');
    Route::get('/getdttconac/{sid}/{distno}', 'Politicalparty\permissionController@getdttconac');
    Route::get('pdf/{filename}', 'Politicalparty\permissionController@Downloadspdf')->name('view-pdf');

    //aditya chaturvedi
  });
});