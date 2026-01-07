<?php
Route::group(['prefix' => 'roac', 'as' => 'roac::', 'middleware' => ['auth:admin']], function(){
		Route::get('e-affidavit/list', 'ACROController@index');
		
	Route::get('affidavitdashboard', 'AffidavitDashboardController@AffidavitDashboard')->name('affidavit.dashboard');
	Route::get('/affidavitdashboard/edit/{id}','AffidavitDashboardController@AffidavitDashboard')->name('affidavit.edit');

	//Route::get('affidavit-e-file', 'AffidavitDashboardController@AffidavitEFile')->name('affidavit.e.file');
	//Route::get('affidavit/my-affidavit', 'AffidavitDashboardController@MyAffidavit')->name('my.affidavit');

	Route::post('InitialDetails', 'AffidavitDashboardController@InitialDetails')->name('initial.details');

	Route::post('update-personal-details', 'AffidavitDashboardController@UpdatePersonalDetails')->name('update.personal.details');
	
	Route::get('affidavit/candidatedetails', 'AffidavitDashboardController@CandidateDetails')->name('roac.affidavit.candidate.details');

	Route::post('affidavit/upload','AffidavitDashboardController@upload_files');

	Route::post('/table/update-pan-details', 'AffidavitDashboardController@UpdatePANDetails')->name('update.pan.details');


	//Route::post('/table/add_data', 'AffidavitDashboardController@AffidavitAddData')->name('table.add_data');
	//Route::post('/table/social_media_add_data', 'AffidavitDashboardController@AffidavitSocialMediaData')->name('social_media_add_data');

	Route::post('/table/update_social_media', 'AffidavitDashboardController@UpdateSocialMedia')->name('update.social.media');

	Route::post('/save-candidate-details', 'AffidavitDashboardController@SaveCandidateDetails')->name('save.candidate.details');

	Route::get('affidavit/pending-criminal-cases', 'AffidavitDashboardController@PendingCriminalCases')->name('pending.criminal.cases');

	
	//Route::delete('users/{id}', 'AffidavitDashboardController@destroy')->name('criminal.record.destroy');
	//Route::delete('conviction/users/{id}', 'AffidavitDashboardController@ConvictionDestroy')->name('conviction.record.destroy');

	Route::post('affidavit/pending-criminal-cases', 'AffidavitDashboardController@SavePendingCriminalCases')->name('save.pending.criminal.cases');

	Route::post('affidavit/conviction-cases', 'AffidavitDashboardController@SaveCaseConvictionCases')->name('save.case.of.conviction.cases');

	Route::post('affidavit/save-final-pending-criminal-conviction-cases', 'AffidavitDashboardController@SaveFinalPendingCriminalConvictionCases')->name('save.final.pending.criminal.conviction.cases');

	Route::get('CriminalDataAvailableNull', 'AffidavitDashboardController@CriminalDataAvailableNull')->name('criminal.data.available.null');

	Route::get('CriminalDataAvailableNotNull', 'AffidavitDashboardController@CriminalDataAvailableNotNull')->name('criminal.data.available.notnull');

	Route::get('getconvictionDataAvailablenotnull', 'AffidavitDashboardController@ConvictionDataAvailableNotNull')->name('conviction.data.available.notnull');

	Route::get('getconvictionDataAvailablenull', 'AffidavitDashboardController@ConvictionDataAvailableNull')->name('conviction.data.available.null');

	
	// JITENDER route//
	Route::get('immovable-assets','ImmovableAssetsController@ImmovableAssets')->name('affidavit.ImmovableAssets.agricultural_land');
	
	/* Route::get('save_agricultural_land', 'ImmovableAssetsController@save_agricultural_land');
	Route::get('save_non_agricultural_land', 'ImmovableAssetsController@save_non_agricultural_land');
	Route::get('save_commercial', 'ImmovableAssetsController@save_commercial');
	Route::get('save_residential', 'ImmovableAssetsController@save_residential');
	Route::get('save_other_immovable', 'ImmovableAssetsController@save_other_immovable'); */
	
	Route::get('update_agricultural_land', 'ImmovableAssetsController@update_agricultural_land');
	Route::get('update_non_agricultural_land', 'ImmovableAssetsController@update_non_agricultural_land');
	Route::get('update_commercial', 'ImmovableAssetsController@update_commercial');
	Route::get('update_residential', 'ImmovableAssetsController@update_residential');
	Route::get('update_other_immovable', 'ImmovableAssetsController@update_other_immovable');
	
	/* Route::get('delete_agricultural_land', 'ImmovableAssetsController@delete_agricultural_land');
	Route::get('delete_non_agricultural_land', 'ImmovableAssetsController@delete_non_agricultural_land');
	Route::get('delete_commercial', 'ImmovableAssetsController@delete_commercial');
	Route::get('delete_residential', 'ImmovableAssetsController@delete_residential');
	Route::get('delete_other_immovable', 'ImmovableAssetsController@delete_other_immovable'); */
	
	Route::get('education','EducationController@education')->name('affidavit.education');
	//Route::get('save_education', 'EducationController@save_education');
	Route::get('update_education', 'EducationController@update_education');
	//Route::get('delete_education', 'EducationController@delete_education');
	Route::get('part-a-detailed-report', 'ReportController@part_a_detailed_report')->name('affidavit.report');

	Route::get('preview', 'FinalizeController@preview')->name('affidavit.preview');
	Route::post('finalize', 'FinalizeController@finalize');

	// END //

	// TARU routes //
	/*----------Movable Assets-------------*/
	//Route::post('save-final-movable-assets', 'AffidavitMovableController@SaveFinalMovableAssets')->name('save.final.movable.assets');
	
	Route::get('Affidavit/MovableAssets', 'AffidavitMovableController@MovableAssets')->name('affidavit.movable_asset');
	Route::get('update_cash', 'AffidavitMovableController@update_cash');
	//Route::get('save_deposit', 'AffidavitMovableController@save_deposit');
	Route::get('update_deposit', 'AffidavitMovableController@update_deposit');
	//Route::get('delete_deposit', 'AffidavitMovableController@delete_deposit');
	//Route::get('save_investment', 'AffidavitMovableController@save_investment');
	Route::get('update_investment', 'AffidavitMovableController@update_investment');
	//Route::get('delete_investment', 'AffidavitMovableController@delete_investment');
	//Route::get('save_savings', 'AffidavitMovableController@save_savings');
	Route::get('update_savings', 'AffidavitMovableController@update_savings');
	//Route::get('delete_savings', 'AffidavitMovableController@delete_savings');
	//Route::get('save_loan', 'AffidavitMovableController@save_loan');
	Route::get('update_loan', 'AffidavitMovableController@update_loan');
	//Route::get('delete_loan', 'AffidavitMovableController@delete_loan');
	//Route::get('save_vehicle', 'AffidavitMovableController@save_vehicle');
	Route::get('update_vehicle', 'AffidavitMovableController@update_vehicle');
	//Route::get('delete_vehicle', 'AffidavitMovableController@delete_vehicle');
	//Route::get('save_jewellery', 'AffidavitMovableController@save_jewellery');
	Route::get('update_jewellery', 'AffidavitMovableController@update_jewellery');
	//Route::get('delete_jewellery', 'AffidavitMovableController@delete_jewellery');	
	//Route::get('save_other', 'AffidavitMovableController@save_other');
	Route::get('update_other', 'AffidavitMovableController@update_other');
	//Route::get('delete_other', 'AffidavitMovableController@delete_other');


	/*---------- Liabilities-------------*/
	Route::get('liabilities', 'AffidavitLiabilityController@Liabilities')->name('affidavit.liabilities');
	//Route::get('save_loan_bank', 'AffidavitLiabilityController@save_loan_bank');
	Route::get('update_loan_bank', 'AffidavitLiabilityController@update_loan_bank');
	//Route::get('delete_loan_bank', 'AffidavitLiabilityController@delete_loan_bank');
	
	//Route::get('save_indi_loan_bank', 'AffidavitLiabilityController@save_indi_loan_bank');
	Route::get('update_indi_loan_bank', 'AffidavitLiabilityController@update_indi_loan_bank');
	//Route::get('delete_indi_loan_bank', 'AffidavitLiabilityController@delete_indi_loan_bank');
	
	//Route::get('save_govt_due', 'AffidavitLiabilityController@save_govt_due');
	Route::post('save_govt_due_image', 'AffidavitLiabilityController@save_govt_due_image');
	Route::get('update_govt_due', 'AffidavitLiabilityController@update_govt_due');
	//Route::get('delete_govt_due', 'AffidavitLiabilityController@delete_govt_due');

	//Route::get('save_other_liabilities', 'AffidavitLiabilityController@save_other_liabilities');
	Route::get('update_other_liabilities', 'AffidavitLiabilityController@update_other_liabilities');
	//Route::get('delete_other_liabilities', 'AffidavitLiabilityController@delete_other_liabilities');

	//Route::get('save_other_disputes_liabilities', 'AffidavitLiabilityController@save_other_disputes_liabilities');
	Route::get('update_other_disputes_liabilities', 'AffidavitLiabilityController@update_other_disputes_liabilities');
	//Route::get('delete_other_disputes_liabilities', 'AffidavitLiabilityController@delete_other_disputes_liabilities');

	/*---------- Profession-------------*/
	Route::get('Profession', 'AffidavitProfessionController@Profession')->name('affidavit.profession');
	//Route::post('save_self_spouse', 'AffidavitProfessionController@save_self_spouse');
	Route::get('update_self_spouse', 'AffidavitProfessionController@update_self_spouse');
	//Route::get('delete_self_spouse', 'AffidavitProfessionController@delete_self_spouse');

	//Route::post('save_dependent_income', 'AffidavitProfessionController@save_dependent_income');
	Route::get('update_dependent_income', 'AffidavitProfessionController@update_dependent_income');
	//Route::get('delete_dependent_income', 'AffidavitProfessionController@delete_dependent_income');

	//Route::post('save_govt_public', 'AffidavitProfessionController@save_govt_public');
	Route::get('update_govt_public', 'AffidavitProfessionController@update_govt_public');
	//Route::get('delete_govt_public', 'AffidavitProfessionController@delete_govt_public');

	//Route::post('save_huf', 'AffidavitProfessionController@save_huf');
	Route::get('update_huf', 'AffidavitProfessionController@update_huf');
	//Route::get('delete_huf', 'AffidavitProfessionController@delete_huf');

	//Route::post('save_partner', 'AffidavitProfessionController@save_partner');
	Route::get('update_partner', 'AffidavitProfessionController@update_partner');
	//Route::get('delete_partner', 'AffidavitProfessionController@delete_partner');

	//Route::post('save_private', 'AffidavitProfessionController@save_private');
	Route::get('update_private', 'AffidavitProfessionController@update_private');
	//Route::get('delete_private', 'AffidavitProfessionController@delete_private');
});


