<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::post('Home_PT', 'API\VtOneController@HomePt'); /// For Home Page
    Route::post('Home_Dashboard', 'API\VtOneController@HomeDashboard'); /// For Home Dashboard Page
    Route::post('DistrictWise_PT', 'API\VtOneController@DistwisePt'); /// For Summary Report of All PC or PC of selected State
    Route::post('PC2ACwise_PT', 'API\VtOneController@PC2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
    Route::post('DIST2ACwise_PT', 'API\VtOneController@Dist2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
    Route::post('AC_PT', 'API\VtOneController@AcPt'); /// For Current Poll turnout status of selected AC
    Route::post('State_PhaseWise', 'API\VtOneController@PhaseWiseState'); /// For Poll turnout status of all States
    Route::post('ElectionTypeState_Pt', 'API\VtOneController@ElectionTypeState_Pt');
    Route::post('app_vtr_message', 'API\VtOneController@app_vtr_message'); /// For Msg All Page
    Route::post('update-firebase-key', 'API\VtOneController@updateFireBaseKey'); /// For Home Page
    Route::post('update-msg', 'API\VtOneController@updateMessage'); /// For Home Page

    ###### FILTERS CALLS
    Route::post('ElectionType_PT', 'API\VtOneController@ElectionTypePt'); /// For List of available election types
    Route::post('PhaseList_PT', 'API\VtOneController@PhaseListPt'); /// For List of phases in selected election type
    Route::post('PhaseList_PTNew', 'API\VtOneController@PhaseListPtNew'); /// For List of phases in selected election type
    Route::post('StateList_PT', 'API\VtOneController@StateListingPT'); /// For List of All States in selected Phase and Election
    Route::post('PcList_PT', 'API\VtOneController@PcListingPT'); /// For List of All PC in selected State
    Route::post('PCWiseAcList_PT', 'API\VtOneController@PC2AcListingPT'); /// For List of All Polling AC in selected PC
    Route::post('DistWiseAcList_PT', 'API\VtOneController@Dist2AcListingPT'); /// For List of All Polling AC in selected District
    Route::post('DistrictList_PT', 'API\VtOneController@DistListingPT'); /// For List of All Polling District in selected State
    Route::post('GetPollDate', 'API\VtOneController@PollDate'); /// For List of All Polling District in selected State
});

Route::prefix('uat')->group(function () {
    ###### PAGE CALLS ######
    Route::post('Home_PT', 'API\VtUatController@HomePt'); /// For Home Page
    Route::post('Home_Dashboard', 'API\VtUatController@HomeDashboard'); /// For Home Dashboard Page
    Route::post('DistrictWise_PT', 'API\VtUatController@DistwisePt'); /// For Summary Report of All PC or PC of selected State
    Route::post('PC2ACwise_PT', 'API\VtUatController@PC2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
    Route::post('DIST2ACwise_PT', 'API\VtUatController@Dist2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
    Route::post('AC_PT', 'API\VtUatController@AcPt'); /// For Current Poll turnout status of selected AC
    Route::post('State_PhaseWise', 'API\VtUatController@PhaseWiseState'); /// For Poll turnout status of all States
    Route::post('ElectionTypeState_Pt', 'API\VtUatController@ElectionTypeState_Pt');
    Route::post('app_vtr_message', 'API\VtUatController@app_vtr_message'); /// For Msg All Page
    Route::post('update-firebase-key', 'API\VtUatController@updateFireBaseKey'); /// For Home Page
    Route::post('update-msg', 'API\VtUatController@updateMessage'); /// For Home Page

    ###### FILTERS CALLS
    Route::post('ElectionType_PT', 'API\VtUatController@ElectionTypePt'); /// For List of available election types
    Route::post('PhaseList_PT', 'API\VtUatController@PhaseListPt'); /// For List of phases in selected election type
    Route::post('PhaseList_PTNew', 'API\VtUatController@PhaseListPtNew');
    Route::post('StateList_PT', 'API\VtUatController@StateListingPT'); /// For List of All States in selected Phase and Election
    Route::post('PcList_PT', 'API\VtUatController@PcListingPT'); /// For List of All PC in selected State
    Route::post('PCWiseAcList_PT', 'API\VtUatController@PC2AcListingPT'); /// For List of All Polling AC in selected PC
    Route::post('DistWiseAcList_PT', 'API\VtUatController@Dist2AcListingPT'); /// For List of All Polling AC in selected District
    Route::post('DistrictList_PT', 'API\VtUatController@DistListingPT'); /// For List of All Polling District in selected State
    Route::post('GetPollDate', 'API\VtUatController@PollDate'); /// For List of All Polling District in selected State

});


Route::prefix('v2')->group(function () {
    ###### PAGE CALLS ######
    Route::post('Home_PT', 'API\VtTwoController@HomePt'); /// For Home Page
    Route::post('Home_Dashboard', 'API\VtTwoController@HomeDashboard'); /// For Home Dashboard Page
    Route::post('DistrictWise_PT', 'API\VtTwoController@DistwisePt'); /// For Summary Report of All PC or PC of selected State
    Route::post('PC2ACwise_PT', 'API\VtTwoController@PC2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
    Route::post('DIST2ACwise_PT', 'API\VtTwoController@Dist2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
    Route::post('AC_PT', 'API\VtTwoController@AcPt'); /// For Current Poll turnout status of selected AC
    Route::post('State_PhaseWise', 'API\VtTwoController@PhaseWiseState'); /// For Poll turnout status of all States
    Route::post('ElectionTypeState_Pt', 'API\VtTwoController@ElectionTypeState_Pt');
    Route::post('app_vtr_message', 'API\VtTwoController@app_vtr_message'); /// For Msg All Page
    Route::post('update-firebase-key', 'API\VtTwoController@updateFireBaseKey'); /// For Home Page
    Route::post('update-msg', 'API\VtTwoController@updateMessage'); /// For Home Page

    ###### FILTERS CALLS
    Route::post('ElectionType_PT', 'API\VtTwoController@ElectionTypePt'); /// For List of available election types
    Route::post('PhaseList_PT', 'API\VtTwoController@PhaseListPt'); /// For List of phases in selected election type
    Route::post('PhaseList_PTNew', 'API\VtTwoController@PhaseListPtNew');
    Route::post('StateList_PT', 'API\VtTwoController@StateListingPT'); /// For List of All States in selected Phase and Election
    Route::post('PcList_PT', 'API\VtTwoController@PcListingPT'); /// For List of All PC in selected State
    Route::post('PCWiseAcList_PT', 'API\VtTwoController@PC2AcListingPT'); /// For List of All Polling AC in selected PC
    Route::post('DistWiseAcList_PT', 'API\VtTwoController@Dist2AcListingPT'); /// For List of All Polling AC in selected District
    Route::post('DistrictList_PT', 'API\VtTwoController@DistListingPT'); /// For List of All Polling District in selected State
    Route::post('GetPollDate', 'API\VtTwoController@PollDate'); /// For List of All Polling District in selected State

});
