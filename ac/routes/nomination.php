<?php
	
	Route::group(['middleware' => ['auth:web','auth','usersession']], function(){ 
	
	Route::post('/save-payment-details-gujrat_our_end','Nomination\NominationController@save_payment_details_gujrat');	
	Route::get('pay-ver','Nomination\NominationController@payment_verification_Gujrat');
	
	
	Route::post('delete-draft-nomination','Nomination\NominationController@delete_draft_nomination'); 	
	Route::post('get-start-end-date','Nomination\NominationController@getStartEndDate');	
	Route::post('/finalize-nomination-payment','Nomination\NominationController@finalize_nomination_payment');
	Route::post('/delete-nomination','Nomination\NominationController@delete_nomination');
	Route::any('/challan/post','Nomination\NominationController@challan');
	
	Route::post('/copy-nomination','Nomination\NominationController@copy_nomination');
    Route::post('/do-copy','Nomination\NominationController@do_copy');	
	Route::post('/save-payment-details-our-end','Nomination\NominationController@save_payment_details_our_end');
	Route::get('get-symbol','Nomination\NominationController@get_symbol');
	Route::post('/finalize-e-affidavit','Nomination\NominationController@finalize_e_affidavit');	
	Route::post('/assign-e-affidavit','Nomination\NominationController@assign_e_affidavit');
	Route::post('/delink-e-affidavit','Nomination\NominationController@delink_e_affidavit');
	
	
	Route::get('/set-param-prev','Nomination\NominationController@set_param_prev');
	Route::any('/prev','Nomination\NominationController@prev_show');
	Route::any('/prev/post','Nomination\NominationController@prev_save');
	Route::post('/save-bank-prev','Nomination\NominationController@save_bank_prev');
	Route::post('/make-finalize','Nomination\NominationController@make_finalize');
	Route::any('/cancel-nomination-prev','Nomination\NominationController@cancel_nomination_prev');
	
	
	Route::get('send-otp-on-mobile', 'Nomination\NominationController@send_otp_on_mobile');
	Route::get('verifyOTP', 'Nomination\NominationController@verifyOTP');
	Route::get('send-otp-on-email', 'Nomination\NominationController@send_otp_on_email');
	Route::get('verifyOTPEmail', 'Nomination\NominationController@verifyOTPEmail');
	
	Route::get('check-email-mobile-onsubmit', 'Nomination\NominationController@check_email_mobile_onsubmit');

	
	Route::post('/mark-defect-as-resolved', 'Nomination\NominationController@mark_defect_as_resolved');
	
	Route::get('/set-param','Nomination\NominationController@set_param');
	
	
	
	Route::get('/book-details','Nomination\NominationController@book_details');
	
	Route::get('/save-first-login','Nomination\NominationController@save_first_login');
	Route::get('/nominations','Nomination\NominationController@nominations');
	Route::post('/save-bank','Nomination\NominationController@save_bank');
	Route::post('/get-nom-total-current','Nomination\NominationController@get_nom_total_current');
	
	Route::get('paywithrazorpay', 'Nomination\RazorpayController@payWithRazorpay')->name('paywithrazorpay');
	Route::post('payment', 'Nomination\RazorpayController@payment')->name('payment');
	Route::get('paymentsucessfull', 'Nomination\RazorpayController@payMentSucess')->name('payMentSucess');
	

	
	Route::get('/prescootiny/{id}','Nomination\NominationController@view_nomination_prescootiny');
   	Route::get('/my-nominations-draft','Nomination\NominationController@my_nominations_draft');
	Route::post('upload','Nomination\NominationController@upload_files');
	Route::post('upload-affidavit','Nomination\NominationController@upload_affidavit');
	Route::post('upload-affidavit-final','Nomination\NominationController@upload_affidavit_final');
	Route::post('save-affidavit','Nomination\NominationController@save_affidavit'); 
	Route::post('get-nomination-start-end-date','Nomination\NominationController@get_nomination_start_end_date');
	Route::get('/detail/{id}','Nomination\NominationController@view_nomination');
	Route::get('/download/{id}','Nomination\NominationController@download_nomination');
	Route::get('/my-nominations','Nomination\NominationController@my_nominations');
	Route::get('/','Nomination\NominationController@apply_nomination_step_1');
	Route::get('/submit-for-pre-scrutiny','Nomination\NominationController@submit_for_pre_scrutiny');
	Route::get('/schedule-appointment','Nomination\NominationController@schedule_appointment');
	Route::get('/track-nomination-status','Nomination\NominationController@track_nomination_status');
	Route::get('/confirm-schedule-appointment','Nomination\NominationController@confirm_schedule_appointment');
	Route::get('/download-scheduled','Nomination\NominationController@download_scheduled');
	Route::any('/confirm-schedule-appointment/post','Nomination\NominationController@save_confirm_schedule_appointment');
	Route::any('/cancel-nomination','Nomination\NominationController@cancel_nomination');
	Route::any('/schedule-appointment/post','Nomination\NominationController@save_appoinment');
	Route::any('/apply_pre_scrutiny/post','Nomination\NominationController@apply_pre_scrutiny');
	Route::get('/apply-nomination-step-1','Nomination\NominationController@apply_nomination_step_1');
	Route::any('/apply-nomination-step-1/post','Nomination\NominationController@save_step_1');
	Route::get('/apply-nomination-step-1/post',function(){
	  return redirect('nomination/apply-nomination-step-1');
	});

	Route::get('/apply-nomination-step-2/','Nomination\NominationController@apply_nomination_step_2');
	Route::any('/apply-nomination-step-2/post','Nomination\NominationController@save_step_2');
	Route::get('/apply-nomination-step-2/post',function(){
	  return redirect('nomination/apply-nomination-step-2');
	});
	
	Route::get('/apply-nomination-step-2/{id}','Nomination\NominationController@apply_nomination_step_2');


	Route::get('/apply-nomination-step-3','Nomination\NominationController@apply_nomination_step_3');
	Route::any('/apply-nomination-step-3/post-part-1','Nomination\NominationController@save_step_3');
	Route::get('/apply-nomination-step-3/post-part-1',function(){
	  return redirect('nomination/apply-nomination-step-3');
	});

	Route::get('/apply-nomination-step-4','Nomination\NominationController@apply_nomination_step_4');
	Route::any('/apply-nomination-step-4/post','Nomination\NominationController@save_step_4');
	Route::get('/apply-nomination-step-4/post',function(){
	  return redirect('nomination/apply-nomination-step-4');
	});

	Route::get('/apply-nomination-step-5','Nomination\NominationController@apply_nomination_step_5');
	Route::any('/apply-nomination-step-5/post','Nomination\NominationController@save_step_5');
	Route::get('/apply-nomination-step-5/post',function(){
	  return redirect('nomination/apply-nomination-step-5');
	});

	Route::get('/apply-nomination-step-6','Nomination\NominationController@apply_nomination_step_6');
	Route::any('/apply-nomination-step-6/post','Nomination\NominationController@save_step_6');
	Route::get('/apply-nomination-step-6/post',function(){
	  return redirect('nomination/apply-nomination-step-6');
	});

	Route::get('/apply-nomination-finalize','Nomination\NominationController@apply_nomination_finalize');
	Route::any('/apply-nomination-finalize/post','Nomination\NominationController@save_nomination_finalize');
	Route::get('/apply-nomination-finalize/post',function(){
	  return redirect('nomination/apply-nomination-finalize');
	});
});


