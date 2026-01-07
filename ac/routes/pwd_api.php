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

Route::prefix('v1')->middleware('pwdapi')->group(function () {
    Route::post(
        'get-elections',
        'PwdController@getElection'
    );
    Route::post(
        'get-states-and-acs',
        'PwdController@getAcStatesAndAcs'
    );
    Route::post(
        'add-wheel-chair',
        'PwdController@addAcWheelChair'
    );
    Route::post(
        'add-pick-drop',
        'PwdController@addAcPickAndDrop'
    );
    Route::post(
        'add-volunteer',
        'PwdController@addAcVolunteer'
    );
});
