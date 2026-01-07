<?php
Route::group(['middleware' =>['adminsession']], function () {

//ECI Mparty STARTS
Route::group(['prefix' => 'eci', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth','mparty']], function(){
	
	 
});
//ECI Mparty ENDS
// start Mparty
Route::group(['prefix' => 'ceo', 'as' => 'ceo::', 'middleware' => ['auth:admin', 'auth']], function(){
Route::get('generate-party', 'Admin\mparty\CeoPartyController@generate_party');
Route::get('generate-symbol', 'Admin\mparty\CeoPartyController@generate_symbol');
Route::GET('/state-party-list', 'Admin\mparty\CeoPartyController@index');
Route::POST('/state-party-list', 'Admin\mparty\CeoPartyController@index');
Route::post('/state-party-update','Admin\mparty\CeoPartyController@updateparty');

Route::GET('/symbol-list','Admin\mparty\CeoPartyController@symbol');   
Route::post('/symbol-list-update','Admin\mparty\CeoPartyController@symbolupdate');

Route::GET('/partywise-reports', 'Admin\mparty\CeoPartyController@partywise_reports');
Route::POST('/partywise-reports', 'Admin\mparty\CeoPartyController@partywise_reports');
Route::post('/symbol-reports','Admin\mparty\CeoPartyController@symbol_reports');

Route::GET('/symbol-list','Admin\mparty\CeoPartyController@symbol'); 

Route::GET('/partywisereportspdf','Admin\mparty\CeoPartyController@partywisereportspdf');
Route::GET('/partywisereportsexcel','Admin\mparty\CeoPartyController@partywisereportsexcel');

Route::GET('/symbol-reports','Admin\mparty\CeoPartyController@symbol_reports'); 
Route::GET('/symbolreportspdf','Admin\mparty\CeoPartyController@symbolreportspdf');
Route::GET('/symbolreportsexcel','Admin\mparty\CeoPartyController@symbolreportsexcel');
});
// End mparty
Route::group(['middleware' =>['mparty']], function () {
    

	Route::get('dashboard', 'Admin\mparty\DashboardController@index');
	Route::get('list-party', 'Admin\mparty\MpartyController@index');
	Route::get('new-party', 'Admin\mparty\MpartyController@add_new_party');
    Route::get('verifypartyabbre', 'Admin\mparty\MpartyController@verifypartyabbre');
    Route::POST('verifypartyabbre', 'Admin\mparty\MpartyController@verifypartyabbre');
    Route::POST('add-new-party', 'Admin\mparty\MpartyController@save_new_party');
    Route::get('getdparty', 'Admin\mparty\MpartyController@getdparty');
    Route::POST('getdparty', 'Admin\mparty\MpartyController@getdparty');
    
    
    Route::get('edit-party', 'Admin\mparty\MpartyController@edit_party');
    Route::POST('update-party', 'Admin\mparty\MpartyController@update_party');
    Route::POST('party-status', 'Admin\mparty\MpartyController@party_status');
    Route::get('change-party-status','Admin\mparty\MpartyController@change_party_status');
    Route::get('getpartybypartytype','Admin\mparty\MpartyController@getpartybypartytype');
    Route::POST('change-status', 'Admin\mparty\MpartyController@change_status');
    Route::get('view-details', 'Admin\mparty\MpartyController@view_details');
    Route::get('view-delisted-details', 'Admin\mparty\MpartyController@view_delisted_details');

    Route::get('list-symbol', 'Admin\mparty\SymbolController@index');
    Route::POST('list-symbol', 'Admin\mparty\SymbolController@index');
    Route::get('add-symbol', 'Admin\mparty\SymbolController@add_symbol');
    Route::get('edit-symbol', 'Admin\mparty\SymbolController@edit_symbol');
    Route::POST('add-new-symbol', 'Admin\mparty\SymbolController@save_symbol');
    Route::POST('update-symbol', 'Admin\mparty\SymbolController@update_symbol');

    Route::get('free-symbol', 'Admin\mparty\SymbolController@free_symbol');
    Route::POST('symbol-status','Admin\mparty\SymbolController@change_status');
    Route::get('state-party-recognized', 'Admin\mparty\MpartyController@state_party_register');
    Route::get('edit-dparty', 'Admin\mparty\MpartyController@edit_dparty');
    Route::POST('dparty-status', 'Admin\mparty\MpartyController@dparty_status');
    Route::get('add-dparty', 'Admin\mparty\MpartyController@add_dparty');
    Route::POST('insert-dparty', 'Admin\mparty\MpartyController@save_dparty');  
    Route::get('verifysymbol', 'Admin\mparty\SymbolController@verifysymbol');
    Route::POST('verifysymbol', 'Admin\mparty\SymbolController@verifysymbol');
    Route::get('symbollog-details', 'Admin\mparty\SymbolController@symbollog_details');

    Route::get('party-symbol-assign', 'Admin\mparty\MpartyController@party_symbol_assign');
    Route::POST('symbol-assign', 'Admin\mparty\MpartyController@symbol_assign');
    Route::POST('editsymbol-assign', 'Admin\mparty\MpartyController@editsymbol_assign');
    Route::get('delisting-party','Admin\mparty\MpartyController@delisting_party');
    Route::post('delistparty','Admin\mparty\MpartyController@delistparty');

  Route::get('list-party-report', 'Admin\mparty\MpartyReportController@index');
  Route::POST('list-party-report', 'Admin\mparty\MpartyReportController@index');
  Route::get('partyreportspdf', 'Admin\mparty\MpartyReportController@partyreportspdf');
  Route::get('partyreportsexcel', 'Admin\mparty\MpartyReportController@partyreportsexcel');
  Route::get('state-wise-recognized-parties', 'Admin\mparty\MpartyReportController@state_wise_recognized_parties');
  Route::POST('state-wise-recognized-parties', 'Admin\mparty\MpartyReportController@state_wise_recognized_parties');
  Route::get('state-wise-recognized-partiesexcel', 'Admin\mparty\MpartyReportController@state_wise_recognized_partiesexcel');
  Route::get('state-wise-recognized-partiespdf', 'Admin\mparty\MpartyReportController@state_wise_recognized_partiespdf');
  
  Route::get('party-symbol-report', 'Admin\mparty\MpartyReportController@party_symbol_report');
  Route::POST('party-symbol-report', 'Admin\mparty\MpartyReportController@party_symbol_report');
  Route::get('party-symbol-reportexcel', 'Admin\mparty\MpartyReportController@party_symbol_reportexcel');
  Route::get('party-symbol-reportpdf', 'Admin\mparty\MpartyReportController@party_symbol_reportpdf'); 
  
  Route::get('delisting-report', 'Admin\mparty\MpartyReportController@delisting_report');
  //Route::POST('delisting-report', 'Admin\mparty\MpartyReportController@delisting_report');
  Route::get('delisting-reportexcel', 'Admin\mparty\MpartyReportController@delisting_reportexcel');
  Route::get('delisting-reportpdf', 'Admin\mparty\MpartyReportController@delisting_reportpdf'); 
  
  Route::get('list-symbol-report', 'Admin\mparty\MpartyReportController@list_symbol_report');
  Route::POST('list-symbol-report', 'Admin\mparty\MpartyReportController@list_symbol_report');
  Route::get('list-symbol-reportexcel', 'Admin\mparty\MpartyReportController@list_symbol_reportexcel');
  Route::get('list-symbol-reportpdf', 'Admin\mparty\MpartyReportController@list_symbol_reportpdf');  
    
        //  <li><a rel="" href="{{url('/mparty/free-symbol-report')}}" 
});    //add-dparty  




});