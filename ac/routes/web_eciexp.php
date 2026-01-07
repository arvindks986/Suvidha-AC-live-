<?php
//created by Niraj for expendature on ECI Level



#########################start by Niraj #############################

Route::match(array('GET','POST'),'/expdashboard/', 'Expenditure\EciExpenditureController@dashboard');
Route::get('/dataentryStart/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListBydataentryStart');
Route::get('/finalizeData/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListByfinalizeData');
Route::get('/logedaccount/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListBylogedaccount');
Route::get('/notintime/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListBynotintime');
Route::get('/formatedefects/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListByformatedefects');
Route::get('/ronotagree/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListByronotagree');
Route::get('/understatedexpense/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListByunderstatedexpense');
Route::get('/dataentrydefects/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListBydataentrydefects');
Route::get('/partyfund/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListBypartyfund');
Route::get('/othersfund/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListByothersfund');
Route::get('/exeedceiling/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListByexeedceiling');
Route::get('/getacbystate', 'Expenditure\EciExpenditureController@getaclist');  

//dashboard current status
Route::match(array('GET','POST'),'/statusdashboard', 'Expenditure\EciExpenditureController@statusdashboard');
Route::get('/pendingdataentry/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getpendingcandidateList');
Route::get('/partiallypending/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getpartiallypendingcandidateList');
Route::get('/filedData/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListByfiledData');
Route::get('/defaulter/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getdefaultercandidateList');
Route::get('/finalbyceo/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@candidateListfinalbyCEO');
Route::get('/finalbyeci-report/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatECIReport');

//MIS Report
Route::match(array('GET','POST'),'/mis-officer-details', 'Expenditure\EciExpenditureController@getOfficersmisDetails');
Route::get('/EciOfficerMISDetailsEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersmisEXLDetails');
Route::get('/EciOfficerMISDetailsPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersmisPDFDetails');
Route::match(array('GET','POST'),'/mis-officer', 'Expenditure\EciExpenditureController@getOfficersmis');
Route::get('/EciOfficerMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersmisEXL');
Route::get('/EciOfficerMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersmisPDF');
Route::get('/allcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@finalCandidateList');
Route::get('/allcandidateEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@finalCandidateListEXL');
Route::get('/allcandidatePDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@finalCandidateListPDF');

Route::get('/pendingatro/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatRO');
Route::get('/pendingatroEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatROEXL');
Route::get('/pendingatroPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatROPDF');

Route::get('/pendingatceo/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatCEO');
Route::get('/pendingatceoEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatCEOEXL');
Route::get('/pendingatceoPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatCEOPDF');

Route::get('/pendingateci/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatECI');
Route::get('/pendingateciEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatECIEXL');
Route::get('/pendingateciPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatECIPDF');

Route::get('/finalbyeci/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListfinalbyECI');
Route::get('/finalbyeciEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListfinalbyECIEXL');
Route::get('/finalbyeciPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListfinalbyECIPDF');


Route::match(array('GET','POST'),'/mis-candidate', 'Expenditure\EciExpenditureController@getCandidatemis');
Route::get('/EciCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getCandidatesmisEXL');
Route::get('/EciCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getCandidatemisPDF');

Route::get('/filedcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@filedcandidateData');
Route::get('/EcifiledCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@filedcandidateDataEXL');
Route::get('/EcifiledCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@filedcandidateDataPDF');

Route::get('/notfiledcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notfiledcandidateData');
Route::get('/EciNotfiledCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notfiledcandidateDataEXL');
Route::get('/EciNotfiledCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notfiledcandidateDataPDF');


Route::get('/notintimecandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notintimecandidateData');
Route::get('/EcinotintimeCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notintimecandidateDataEXL');
Route::get('/EcinotintimeCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notintimecandidateDataPDF');

Route::get('/defaultercandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@defaultercandidateData');
Route::get('/EciDefaulterCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@defaultercandidateDataEXL');
Route::get('/EciDefaulterCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@defaultercandidateDataPDF');
Route::get('/getCandTracking/{candidate_id}', 'Expenditure\EciExpenditureController@getCandTracking');

