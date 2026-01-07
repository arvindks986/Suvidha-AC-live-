<?php
Route::group(['middleware' => ['auth:admin', 'auth','maintenance']], function () {
  	Route::get('dashboard','Admin\Maintenance\DashboardController@get_dashboard');
  	Route::get('/table','Admin\Maintenance\SqlQueryController@get_table_data');
  	Route::group(['prefix' => 'officer'], function(){
  		Route::get('reset-password','Admin\Maintenance\OfficerResetPinController@index');
  		Route::post('/update-pin','Admin\Eci\Profile\OfficerResetPinController@update_pin');
		Route::post('/update-password','Admin\Eci\Profile\OfficerResetPinController@update_password');
	});
	Route::group(['prefix' => 'setting'], function(){
    Route::get('/setting','Admin\Maintenance\SettingController@index');
    Route::post('/setting/save','Admin\Maintenance\SettingController@save');
  });
  Route::group(['prefix' => 'booth-app-revamp'], function(){
    Route::get('/send_sms_to_boothapp','Admin\BoothAppRevamp\PollingController@send_sms_to_boothapp');
  });
});