Route::group(['middleware' => ['auth:web']], function(){

	Route::get('affidavitdashboard', 'AffidavitDashboardController@AffidavitDashboard')->name('affidavit.dashboard');
	Route::get('/affidavitdashboard/edit/{id}','AffidavitDashboardController@AffidavitDashboard')->name('affidavit.edit');

	Route::get('affidavit-e-file', 'AffidavitDashboardController@AffidavitEFile')->name('affidavit.e.file');
	Route::get('affidavit/my-affidavit', 'AffidavitDashboardController@MyAffidavit')->name('my.affidavit');

	Route::post('InitialDetails', 'AffidavitDashboardController@InitialDetails')->name('initial.details');

	Route::post('update-personal-details', 'AffidavitDashboardController@UpdatePersonalDetails')->name('update.personal.details');
	
	Route::get('affidavit/candidatedetails', 'AffidavitDashboardController@CandidateDetails')->name('affidavit.candidate.details');

	Route::post('affidavit/upload','AffidavitDashboardController@upload_files');

	Route::post('/table/update-pan-details', 'AffidavitDashboardController@UpdatePANDetails')->name('update.pan.details');
	
	Route::any('delete_spouse', 'AffidavitDashboardController@delete_spouse')->name('delete.pan.details');


	Route::post('/table/add_data', 'AffidavitDashboardController@AffidavitAddData')->name('table.add_data');
	Route::post('/table/social_media_add_data', 'AffidavitDashboardController@AffidavitSocialMediaData')->name('social_media_add_data');

	Route::post('/table/update_social_media', 'AffidavitDashboardController@UpdateSocialMedia')->name('update.social.media');

	Route::post('/save-candidate-details', 'AffidavitDashboardController@SaveCandidateDetails')->name('save.candidate.details');

	Route::get('affidavit/pending-criminal-cases', 'AffidavitDashboardController@PendingCriminalCases')->name('pending.criminal.cases');

	
	Route::delete('users/{id}', 'AffidavitDashboardController@destroy')->name('criminal.record.destroy');
	Route::delete('conviction/users/{id}', 'AffidavitDashboardController@ConvictionDestroy')->name('conviction.record.destroy');

	Route::post('affidavit/pending-criminal-cases', 'AffidavitDashboardController@SavePendingCriminalCases')->name('save.pending.criminal.cases');

	Route::post('affidavit/conviction-cases', 'AffidavitDashboardController@SaveCaseConvictionCases')->name('save.case.of.conviction.cases');

	Route::post('affidavit/save-final-pending-criminal-conviction-cases', 'AffidavitDashboardController@SaveFinalPendingCriminalConvictionCases')->name('save.final.pending.criminal.conviction.cases');

	Route::get('CriminalDataAvailableNull', 'AffidavitDashboardController@CriminalDataAvailableNull')->name('criminal.data.available.null');

	Route::get('CriminalDataAvailableNotNull', 'AffidavitDashboardController@CriminalDataAvailableNotNull')->name('criminal.data.available.notnull');

	Route::get('getconvictionDataAvailablenotnull', 'AffidavitDashboardController@ConvictionDataAvailableNotNull')->name('conviction.data.available.notnull');

	Route::get('getconvictionDataAvailablenull', 'AffidavitDashboardController@ConvictionDataAvailableNull')->name('conviction.data.available.null');

	
	// JITENDER route//
	Route::get('immovable-assets','ImmovableAssetsController@ImmovableAssets')->name('affidavit.ImmovableAssets.agricultural_land');
	
	Route::get('save_agricultural_land', 'ImmovableAssetsController@save_agricultural_land');
	Route::get('save_non_agricultural_land', 'ImmovableAssetsController@save_non_agricultural_land');
	Route::get('save_commercial', 'ImmovableAssetsController@save_commercial');
	Route::get('save_residential', 'ImmovableAssetsController@save_residential');
	Route::get('save_other_immovable', 'ImmovableAssetsController@save_other_immovable');
	
	Route::get('update_agricultural_land', 'ImmovableAssetsController@update_agricultural_land');
	Route::get('update_non_agricultural_land', 'ImmovableAssetsController@update_non_agricultural_land');
	Route::get('update_commercial', 'ImmovableAssetsController@update_commercial');
	Route::get('update_residential', 'ImmovableAssetsController@update_residential');
	Route::get('update_other_immovable', 'ImmovableAssetsController@update_other_immovable');
	
	Route::get('delete_agricultural_land', 'ImmovableAssetsController@delete_agricultural_land');
	Route::get('delete_non_agricultural_land', 'ImmovableAssetsController@delete_non_agricultural_land');
	Route::get('delete_commercial', 'ImmovableAssetsController@delete_commercial');
	Route::get('delete_residential', 'ImmovableAssetsController@delete_residential');
	Route::get('delete_other_immovable', 'ImmovableAssetsController@delete_other_immovable');
	
	Route::get('education','EducationController@education')->name('affidavit.education');
	Route::get('save_education', 'EducationController@save_education');
	Route::get('update_education', 'EducationController@update_education');
	Route::get('delete_education', 'EducationController@delete_education');
	Route::get('part-a-detailed-report', 'ReportController@part_a_detailed_report')->name('affidavit.report');

	Route::get('preview', 'FinalizeController@preview')->name('affidavit.preview');
	Route::post('finalize', 'FinalizeController@finalize');

	// END //

	// TARU routes //
	/*----------Movable Assets-------------*/
	Route::post('save-final-movable-assets', 'AffidavitMovableController@SaveFinalMovableAssets')->name('save.final.movable.assets');
	Route::get('Affidavit/MovableAssets', 'AffidavitMovableController@MovableAssets')->name('affidavit.movable_asset');
	Route::get('update_cash', 'AffidavitMovableController@update_cash');
	Route::get('save_deposit', 'AffidavitMovableController@save_deposit');
	Route::get('update_deposit', 'AffidavitMovableController@update_deposit');
	Route::get('delete_deposit', 'AffidavitMovableController@delete_deposit');
	Route::get('save_investment', 'AffidavitMovableController@save_investment');
	Route::get('update_investment', 'AffidavitMovableController@update_investment');
	Route::get('delete_investment', 'AffidavitMovableController@delete_investment');
	Route::get('save_savings', 'AffidavitMovableController@save_savings');
	Route::get('update_savings', 'AffidavitMovableController@update_savings');
	Route::get('delete_savings', 'AffidavitMovableController@delete_savings');
	Route::get('save_loan', 'AffidavitMovableController@save_loan');
	Route::get('update_loan', 'AffidavitMovableController@update_loan');
	Route::get('delete_loan', 'AffidavitMovableController@delete_loan');
	Route::get('save_vehicle', 'AffidavitMovableController@save_vehicle');
	Route::get('update_vehicle', 'AffidavitMovableController@update_vehicle');
	Route::get('delete_vehicle', 'AffidavitMovableController@delete_vehicle');
	Route::get('save_jewellery', 'AffidavitMovableController@save_jewellery');
	Route::get('update_jewellery', 'AffidavitMovableController@update_jewellery');
	Route::get('delete_jewellery', 'AffidavitMovableController@delete_jewellery');	
	Route::get('save_other', 'AffidavitMovableController@save_other');
	Route::get('update_other', 'AffidavitMovableController@update_other');
	Route::get('delete_other', 'AffidavitMovableController@delete_other');


	/*---------- Liabilities-------------*/
	Route::get('liabilities', 'AffidavitLiabilityController@Liabilities')->name('affidavit.liabilities');
	Route::get('save_loan_bank', 'AffidavitLiabilityController@save_loan_bank');
	Route::get('update_loan_bank', 'AffidavitLiabilityController@update_loan_bank');
	Route::get('delete_loan_bank', 'AffidavitLiabilityController@delete_loan_bank');
	
	Route::get('save_indi_loan_bank', 'AffidavitLiabilityController@save_indi_loan_bank');
	Route::get('update_indi_loan_bank', 'AffidavitLiabilityController@update_indi_loan_bank');
	Route::get('delete_indi_loan_bank', 'AffidavitLiabilityController@delete_indi_loan_bank');
	
	Route::get('save_govt_due', 'AffidavitLiabilityController@save_govt_due');
	Route::post('save_govt_due_image', 'AffidavitLiabilityController@save_govt_due_image');
	Route::get('update_govt_due', 'AffidavitLiabilityController@update_govt_due');
	Route::get('delete_govt_due', 'AffidavitLiabilityController@delete_govt_due');

	Route::get('save_other_liabilities', 'AffidavitLiabilityController@save_other_liabilities');
	Route::get('update_other_liabilities', 'AffidavitLiabilityController@update_other_liabilities');
	Route::get('delete_other_liabilities', 'AffidavitLiabilityController@delete_other_liabilities');

	Route::get('save_other_disputes_liabilities', 'AffidavitLiabilityController@save_other_disputes_liabilities');
	Route::get('update_other_disputes_liabilities', 'AffidavitLiabilityController@update_other_disputes_liabilities');
	Route::get('delete_other_disputes_liabilities', 'AffidavitLiabilityController@delete_other_disputes_liabilities');

	/*---------- Profession-------------*/
	Route::get('Profession', 'AffidavitProfessionController@Profession')->name('affidavit.profession');
	Route::post('save_self_spouse', 'AffidavitProfessionController@save_self_spouse');
	Route::get('update_self_spouse', 'AffidavitProfessionController@update_self_spouse');
	Route::get('delete_self_spouse', 'AffidavitProfessionController@delete_self_spouse');

	Route::post('save_dependent_income', 'AffidavitProfessionController@save_dependent_income');
	Route::get('update_dependent_income', 'AffidavitProfessionController@update_dependent_income');
	Route::get('delete_dependent_income', 'AffidavitProfessionController@delete_dependent_income');

	Route::post('save_govt_public', 'AffidavitProfessionController@save_govt_public');
	Route::get('update_govt_public', 'AffidavitProfessionController@update_govt_public');
	Route::get('delete_govt_public', 'AffidavitProfessionController@delete_govt_public');

	Route::post('save_huf', 'AffidavitProfessionController@save_huf');
	Route::get('update_huf', 'AffidavitProfessionController@update_huf');
	Route::get('delete_huf', 'AffidavitProfessionController@delete_huf');

	Route::post('save_partner', 'AffidavitProfessionController@save_partner');
	Route::get('update_partner', 'AffidavitProfessionController@update_partner');
	Route::get('delete_partner', 'AffidavitProfessionController@delete_partner');

	Route::post('save_private', 'AffidavitProfessionController@save_private');
	Route::get('update_private', 'AffidavitProfessionController@update_private');
	Route::get('delete_private', 'AffidavitProfessionController@delete_private');

	// end //
});
	
	Route::get('epic-search', 'CommanFunctionController@SearchEpic')->name('search.epic');
	Route::get('get-districts', 'CommanFunctionController@getDistricts')->name('getdistricts');
	Route::get('getac_list', 'CommanFunctionController@getACList')->name('getACList');
	Route::get('aff-political-party', 'CommanFunctionController@AffPoliticalParty')->name('aff.political.party');
	Route::get('check-mobile-no', 'CommanFunctionController@CheckMobileNo')->name('check.mobile.no');
	Route::post('verifyOTP', 'CommanFunctionController@verifyOTP')->name('verifyOTP');

	Route::get('check-email-address', 'CommanFunctionController@CheckEmailAddress')->name('check.email.address');
	Route::get('verify-otp-emailId', 'CommanFunctionController@VerifyOTPEmailId')->name('verify.otp.emailId');
	
	Route::get('setlocale/{locale}', function ($locale) {
	  if (in_array($locale, \Config::get('app.locales'))) {
	  	
	    session(['locale' => $locale]);
	  }
	  return redirect()->back();
	});
?>