//date 02-07-2019
Route::get('/Ecistartedcandidate/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@Ecistartedcandidate');
//date 02-07-2019
Route::get('/Ecinotstarted/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@Ecinotstarted');


//date 02-07-2019
Route::get('/EcifinalbyDEO/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@EcifinalbyDEO');
Route::get('/EcifinalbyDEOMISEXL/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@EcifinalbyDEOMISEXL');
Route::get('/EcifinalbyDEOMISPDF/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@EcifinalbyDEOMISPDF');

//Dated : 09-09-2019 by Niraj 
Route::get('/disqualifiedbyeci/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getdisqualifiedcandidateListbyECI');
Route::get('/disqualifiedbyeciEXL/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getdisqualifiedcandidateListbyECIEXL');

//Report Section
Route::match(array('GET','POST'),'/report-officer', 'Expenditure\EciExpenditureController@getOfficersreport');
Route::get('/EciOfficerReportEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersreportEXL');
Route::get('/EciOfficerReportPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersreportPDF');
//date 20-08-2019
Route::match(array('GET','POST'),'/fund-nationalparties', 'Expenditure\EciExpenditureController@getNationlPartyWiseExpenditure');
Route::get('/fund-nationalparties-graph', 'Expenditure\EciExpenditureController@getNationlPartyWiseExpendituregraph');
Route::match(array('GET','POST'),'/fund-nationalpartiesavggraph', 'Expenditure\EciExpenditureController@getNationlPartyWiseExpenditureAvgGraph');
Route::match(array('GET','POST'),'/fund-nationalpartiesnationgraph', 'Expenditure\EciExpenditureController@getNationlPartyWiseExpenditureNationGraph');


//Notice Section 

Route::get('/noticeatceo/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getnoticeatCEO');
Route::get('/noticeatceoEXL/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getnoticeatCEOEXL');
//Route::get('/pendingatceoPDF/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatCEOPDF');
Route::get('/noticeatdeo/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getnoticeatDEO');
Route::get('/noticeatdeoEXL/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getnoticeatDEOEXL');

// Summary Analytics Section Date 18-09-2019

Route::match(array('GET','POST'),'/analytic-summary/{record?}', 'Expenditure\EciExpenditureController@getanalyticsummary');
// Breach-report Section Date 22-01-2020
Route::get('/breach-details/{st_code}/{pc_no}','Expenditure\EciExpenditureController@getbreachAmnt');
Route::any('/breach-report','Expenditure\EciExpenditureController@getbreachAmntMis');

#########################end by Niraj #############################

Route::get('/masterEntry/', 'Expenditure\EciExpenditureController@masterEntry');
Route::post('/storeMasterEntry{mid?}','Expenditure\EciExpenditureController@storeMasterEntry');
Route::get('/ActionOnCandidate', 'Expenditure\EciExpenditureController@ActionOnCandidate');
Route::get('/printingNoticeDeoLetter', 'Expenditure\EciExpenditureController@printingNoticeDeoLetter');
Route::get('/UploadNoticeDeoLetter', 'Expenditure\EciExpenditureController@UploadNoticeDeoLetter');
Route::get('/MasterDataListing', 'Expenditure\EciExpenditureController@MasterDataListing');
Route::post('/saveComment','Expenditure\EciExpenditureController@saveComment');
Route::get('/confirmReport','Expenditure\EciExpenditureController@confirmReport');
Route::get('/editExpenditureReport', 'Expenditure\EciExpenditureController@editExpenditureReport'); 
Route::post('/StoreMisExpenseReport', 'Expenditure\EciExpenditureController@StoreMisExpenseReport'); 

// manish end here
 // manoj start 
  // tracking start here 
    /////////////////////////////tracking//////// 
