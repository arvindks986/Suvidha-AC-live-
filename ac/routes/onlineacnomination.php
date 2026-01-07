<?php
Route::group(['middleware' => 'adminsession'], function () {

	Route::get('download_candidate_payment_receipt/{nom_id}', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@download_payment_receipt');

//ROAC TURNOUT ROUTES STARTS SACHCHIDA
Route::group(['prefix' => 'roac', 'as' => 'roac::', 'middleware' => ['auth:admin', 'auth']], function(){
		// Sub Module By Look
	
	Route::get('/listallapplicant_prescrutiny', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@list_of_applicants');
	Route::get('/new_updatenomination/{nomid?}', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@updatenominationform');
	Route::POST('/new_newupdatenomination/{nomid?}', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@updatenomination');
	Route::get('/appointment_request', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@appointment_request');
	Route::get('/list_of_applicatiant_pdf', 'Admin\CandNomination\ApplicantController@list_of_applicatiant_pdf');
	Route::post('/decrypt_nom_id', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@decrypt_nom_id');
	Route::get('/appointment_request_pdf', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@appointment_request_pdf');
	Route::post('/submit_prescrutiny_deatils', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@submit_prescrutiny_details');
	Route::post('/cleared_pre_scrutiny', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@cleared_pre_scrutiny'); 
	Route::get('/nomination_detail/{id}','Admin\CandNomination\PreScrutiny\PreScrutinyController@view_nomination');
	Route::get('/detail/{id}','Admin\CandNomination\PreScrutiny\PreScrutinyController@view_nomination_full');
	Route::get('/nomination_detail_download/{id}','Admin\CandNomination\PreScrutiny\PreScrutinyController@download_nomination');
	Route::post('/recipt_details','Admin\CandNomination\PreScrutiny\PreScrutinyController@get_payment_recipt');
	Route::post('/challan_recipt_details','Admin\CandNomination\PreScrutiny\PreScrutinyController@get_challan_payment_recipt');
	Route::post('/appointment_accepted','Admin\CandNomination\PreScrutiny\PreScrutinyController@appointment_accepted');
	Route::post('submit_challan_details', 'Admin\CandNomination\PaymentGateway\EnablePaymentController@update_challan_details');
	// Sub Module End

	Route::get('/listallapplicant', 'Admin\CandNomination\ApplicantController@index'); 
    Route::get('/qrscan', 'Admin\CandNomination\ApplicantController@qrscanfunction'); 
    Route::POST('/verifyqrcode', 'Admin\CandNomination\ApplicantController@Verifyqrcode'); 
    //Route::POST('/candidateinformation','Admin\CandNomination\ApplicantController@candidateinformation'); 
    Route::get('/candidateinformation','Admin\CandNomination\ApplicantController@candidateinformation'); 
    Route::POST('/candidatevalidation','Admin\CandNomination\ApplicantController@candidatevalidation');
    Route::get('/candidatevalidation', 'Admin\CandNomination\ApplicantController@candidatevalidation');
    Route::get('/decisionbyro', 'Admin\CandNomination\ApplicantController@decisionbyro'); 
    Route::get('/finalreceipt', 'Admin\CandNomination\ApplicantController@finalreceipt');  

    Route::POST('/decisionvalidate', 'Admin\CandNomination\ApplicantController@decisionvalidate'); 
    Route::GET('/decisionvalidate', 'Admin\CandNomination\ApplicantController@decisionvalidate'); 
    Route::get('/decisionvalidatel', 'Admin\CandNomination\ApplicantController@decisionvalidatel'); 
    Route::POST('/print-receipt', 'Admin\CandNomination\ApplicantController@print_receipt'); 
    Route::GET('/nomination-receipt-print', 'Admin\CandNomination\ApplicantController@nomination_receipt_print');
	Route::GET('/nomination-receipt-print/Hindi', 'Admin\CandNomination\ApplicantController@nomination_receipt_print_hindi');


    Route::group(['prefix' => 'nomination', 'as' => 'nomination::', 'middleware' => ['auth:admin', 'auth']], function(){
    		Route::post('upload','Admin\Nfd\NominationController@upload_files');
    		Route::post('upload-affidavit','Admin\Nfd\NominationController@upload_affidavit');
    		Route::get('/detail/{id}','Admin\Nfd\NominationController@view_nomination');
    		Route::get('/download/{id}','Admin\Nfd\NominationController@download_nomination');

	    Route::get('/apply-nomination-step-1','Admin\CandNomination\OnlineNominationController@apply_nomination_step_1');
	    Route::post('/apply-nomination-step-1','Admin\CandNomination\OnlineNominationController@apply_nomination_step_1');
	    Route::any('/apply-nomination-step-1/post','Admin\CandNomination\OnlineNominationController@save_step_1');
	    Route::get('/apply-nomination-step-2','Admin\CandNomination\OnlineNominationController@apply_nomination_step_2');
	    Route::any('/apply-nomination-step-2/post','Admin\CandNomination\OnlineNominationController@save_step_2');
	    Route::get('/apply-nomination-step-2/{id}','Admin\CandNomination\OnlineNominationController@apply_nomination_step_2');
	    Route::get('/apply-nomination-step-3','Admin\CandNomination\OnlineNominationController@apply_nomination_step_3');
	    Route::any('/apply-nomination-step-3/post-part-1','Admin\CandNomination\OnlineNominationController@save_step_3');
	    Route::get('/apply-nomination-step-4','Admin\CandNomination\OnlineNominationController@apply_nomination_step_4');
	    Route::any('/apply-nomination-step-4/post','Admin\CandNomination\OnlineNominationController@save_step_4');
	    Route::get('/apply-nomination-step-5','Admin\CandNomination\OnlineNominationController@apply_nomination_step_5');
	    Route::any('/apply-nomination-step-5/post','Admin\CandNomination\OnlineNominationController@save_step_5');
	    Route::get('/apply-nomination-step-6','Admin\CandNomination\OnlineNominationController@apply_nomination_step_6');
	    Route::any('/apply-nomination-step-6/post','Admin\CandNomination\OnlineNominationController@save_step_6');
	    Route::get('/apply-nomination-finalize','Admin\CandNomination\OnlineNominationController@apply_nomination_finalize');
	    Route::any('/apply-nomination-finalize/post','Admin\CandNomination\OnlineNominationController@save_nomination_finalize');


	});
	
	// Edit Nomination

	Route::post('/copy-nomination','Admin\CandNomination\EditNomination\NominationController@copy_nomination');
	
	
	Route::post('/save-payment-details-our-end','Admin\CandNomination\EditNomination\NominationController@save_payment_details_our_end');
	Route::get('get-symbol','Admin\CandNomination\EditNomination\NominationController@get_symbol');
	Route::post('/finalize-e-affidavit','Admin\CandNomination\EditNomination\NominationController@finalize_e_affidavit');	
	Route::post('/assign-e-affidavit','Admin\CandNomination\EditNomination\NominationController@assign_e_affidavit');
	Route::post('/delink-e-affidavit','Admin\CandNomination\EditNomination\NominationController@delink_e_affidavit');
	
	
	Route::get('/set-param-prev','Admin\CandNomination\EditNomination\NominationController@set_param_prev');
	Route::get('/prev','Admin\CandNomination\EditNomination\NominationController@prev_show');
	Route::any('/prev/post','Admin\CandNomination\EditNomination\NominationController@prev_save');
	Route::post('/save-bank-prev','Admin\CandNomination\EditNomination\NominationController@save_bank_prev');
	Route::post('/make-finalize','Admin\CandNomination\EditNomination\NominationController@make_finalize');
	Route::any('/cancel-nomination-prev','Admin\CandNomination\EditNomination\NominationController@cancel_nomination_prev');
	
	
	Route::get('send-otp-on-mobile', 'Admin\CandNomination\EditNomination\NominationController@send_otp_on_mobile');
	Route::get('verifyOTP', 'Admin\CandNomination\EditNomination\NominationController@verifyOTP');
	Route::get('send-otp-on-email', 'Admin\CandNomination\EditNomination\NominationController@send_otp_on_email');
	Route::get('verifyOTPEmail', 'Admin\CandNomination\EditNomination\NominationController@verifyOTPEmail');
	
	Route::get('check-email-mobile-onsubmit', 'Admin\CandNomination\EditNomination\NominationController@check_email_mobile_onsubmit');

	
	Route::post('/mark-defect-as-resolved', 'Admin\CandNomination\EditNomination\NominationController@mark_defect_as_resolved');
	
	Route::get('/set-param','Admin\CandNomination\EditNomination\NominationController@set_param');
	
	
	
	Route::get('/book-details','Admin\CandNomination\EditNomination\NominationController@book_details');
	
	Route::get('/save-first-login','Admin\CandNomination\EditNomination\NominationController@save_first_login');
	Route::get('/nominations','Admin\CandNomination\EditNomination\NominationController@nominations');
	Route::post('/save-bank','Admin\CandNomination\EditNomination\NominationController@save_bank');
	Route::post('/get-nom-total-current','Admin\CandNomination\EditNomination\NominationController@get_nom_total_current');
	
	Route::get('paywithrazorpay', 'Nomination\RazorpayController@payWithRazorpay')->name('paywithrazorpay');
	Route::post('payment', 'Nomination\RazorpayController@payment')->name('payment');
	Route::get('paymentsucessfull', 'Nomination\RazorpayController@payMentSucess')->name('payMentSucess');
	

	
	Route::get('/prescootiny/{id}','Admin\CandNomination\EditNomination\NominationController@view_nomination_prescootiny');
   	Route::get('/my-nominations-draft','Admin\CandNomination\EditNomination\NominationController@my_nominations_draft');
	Route::post('upload','Admin\CandNomination\EditNomination\NominationController@upload_files');
	Route::post('upload-affidavit','Admin\CandNomination\EditNomination\NominationController@upload_affidavit');
	Route::post('upload-affidavit-final','Admin\CandNomination\EditNomination\NominationController@upload_affidavit_final');
	Route::post('save-affidavit','Admin\CandNomination\EditNomination\NominationController@save_affidavit'); 
	Route::post('get-nomination-start-end-date','Admin\CandNomination\EditNomination\NominationController@get_nomination_start_end_date');
	// Route::get('/detail/{id}','Admin\CandNomination\EditNomination\NominationController@view_nomination');
	Route::get('/download/{id}','Admin\CandNomination\EditNomination\NominationController@download_nomination');
	Route::get('/my-nominations','Admin\CandNomination\EditNomination\NominationController@my_nominations');
	Route::get('/','Admin\CandNomination\EditNomination\NominationController@apply_nomination_step_1');
	Route::get('/submit-for-pre-scrutiny','Admin\CandNomination\EditNomination\NominationController@submit_for_pre_scrutiny');
	Route::get('/schedule-appointment','Admin\CandNomination\EditNomination\NominationController@schedule_appointment');
	Route::get('/track-nomination-status','Admin\CandNomination\EditNomination\NominationController@track_nomination_status');
	Route::get('/confirm-schedule-appointment','Admin\CandNomination\EditNomination\NominationController@confirm_schedule_appointment');
	Route::get('/download-scheduled','Admin\CandNomination\EditNomination\NominationController@download_scheduled');
	Route::any('/confirm-schedule-appointment/post','Admin\CandNomination\EditNomination\NominationController@save_confirm_schedule_appointment');
	Route::any('/cancel-nomination','Admin\CandNomination\EditNomination\NominationController@cancel_nomination');
	Route::any('/schedule-appointment/post','Admin\CandNomination\EditNomination\NominationController@save_appoinment');
	Route::any('/apply_pre_scrutiny/post','Admin\CandNomination\EditNomination\NominationController@apply_pre_scrutiny');
	Route::get('/apply-nomination-step-1','Admin\CandNomination\EditNomination\NominationController@apply_nomination_step_1');
	Route::any('/apply-nomination-step-1/post','Admin\CandNomination\EditNomination\NominationController@save_step_1');
	Route::get('/apply-nomination-step-1/post',function(){
	  return redirect('nomination/apply-nomination-step-1');
	});

	Route::get('/apply-nomination-step-2','Admin\CandNomination\EditNomination\NominationController@apply_nomination_step_2');
	Route::any('/apply-nomination-step-2/post','Admin\CandNomination\EditNomination\NominationController@save_step_2');
	Route::get('/apply-nomination-step-2/post',function(){
	  return redirect('nomination/apply-nomination-step-2');
	});

	Route::post('get-start-end-date','Admin\CandNomination\EditNomination\NominationController@getStartEndDate');
	
	Route::get('/apply-nomination-step-2/{id}','Admin\CandNomination\EditNomination\NominationController@apply_nomination_step_2');


	Route::get('/apply-nomination-step-3','Admin\CandNomination\EditNomination\NominationController@apply_nomination_step_3');
	Route::any('/apply-nomination-step-3/post-part-1','Admin\CandNomination\EditNomination\NominationController@save_step_3');
	Route::get('/apply-nomination-step-3/post-part-1',function(){
	  return redirect('nomination/apply-nomination-step-3');
	});

	Route::get('/apply-nomination-step-4','Admin\CandNomination\EditNomination\NominationController@apply_nomination_step_4');
	Route::any('/apply-nomination-step-4/post','Admin\CandNomination\EditNomination\NominationController@save_step_4');
	Route::get('/apply-nomination-step-4/post',function(){
	  return redirect('nomination/apply-nomination-step-4');
	});

	Route::get('/apply-nomination-step-5','Admin\CandNomination\EditNomination\NominationController@apply_nomination_step_5');
	Route::any('/apply-nomination-step-5/post','Admin\CandNomination\EditNomination\NominationController@save_step_5');
	Route::get('/apply-nomination-step-5/post',function(){
	  return redirect('nomination/apply-nomination-step-5');
	});

	Route::get('/apply-nomination-step-6','Admin\CandNomination\EditNomination\NominationController@apply_nomination_step_6');
	Route::any('/apply-nomination-step-6/post','Admin\CandNomination\EditNomination\NominationController@save_step_6');
	Route::get('/apply-nomination-step-6/post',function(){
	  return redirect('nomination/apply-nomination-step-6');
	});

	Route::get('/apply-nomination-finalize','Admin\CandNomination\EditNomination\NominationController@apply_nomination_finalize');
	Route::any('/apply-nomination-finalize/post','Admin\CandNomination\EditNomination\NominationController@save_nomination_finalize');
	Route::get('/apply-nomination-finalize/post',function(){
	  return redirect('nomination/apply-nomination-finalize');
	});

	Route::post('upload','Admin\CandNomination\EditNomination\NominationController@upload_files');
	Route::post('upload-affidavit','Admin\CandNomination\EditNomination\NominationController@upload_affidavit');
	// Route::get('/detail/{id}','Admin\CandNomination\EditNomination\NominationController@view_nomination');
	Route::get('/download/{id}','Admin\CandNomination\EditNomination\NominationController@download_nomination');

	// End Edit Nomination
});  //ROAC TURNOUT ROUTES ENDS




});