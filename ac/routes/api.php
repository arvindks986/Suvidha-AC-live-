<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\suvidhaapi\AuthenticationController;
use App\Http\Controllers\API\suvidhaapi\PermissionApiController;
use App\Http\Controllers\API\suvidhaapi\NominationApiController;
use App\Http\Controllers\API\vv1\AuthenticationController as V1AuthenticationController;
use App\Http\Controllers\API\vv1\PermissionApiController as V1PermissionApiController;
use App\Http\Controllers\API\vv1\NominationApiController as V1NominationApiController;

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

//booth app
Route::group(['prefix' => 'booth-app'], function () {
	Route::post('login', 'API\BoothApp\AuthController@login');
	Route::post('verify-otp', 'API\BoothApp\AuthController@verify_otp');
	Route::post('update-ps-details', 'API\BoothApp\AuthController@update_ps_details');
	Route::post('logout', 'API\BoothApp\AuthController@logout');
	Route::post('GetTurnout', 'API\BoothApp\AuthController@turnout_data');
	Route::middleware('auth:boothapp')->group(function () {

		Route::post('profile/info', 'API\BoothApp\AuthController@profile_info');
	});
});
//end booth app
//aditya
Route::group(['prefix' => 'VatAC'], function () {
	Route::post('login', [AuthenticationController::class, 'login']);
	Route::post('verifyuser', [AuthenticationController::class, 'verifyuser']);
	Route::post('verify-otp', [AuthenticationController::class, 'verifyOtp']);
	Route::post('resendOtp', [AuthenticationController::class, 'resendOtp']);
	Route::post('verify-pwd', [AuthenticationController::class, 'verifyPwd']);
	Route::post('singupmobile', [AuthenticationController::class, 'singupmobile']);
	Route::post('singuppsssword', [AuthenticationController::class, 'singuppsssword']);
	Route::group(['middleware' => 'suvidhaapiauth'], function () {
		Route::post('logout', [AuthenticationController::class, 'logout']);
		Route::post('getparty', [PermissionApiController::class, 'getparty']);
		Route::post('get_state', [PermissionApiController::class, 'get_state']);
		Route::post('getdist', [PermissionApiController::class, 'getdist']);
		Route::post('getAc', [PermissionApiController::class, 'getAc']);
		Route::post('get_police_station', [PermissionApiController::class, 'get_police_station']);
		Route::post('get_location', [PermissionApiController::class, 'get_location']);
		Route::post('user_profile', [PermissionApiController::class, 'profile']);
		Route::post('addprofile', [PermissionApiController::class, 'addprofile']);
		Route::post('updateprofile', [PermissionApiController::class, 'updateprofile']);
		Route::post('updateData', [PermissionApiController::class, 'update']);
		Route::post('getpermissiondata', [PermissionApiController::class, 'getpermissiondata']);
		Route::post('getSelectpermission_doc', [PermissionApiController::class, 'getSelectpermission_doc']);
		Route::post('getpolldays', [PermissionApiController::class, 'getpolldays']);
		Route::post('permisson_apply', [PermissionApiController::class, 'store']);
		Route::post('AllPermissionRequest', [PermissionApiController::class, 'AllPermissionRequest']);
		Route::post('getPermissionDetails', [PermissionApiController::class, 'getPermissionDetails']);
		Route::post('getnominationlist', [NominationApiController::class, 'getnominationlist']);
		Route::post('nominationstatus', [NominationApiController::class, 'nominationstatus']);
		
	});
});

