<?php  //created by Niraj for expendature on DEO Level
   

   ##################Start Niraj Route ####################
Route::get('/scrutinyExpenditure', 'Expenditure\DeoExpenditureController@getmasterdata');
Route::match(['GET','POST'],'/constiuancyinfo/{ac?}', 'Expenditure\DeoExpenditureController@getcandidateListbyDeo'); 
Route::get('/expdeoreport/{candidate_id}', 'Expenditure\DeoExpenditureController@getcandidateDetails'); 
Route::get('/expdashboard', 'Expenditure\DeoExpenditureController@dashboard');
Route::get('/dataentryStart', 'Expenditure\DeoExpenditureController@candidateListBydataentryStart');
Route::get('/finalizeData', 'Expenditure\DeoExpenditureController@candidateListByfinalizeData');
Route::get('/logedaccount', 'Expenditure\DeoExpenditureController@candidateListBylogedaccount');
Route::get('/notintime', 'Expenditure\DeoExpenditureController@candidateListBynotintime');
Route::get('/formatedefects', 'Expenditure\DeoExpenditureController@candidateListByformatedefects');
Route::get('/ronotagree', 'Expenditure\DeoExpenditureController@candidateListByronotagree');
Route::get('/understatedexpense', 'Expenditure\DeoExpenditureController@candidateListByunderstatedexpense');
Route::get('/dataentrydefects', 'Expenditure\DeoExpenditureController@candidateListBydataentrydefects');
Route::get('/partyfund', 'Expenditure\DeoExpenditureController@candidateListBypartyfund');
Route::get('/othersfund', 'Expenditure\DeoExpenditureController@candidateListByothersfund');
Route::get('/exeedceiling', 'Expenditure\DeoExpenditureController@candidateListByexeedceiling');

//dashboard current status
Route::get('/statusdashboard', 'Expenditure\DeoExpenditureController@statusdashboard');
Route::get('/pendingdataentry', 'Expenditure\DeoExpenditureController@getpendingcandidateList');
Route::get('/partiallypending', 'Expenditure\DeoExpenditureController@getpartiallypendingcandidateList');
Route::get('/filedData', 'Expenditure\DeoExpenditureController@candidateListByfiledData');
Route::get('/defaulter', 'Expenditure\DeoExpenditureController@getdefaultercandidateList');
Route::get('/finalbyceo', 'Expenditure\DeoExpenditureController@candidateListfinalbyCEO');
Route::get('/finalbyeci', 'Expenditure\DeoExpenditureController@candidateListfinalbyECI');

Route::get('/rotracking-status', 'Expenditure\DeoExpenditureController@tarcking');

//MIS Report
Route::get('/mis-officer', 'Expenditure\DeoExpenditureController@getOfficersmis');
Route::get('/mis-candidate', 'Expenditure\DeoExpenditureController@getCandidatemis');

//Notice Section 
Route::get('/noticeatdeo', 'Expenditure\DeoExpenditureController@getnoticeatDEO');
Route::get('/noticeatdeoEXL', 'Expenditure\DeoExpenditureController@getnoticeatDEOEXL');

###################end Niraj rout ###################


//manoj start here  
Route::get('/viewbyid/{id}', 'Expenditure\DeoExpenditureController@viewById')->name('viewbyid');
Route::post('/deoForm', 'Expenditure\DeoExpenditureController@deoForm')->name('deoForm');
Route::get('/deoformview/{id}/{ac_no}','Expenditure\DeoExpenditureController@deoFormView');
Route::post('/updateAccountDeoForm','Expenditure\DeoExpenditureController@updateAccountDeoForm');
Route::post('/updateDefectDeoForm','Expenditure\DeoExpenditureController@updateDefectDeoForm');
// for graph 
Route::get('/ExpDataEntrySummaryReport', 'Expenditure\DeoExpenditureController@ExpDataEntrySummaryReport');
Route::get('/summary-graph/{id}', 'Expenditure\DeoExpenditureController@getSummaryGraphData');
// graph for individual start here
Route::get('/candidateListBydataentryStartGraph', 'Expenditure\DeoExpenditureController@candidateListBydataentryStartGraph');
Route::get('/candidateListByfinalizeDatagraph', 'Expenditure\DeoExpenditureController@candidateListByfinalizeDatagraph');
Route::get('/logedaccountgraph', 'Expenditure\DeoExpenditureController@candidateListBylogedaccountgraph');
Route::get('/notintime', 'Expenditure\DeoExpenditureController@candidateListBynotintime');
Route::get('/formatedefectsgraph', 'Expenditure\DeoExpenditureController@candidateListByformatedefectsgraph');
Route::get('/ronotagree', 'Expenditure\DeoExpenditureController@candidateListByronotagree');
Route::get('/understatedexpensegraph', 'Expenditure\DeoExpenditureController@candidateListByunderstatedexpense');
//Route::get('/dataentrydefects', 'Expenditure\DeoExpenditureController@candidateListBydataentrydefects');
Route::get('/partyfundgraph', 'Expenditure\DeoExpenditureController@candidateListBypartyfundgraph');
Route::get('/othersfundgraph', 'Expenditure\DeoExpenditureController@candidateListByothersfundgraph');
// status 
Route::get('/getpendingcandidateListgraph', 'Expenditure\DeoExpenditureController@getpendingcandidateListgraph');
Route::get('/getpartiallypendingcandidateListgraph', 'Expenditure\DeoExpenditureController@getpartiallypendingcandidateListgraph');
Route::get('/getdefaultercandidateListgraph', 'Expenditure\DeoExpenditureController@getdefaultercandidateListgraph');