Route::match(array('GET','POST'),'/GetTrackingReportData', 'Expenditure\EciController@GetTrackingReportData'); 
Route::get('/updateData', 'Expenditure\EciController@updateData'); 
Route::get('/getscrutinyreport','Expenditure\EciExpenditureController@getscrutinyreport');
Route::get('/generatePDF/{id}','Expenditure\EciExpenditureController@generatePDF');
Route::get('/GetProfileECI','Expenditure\EciExpenditureController@GetProfileECI');


 // graph start here 

Route::get('/candidateListBydataentryStartgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBydataentryStartgraph');
Route::get('/candidateListByfinalizeDatagraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByfinalizeDatagraph');
Route::get('/candidateListBylogedaccountgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBylogedaccountgraph');
Route::get('/candidateListBynotintimegraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBynotintimegraph');
Route::get('/candidateListByformatedefectsgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByformatedefectsgraph');
 
Route::get('/candidateListByunderstatedexpensegraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByunderstatedexpensegraph');
 
Route::get('/candidateListBypartyfundgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBypartyfundgraph');
Route::get('/candidateListByothersfundgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByothersfundgraph');
Route::get('/getpendingcandidateListgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getpendingcandidateListgraph');
Route::get('/getpartiallypendingcandidateListgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getpartiallypendingcandidateListgraph');
Route::get('/getdefaultercandidateListgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getdefaultercandidateListgraph');
 // for graph end here 
Route::get('/getprofile','Expenditure\EciExpenditureController@getprofile');
Route::get('/printScrutinyReport/{id}/{ac}',array('as'=>'printScrutinyReport','uses'=>'Expenditure\EciExpenditureController@printScrutinyReport'));
Route::get('/FinalizedcandidateList', 'Expenditure\EciExpenditureController@getcandidateList');
Route::get('/updateStatusReport', 'Expenditure\EciExpenditureController@updateStatusReport');
Route::get('/ecinotification', 'Expenditure\EciNotificationExpenditureController@scrutiny');
Route::get('/eciallscrutiny', 'Expenditure\EciNotificationExpenditureController@allscrutiny');  
Route::get('/eciallscrutinybyepass', 'Expenditure\EciNotificationExpenditureController@allscrutinyByPass');  
    //district wise
Route::match(array('GET','POST'),'/district-report', 'Expenditure\EciExpenditureController@getDistrictReport');
Route::get('/districtreportexl/{st_code}/{district}/{ac_no}', 'Expenditure\EciExpenditureController@getDistrictReportExl');
Route::get('/districtreportpdf/{st_code}/{district}/{ac_no}', 'Expenditure\EciExpenditureController@getDistrictReportPdf');
	
Route::get('/getdistricts/{st_code}', 'Expenditure\EciExpenditureController@Alldistrict');
Route::get('/getdistrictacs', 'Expenditure\EciExpenditureController@getAllACs');
	 //district wise end
// new add manoj
Route::get('/view/{id}/{ac_no}','Expenditure\EciExpenditureController@viewByCandidateId');

Route::get('/receivedNotification', 'Expenditure\EciNotificationExpenditureController@receivedNotification'); 
Route::post('/updateReceived', 'Expenditure\EciNotificationExpenditureController@updateReceived')->name('updateReceived'); 
Route::match(array('GET','POST'),'/reports','Expenditure\EciExpenditureController@trackingReport');
Route::match(array('GET','POST'),'/trackingReportprint/{st_code}/{ac_no}','Expenditure\EciExpenditureController@trackingReportprint'); 
// manoj end
 
Route::get('/return/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getReturn');
//added by Niraj for getting all electedcandidate
Route::get('/electedcandidate/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getElectedcand');

Route::get('/non-return/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getNonReturn');
Route::get('/candidate_wise_expenditure','Expenditure\EciExpenditureController@candidate_wise_expenditure');
Route::get('/getPartyWiseExpenditure','Expenditure\EciExpenditureController@getPartyWiseExpenditure');




// for de-finalized candidate 

Route::get('/definalizedcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@definalizedcandidate');
Route::get('/get_definalize_data','Expenditure\EciExpenditureController@get_definalize_data');