Route::group(['prefix' => 'v5' ,'middleware' => 'Encapp'], function () {
	Route::post('login', [V1AuthenticationController::class, 'login']);
	Route::post('verifyuser', [V1AuthenticationController::class, 'verifyuser']);
	Route::post('verify-otp', [V1AuthenticationController::class, 'verifyOtp']);
	Route::post('resendOtp', [V1AuthenticationController::class, 'resendOtp']);
	Route::post('verify-pwd', [V1AuthenticationController::class, 'verifyPwd']);
	Route::post('singupmobile', [V1AuthenticationController::class, 'singupmobile']);
	Route::post('singuppsssword', [V1AuthenticationController::class, 'singuppsssword']);

	Route::group(['middleware' => 'suvidhaapiauth'], function () {
		Route::post('logout', [V1AuthenticationController::class, 'logout']);
		Route::post('getparty', [V1PermissionApiController::class, 'getparty']);
		Route::post('get_state', [V1PermissionApiController::class, 'get_state']);
		Route::post('getdist', [V1PermissionApiController::class, 'getdist']);
		Route::post('getAc', [V1PermissionApiController::class, 'getAc']);
		Route::post('get_police_station', [V1PermissionApiController::class, 'get_police_station']);
		Route::post('get_location', [V1PermissionApiController::class, 'get_location']);
		Route::post('user_profile', [V1PermissionApiController::class, 'profile']);
		Route::post('addprofile', [V1PermissionApiController::class, 'addprofile']);
		Route::post('updateprofile', [V1PermissionApiController::class, 'updateprofile']);
		Route::post('updateData', [V1PermissionApiController::class, 'update']);
		Route::post('getpermissiondata', [V1PermissionApiController::class, 'getpermissiondata']);
		Route::post('getSelectpermission_doc', [V1PermissionApiController::class, 'getSelectpermission_doc']);
		Route::post('getpolldays', [V1PermissionApiController::class, 'getpolldays']);
		Route::post('permisson_apply', [V1PermissionApiController::class, 'store']);
		Route::post('AllPermissionRequest', [V1PermissionApiController::class, 'AllPermissionRequest']);
		Route::post('getPermissionDetails', [V1PermissionApiController::class, 'getPermissionDetails']);
		Route::post('getnominationlist', [V1NominationApiController::class, 'getnominationlist']);
		Route::post('nominationstatus', [V1NominationApiController::class, 'nominationstatus']);
		
	});
});
//end aditya
###################################### Common API ###########################################
Route::get('getElectionByDate', 'API\CommonApiController@getElectionByDate');
Route::get('getElectionByDatePC', 'API\CommonApiController@getElectionByDatePC');
Route::get('getactiveelction', 'API\CommonApiController@getactiveelction');

Route::group(['middleware' => 'auth:api'], function () {
	//Route::post('nominationstatus', 'API\UserController@nominationstatus');
});
Route::group(['middleware' => ['Encrypt','XSS']], function () {
	//Candidate App
	Route::post('userlogin', 'API\UsersController@login');
	Route::post('verifyotp', 'API\UsersController@verifyOtp');
	Route::post('nominationlisting', 'API\UsersController@nominationlisting');
	Route::post('nominationstatus', 'API\UsersController@nominationstatus');
	Route::post('permissionlistview', 'API\UsersController@permissionlistview');
	Route::post('permissionpreview', 'API\UsersController@permissionpreview');
	Route::post('logout', 'API\UsersController@logout');
	//Nodal Candidates App
	Route::post('nodallogin', 'API\NodalLoginApi@login');
	Route::post('nodalverifyotp', 'API\NodalLoginApi@verifyOtp');
	Route::post('nodallogout', 'API\NodalLoginApi@logout');
	Route::post('permissionlist', 'API\NodalLoginApi@permissionlist');
	Route::post('permissionupdate', 'API\NodalLoginApi@permissionupdate');
	Route::post('notificationlist', 'API\NodalLoginApi@notificationlist');
	Route::post('clearnotificationlist', 'API\NodalLoginApi@clearnotificationlist');
	Route::get('nodalappversion', 'API\NodalLoginApi@appversion');
	//Route::get('getElectionByDate', 'API\NodalLoginApiController@getElectionByDate');
});

Route::post('permisssion_Search', 'API\NodalLoginApi@permisssion_Search');

########################New API Candidate##################################

Route::post('getaclisting', 'API\CandidateController@getAcListing');
Route::post('getcountingac', 'API\CandidateController@getCountingAc');
Route::post('getstate', 'API\CandidateController@getStateByPhase');
Route::get('getelectiontypedetails', 'API\CandidateController@getElectionTypeDetails');
Route::get('getstatus', 'API\CandidateController@getStatus');
Route::post('getcandidatelist', 'API\CandidateController@getCandidateList');
Route::post('getcandidatedetails', 'API\CandidateController@getCandidateDetails');
Route::post('officerlogin', 'API\OfficerController@authenticate');
Route::post('offlogout', 'API\OfficerController@logout');
Route::post('officelogout', 'API\OfficerController@officerlogout');
Route::post('getelectionschedul', 'API\CandidateController@getelectionschedul');

Route::post('officerlogin_central', 'API\OfficerController@authenticate_central');
Route::post('officelogout_central', 'API\OfficerController@officerlogout_central');

