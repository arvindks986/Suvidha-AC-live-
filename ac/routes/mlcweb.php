<?php
Route::group(['middleware' =>['adminsession','auth:admin', 'auth']], function () {

//ECI mlc STARTS
Route::group(['prefix' => 'eci', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function(){
	
	 
});
//ECI mlc ENDS

 
//mlc ro 1 ROUTES STARTS
Route::group(['prefix' => 'ro', 'as' => 'ro::', 'middleware' => ['auth:admin', 'auth','mlc']], function(){
 Route::get('dashboard', 'Admin\mlc\DashboardController@index');
 Route::get('/listallapplicant', 'Admin\mlc\MlcroController@index'); 
});
//mlc ro 1 ROUTES ENDS




});