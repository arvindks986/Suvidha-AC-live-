<?php  

Route::group(['middleware' => ['adminsession','auth:admin', 'auth']], function(){
    Route::get('push-event','Admin\BoothAppRevamp\PollingController@event_pusher');
    Route::get('fixed-ps-no','Admin\BoothAppRevamp\FixedController@index');

    Route::get('load-state-by-phase','Admin\Common\CommonBoothAppController@load_state_by_ajax');
    Route::get('load-ac-by-state','Admin\Common\CommonBoothAppController@load_ac_by_ajax');
    Route::get('load-ps-by-ac','Admin\Common\CommonBoothAppController@load_ps_by_ajax');
});

Route::get('/clear-revamp/{st_code?}/{ac_no?}/{ps_no?}','Admin\Common\CommonBoothAppController@index');

//Route::group(['middleware' => ['by_pass_security']], function(){
    Route::group(['prefix' => 'roac', 'middleware' => ['adminsession','auth:admin', 'auth','ro']], function(){

    //Booth level app
      Route::group(['prefix' => 'booth-app-revamp'], function(){
Route::get('search_officer','Admin\BoothAppRevamp\OfficerController@search_officer');

        //officer not activated
    Route::get('/not-activated-officer','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report');
    Route::get('/not-activated-officer/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_excel');
    Route::get('/not-activated-officer/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_pdf');
    Route::get('/not-activated-officer/ac','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac');
    Route::get('/not-activated-officer/ac/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_excel');
    Route::get('/not-activated-officer/ac/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_pdf');
    Route::get('/not-activated-officer/ac/ps','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps');
    Route::get('/not-activated-officer/ac/ps/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_excel');
    Route::get('/not-activated-officer/ac/ps/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_pdf');

		  
		  Route::get('import-excel','Admin\BoothAppRevamp\OfficerController@import_excel');
        Route::post('upload-excel','Admin\BoothAppRevamp\OfficerController@upload_excel');
        Route::post('confirm-import','Admin\BoothAppRevamp\OfficerController@verify_and_import');
		  
		  
		//officer-assignment-report state
        Route::get('/officer-assignment-report','Admin\BoothAppRevamp\BoarController@officer_assignment_report');
        Route::get('/officer-assignment-report/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_excel');
        Route::get('/officer-assignment-report/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_pdf');
        Route::get('/officer-assignment-report/ac','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac');
        Route::get('/officer-assignment-report/ac/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_excel');
        Route::get('/officer-assignment-report/ac/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_pdf');
        Route::get('/officer-assignment-report/ac/ps','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps');
        Route::get('/officer-assignment-report/ac/ps/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_excel');
        Route::get('/officer-assignment-report/ac/ps/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_pdf');

        //poll-turnout-report ac
        Route::get('/poll-turnout-report/state/ac','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac');
        Route::get('/poll-turnout-report/state/ac/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_excel');
        Route::get('/poll-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_pdf');

        //poll-turnout-report ps
        Route::get('/poll-turnout-report/state/ac/ps','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps');
        Route::get('/poll-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_excel');
        Route::get('/poll-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_pdf');
        //blo pro turnout ac
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ac/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report_excel');
        Route::get('ac/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report_pdf');
        //blo pro turnout ps
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');
        Route::get('ps/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report_excel');
        Route::get('ps/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report_pdf');
		
		//evm-comparision ac
        Route::get('evm-comparision/state/ac','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report');
        Route::get('evm-comparision/state/ac/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report_excel');
        Route::get('evm-comparision/state/ac/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report_pdf');
        //evm-comparision ps
        Route::get('evm-comparision/state/ac/ps','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report');
        Route::get('evm-comparision/state/ac/ps/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report_excel');
        Route::get('evm-comparision/state/ac/ps/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report_pdf');
        //report pradeepro ends
		
		Route::get('poll-material','Admin\BoothAppRevamp\PollMaterialController@state');
        Route::get('poll-material/ac','Admin\BoothAppRevamp\PollMaterialController@ac');
        Route::get('poll-material/ac/ps','Admin\BoothAppRevamp\PollMaterialController@ps');

        Route::get('incident','Admin\BoothAppRevamp\IncidentController@state');
        Route::get('incident/ac','Admin\BoothAppRevamp\IncidentController@ac');
        Route::get('incident/ac/ps','Admin\BoothAppRevamp\IncidentController@ps');
        Route::get('incident/ac/ps/download','Admin\BoothAppRevamp\IncidentController@ps_download');
        Route::get('mock-poll','Admin\BoothAppRevamp\MockPollController@state');
        Route::get('mock-poll/ac','Admin\BoothAppRevamp\MockPollController@ac');
        Route::get('mock-poll/ac/ps','Admin\BoothAppRevamp\MockPollController@ps');

        Route::get('infra','Admin\BoothAppRevamp\InfraController@state');
        Route::get('infra/ac','Admin\BoothAppRevamp\InfraController@ac');
        Route::get('infra/ac/ps','Admin\BoothAppRevamp\InfraController@ps');

        Route::get('/officer-list','Admin\BoothAppRevamp\PollingController@get_polling_station');
        Route::get('/officer-list/add/{id}','Admin\BoothAppRevamp\PollingController@add_officer');
        Route::post('/officer-list/post','Admin\BoothAppRevamp\PollingController@post_officer');
        Route::get('/officer-list/post',function(){
          return redirect('roac/booth-app-revamp/officer-list');
        });
Route::get('electors-verification-by-ps','Admin\BoothAppRevamp\ElectorsVerificationByPsController@index');
        Route::post('electors-verification-by-ps/post','Admin\BoothAppRevamp\ElectorsVerificationByPsController@post');

        //ps location
        Route::get('exempted','Admin\BoothAppRevamp\ExemptedPollingStationController@exempted');
        Route::post('exempted/post','Admin\BoothAppRevamp\ExemptedPollingStationController@post_exempted');
        Route::get('exempted/post',function(){
          return redirect('roac/booth-app-revamp/exempted');
        });

		//exempted turnout
        Route::get('exempted-turnout','Admin\BoothAppRevamp\ExemptedPollingStationController@turnout');
        Route::post('exempted/post-turnout','Admin\BoothAppRevamp\ExemptedPollingStationController@post_turnout');
        Route::get('exempted/post-turnout',function(){
          return redirect('roac/booth-app-revamp/exempted-turnout');
        });

        //exempted turnout boothapp praveen
        Route::get('exempted-boothapp-pollingstation','Admin\BoothAppRevamp\PollingController@turnout_new');
        Route::get('view-exempted-pollingstation','Admin\BoothAppRevamp\PollingController@view_turnout_new');
        Route::post('view-exempted-pollingstation','Admin\BoothAppRevamp\PollingController@view_turnout_new');
        Route::post('post-exempted-boothapp-pollingstation','Admin\BoothAppRevamp\PollingController@turnout_new_ajax');
        Route::post('update_turnout_pswise','Admin\BoothAppRevamp\PollingController@update_turnout');
        Route::post('exempt-ps-wise','Admin\BoothAppRevamp\PollingController@exempt_ps_wise');
        Route::post('delete_user_pso','Admin\BoothAppRevamp\PollingController@delete_user_pso');
        
        //ps location
        Route::get('location','Admin\BoothAppRevamp\PollingStationLocationController@location');
        Route::post('location/post','Admin\BoothAppRevamp\PollingStationLocationController@post_location');
        Route::get('location/post',function(){
          return redirect('roac/booth-app-revamp/location');
        });


        //assign so
        Route::get('assign-so','Admin\BoothAppRevamp\OfficerController@assign_so');
        Route::get('assign-so-new','Admin\BoothAppRevamp\OfficerController@assign_so_new');
        Route::post('assign-so/post','Admin\BoothAppRevamp\OfficerController@post_so');
        Route::get('/assign-so/post',function(){
          return redirect('roac/booth-app-revamp/assign-so');
        });
        Route::post('assign-so/save-via-ajax','Admin\BoothAppRevamp\OfficerController@post_so_ajax');
        Route::post('assign-so/save-via-ajax-new','Admin\BoothAppRevamp\OfficerController@post_so_ajax_new');
        Route::post('assign-so/save-sub-so','Admin\BoothAppRevamp\OfficerController@save_sub_so');
        Route::post('assign-so/save-sub-so-new','Admin\BoothAppRevamp\OfficerController@save_sub_so_new');


        //assign blo
        Route::get('assign-blo','Admin\BoothAppRevamp\OfficerController@assign_blo');
        Route::post('assign-blo/post','Admin\BoothAppRevamp\OfficerController@post_blo');
        Route::get('/assign-blo/post',function(){
          return redirect('roac/booth-app-revamp/assign-blo');
        });
        Route::post('assign-blo/save-via-ajax','Admin\BoothAppRevamp\OfficerController@post_blo_ajax');
        Route::post('assign-blo/save-sub-blo','Admin\BoothAppRevamp\OfficerController@save_sub_blo');




        Route::get('/voter-list','Admin\BoothAppRevamp\PollingController@get_voter_list');
        Route::get('/voter-list/{id}','Admin\BoothAppRevamp\PollingController@download_poll_pdf');
        Route::get('/download-electoral-list/{id}','Admin\BoothAppRevamp\PollingController@download_electoral_list');
        Route::get('/dashboard','Admin\BoothAppRevamp\DashboardController@dashboard');
        Route::get('/get_voter_turnout','Admin\BoothAppRevamp\PollingController@get_voter_turnout');
        Route::get('/polling-station','Admin\BoothAppRevamp\PollingController@polling_station');
        Route::get('/officers','Admin\BoothAppRevamp\PollingController@get_officers');
        Route::get('/e-roll-download','Admin\BoothAppRevamp\PollingController@get_e_roll_download');
        Route::get('download-pro-diary','Admin\BoothAppRevamp\PollingController@download_pro_diary');

        //poll detail
        Route::get('/poll-detail','Admin\BoothAppRevamp\PollingController@get_poll_detail');

        Route::get('/get-dashboard-data','Admin\BoothAppRevamp\DashboardController@get_dashboard_data');
		
        Route::get('referesh_age_graph','Admin\BoothAppRevamp\DashboardController@referesh_age_graph');
        Route::get('get_cumulative_time_data','Admin\BoothAppRevamp\DashboardController@get_cumulative_time_data');
        Route::get('get_voters_by_time','Admin\BoothAppRevamp\DashboardController@get_voters_by_time');
        Route::get('get_doughnut_data','Admin\BoothAppRevamp\DashboardController@get_doughnut_data');
        Route::get('get_gender_data','Admin\BoothAppRevamp\DashboardController@get_gender_data');
		
        Route::get('/scan-data','Admin\BoothAppRevamp\PollingController@get_scan_data');
        Route::post('/add_download_log/{id}','Admin\BoothAppRevamp\PollingController@add_download_log');
        Route::post('/reset_otp','Admin\BoothAppRevamp\PollingController@reset_otp');
        Route::post('/reset_pin','Admin\BoothAppRevamp\PollingController@reset_pin');

        Route::get('get-form-17-a','Admin\BoothAppRevamp\PollingController@get_form_17_a');
        Route::get('download-form-17-a','Admin\BoothAppRevamp\PollingController@download_17_a_form');

    // Report
        Route::get('/officer-assignment-report','Admin\BoothAppRevamp\ReportController@officer_assignment_report');
        Route::get('/officer-assignment-report-pdf/{phase_no}','Admin\BoothAppRevamp\ReportController@officer_assignment_report');
        Route::get('/officer-assignment-report-xls/{phase_no}','Admin\BoothAppRevamp\ReportController@officer_assignment_report');
		
		//exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report/state/ac','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac');
        Route::get('/exempt-turnout-report/state/ac/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_excel');
        Route::get('/exempt-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_pdf');

        //exmpt poll-turnout-report ps
        Route::get('/exempt-turnout-report/state/ac/ps','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps');
        Route::get('/exempt-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_excel');
        Route::get('/exempt-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_pdf');
		
		//Sanjay
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');

        Route::get('/poll-event-report','Admin\BoothAppRevamp\ReportController@poll_event_report');
        Route::get('/poll-event-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_report');
        Route::get('/poll-event-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_report');

        Route::get('/poll-event-ps-wise-report','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
        Route::get('/poll-event-ps-wise-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
        Route::get('/poll-event-ps-wise-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
		
		Route::get('/disconnected-ps-report','Admin\BoothAppRevamp\ReportController@getdisconnectedps');
		Route::get('/form49-ps-report','Admin\BoothAppRevamp\ReportController@getform49count');
        Route::get('/tendered-ps-report','Admin\BoothAppRevamp\ReportController@getTenderedcount');

      });

    });


    Route::group(['prefix' => 'eci', 'middleware' => ['adminsession','auth:admin', 'auth','eci']], function(){
    //Booth level app
      Route::group(['prefix' => 'booth-app-revamp'], function(){

        Route::get('search_officer','Admin\BoothAppRevamp\OfficerController@search_officer');

        //officer not activated
    Route::get('/not-activated-officer','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report');
    Route::get('/not-activated-officer/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_excel');
    Route::get('/not-activated-officer/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_pdf');
    Route::get('/not-activated-officer/ac','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac');
    Route::get('/not-activated-officer/ac/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_excel');
    Route::get('/not-activated-officer/ac/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_pdf');
    Route::get('/not-activated-officer/ac/ps','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps');
    Route::get('/not-activated-officer/ac/ps/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_excel');
    Route::get('/not-activated-officer/ac/ps/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_pdf');

		Route::get('poll-material','Admin\BoothAppRevamp\PollMaterialController@state');
        Route::get('poll-material/ac','Admin\BoothAppRevamp\PollMaterialController@ac');
        Route::get('poll-material/ac/ps','Admin\BoothAppRevamp\PollMaterialController@ps');
		
        Route::get('incident','Admin\BoothAppRevamp\IncidentController@state');
        Route::get('incident/ac','Admin\BoothAppRevamp\IncidentController@ac');
        Route::get('incident/ac/ps','Admin\BoothAppRevamp\IncidentController@ps');
        Route::get('incident/ac/ps/download','Admin\BoothAppRevamp\IncidentController@ps_download');
        Route::get('mock-poll','Admin\BoothAppRevamp\MockPollController@state');
        Route::get('mock-poll/ac','Admin\BoothAppRevamp\MockPollController@ac');
        Route::get('mock-poll/ac/ps','Admin\BoothAppRevamp\MockPollController@ps');
        Route::get('infra','Admin\BoothAppRevamp\InfraController@state');
        Route::get('infra/ac','Admin\BoothAppRevamp\InfraController@ac');
        Route::get('infra/ac/ps','Admin\BoothAppRevamp\InfraController@ps');
		
		Route::get('/update-voters','Admin\BoothAppRevamp\VoterTurnoutController@update_voter_turnout');
		  
        Route::get('/officer-list','Admin\BoothAppRevamp\PollingController@get_polling_station');
        Route::get('/voter-list','Admin\BoothAppRevamp\PollingController@get_voter_list');
        Route::get('/voter-list/{id}','Admin\BoothAppRevamp\PollingController@download_poll_pdf');
        Route::get('/download-electoral-list/{id}','Admin\BoothAppRevamp\PollingController@download_electoral_list');
        Route::get('/dashboard','Admin\BoothAppRevamp\DashboardController@dashboard');
        Route::get('/get_voter_turnout','Admin\BoothAppRevamp\PollingController@get_voter_turnout');
        Route::get('/polling-station','Admin\BoothAppRevamp\PollingController@polling_station');
        Route::get('/officers','Admin\BoothAppRevamp\PollingController@get_officers');
        Route::get('/e-roll-download','Admin\BoothAppRevamp\PollingController@get_e_roll_download');
		
        Route::get('download-pro-diary','Admin\BoothAppRevamp\PollingController@download_pro_diary');
		Route::get('get-form-17-a','Admin\BoothAppRevamp\PollingController@get_form_17_a');
        Route::get('download-form-17-a','Admin\BoothAppRevamp\PollingController@download_17_a_form');
       
        //poll detail
        Route::get('/poll-detail','Admin\BoothAppRevamp\PollingController@get_poll_detail');
        Route::get('/poll-detail/state','Admin\BoothAppRevamp\PollingController@poll_detail_state');
        Route::get('/poll-detail/ac','Admin\BoothAppRevamp\PollingController@poll_detail_ac');


        Route::get('/get-dashboard-data','Admin\BoothAppRevamp\DashboardController@get_dashboard_data');
		
        Route::get('referesh_age_graph','Admin\BoothAppRevamp\DashboardController@referesh_age_graph');
        Route::get('get_cumulative_time_data','Admin\BoothAppRevamp\DashboardController@get_cumulative_time_data');
        Route::get('get_voters_by_time','Admin\BoothAppRevamp\DashboardController@get_voters_by_time');
        Route::get('get_doughnut_data','Admin\BoothAppRevamp\DashboardController@get_doughnut_data');
        Route::get('get_gender_data','Admin\BoothAppRevamp\DashboardController@get_gender_data');
		
        Route::get('/scan-data','Admin\BoothAppRevamp\PollingController@get_scan_data');
        Route::post('/add_download_log/{id}','Admin\BoothAppRevamp\PollingController@add_download_log');
        Route::get('/generate_polling_station','Admin\BoothAppRevamp\PollingController@generate_polling_station');
        Route::get('/generate_electors','Admin\BoothAppRevamp\PollingController@generate_electors');
        

        // Report
		
		//officer-assignment-report state
        Route::get('/officer-assignment-report','Admin\BoothAppRevamp\BoarController@officer_assignment_report');
        Route::get('/officer-assignment-report/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_excel');
        Route::get('/officer-assignment-report/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_pdf');
        Route::get('/officer-assignment-report/ac','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac');
        Route::get('/officer-assignment-report/ac/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_excel');
        Route::get('/officer-assignment-report/ac/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_pdf');
        Route::get('/officer-assignment-report/ac/ps','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps');
        Route::get('/officer-assignment-report/ac/ps/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_excel');
        Route::get('/officer-assignment-report/ac/ps/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_pdf');
		
		//praveen exempted-poll-turnout-report
		Route::get('/poll-turnout-report-exempted','Admin\BoothAppRevamp\BptoController@poll_turnout_report_exempted');
		Route::get('/poll-turnout-report-exempted/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_exempted_pdf');
		Route::get('/poll-turnout-report-exempted/state/ac','Admin\BoothAppRevamp\BptoController@poll_turnout_report_exempted_ac');
		Route::get('/poll-turnout-report-exempted/state/ac/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_exempted_ac_pdf');
		Route::get('/poll-turnout-report-exempted/state/ac/ps','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_exempted');
		Route::get('/poll-turnout-report-exempted/state/ac/ps/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_exempted_pdf');

        //poll-turnout-report
        Route::get('/poll-turnout-report','Admin\BoothAppRevamp\BptoController@poll_turnout_report');
        Route::get('/poll-turnout-report/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_excel');
        Route::get('/poll-turnout-report/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_pdf');

        //poll-turnout-report ac
        Route::get('/poll-turnout-report/state/ac','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac');
        Route::get('/poll-turnout-report/state/ac/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_excel');
        Route::get('/poll-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_pdf');

        //poll-turnout-report ps
        Route::get('/poll-turnout-report/state/ac/ps','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps');
        Route::get('/poll-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_excel');
        Route::get('/poll-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_pdf');
		
		//blo pro turnout state
        Route::get('state/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report');
        Route::get('state/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report_excel');
        Route::get('state/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report_pdf');
        //blo pro turnout ac
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ac/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report_excel');
        Route::get('ac/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report_pdf');
        //blo pro turnout ps
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');
        Route::get('ps/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report_excel');
        Route::get('ps/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report_pdf');
		
		 //evm-comparision state
        Route::get('evm-comparision/state','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_state_report');
        Route::get('evm-comparision/state/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_state_report_excel');
        Route::get('evm-comparision/state/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_state_report_pdf');
        //evm-comparision ac
        Route::get('evm-comparision/state/ac','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report');
        Route::get('evm-comparision/state/ac/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report_excel');
        Route::get('evm-comparision/state/ac/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report_pdf');
        //evm-comparision ps
        Route::get('evm-comparision/state/ac/ps','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report');
        Route::get('evm-comparision/state/ac/ps/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report_excel');
        Route::get('evm-comparision/state/ac/ps/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report_pdf');
		
		//exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report');
        Route::get('/exempt-turnout-report/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_excel');
        Route::get('/exempt-turnout-report/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_pdf');

        //exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report/state/ac','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac');
        Route::get('/exempt-turnout-report/state/ac/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_excel');
        Route::get('/exempt-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_pdf');

        //exmpt poll-turnout-report ps
        Route::get('/exempt-turnout-report/state/ac/ps','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps');
        Route::get('/exempt-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_excel');
        Route::get('/exempt-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_pdf');
        

        // Report pradeepeci ends
        /*Route::get('/officer-assignment-report','Admin\BoothAppRevamp\ReportController@officer_assignment_report');
        Route::get('/officer-assignment-report-pdf/{phase_no}','Admin\BoothAppRevamp\ReportController@officer_assignment_report');
        Route::get('/officer-assignment-report-xls/{phase_no}','Admin\BoothAppRevamp\ReportController@officer_assignment_report');
		
		Route::get('/officer-assignment-ps-wise-report','Admin\BoothAppRevamp\ReportController@officer_assignment_ps_wise_report');
        Route::get('/officer-assignment-ps-wise-report-pdf','Admin\BoothAppRevamp\ReportController@officer_assignment_ps_wise_report_pdf');
        Route::get('/officer-assignment-ps-wise-report-xls','Admin\BoothAppRevamp\ReportController@officer_assignment_ps_wise_report_xls');

        Route::get('/poll-turnout-report','Admin\BoothAppRevamp\ReportController@poll_turnout_report');
        Route::get('/poll-turnout-report-pdf','Admin\BoothAppRevamp\ReportController@poll_turnout_report');
        Route::get('/poll-turnout-report-xls','Admin\BoothAppRevamp\ReportController@poll_turnout_report');*/
        
        Route::get('/poll-event-report','Admin\BoothAppRevamp\ReportController@poll_event_report');
        Route::get('/poll-event-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_report');
        Route::get('/poll-event-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_report');

    	Route::get('/poll-event-ps-wise-report','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
        Route::get('/poll-event-ps-wise-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
        Route::get('/poll-event-ps-wise-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
		
		//Sanjay
		Route::get('/mapped-location-report','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report');
		Route::get('/mapped-location-report/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_excel');
		Route::get('/mapped-location-report/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_pdf');
		
		Route::get('/mapped-location-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac');
		Route::get('/mapped-location-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac_excel');
		Route::get('/mapped-location-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac_pdf');
		
		Route::get('/mapped-location-ps-wise-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise');
        Route::get('/mapped-location-ps-wise-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise_excel');
        Route::get('/mapped-location-ps-wise-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise_pdf');
		
		Route::get('/unmapped-location-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report');
		Route::get('/unmapped-location-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report_excel');
		Route::get('/unmapped-location-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report_pdf');

				//Elector verification report
		Route::get('/elector-verify-report','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report');
		Route::get('/elector-verify-report/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_excel');
		Route::get('/elector-verify-report/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_pdf');
		
		Route::get('/elector-verify-report/state/ac','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac');
		Route::get('/elector-verify-report/state/ac/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac_excel');
		Route::get('/elector-verify-report/state/ac/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac_pdf');
		
		Route::get('/elector-verify-ps-wise-report/state/ac','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report');
		Route::get('/elector-verify-ps-wise-report/state/ac/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report_excel');
		Route::get('/elector-verify-ps-wise-report/state/ac/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report_pdf');

				//exmpt poll-turnout-report ac
		Route::get('/exempt-turnout-report','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report');
        Route::get('/exempt-turnout-report/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_excel');
        Route::get('/exempt-turnout-report/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_pdf');
		
		//exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report/state/ac','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac');
        Route::get('/exempt-turnout-report/state/ac/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_excel');
        Route::get('/exempt-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_pdf');

        //exmpt poll-turnout-report ps
        Route::get('/exempt-turnout-report/state/ac/ps','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps');
        Route::get('/exempt-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_excel');
        Route::get('/exempt-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_pdf');

        	//exempt ps count report
		Route::get('/exemted-ps-count-report','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report');
        Route::get('/exemted-ps-count-report/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_excel');
        Route::get('/exemted-ps-count-report/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_pdf');
		
		//exempt ps count report ac
        Route::get('/exemted-ps-count-report/state/ac','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac');
        Route::get('/exemted-ps-count-report/state/ac/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac_excel');
        Route::get('/exemted-ps-count-report/state/ac/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac_pdf');

        //exempt ps count report ps
        Route::get('/exemted-ps-count-pswise-report/state/ac','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise');
        Route::get('/exemted-ps-count-pswise-report/state/ac/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise_excel');
        Route::get('/exemted-ps-count-pswise-report/state/ac/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise_pdf');
		
		

		//cleared ps count report
		Route::get('/cleared-ps-count-report','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report');
        Route::get('/cleared-ps-count-report/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_excel');
        Route::get('/cleared-ps-count-report/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_pdf');
		
		//cleared ps count report ac
        Route::get('/cleared-ps-count-report/state/ac','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac');
        Route::get('/cleared-ps-count-report/state/ac/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac_excel');
        Route::get('/cleared-ps-count-report/state/ac/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac_pdf');

        //cleared ps count report ps
        Route::get('/cleared-ps-count-pswise-report/state/ac','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise');
        Route::get('/cleared-ps-count-pswise-report/state/ac/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise_excel');
        Route::get('/cleared-ps-count-pswise-report/state/ac/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise_pdf');

        Route::get('state/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report');
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');
		
		Route::get('state/evm-comparision','Admin\BoothAppRevamp\EvmController@evm_comparision_state_report');
        Route::get('ac/evm-comparision','Admin\BoothAppRevamp\EvmController@evm_comparision_ac_report');
        Route::get('ps/evm-comparision','Admin\BoothAppRevamp\EvmController@evm_comparision_ps_report');
		
		Route::get('pro-diary','Admin\BoothAppRevamp\EvmController@get_aggregate_pro_diary');

        Route::get('/poll-event-dashboard','Admin\BoothAppRevamp\ReportController@poll_event_dashboard');
		Route::get('/dashboard_data_analytics','Admin\BoothAppRevamp\ReportController@getanalyticsdashboard');
		Route::get('/disconnected-ps-report','Admin\BoothAppRevamp\ReportController@getdisconnectedps');
		Route::get('/form49-ps-report','Admin\BoothAppRevamp\ReportController@getform49count');
        Route::get('/tendered-ps-report','Admin\BoothAppRevamp\ReportController@getTenderedcount');


      });
    });	


    Route::group(['prefix' => 'acceo', 'middleware' => ['adminsession','auth:admin', 'auth','ceo']], function(){
    //Booth level app
      Route::group(['prefix' => 'booth-app-revamp'], function(){

        Route::get('search_officer','Admin\BoothAppRevamp\OfficerController@search_officer');

        //officer not activated
    Route::get('/not-activated-officer','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report');
    Route::get('/not-activated-officer/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_excel');
    Route::get('/not-activated-officer/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_pdf');
    Route::get('/not-activated-officer/ac','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac');
    Route::get('/not-activated-officer/ac/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_excel');
    Route::get('/not-activated-officer/ac/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_pdf');
    Route::get('/not-activated-officer/ac/ps','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps');
    Route::get('/not-activated-officer/ac/ps/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_excel');
    Route::get('/not-activated-officer/ac/ps/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_pdf');

		Route::get('poll-material','Admin\BoothAppRevamp\PollMaterialController@state');
        Route::get('poll-material/ac','Admin\BoothAppRevamp\PollMaterialController@ac');
        Route::get('poll-material/ac/ps','Admin\BoothAppRevamp\PollMaterialController@ps');

        Route::get('incident','Admin\BoothAppRevamp\IncidentController@state');
        Route::get('incident/ac','Admin\BoothAppRevamp\IncidentController@ac');
        Route::get('incident/ac/ps','Admin\BoothAppRevamp\IncidentController@ps');
        Route::get('incident/ac/ps/download','Admin\BoothAppRevamp\IncidentController@ps_download');
        Route::get('mock-poll','Admin\BoothAppRevamp\MockPollController@state');
        Route::get('mock-poll/ac','Admin\BoothAppRevamp\MockPollController@ac');
        Route::get('mock-poll/ac/ps','Admin\BoothAppRevamp\MockPollController@ps');
        Route::get('infra','Admin\BoothAppRevamp\InfraController@state');
        Route::get('infra/ac','Admin\BoothAppRevamp\InfraController@ac');
        Route::get('infra/ac/ps','Admin\BoothAppRevamp\InfraController@ps');


        Route::get('/officer-list','Admin\BoothAppRevamp\PollingController@get_polling_station');
        Route::get('/voter-list','Admin\BoothAppRevamp\PollingController@get_voter_list');
        Route::get('/voter-list/{id}','Admin\BoothAppRevamp\PollingController@download_poll_pdf');
        Route::get('/download-electoral-list/{id}','Admin\BoothAppRevamp\PollingController@download_electoral_list');
        Route::get('/dashboard','Admin\BoothAppRevamp\DashboardController@dashboard');
        Route::get('/get_voter_turnout','Admin\BoothAppRevamp\PollingController@get_voter_turnout');
        Route::get('/polling-station','Admin\BoothAppRevamp\PollingController@polling_station');
        Route::get('/officers','Admin\BoothAppRevamp\PollingController@get_officers');
        Route::get('/e-roll-download','Admin\BoothAppRevamp\PollingController@get_e_roll_download');
        Route::get('download-pro-diary','Admin\BoothAppRevamp\PollingController@download_pro_diary');
        Route::get('/poll-detail','Admin\BoothAppRevamp\PollingController@get_poll_detail');
        Route::get('/get-dashboard-data','Admin\BoothAppRevamp\DashboardController@get_dashboard_data');
		
        Route::get('referesh_age_graph','Admin\BoothAppRevamp\DashboardController@referesh_age_graph');
        Route::get('get_cumulative_time_data','Admin\BoothAppRevamp\DashboardController@get_cumulative_time_data');
        Route::get('get_voters_by_time','Admin\BoothAppRevamp\DashboardController@get_voters_by_time');
        Route::get('get_doughnut_data','Admin\BoothAppRevamp\DashboardController@get_doughnut_data');
        Route::get('get_gender_data','Admin\BoothAppRevamp\DashboardController@get_gender_data');
		
        Route::get('/scan-data','Admin\BoothAppRevamp\PollingController@get_scan_data');
        Route::post('/add_download_log/{id}','Admin\BoothAppRevamp\PollingController@add_download_log');

    // Report
	
	// Report pradeepacceo starts
	
	 //exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report');
        Route::get('/exempt-turnout-report/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_excel');
        Route::get('/exempt-turnout-report/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_pdf');
		
		Route::get('/poll-turnout-report-exempted','Admin\BoothAppRevamp\BptoController@poll_turnout_report_exempted');
		Route::get('/poll-turnout-report-exempted/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_exempted_pdf');
		Route::get('/poll-turnout-report-exempted/state/ac','Admin\BoothAppRevamp\BptoController@poll_turnout_report_exempted_ac');
		Route::get('/poll-turnout-report-exempted/state/ac/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_exempted_ac_pdf');
		Route::get('/poll-turnout-report-exempted/state/ac/ps','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_exempted');
		Route::get('/poll-turnout-report-exempted/state/ac/ps/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_exempted_pdf');

        //exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report/state/ac','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac');
        Route::get('/exempt-turnout-report/state/ac/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_excel');
        Route::get('/exempt-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_pdf');

        //exmpt poll-turnout-report ps
        Route::get('/exempt-turnout-report/state/ac/ps','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps');
        Route::get('/exempt-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_excel');
        Route::get('/exempt-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_pdf');
       //officer-assignment-report state
        Route::get('/officer-assignment-report','Admin\BoothAppRevamp\BoarController@officer_assignment_report');
        Route::get('/officer-assignment-report/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_excel');
        Route::get('/officer-assignment-report/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_pdf');
        Route::get('/officer-assignment-report/ac','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac');
        Route::get('/officer-assignment-report/ac/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_excel');
        Route::get('/officer-assignment-report/ac/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_pdf');
        Route::get('/officer-assignment-report/ac/ps','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps');
        Route::get('/officer-assignment-report/ac/ps/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_excel');
        Route::get('/officer-assignment-report/ac/ps/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_pdf');

        //poll-turnout-report
        Route::get('/poll-turnout-report','Admin\BoothAppRevamp\BptoController@poll_turnout_report');
        Route::get('/poll-turnout-report/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_excel');
        Route::get('/poll-turnout-report/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_pdf');

        //poll-turnout-report ac
        Route::get('/poll-turnout-report/state/ac','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac');
        Route::get('/poll-turnout-report/state/ac/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_excel');
        Route::get('/poll-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_pdf');

        //poll-turnout-report ps
        Route::get('/poll-turnout-report/state/ac/ps','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps');
        Route::get('/poll-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_excel');
        Route::get('/poll-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_pdf');
		
		//blo pro turnout state
        Route::get('state/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report');
        Route::get('state/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report_excel');
        Route::get('state/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report_pdf');
        //blo pro turnout ac
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ac/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report_excel');
        Route::get('ac/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report_pdf');
        //blo pro turnout ps
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');
        Route::get('ps/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report_excel');
        Route::get('ps/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report_pdf');
		
		//evm-comparision state
        Route::get('evm-comparision/state','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_state_report');
        Route::get('evm-comparision/state/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_state_report_excel');
        Route::get('evm-comparision/state/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_state_report_pdf');
        //evm-comparision ac
        Route::get('evm-comparision/state/ac','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report');
        Route::get('evm-comparision/state/ac/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report_excel');
        Route::get('evm-comparision/state/ac/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report_pdf');
        //evm-comparision ps
        Route::get('evm-comparision/state/ac/ps','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report');
        Route::get('evm-comparision/state/ac/ps/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report_excel');
        Route::get('evm-comparision/state/ac/ps/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report_pdf');

    //report pradeepacceo ends
	
        /*Route::get('/officer-assignment-report','Admin\BoothAppRevamp\ReportController@officer_assignment_report');
        Route::get('/officer-assignment-report-pdf/{phase_no}','Admin\BoothAppRevamp\ReportController@officer_assignment_report');
        Route::get('/officer-assignment-report-xls/{phase_no}','Admin\BoothAppRevamp\ReportController@officer_assignment_report');*/

        //poll detail
        Route::get('/poll-detail','Admin\BoothAppRevamp\PollingController@get_poll_detail');
        Route::get('/poll-detail/ac','Admin\BoothAppRevamp\PollingController@poll_detail_ac');
		
		//Sanjay
        Route::get('/mapped-location-report','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report');
        Route::get('/mapped-location-report/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_excel');
        Route::get('/mapped-location-report/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_pdf');
        
        Route::get('/mapped-location-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac');
        Route::get('/mapped-location-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac_excel');
        Route::get('/mapped-location-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac_pdf');
        
        Route::get('/mapped-location-ps-wise-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise');
        Route::get('/mapped-location-ps-wise-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise_excel');
		
		Route::get('/unmapped-location-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report');
		Route::get('/unmapped-location-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report_excel');
		Route::get('/unmapped-location-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report_pdf');


		
        Route::get('/mapped-location-ps-wise-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise_pdf');
        		//Elector verification report
		Route::get('/elector-verify-report','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report');
		Route::get('/elector-verify-report/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_excel');
		Route::get('/elector-verify-report/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_pdf');
		
		Route::get('/elector-verify-report/state/ac','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac');
		Route::get('/elector-verify-report/state/ac/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac_excel');
		Route::get('/elector-verify-report/state/ac/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac_pdf');
		
		Route::get('/elector-verify-ps-wise-report/state/ac','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report');
		Route::get('/elector-verify-ps-wise-report/state/ac/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report_excel');
		Route::get('/elector-verify-ps-wise-report/state/ac/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report_pdf');

        Route::get('/poll-event-report','Admin\BoothAppRevamp\ReportController@poll_event_report');
        Route::get('/poll-event-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_report');
        Route::get('/poll-event-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_report');

        Route::get('/poll-event-ps-wise-report','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
        Route::get('/poll-event-ps-wise-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
        Route::get('/poll-event-ps-wise-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');

        //exmpt poll-turnout-report ac
		Route::get('/exempt-turnout-report','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report');
        Route::get('/exempt-turnout-report/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_excel');
        Route::get('/exempt-turnout-report/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_pdf');
		
		//exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report/state/ac','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac');
        Route::get('/exempt-turnout-report/state/ac/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_excel');
        Route::get('/exempt-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_pdf');

        //exmpt poll-turnout-report ps
        Route::get('/exempt-turnout-report/state/ac/ps','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps');
        Route::get('/exempt-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_excel');
        Route::get('/exempt-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_pdf');

        	//exempt ps count report
		Route::get('/exemted-ps-count-report','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report');
        Route::get('/exemted-ps-count-report/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_excel');
        Route::get('/exemted-ps-count-report/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_pdf');
		
		//exempt ps count report ac
        Route::get('/exemted-ps-count-report/state/ac','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac');
        Route::get('/exemted-ps-count-report/state/ac/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac_excel');
        Route::get('/exemted-ps-count-report/state/ac/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac_pdf');

        //exempt ps count report ps
        Route::get('/exemted-ps-count-pswise-report/state/ac','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise');
        Route::get('/exemted-ps-count-pswise-report/state/ac/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise_excel');
        Route::get('/exemted-ps-count-pswise-report/state/ac/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise_pdf');
		
		

		//cleared ps count report
		Route::get('/cleared-ps-count-report','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report');
        Route::get('/cleared-ps-count-report/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_excel');
        Route::get('/cleared-ps-count-report/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_pdf');
		
		//cleared ps count report ac
        Route::get('/cleared-ps-count-report/state/ac','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac');
        Route::get('/cleared-ps-count-report/state/ac/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac_excel');
        Route::get('/cleared-ps-count-report/state/ac/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac_pdf');

        //cleared ps count report ps
        Route::get('/cleared-ps-count-pswise-report/state/ac','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise');
        Route::get('/cleared-ps-count-pswise-report/state/ac/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise_excel');
        Route::get('/cleared-ps-count-pswise-report/state/ac/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise_pdf');


        Route::get('state/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report');
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');

        
        Route::get('state/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report');
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');
		
		Route::get('/disconnected-ps-report','Admin\BoothAppRevamp\ReportController@getdisconnectedps');
		Route::get('/form49-ps-report','Admin\BoothAppRevamp\ReportController@getform49count');
        Route::get('/tendered-ps-report','Admin\BoothAppRevamp\ReportController@getTenderedcount');


      });
    });



    ######################### REPORT By  Mayank ############################
    Route::group(['prefix' => 'acdeo', 'middleware' => ['adminsession','auth:admin', 'auth','deo']], function(){
    //Booth level app
      Route::group(['prefix' => 'booth-app-revamp'], function(){

        Route::get('search_officer','Admin\BoothAppRevamp\OfficerController@search_officer');
		Route::get('/disconnected-ps-report','Admin\BoothAppRevamp\ReportController@getdisconnectedps');
		Route::get('/form49-ps-report','Admin\BoothAppRevamp\ReportController@getform49count');
        Route::get('/tendered-ps-report','Admin\BoothAppRevamp\ReportController@getTenderedcount');

		
        //officer not activated
    Route::get('/not-activated-officer','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report');
    Route::get('/not-activated-officer/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_excel');
    Route::get('/not-activated-officer/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_pdf');
    Route::get('/not-activated-officer/ac','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac');
    Route::get('/not-activated-officer/ac/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_excel');
    Route::get('/not-activated-officer/ac/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_pdf');
    Route::get('/not-activated-officer/ac/ps','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps');
    Route::get('/not-activated-officer/ac/ps/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_excel');
    Route::get('/not-activated-officer/ac/ps/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_pdf');
		  
		Route::get('poll-material','Admin\BoothAppRevamp\PollMaterialController@state');
        Route::get('poll-material/ac','Admin\BoothAppRevamp\PollMaterialController@ac');
        Route::get('poll-material/ac/ps','Admin\BoothAppRevamp\PollMaterialController@ps');

        Route::get('incident','Admin\BoothAppRevamp\IncidentController@state');
        Route::get('incident/ac','Admin\BoothAppRevamp\IncidentController@ac');
        Route::get('incident/ac/ps','Admin\BoothAppRevamp\IncidentController@ps');
        Route::get('incident/ac/ps/download','Admin\BoothAppRevamp\IncidentController@ps_download');
        Route::get('mock-poll','Admin\BoothAppRevamp\MockPollController@state');
        Route::get('mock-poll/ac','Admin\BoothAppRevamp\MockPollController@ac');
        Route::get('mock-poll/ac/ps','Admin\BoothAppRevamp\MockPollController@ps');
        Route::get('infra','Admin\BoothAppRevamp\InfraController@state');
        Route::get('infra/ac','Admin\BoothAppRevamp\InfraController@ac');
        Route::get('infra/ac/ps','Admin\BoothAppRevamp\InfraController@ps');


        Route::get('/officer-list','Admin\BoothAppRevamp\PollingController@get_polling_station');
        Route::get('/voter-list','Admin\BoothAppRevamp\PollingController@get_voter_list');
        Route::get('/voter-list/{id}','Admin\BoothAppRevamp\PollingController@download_poll_pdf');
        Route::get('/download-electoral-list/{id}','Admin\BoothAppRevamp\PollingController@download_electoral_list');
        Route::get('/dashboard','Admin\BoothAppRevamp\DashboardController@dashboard');
        Route::get('/get_voter_turnout','Admin\BoothAppRevamp\PollingController@get_voter_turnout');
        Route::get('/polling-station','Admin\BoothAppRevamp\PollingController@polling_station');
        Route::get('/officers','Admin\BoothAppRevamp\PollingController@get_officers');
        Route::get('/e-roll-download','Admin\BoothAppRevamp\PollingController@get_e_roll_download');
        Route::get('download-pro-diary','Admin\BoothAppRevamp\PollingController@download_pro_diary');

        Route::get('/get-dashboard-data','Admin\BoothAppRevamp\DashboardController@get_dashboard_data');
		
        Route::get('referesh_age_graph','Admin\BoothAppRevamp\DashboardController@referesh_age_graph');
        Route::get('get_cumulative_time_data','Admin\BoothAppRevamp\DashboardController@get_cumulative_time_data');
        Route::get('get_voters_by_time','Admin\BoothAppRevamp\DashboardController@get_voters_by_time');
        Route::get('get_doughnut_data','Admin\BoothAppRevamp\DashboardController@get_doughnut_data');
        Route::get('get_gender_data','Admin\BoothAppRevamp\DashboardController@get_gender_data');
		
        Route::get('/scan-data','Admin\BoothAppRevamp\PollingController@get_scan_data');
        Route::post('/add_download_log/{id}','Admin\BoothAppRevamp\PollingController@add_download_log');

        //poll detail
        Route::get('/poll-detail','Admin\BoothAppRevamp\PollingController@get_poll_detail');
        Route::get('/poll-detail/ac','Admin\BoothAppRevamp\PollingController@poll_detail_ac');
		
		//officer-assignment-report state
        Route::get('/officer-assignment-report','Admin\BoothAppRevamp\BoarController@officer_assignment_report');
        Route::get('/officer-assignment-report/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_excel');
        Route::get('/officer-assignment-report/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_pdf');
        Route::get('/officer-assignment-report/ac','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac');
        Route::get('/officer-assignment-report/ac/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_excel');
        Route::get('/officer-assignment-report/ac/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_pdf');
        Route::get('/officer-assignment-report/ac/ps','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps');
        Route::get('/officer-assignment-report/ac/ps/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_excel');
        Route::get('/officer-assignment-report/ac/ps/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_pdf');

        //poll-turnout-report ac
        Route::get('/poll-turnout-report/state/ac','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac');
        Route::get('/poll-turnout-report/state/ac/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_excel');
        Route::get('/poll-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_pdf');
        
        //poll-turnout-report ps
        Route::get('/poll-turnout-report/state/ac/ps','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps');
        Route::get('/poll-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_excel');
        Route::get('/poll-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_pdf');
		
		//blo pro turnout ac
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ac/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report_excel');
        Route::get('ac/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report_pdf');
        //blo pro turnout ps
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');
        Route::get('ps/blo-pro-difference/excel','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report_excel');
        Route::get('ps/blo-pro-difference/pdf','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report_pdf');
		
		 //evm-comparision ac
        Route::get('evm-comparision/state/ac','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report');
        Route::get('evm-comparision/state/ac/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report_excel');
        Route::get('evm-comparision/state/ac/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ac_report_pdf');
        //evm-comparision ps
        Route::get('evm-comparision/state/ac/ps','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report');
        Route::get('evm-comparision/state/ac/ps/excel','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report_excel');
        Route::get('evm-comparision/state/ac/ps/pdf','Admin\BoothAppRevamp\BevmCompareController@evm_comparision_ps_report_pdf');
		
		
        //exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report/state/ac','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac');
        Route::get('/exempt-turnout-report/state/ac/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_excel');
        Route::get('/exempt-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_pdf');

        //exmpt poll-turnout-report ps
        Route::get('/exempt-turnout-report/state/ac/ps','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps');
        Route::get('/exempt-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_excel');
        Route::get('/exempt-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_pdf');
    //report pradeepdeo ends
		
		//Sanjay
        
        
        Route::get('/mapped-location-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac');
        Route::get('/mapped-location-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac_excel');
        Route::get('/mapped-location-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac_pdf');
        
        Route::get('/mapped-location-ps-wise-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise');
        Route::get('state/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_state_report');
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');
        Route::get('/mapped-location-ps-wise-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise_excel');
Route::get('/mapped-location-ps-wise-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise_pdf');

Route::get('/unmapped-location-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report');
Route::get('/unmapped-location-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report_excel');
Route::get('/unmapped-location-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report_pdf');

		//Elector verification report
		Route::get('/elector-verify-report','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report');
		Route::get('/elector-verify-report/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_excel');
		Route::get('/elector-verify-report/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_pdf');
		
		Route::get('/elector-verify-report/state/ac','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac');
		Route::get('/elector-verify-report/state/ac/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac_excel');
		Route::get('/elector-verify-report/state/ac/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_report_ac_pdf');
		
		Route::get('/elector-verify-ps-wise-report/state/ac','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report');
		Route::get('/elector-verify-ps-wise-report/state/ac/excel','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report_excel');
		Route::get('/elector-verify-ps-wise-report/state/ac/pdf','Admin\BoothAppRevamp\ElectorVerifyController@elector_verification_pswise_report_pdf');

        Route::get('/poll-event-report','Admin\BoothAppRevamp\ReportController@poll_event_report');
        Route::get('/poll-event-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_report');
        Route::get('/poll-event-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_report');

        Route::get('/poll-event-ps-wise-report','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
        Route::get('/poll-event-ps-wise-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
        Route::get('/poll-event-ps-wise-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');

        		//exmpt poll-turnout-report ac
		Route::get('/exempt-turnout-report','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report');
        Route::get('/exempt-turnout-report/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_excel');
        Route::get('/exempt-turnout-report/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_pdf');
		
		//exmpt poll-turnout-report ac
        Route::get('/exempt-turnout-report/state/ac','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac');
        Route::get('/exempt-turnout-report/state/ac/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_excel');
        Route::get('/exempt-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ac_pdf');

        //exmpt poll-turnout-report ps
        Route::get('/exempt-turnout-report/state/ac/ps','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps');
        Route::get('/exempt-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_excel');
        Route::get('/exempt-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\ExemptController@exempt_turnout_report_ps_pdf');

        	//exempt ps count report
		Route::get('/exemted-ps-count-report','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report');
        Route::get('/exemted-ps-count-report/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_excel');
        Route::get('/exemted-ps-count-report/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_pdf');
		
		

		//cleared ps count report
		Route::get('/cleared-ps-count-report','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report');
        Route::get('/cleared-ps-count-report/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_excel');
        Route::get('/cleared-ps-count-report/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_pdf');
		
		//cleared ps count report ac
        Route::get('/cleared-ps-count-report/state/ac','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac');
        Route::get('/cleared-ps-count-report/state/ac/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac_excel');
        Route::get('/cleared-ps-count-report/state/ac/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_report_ac_pdf');

        //cleared ps count report ps
        Route::get('/cleared-ps-count-pswise-report/state/ac','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise');
        Route::get('/cleared-ps-count-pswise-report/state/ac/excel','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise_excel');
        Route::get('/cleared-ps-count-pswise-report/state/ac/pdf','Admin\BoothAppRevamp\ClearedPollController@cleared_ps_count_ps_wise_pdf');
		
		//exempt ps count report ac
        Route::get('/exemted-ps-count-report/state/ac','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac');
        Route::get('/exemted-ps-count-report/state/ac/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac_excel');
        Route::get('/exemted-ps-count-report/state/ac/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_report_ac_pdf');

        //exempt ps count report ps
        Route::get('/exemted-ps-count-pswise-report/state/ac','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise');
        Route::get('/exemted-ps-count-pswise-report/state/ac/excel','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise_excel');
        Route::get('/exemted-ps-count-pswise-report/state/ac/pdf','Admin\BoothAppRevamp\ExemPsCountController@exemted_ps_count_ps_wise_pdf');
        
        Route::get('ac/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ac_report');
        Route::get('ps/blo-pro-difference','Admin\BoothAppRevamp\StaticticsController@blo_pro_ps_report');

      });
    });
//});