Route::post('sendotp', 'API\NodalLoginApi@sendotp1');

// Chanderkant Ji #########################3#####
Route::post('Home_PT', 'API\VtController@HomePt'); /// For Home Page
Route::post('PCwise_PT', 'API\VtController@PcwisePt'); /// For Summary Report of All PC or PC of selected State
Route::post('DistrictWise_PT', 'API\VtController@DistwisePt'); /// For Summary Report of All PC or PC of selected State
//Route::post('Distwise_PT', 'API\VtController@DistwisePt'); /// For Summary Report of All District or District of selected State
Route::post('PC2ACwise_PT', 'API\VtController@PC2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
Route::post('DIST2ACwise_PT', 'API\VtController@Dist2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
Route::post('AC_PT', 'API\VtController@AcPt'); /// For Current Poll turnout status of selected AC
Route::post('State_PhaseWise', 'API\VtController@PhaseWiseState'); /// For Poll turnout status of all States
Route::post('PC_PhaseWise', 'API\VtController@PhaseWisePC'); /// For Poll turnout status of all PC in state
Route::post('AC_PhaseWise', 'API\VtController@PhaseWiseAC'); /// For Poll turnout status of all AC in PC
Route::post('FinalHome_PhaseWise', 'API\VtController@FinalHome'); /// For Poll turnout status of Single AC

###### FILTERS CALLS
Route::post('ElectionType_PT', 'API\VtController@ElectionTypePt'); /// For List of available election types
Route::post('PhaseList_PT', 'API\VtController@PhaseListPt'); /// For List of phases in selected election type
Route::post('StateList_PT', 'API\VtController@StateListingPT'); /// For List of All Polling States in selected Phase and Election
Route::post('PcList_PT', 'API\VtController@PcListingPT'); /// For List of All Polling PC in selected State
Route::post('PCWiseAcList_PT', 'API\VtController@PC2AcListingPT'); /// For List of All Polling AC in selected PC
Route::post('DistWiseAcList_PT', 'API\VtController@Dist2AcListingPT'); /// For List of All Polling AC in selected District
Route::post('DistrictList_PT', 'API\VtController@DistListingPT'); /// For List of All Polling District in selected State
Route::post('GetPollDate', 'API\VtController@PollDate'); /// For List of All Polling District in selected State
############ Routs for PWD app By CK ###############
Route::post('AddAbsentee', 'API\PwdController@AbsenteeAdd'); /// For adding Absentee entry
Route::post('Form12D', 'API\PwdController@D12'); /// For adding Absentee entry VH
############Routes for BoothApp DashBoard in Suvidha Admin by CK #######################
Route::post('ba_Schedule', 'API\ba_SuvidhaAdminController@ba_get_schedule'); //BoothSchedule
Route::post('ba_PollData', 'API\ba_SuvidhaAdminController@ba_get_polldata'); //PollDayData
Route::post('ba_PollTurnout', 'API\ba_SuvidhaAdminController@ba_get_pollturnout'); //BoothPollTurnout
Route::post('ba_PollResult', 'API\ba_SuvidhaAdminController@ba_get_pollresult'); //PollDResultData
Route::post('ba_PrePollData', 'API\ba_SuvidhaAdminController@ba_get_prepolldata'); //PrePollData
Route::post('VHA_TurnOut', 'API\ba_SuvidhaAdminController@vha_pollturnout'); //PrePollData
Route::post('AC_PT', 'API\ba_SuvidhaAdminController@AcPt'); //PrePollData
##########END OF Routes for BoothApp DashBoard in Suvidha Admin by CK #######################
##########Routes Encore Admin by Neera #######################
Route::post('permissionlisting', 'API\PermissionEncoreAdminAPI@permissionlisting');
Route::post('getpermissiondetails', 'API\PermissionEncoreAdminAPI@getpermissiondetails');
##########END Routes Encore Admin by Neera ###################

Route::post('list-indexcard-finalized', 'API\IndexcardController@listIndexcardFinalized');
Route::post('indexcard', 'API\IndexcardController@indexcard');



Route::group(['prefix' => 'vtpt'], function () {
	Route::post('login', 'Vtpt\OfficerVtpt@login');
	Route::post('otp', 'Vtpt\OfficerVtpt@verify_otp');
	Route::post('logout', 'Vtpt\OfficerVtpt@logout');
});

// Matdan App

    Route::group(['prefix' => 'matdan','middleware' => ['XSS']], function(){
		
		Route::post('loginofficer', 'API\MatdanController@login');
		Route::post('verifyotp', 'API\MatdanController@verifyOtp');
		Route::post('contacts', 'API\MatdanController@contactslist');
		Route::post('storeform12', 'API\MatdanController@storeform12');
		Route::get('fetchform12', 'API\MatdanController@fetchform12');
		Route::post('storeform12d', 'API\MatdanController@storeform12d');
		Route::get('fetchform12d', 'API\MatdanController@fetchform12d');
		Route::get('total_elector_ac/{st_code}/{ac_no}', 'API\MatdanController@total_elector_ac');
		Route::get('total_elector_pc', 'API\MatdanController@total_elector_pc');
		Route::post('postal_ballot_cast', 'API\MatdanController@pb_cast12d');
		Route::post('ca_published', 'API\MatdanController@ca_published_insert');
		Route::get('ca_published_record', 'API\MatdanController@ca_published_record');
		Route::get('profile', 'API\MatdanController@profile');
		Route::post('profile_image', 'API\MatdanController@profile_image');
		Route::post('fetch_postal_ballot_cast', 'API\MatdanController@fetch_postal_ballot_cast');
		
		

		Route::get('ca_details', 'API\MatdanController@ca_details_ac');
		
		Route::post('logout', 'API\MatdanController@logout');

  });
  
  //EMS 2.0 
  Route::post('GetElectionSchedule', 'API\EMSApiController@GetElectionSchedule');
  Route::post('GetCandidateCount', 'API\EMSApiController@GetCandidateCount');


Route::prefix('v1')->group(function () {
    



Route::get('getElectionByDate', 'API\UsersController@getElectionByDate');
Route::get('getElectionByDatePC', 'API\UsersController@getElectionByDatePC');
Route::get('getactiveelction', 'API\UsersController@getactiveelction');

});
Route::prefix('uat')->group(function () {


Route::get('getElectionByDate', 'API\UsersController@getElectionByDateuat');
Route::get('getElectionByDatePC', 'API\UsersController@getElectionByDatePCuat');
Route::get('getactiveelction', 'API\UsersController@getactiveelction');

});

 Route::group(['prefix' => 'v1', 'middleware' => ['Encrypt','XSS']], function () {
    //Candidate App
    Route::post('userlogin', 'API\UsersController@login');
    Route::post('verifyotp', 'API\UsersController@verifyOtp');
    Route::post('nominationlisting', 'API\UsersController@nominationlisting');
    Route::post('nominationstatus', 'API\UsersController@nominationstatus');
    Route::post('permissionlistview', 'API\UsersController@permissionlistview');
    Route::post('permissionpreview', 'API\UsersController@permissionpreview');
    Route::post('logout', 'API\UsersController@logout');
    //Nodal Candidates App
    
    
});

  Route::group(['prefix' => 'uat', 'middleware' => ['UAT','Encrypt','XSS']], function () {
    //Candidate App
    Route::post('userlogin', 'API\UsersController@login');
    Route::post('verifyotp', 'API\UsersController@verifyOtp');
    Route::post('nominationlisting', 'API\UsersController@nominationlisting');
    Route::post('nominationstatus', 'API\UsersController@nominationstatus');
    Route::post('permissionlistview', 'API\UsersController@permissionlistview');
    Route::post('permissionpreview', 'API\UsersController@permissionpreview');
    Route::post('logout', 'API\UsersController@logout');
    //Nodal Candidates App
    
    
});

Route::prefix('uat')->middleware('UAT','Encrypt', 'XSS')->group(function () { 
	Route::post('nodallogin', 'API\NodalLoginApiUat@login');
	Route::post('nodalverifyotp', 'API\NodalLoginApiUat@verifyOtp');
	Route::post('nodallogout', 'API\NodalLoginApiUat@logout');
	Route::post('permissionlist', 'API\NodalLoginApiUat@permissionlist');
	Route::post('permissionupdate', 'API\NodalLoginApiUat@permissionupdate');
	Route::post('notificationlist', 'API\NodalLoginApiUat@notificationlist');
	Route::post('clearnotificationlist', 'API\NodalLoginApiUat@clearnotificationlist');
	Route::get('nodalappversion', 'API\NodalLoginApiUat@appversion');
});
