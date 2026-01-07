<?php
Route::get('{st_code}/result-sheet-report','Admin\ResultSheetController@index');
Route::group(['middleware' => 'adminsession'], function () {

//ECI TURNOUT ROUTES STARTS
Route::group(['prefix' => 'eci', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function(){
	
Route::any('result-report','Admin\ResultSheetController@result_report');	
Route::any('party-wise','Admin\ResultSheetController@party_wise');
Route::any('ac-wise-report','Admin\ResultSheetController@ac_wise_report');	
//form 20 by
Route::get('counting/get_form_20','Admin\counting\Form20Controller@get_form_20')->middleware('eci');
Route::get('counting/get_form_20/excel','Admin\counting\Form20Controller@excel')->middleware('eci');
Route::get('counting/get_form_20/pdf','Admin\counting\Form20Controller@pdf')->middleware('eci');
Route::namespace('Admin\counting')->prefix('counting')->middleware('auth:admin', 'auth')->group(function(){

        
        //COUNTING STATUS REPORT
        Route::get('/BoothCountingStatusReport', 'BoothCountingStatusReportController@BoothCountingStatusReport');
        //COUNTING STATUS EXCEL REPORT
        Route::get('/BoothCountingStatusReport/excel', 'BoothCountingStatusReportController@BoothCountingStatusExcel');
       //COUNTING STATUS PDF REPORT
        Route::get('/BoothCountingStatusReport/pdf', 'BoothCountingStatusReportController@BoothCountingStatusPdf');
		
		//COUNTING STATUS REPORT
        Route::get('/BoothCountingStatusCeo', 'BoothCountingStatusReportController@BoothCountingStatusCeo');
        //COUNTING STATUS EXCEL REPORT
        Route::get('/BoothCountingStatusCeo/excel', 'BoothCountingStatusReportController@BoothCountingStatusCeoExcel');
       //COUNTING STATUS PDF REPORT
        Route::get('/BoothCountingStatusCeo/pdf', 'BoothCountingStatusReportController@BoothCountingStatusCeoPdf');
		
		// Jitendra Singh Start
//COUNTING Summary REPORT
    Route::get('/BoothCountingSummaryReport', 'BoothCountingSummaryReportController@BoothCountingSummaryReport');
    //COUNTING summary EXCEL REPORT
    Route::get('/BoothCountingSummaryReport/excel', 'BoothCountingSummaryReportController@BoothCountingSummaryExcel');
    //COUNTING summary PDF REPORT
    Route::get('/BoothCountingSummaryReport/pdf', 'BoothCountingSummaryReportController@BoothCountingSummaryPdf');
// Jitendra Singh End


        Route::get('/constituency-wise-report','ConstituencyWiseReportController@index'); 
        Route::post('/get-pc-by-state-id-eci-pcwise-constituency', 'ConstituencyWiseReportController@getMatchedPcByStateId');  
        Route::post('/get-ac-by-state-and-pc-id-eci-pcwise-constituency', 'ConstituencyWiseReportController@getMatchedAc');    
        Route::post('/get-condidate-details-eci-pcwise-constituency', 'ConstituencyWiseReportController@getCondidfateListpkpk');
        Route::post('/get-all-result-eci-pcwise-constituency', 'ConstituencyWiseReportController@getCompleteResult');
        Route::post('/csvDownload-pcwise-constituency', 'ConstituencyWiseReportController@csvDownload');
            
        //TABLE REPORTS STARTS
		Route::get('/report_state', 'BoothCountingTableReportController@report_state');
		Route::get('/report_state/excel', 'BoothCountingTableReportController@export_excel_report_state');
		Route::get('/report_state/pdf', 'BoothCountingTableReportController@export_pdf_report_state');

		Route::get('/report_state/state/ac', 'BoothCountingTableReportController@report_ac');
		Route::get('/report_state/state/ac/excel', 'BoothCountingTableReportController@export_excel_report_ac');
		Route::get('/report_state/state/ac/pdf', 'BoothCountingTableReportController@export_pdf_report_ac');

		Route::get('/report_state/state/ac', 'BoothCountingTableReportController@report_ac');
		Route::get('/report_state/state/ac/excel', 'BoothCountingTableReportController@export_excel_report_ac');
		Route::get('/report_state/state/ac/pdf', 'BoothCountingTableReportController@export_pdf_report_ac');
		//TABLE REPORTS ENDS


        //Schedule Round REPORT
    Route::get('/BoothCountingScheduleReport','BoothCountingScheduleReportController@BoothCountingScheduleReport');
  Route::get('/BoothCounting_main_ScheduleReport','BoothCountingScheduleReportController@BoothCounting_main_ScheduleReport');
  Route::get('/boothschedule-report-main-pdf/{s_code}/{ac_id}','BoothCountingScheduleReportController@BoothCountingSchedulePdf_main');
  Route::get('/boothschedule-report-main-excel/{s_code}/{ac_id}','BoothCountingScheduleReportController@BoothCountingScheduleExcel_main');
  Route::post('/BoothCountingScheduleReport','BoothCountingScheduleReportController@BoothCountingScheduleReport');
  Route::get('/boothstate-by-ac/{s_code}','BoothCountingScheduleReportController@acList');
  Route::get('/boothschedule-report-pdf/{s_code}/{ac_id}','BoothCountingScheduleReportController@BoothCountingSchedulePdf');
  Route::get('/boothschedule-report-excel/{s_code}/{ac_id}','BoothCountingScheduleReportController@BoothCountingScheduleExcel');
    

    //Round Wise Report
        Route::get('/boothround-wise-report','BoothRoundWiseReportController@index'); 
  Route::post('/boothget-pc-by-state-id-eci', 'BoothRoundWiseReportController@getMatchedPcByStateId'); 
  Route::post('/boothget-ac-by-state-and-pc-id-eci', 'BoothRoundWiseReportController@getMatchedAc'); 
  Route::post('/boothget-condidate-details-eci', 'BoothRoundWiseReportController@getCondidfateListpkpk');
  Route::post('/boothget-condidate-details-eci-ac-wise', 'BoothRoundWiseReportController@getCondidfateListAcWise');
  Route::post('/boothget-all-result-eci', 'BoothRoundWiseReportController@getCompleteResult');
  Route::post('/boothcsvDownload', 'BoothRoundWiseReportController@csvDownload');

        });
		
		// mayank
    Route::group(['prefix' => 'booth-counting', 'middleware' => ['eci']], function () {
        Route::get('/active-user-report', 'Admin\BoothCountingReport\ActiveUserReportController@show_active_user_count');
        Route::get('/active-user-detail', 'Admin\BoothCountingReport\ActiveUserReportController@show_active_user');
		Route::get('/export_pdf_report_state', 'Admin\BoothCountingReport\ActiveUserReportController@export_pdf_report_state');
		Route::get('/export_pdf_count', 'Admin\BoothCountingReport\ActiveUserReportController@export_pdf_count');
		Route::get('/export_excel_active_users', 'Admin\BoothCountingReport\ActiveUserReportController@export_excel_active_users');
		Route::get('/export_excel_active_users_details', 'Admin\BoothCountingReport\ActiveUserReportController@export_excel_active_users_details');
        	//Form21 Download
        Route::get('/form21c-download','Admin\BoothCountingReport\FormDownloadACController@form21download');
        Route::post('/form21c-download','Admin\BoothCountingReport\FormDownloadACController@form21download')->name('eci.download.form21c');

        //candidate waise report
        Route::get('/candidate-wise-report', 'Admin\BoothCountingReport\VoterTypeWiseReportController@reportIndex');
        Route::get('/candidate-wise-report-get-ac-state/{state}', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getAcByState');
        Route::post('/candidate-wise-report-ac', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getSearchReport');
        Route::get('/candidate-wise-report-get-party/{ac}/{state}', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getPartyByAc');
        Route::post('/candidate-wise-report-excel', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getReportExcel');
        Route::post('/candidate-wise-report-pdf', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getReportPdf');
		
		//Sanjay 25-10-2019
		Route::get('/winning-candidate-list', 'Admin\BoothCountingReport\GazzeteReportController@getPdfView');
		Route::get('/winning-candidate-list-pdf', 'Admin\BoothCountingReport\GazzeteReportController@getDownloadPdf');
		Route::get('/result-sheet-report','Admin\ResultSheet\ResultSheetReportController@index'); 
    });
	// end mayank

});
//ECI TURNOUT ROUTES ENDS


//AC-CEO TURNOUT ROUTES STARTS
Route::group(['prefix' => 'acceo', 'as' => 'acceo::', 'middleware' => ['auth:admin', 'auth']], function(){
	
	//form 20 by
Route::get('counting/get_form_20','Admin\counting\Form20Controller@get_form_20')->middleware('ceo');
Route::get('counting/get_form_20/excel','Admin\counting\Form20Controller@excel')->middleware('ceo');
Route::get('counting/get_form_20/pdf','Admin\counting\Form20Controller@pdf')->middleware('ceo');

	Route::namespace('Admin\counting')->prefix('counting')->middleware('auth:admin', 'auth')->group(function(){
       
        //TABLE REPORTS STARTS
		Route::get('/report_state', 'BoothCountingTableReportController@report_state');
		Route::get('/report_state/excel', 'BoothCountingTableReportController@export_excel_report_state');
		Route::get('/report_state/pdf', 'BoothCountingTableReportController@export_pdf_report_state');

		Route::get('/report_state/state/ac', 'BoothCountingTableReportController@report_ac');
		Route::get('/report_state/state/ac/excel', 'BoothCountingTableReportController@export_excel_report_ac');
		Route::get('/report_state/state/ac/pdf', 'BoothCountingTableReportController@export_pdf_report_ac');
		//TABLE REPORTS ENDS

		//COUNTING STATUS REPORT
        Route::get('/BoothCountingStatusCeo', 'BoothCountingStatusReportController@BoothCountingStatusCeo');
        //COUNTING STATUS EXCEL REPORT
        Route::get('/BoothCountingStatusCeo/excel', 'BoothCountingStatusReportController@BoothCountingStatusCeoExcel');
       //COUNTING STATUS PDF REPORT
        Route::get('/BoothCountingStatusCeo/pdf', 'BoothCountingStatusReportController@BoothCountingStatusCeoPdf');   

        Route::get('/constituency-wise-report','ConstituencyWiseReportController@index'); 
    Route::post('/get-pc-by-state-id-eci-pcwise-constituency', 'ConstituencyWiseReportController@getMatchedPcByStateId');  
    Route::post('/get-ac-by-state-and-pc-id-eci-pcwise-constituency', 'ConstituencyWiseReportController@getMatchedAc');    
    Route::post('/get-condidate-details-eci-pcwise-constituency', 'ConstituencyWiseReportController@getCondidfateListpkpk');
    Route::post('/get-all-result-eci-pcwise-constituency', 'ConstituencyWiseReportController@getCompleteResult');
    Route::post('/csvDownload-pcwise-constituency', 'ConstituencyWiseReportController@csvDownload');

   //Schedule Round REPORT
        Route::get('/schedule-report','CEOScheduleReportController@scheduleReport');
	Route::post('/schedule-report','CEOScheduleReportController@scheduleReport');
	Route::get('/schedule-report-pdf/{ac_id}','CEOScheduleReportController@scheduleReportPDF');
	Route::get('/schedule-report-excel/{ac_id}','CEOScheduleReportController@scheduleReportExcel');
    
    

    //Round Wise Report
        Route::get('/boothround-wise-report','BoothRoundWiseReportController@index'); 
  Route::post('/boothget-pc-by-state-id-eci', 'BoothRoundWiseReportController@getMatchedPcByStateId'); 
  Route::post('/boothget-ac-by-state-and-pc-id-eci', 'BoothRoundWiseReportController@getMatchedAc'); 
  Route::post('/boothget-condidate-details-eci', 'BoothRoundWiseReportController@getCondidfateListpkpk');
  Route::post('/boothget-condidate-details-eci-ac-wise', 'BoothRoundWiseReportController@getCondidfateListAcWise');
  Route::post('/boothget-all-result-eci', 'BoothRoundWiseReportController@getCompleteResult');
  Route::post('/boothcsvDownload', 'BoothRoundWiseReportController@csvDownload');

        
        });
		
		
		// mayank ceo
    Route::group(['prefix' => 'booth-counting', 'middleware' => ['ceo']], function(){
      Route::get('/active-user-report', 'Admin\BoothCountingReport\ActiveUserReportController@show_active_user_count');
      Route::get('/active-user-detail', 'Admin\BoothCountingReport\ActiveUserReportController@show_active_user');
	  Route::get('/export_pdf_report_state', 'Admin\BoothCountingReport\ActiveUserReportController@export_pdf_report_state');
	  Route::get('/export_pdf_count', 'Admin\BoothCountingReport\ActiveUserReportController@export_pdf_count');
	  Route::get('/export_excel_active_users', 'Admin\BoothCountingReport\ActiveUserReportController@export_excel_active_users');
	  Route::get('/export_excel_active_users_details', 'Admin\BoothCountingReport\ActiveUserReportController@export_excel_active_users_details');
      	//Form21 Download
    //Form21 Download
    Route::get('/form21-download','Admin\CountingReport\CEOFormDownloadACController@form21Download');
    Route::post('/form21-download','Admin\CountingReport\CEOFormDownloadACController@form21Download')->name('eci.download.form21');

        // candidate wise
    Route::get('/candidate-wise-report', 'Admin\BoothCountingReport\VoterTypeWiseReportController@reportIndex');
    Route::get('/candidate-wise-report-get-ac-state/{state}', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getAcByState');
    Route::post('/candidate-wise-report-ac', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getSearchReport');
    Route::get('/candidate-wise-report-get-party/{ac}/{state}', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getPartyByAc');
    Route::post('/candidate-wise-report-excel', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getReportExcel');
    Route::post('/candidate-wise-report-pdf', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getReportPdf');
    });
    // mayank ceo end

});
//AC-CEO TURNOUT ROUTES ENDS


//AC-DEO TURNOUT ROUTES STARTS
Route::group(['prefix' => 'acdeo', 'as' => 'acdeo::', 'middleware' => ['auth:admin', 'auth']], function(){
	
	Route::get('counting/get_form_20','Admin\counting\Form20Controller@get_form_20')->middleware('deo');
Route::get('counting/get_form_20/excel','Admin\counting\Form20Controller@excel')->middleware('deo');
Route::get('counting/get_form_20/pdf','Admin\counting\Form20Controller@pdf')->middleware('deo');

	//mayank deo
		Route::group(['prefix' => 'booth-counting', 'middleware' => ['deo']], function () {
        Route::get('/active-user-report', 'Admin\BoothCountingReport\ActiveUserReportController@show_active_user_count');
        Route::get('/active-user-detail', 'Admin\BoothCountingReport\ActiveUserReportController@show_active_user');
        Route::get('/export_pdf_report_state', 'Admin\BoothCountingReport\ActiveUserReportController@export_pdf_report_state');
		Route::get('/export_pdf_count', 'Admin\BoothCountingReport\ActiveUserReportController@export_pdf_count');
		Route::get('/export_excel_active_users', 'Admin\BoothCountingReport\ActiveUserReportController@export_excel_active_users');
		Route::get('/export_excel_active_users_details', 'Admin\BoothCountingReport\ActiveUserReportController@export_excel_active_users_details');
            //Form21 Download
		Route::get('/form21-download','Admin\BoothCountingReport\CEOFormDownloadACController@form21Download');
		Route::post('/form21-download','Admin\BoothCountingReport\CEOFormDownloadACController@form21Download')->name('eci.download.form21');

        Route::get('/candidate-wise-report', 'Admin\BoothCountingReport\VoterTypeWiseReportController@reportIndex');
        Route::get('/candidate-wise-report-get-ac-state/{state}', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getAcByState');
        Route::post('/candidate-wise-report-ac', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getSearchReport');
        Route::get('/candidate-wise-report-get-party/{ac}/{state}', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getPartyByAc');
        Route::post('/candidate-wise-report-excel', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getReportExcel');
        Route::post('/candidate-wise-report-pdf', 'Admin\BoothCountingReport\VoterTypeWiseReportController@getReportPdf');
      });
  // end mayank deo
  
  Route::namespace('Admin\counting')->prefix('counting')->middleware('auth:admin', 'auth')->group(function(){


   //COUNTING STATUS REPORT
        Route::get('/BoothCountingStatusCeo', 'BoothCountingStatusReportController@BoothCountingStatusCeo');
        //COUNTING STATUS EXCEL REPORT
        Route::get('/BoothCountingStatusCeo/excel', 'BoothCountingStatusReportController@BoothCountingStatusCeoExcel');
       //COUNTING STATUS PDF REPORT
        Route::get('/BoothCountingStatusCeo/pdf', 'BoothCountingStatusReportController@BoothCountingStatusCeoPdf');


 Route::get('/constituency-wise-report','ConstituencyWiseReportController@index'); 
    Route::post('/get-pc-by-state-id-eci-pcwise-constituency', 'ConstituencyWiseReportController@getMatchedPcByStateId');  
    Route::post('/get-ac-by-state-and-pc-id-eci-pcwise-constituency', 'ConstituencyWiseReportController@getMatchedAc');    
    Route::post('/get-condidate-details-eci-pcwise-constituency', 'ConstituencyWiseReportController@getCondidfateListpkpk');
    Route::post('/get-all-result-eci-pcwise-constituency', 'ConstituencyWiseReportController@getCompleteResult');
    Route::post('/csvDownload-pcwise-constituency', 'ConstituencyWiseReportController@csvDownload');

   //Schedule Round REPORT
  Route::get('/schedule-report','CEOScheduleReportController@scheduleReport');
  Route::post('/schedule-report','CEOScheduleReportController@scheduleReport');
  Route::get('/schedule-report-pdf/{ac_id}','CEOScheduleReportController@scheduleReportPDF');
  Route::get('/schedule-report-excel/{ac_id}','CEOScheduleReportController@scheduleReportExcel');
    
    

    //Round Wise Report
        Route::get('/boothround-wise-report','BoothRoundWiseReportController@index'); 
  Route::post('/boothget-pc-by-state-id-eci', 'BoothRoundWiseReportController@getMatchedPcByStateId'); 
  Route::post('/boothget-ac-by-state-and-pc-id-eci', 'BoothRoundWiseReportController@getMatchedAc'); 
  Route::post('/boothget-condidate-details-eci', 'BoothRoundWiseReportController@getCondidfateListpkpk');
  Route::post('/boothget-condidate-details-eci-ac-wise', 'BoothRoundWiseReportController@getCondidfateListAcWise');
  Route::post('/boothget-all-result-eci', 'BoothRoundWiseReportController@getCompleteResult');
  Route::post('/boothcsvDownload', 'BoothRoundWiseReportController@csvDownload');


Route::get('/report_state/state/ac', 'BoothCountingTableReportController@report_ac');
		Route::get('/report_state/state/ac/excel', 'BoothCountingTableReportController@export_excel_report_ac');
		Route::get('/report_state/state/ac/pdf', 'BoothCountingTableReportController@export_pdf_report_ac');
		//TABLE REPORTS ENDS

      
      

     


    });
});
//AC-DEO TURNOUT ROUTES ENDS


//ROAC TURNOUT ROUTES STARTS SACHCHIDA
Route::group(['prefix' => 'roac', 'as' => 'roac::', 'middleware' => ['auth:admin', 'auth']], function(){
Route::group(['prefix' => 'counting', 'as' => 'counting::', 'middleware' => ['auth:admin', 'auth']], function(){ 
 ///  booth level counting   Code heare sachchidana august-2019
          
             //Route::get('/prepare-counting-data', 'Admin\counting\PostalCountingController@prepare_counting_data');
          Route::get('/counting-center-details', 'Admin\counting\CountingCenterDetailsController@index');
          Route::POST('/verify-counting-center-details', 'Admin\counting\CountingCenterDetailsController@verify_counting_center_details');

          Route::get('/round-schedule-details', 'Admin\counting\CountingCenterDetailsController@round_schedule');
          Route::POST('/verifyround-schedule', 'Admin\counting\CountingCenterDetailsController@verifyround');

          Route::get('/counting-type','Admin\counting\CountingCenterDetailsController@counting_type');
          Route::POST('/verifycounting-type','Admin\counting\CountingCenterDetailsController@verifycounting_type');
         
		  Route::get('/empty-ps-wise-entry','Admin\counting\BoothCountingController@empty_polling_station_wisevote_entry'); 
          Route::post('/update-empty-ps-entry', 'Admin\counting\BoothCountingController@mark_ps_as_null');
          
          Route::get('/polling-station-wisevote-entry', 'Admin\counting\BoothCountingController@polling_station_wisevote_entry');
          Route::POST('/verifypolling-station-wisevote-entry', 'Admin\counting\BoothCountingController@verifypolling_station_wisevote_entry');
          Route::GET('/pswisepdf', 'Admin\counting\BoothCountingController@pswisepdf');
          Route::GET('/tabulating-trend-results', 'Admin\counting\BoothCountingController@tabulating_trend_results');
          Route::GET('/download-tabulating-trend-results', 'Admin\counting\BoothCountingController@download_tabulating_trend_results');
          Route::GET('/round-wise-calculate-vote','Admin\counting\BoothCountingController@round_wise_calculate_vote');
          Route::GET('/generate-form20','Admin\counting\BoothCountingController@generate_form20');

          Route::GET('/pollingstationdetails','Admin\counting\BoothCountingController@pollingstationdetails');  // ajax 
		  
		  Route::GET('/check-empty-ps-details','Admin\counting\VerifyEmptyPsController@checkEmptyPs');  // page before finalize 
          Route::POST('/finalized-ps-verification','Admin\counting\VerifyEmptyPsController@finalizeEmptyPs');  // page before finalize 

          Route::get('/form20pdf','Admin\counting\BoothCountingController@download_pdf_form20');
          Route::get('/form20excel','Admin\counting\BoothCountingController@export_excel_form20');
          Route::get('/round-wise-results','Admin\counting\BoothCountingController@round_wise_results');
        
          Route::POST('/createcounting-user','Admin\counting\CountinguserController@createcounting_user');
          Route::get('/user-assign-table-details','Admin\counting\CountinguserController@user_assign_table');
          Route::get('/counting-user','Admin\counting\CountinguserController@counting_user');
          Route::POST('/verify-user-assign','Admin\counting\CountinguserController@verify_user_assign');
          Route::POST('/update-counting-user','Admin\counting\CountinguserController@update_counting_user');
         //update-counting-user
          Route::get('/remove-counting-users-table', 'Admin\counting\CountinguserController@remove_counting_users');

          Route::POST('/polling-station-wisevote-entry-edit','Admin\counting\BoothCountingController@counting_data_entry_edit');
          Route::Get('/result-publish','Admin\counting\BoothCountingController@result_publish');

          Route::get('/bpostal-data-entry', 'Admin\counting\PostalCountingController@postal_data_entry');
          Route::POST('/verify-postal-entry', 'Admin\counting\PostalCountingController@verifypostalentry');
         
          Route::get('/postal-counting-finalized', 'Admin\counting\PostalCountingController@counting_finalized');
          Route::POST('/postal-counting-finalized-verify', 'Admin\counting\PostalCountingController@counting_finalized_verify');
        
          Route::GET('/evm-votes-finalized', 'Admin\counting\PostalCountingController@evm_votes_finalized');
          Route::POST('/finalize-evm', 'Admin\counting\PostalCountingController@finalize_evm');
          Route::POST('/finalize-ac-counting', 'Admin\counting\PostalCountingController@finalize_ac_counting');

          Route::get('/boothcounting-results', 'Admin\counting\PostalCountingController@counting_results');  
          Route::post('/boothresults-declaration', 'Admin\counting\PostalCountingController@results_declaration');
          Route::post('/boothresults-declared', 'Admin\counting\PostalCountingController@results_declared');
          Route::get('/boothresults-verified', 'Admin\counting\PostalCountingController@results_verified');
          Route::POST('/booth-tenders-votes', 'Admin\counting\PostalCountingController@tenders_votes');
          Route::post('/bothverify_winner_by_name','Admin\counting\PostalCountingController@verify_winner_by_name');
         Route::post('/boothresult_declared_by_lottery','Admin\counting\PostalCountingController@result_declared_by_lottery');
          Route::GET('/boothballot_pdf', 'Admin\counting\PostalCountingController@ballot_pdf');
 
         Route::get('/form-21-report-pdf', 'Admin\counting\FormgeneratedreportController@getForm21Pdf');
        
        //Form 21C
        Route::get('/form-21c-report', 'Admin\counting\FormgeneratedreportController@getForm21C');
        Route::get('/form-21c-report-pdf', 'Admin\counting\FormgeneratedreportController@getForm21CPdf');
        Route::get('/form-21-report-upload', 'Admin\counting\FormgeneratedreportController@getForm21CUpload');
        Route::post('/form-21-report-upload', 'Admin\counting\FormgeneratedreportController@storeFile');
		Route::post('/upload-results', 'Admin\counting\BoothCountingController@store_upload_results');
		 Route::post('/upload-postal-results', 'Admin\counting\BoothCountingController@upload_postal_results');

    // //  end booth level counting postal-counting-finalized  
	
	Route::get('/constituency-wise-report','Admin\counting\ConstituencyWiseReportController@index'); 
        Route::post('/get-pc-by-state-id-eci-pcwise-constituency', 'Admin\counting\ConstituencyWiseReportController@getMatchedPcByStateId');  
        Route::post('/get-ac-by-state-and-pc-id-eci-pcwise-constituency', 'Admin\counting\ConstituencyWiseReportController@getMatchedAc');    
        Route::post('/get-condidate-details-eci-pcwise-constituency', 'Admin\counting\ConstituencyWiseReportController@getCondidfateListpkpk');
        Route::post('/get-all-result-eci-pcwise-constituency', 'Admin\counting\ConstituencyWiseReportController@getCompleteResult');
        Route::post('/csvDownload-pcwise-constituency', 'Admin\counting\ConstituencyWiseReportController@csvDownload');
		
		   //Schedule Round REPORT
    Route::get('/BoothCountingScheduleReport','Admin\counting\BoothCountingScheduleReportController@BoothCountingScheduleReport');
  Route::post('/BoothCountingScheduleReport','Admin\counting\BoothCountingScheduleReportController@BoothCountingScheduleReport');
  Route::get('/boothstate-by-ac/{s_code}','Admin\counting\BoothCountingScheduleReportController@acList');
  Route::get('/boothschedule-report-pdf/{s_code}/{ac_id}','Admin\counting\BoothCountingScheduleReportController@BoothCountingSchedulePdf');
  Route::get('/boothschedule-report-excel/{s_code}/{ac_id}','Admin\counting\BoothCountingScheduleReportController@BoothCountingScheduleExcel');
  
  //Round Wise Report
        Route::get('/boothround-wise-report','Admin\counting\BoothRoundWiseReportController@index'); 
  Route::post('/boothget-pc-by-state-id-eci', 'Admin\counting\BoothRoundWiseReportController@getMatchedPcByStateId'); 
  Route::post('/boothget-ac-by-state-and-pc-id-eci', 'Admin\counting\BoothRoundWiseReportController@getMatchedAc'); 
  Route::post('/boothget-condidate-details-eci', 'Admin\counting\BoothRoundWiseReportController@getCondidfateListpkpk');
  Route::post('/boothget-condidate-details-eci-ac-wise', 'Admin\counting\BoothRoundWiseReportController@getCondidfateListAcWise');
  Route::post('/boothget-all-result-eci', 'Admin\counting\BoothRoundWiseReportController@getCompleteResult');
  Route::post('/boothcsvDownload', 'Admin\counting\BoothRoundWiseReportController@csvDownload'); 
  
   //TABLE REPORTS STARTS
	Route::get('/report_state/state/ac', 'Admin\counting\BoothCountingTableReportController@report_ac');
	Route::get('/report_state/state/ac/excel', 'Admin\counting\BoothCountingTableReportController@export_excel_report_ac');
	Route::get('/report_state/state/ac/pdf', 'Admin\counting\BoothCountingTableReportController@export_pdf_report_ac');
    
				
  });
  
  Route::group(['prefix' => 'booth-counting', 'middleware' => ['ro']], function(){
    Route::get('/active-user-report', 'Admin\BoothCountingReport\ActiveUserReportController@show_active_user_count');
    Route::get('/active-user-detail', 'Admin\BoothCountingReport\ActiveUserReportController@show_active_user');
	Route::get('/export_pdf_report_state', 'Admin\BoothCountingReport\ActiveUserReportController@export_pdf_report_state');
	Route::get('/export_pdf_count', 'Admin\BoothCountingReport\ActiveUserReportController@export_pdf_count');
	Route::get('/export_excel_active_users', 'Admin\BoothCountingReport\ActiveUserReportController@export_excel_active_users');
	Route::get('/export_excel_active_users_details', 'Admin\BoothCountingReport\ActiveUserReportController@export_excel_active_users_details');
	Route::get('/candidate-wise-report', 'Report\VoterTypeWiseReportController@reportIndex');
    Route::get('/candidate-wise-report-get-ac-state/{state}', 'Report\VoterTypeWiseReportController@getAcByState');
    Route::post('/candidate-wise-report-ac', 'Report\VoterTypeWiseReportController@getSearchReport');
    Route::get('/candidate-wise-report-get-party/{ac}/{state}', 'Report\VoterTypeWiseReportController@getPartyByAc');
    Route::post('/candidate-wise-report-excel', 'Report\VoterTypeWiseReportController@getReportExcel');
    Route::post('/candidate-wise-report-pdf', 'Report\VoterTypeWiseReportController@getReportPdf');
	});
  
});
//ROAC TURNOUT ROUTES ENDS


});