// graph for individual end here
// tracking start here 
Route::get("/tracking","Expenditure\DeoExpenditureController@getTrackingByROUserId");
Route::get('/getscrutinyreport','Expenditure\DeoExpenditureController@getscrutinyreport');
Route::get('/getProfile','Expenditure\DeoExpenditureController@getprofile');
Route::get('/generatePDF/{id}','Expenditure\DeoExpenditureController@generatePDF');
Route::get('/confirmReport','Expenditure\DeoExpenditureController@confirmReport');
Route::get('/printScrutinyReport/{id}/{ac}',array('as'=>'printScrutinyReport','uses'=>'Expenditure\DeoExpenditureController@printScrutinyReport'));
Route::get('/GetProfileRO','Expenditure\DeoExpenditureController@GetProfileRO');
Route::get('/printTrackingStatus/{id}','Expenditure\DeoExpenditureController@printTrackingStatus');

// tracking end here
Route::post('/update_understated_file1','Expenditure\DeoExpenditureController@update_understated_file1');
Route::post('/update_understated_file2','Expenditure\DeoExpenditureController@update_understated_file2');
Route::post('/update_understated_file4','Expenditure\DeoExpenditureController@update_understated_file4');
Route::post('/uploadsigned','Expenditure\DeoExpenditureController@uploadsigned');
Route::post('/updateNoticeFile','Expenditure\DeoExpenditureController@updateNoticeFile');
Route::get('/tracking-status','Expenditure\DeoExpenditureController@tracking_status');
Route::get('/reports','Expenditure\DeoExpenditureController@trackingReport');
Route::get('/trackingReportprint','Expenditure\DeoExpenditureController@trackingReportprint');
Route::get('/view/{id}/{ac_no}','Expenditure\DeoExpenditureController@viewByCandidateId');
// ECRP
Route::get('/ecrp-registration', 'Expenditure\DeoExpenditureController@ecrpRegistration');
Route::post('/saveEcrpRegistration', 'Expenditure\DeoExpenditureController@saveEcrpRegistration');
Route::get('/getdistrictsbystate', 'Expenditure\DeoExpenditureController@getdistrictsbystate');
Route::post('/assignEcrpRegistration', 'Expenditure\DeoExpenditureController@assignEcrpRegistration');
Route::get('/getEcrpList', 'Expenditure\DeoExpenditureController@getEcrpList');
Route::get('/getParty', 'Expenditure\DeoExpenditureController@getParty');
Route::get('/getECRPCandidateList/{stcode}', 'Expenditure\DeoExpenditureController@getECRPCandidateList');
Route::get('/getFiledStatementList', 'Expenditure\DeoExpenditureController@getFiledStatementList');
//Route::post('/updateNoticeFile','Expenditure\DeoExpenditureController@updateNoticeFile');
// end ECRP
// Manoj end here
// manish start here
Route::post('/updateUnderstatedDetail', 'Expenditure\DeoExpenditureController@updateUnderstatedDetail'); 
Route::post('/UpdateSourceFundData', 'Expenditure\DeoExpenditureController@UpdateSourceFundData'); 
Route::post('/UpdatePartyFundData', 'Expenditure\DeoExpenditureController@UpdatePartyFundData'); 
Route::post('/SaveExpenseData', 'Expenditure\DeoExpenditureController@SaveExpenseData'); 
Route::post('/DeleteSourceFundData','Expenditure\DeoExpenditureController@DeleteSourceFundData');
Route::post('/DeleteUnderStatedData','Expenditure\DeoExpenditureController@DeleteUnderStatedData');
Route::post('/FinalizedData','Expenditure\DeoExpenditureController@FinalizedData');
// manish end here



//////abstrac form ///////////////
Route::get('/candidateList_abstract', 'Expenditure\DeoExpenditureController@candidateList_abstract');
Route::get('/annuxure/{id}', 'Expenditure\DeoExpenditureController@annuxure');
Route::post('/SaveAnnuxureData', 'Expenditure\DeoExpenditureController@SaveAnnuxureData');



/////////////////////////////tracking////////
Route::get('/GetTrackingReportData', 'Expenditure\DeoExpenditureController@GetTrackingReportData'); 
Route::get('/editExpenditureData/{id}', 'Expenditure\DeoExpenditureController@editExpenditureData'); 
Route::post('/StoreMisExpenseReport', 'Expenditure\DeoExpenditureController@StoreMisExpenseReport'); 
Route::get('/updateData', 'Expenditure\DeoExpenditureController@updateData'); 
Route::get('/editExpenditureReport', 'Expenditure\DeoExpenditureController@editExpenditureReport'); 
Route::post('/updateNoticeFile','Expenditure\DeoExpenditureController@updateNoticeFile');
// Manoj
Route::get('/Summary', 'Expenditure\DeoExpenditureController@getSummary');
Route::match(['GET','POST'],'/reports', 'Expenditure\DeoExpenditureController@trackingReport'); 
 
Route::get('/trackingReportprint/{acno}','Expenditure\DeoExpenditureController@trackingReportprint');
//
Route::get('/return', 'Expenditure\DeoExpenditureController@getReturn');
Route::get('/non-return', 'Expenditure\DeoExpenditureController@getNonReturn');
Route::get('/FinalizedcandidateList', 'Expenditure\DeoExpenditureController@getcandidateList')->name('FinalizedcandidateList');
Route::get('/updateStatusReport', 'Expenditure\DeoExpenditureController@updateStatusReport');