######################## MLC Route Start Here (Sanjay)##########################
	Route::group(['prefix' => 'mlc', 'middleware' => ['auth:web','auth','usersession']], function () {
            
		Route::get('/set-param','Nomination\Mlc\MlcNominationController@set_param');

		Route::get('/book-details','Nomination\Mlc\MlcNominationController@book_details');

		Route::get('/save-first-login','Nomination\Mlc\MlcNominationController@save_first_login');
		Route::post('/save-bank','Nomination\Mlc\MlcNominationController@save_bank');
		Route::post('/get-nom-total-current','Nomination\Mlc\MlcNominationController@get_nom_total_current');

		Route::get('paywithrazorpay', 'Nomination\Mlc\MlcRazorpayController@payWithRazorpay')->name('paywithrazorpay');
		Route::post('payment', 'Nomination\Mlc\MlcRazorpayController@payment')->name('payment');
		Route::get('paymentsucessfull', 'Nomination\Mlc\MlcRazorpayController@payMentSucess')->name('payMentSucess');


		Route::get('/prescootiny/{id}','Nomination\Mlc\MlcNominationController@view_nomination_prescootiny');
		Route::post('upload','Nomination\Mlc\MlcNominationController@upload_files');
		Route::post('upload-affidavit','Nomination\Mlc\MlcNominationController@upload_affidavit');
		
		Route::post('upload-affidavit-final','Nomination\Mlc\MlcNominationController@upload_affidavit_final');
		Route::post('save-affidavit','Nomination\Mlc\MlcNominationController@save_affidavit');
		Route::post('save-affidavit','Nomination\Mlc\MlcNominationController@save_affidavit');
		
		Route::get('/my-nominations-draft','Nomination\Mlc\MlcNominationController@my_nominations_draft');
		
		
		Route::post('get-nomination-start-end-date','Nomination\Mlc\MlcNominationController@get_nomination_start_end_date');

		Route::get('/detail/{id}','Nomination\Mlc\MlcNominationController@view_nomination');
		Route::get('/download/{id}','Nomination\Mlc\MlcNominationController@download_nomination');
		Route::get('/my-nominations','Nomination\Mlc\MlcNominationController@my_nominations');
		Route::get('/','Nomination\Mlc\MlcNominationController@apply_nomination_step_1');
		
		Route::get('/submit-for-pre-scrutiny','Nomination\Mlc\MlcNominationController@submit_for_pre_scrutiny');
		Route::get('/schedule-appointment','Nomination\Mlc\MlcNominationController@schedule_appointment');
		Route::get('/track-nomination-status','Nomination\Mlc\MlcNominationController@track_nomination_status');

		Route::get('/confirm-schedule-appointment','Nomination\Mlc\MlcNominationController@confirm_schedule_appointment');
		Route::get('/download-scheduled','Nomination\Mlc\MlcNominationController@download_scheduled');
		
		
		
		Route::any('/confirm-schedule-appointment/post','Nomination\Mlc\MlcNominationController@save_confirm_schedule_appointment');

		Route::any('/cancel-nomination','Nomination\Mlc\MlcNominationController@cancel_nomination');
		Route::any('/schedule-appointment/post','Nomination\Mlc\MlcNominationController@save_appoinment');
		Route::any('/apply_pre_scrutiny/post','Nomination\Mlc\MlcNominationController@apply_pre_scrutiny');
		
		Route::get('/apply-nomination-step-1','Nomination\Mlc\MlcNominationController@apply_nomination_step_1');
		Route::any('/apply-nomination-step-1/post','Nomination\Mlc\MlcNominationController@save_step_1');
		Route::get('/apply-nomination-step-1/post',function(){
		  return redirect('nomination/mlc/apply-nomination-step-1');
		});

		Route::get('/apply-nomination-step-2','Nomination\Mlc\MlcNominationController@apply_nomination_step_2');
		Route::any('/apply-nomination-step-2/post','Nomination\Mlc\MlcNominationController@save_step_2');
		Route::get('/apply-nomination-step-2/post',function(){
		  return redirect('nomination/mlc/apply-nomination-step-2');
		});
		
		Route::get('/apply-nomination-step-2/{id}','Nomination\Mlc\MlcNominationController@apply_nomination_step_2');


		Route::get('/apply-nomination-step-3','Nomination\Mlc\MlcNominationController@apply_nomination_step_3');
		Route::any('/apply-nomination-step-3/post-part-1','Nomination\Mlc\MlcNominationController@save_step_3');
		Route::get('/apply-nomination-step-3/post-part-1',function(){
		  return redirect('nomination/mlc/apply-nomination-step-3');
		});

		Route::get('/apply-nomination-step-4','Nomination\Mlc\MlcNominationController@apply_nomination_step_4');
		Route::any('/apply-nomination-step-4/post','Nomination\Mlc\MlcNominationController@save_step_4');
		Route::get('/apply-nomination-step-4/post',function(){
		  return redirect('nomination/mlc/apply-nomination-step-4');
		});

		Route::get('/apply-nomination-step-5','Nomination\Mlc\MlcNominationController@apply_nomination_step_5');
		Route::any('/apply-nomination-step-5/post','Nomination\Mlc\MlcNominationController@save_step_5');
		Route::get('/apply-nomination-step-5/post',function(){
		  return redirect('nomination/mlc/apply-nomination-step-5');
		});

		Route::get('/apply-nomination-step-6','Nomination\Mlc\MlcNominationController@apply_nomination_step_6');
		Route::any('/apply-nomination-step-6/post','Nomination\Mlc\MlcNominationController@save_step_6');
		Route::get('/apply-nomination-step-6/post',function(){
		  return redirect('nomination/mlc/apply-nomination-step-6');
		});

		Route::get('/apply-nomination-finalize','Nomination\Mlc\MlcNominationController@apply_nomination_finalize');
		Route::any('/apply-nomination-finalize/post','Nomination\Mlc\MlcNominationController@save_nomination_finalize');
		Route::get('/apply-nomination-finalize/post',function(){
		  return redirect('nomination/mlc/apply-nomination-finalize');
		});
    });
	######################## MLC Route End Here (Sanjay) ##########################
