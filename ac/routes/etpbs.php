<?php
Route::group(['prefix' => 'roac', 'middleware' => ['adminsession','auth:admin', 'auth','ro']], function(){
});

Route::group(['prefix' => 'eci', 'middleware' => ['adminsession','auth:admin', 'auth','eci']], function(){
});	

Route::group(['prefix' => 'acceo', 'middleware' => ['adminsession','auth:admin', 'auth','ceo']], function(){
});

Route::group(['prefix' => 'acdeo', 'middleware' => ['adminsession','auth:admin', 'auth','deo']], function(){
});

Route::group(['prefix' => 'etpbs', 'middleware' => ['adminsession','auth:admin', 'auth', 'etpbs']], function(){

    Route::get('/', function(){
        return redirect("etpbs/dashboard");
    });
    Route::get('dashboard','Admin\Etpbs\DashboardController@dashboard');

});