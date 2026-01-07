<?php
//created by Niraj for expendature on CEO Level


#########################start by Niraj #############################
Route::match(array('GET','POST'),'/expdashboard', 'Expenditure\ACCeoExpenditureController@dashboard');
Route::get('/dataentryStart/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListBydataentryStart');
Route::get('/finalizeData/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListByfinalizeData');
Route::get('/logedaccount/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListBylogedaccount');
Route::get('/notintime/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListBynotintime');
Route::get('/formatedefects/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListByformatedefects');
Route::get('/ronotagree/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListByronotagree');
Route::get('/understatedexpense/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListByunderstatedexpense');
Route::get('/dataentrydefects/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListBydataentrydefects');
Route::get('/partyfund/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListBypartyfund');
Route::get('/othersfund/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListByothersfund');
Route::get('/exeedceiling/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListByexeedceiling');



//dashboard current status
Route::match(array('GET','POST'),'/statusExpdashboard', 'Expenditure\ACCeoExpenditureController@statusdashboard');
Route::get('/pendingdataentry/{ac}', 'Expenditure\ACCeoExpenditureController@getpendingcandidateList');
Route::get('/partiallypending/{ac}', 'Expenditure\ACCeoExpenditureController@getpartiallypendingcandidateList');
Route::get('/filedData/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListByfiledData');
Route::get('/defaulter/{ac}', 'Expenditure\ACCeoExpenditureController@getdefaultercandidateList');
Route::get('/finalbyceo/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListfinalbyCEO');
Route::get('/finalbyeci/{ac}', 'Expenditure\ACCeoExpenditureController@candidateListfinalbyECI');

//Notice Section
Route::get('/noticeatceo/{ac}', 'Expenditure\ACCeoExpenditureController@getnoticeatCEO');
Route::get('/noticeatceoEXL/{ac}', 'Expenditure\ACCeoExpenditureController@getnoticeatCEOEXL');

//MIS Report Date 21-08-2019
Route::match(array('GET','POST'),'/mis-officer', 'Expenditure\ACCeoExpenditureController@getOfficersmis');
Route::get('/OfficerMISEXL/{ac}', 'Expenditure\ACCeoExpenditureController@getOfficersmisEXL');
Route::get('/OfficerMISPDF/{ac}', 'Expenditure\ACCeoExpenditureController@getOfficersmisPDF');

Route::get('/expallcandidate/{ac}', 'Expenditure\ACCeoExpenditureController@finalCandidateList');
Route::get('/expallcandidateEXL/{ac}', 'Expenditure\ACCeoExpenditureController@finalCandidateListEXL');
Route::get('/expallcandidatePDF/{ac}', 'Expenditure\ACCeoExpenditureController@finalCandidateListPDF');

Route::get('/expstartedcandidate/{ac}', 'Expenditure\ACCeoExpenditureController@getStartedcandidateMIS');
Route::get('/expstartedcandidateEXL/{ac}', 'Expenditure\ACCeoExpenditureController@getStartedcandidateMISEXL');
Route::get('/expstartedcandidatePDF/{ac}', 'Expenditure\ACCeoExpenditureController@getStartedcandidateMISPDF');

Route::get('/expnotstarted/{ac}', 'Expenditure\ACCeoExpenditureController@getNotstartedMIS');
Route::get('/expnotstartedEXL/{ac}', 'Expenditure\ACCeoExpenditureController@getNotstartedMISEXL');
Route::get('/expnotstartedPDF/{ac}', 'Expenditure\ACCeoExpenditureController@getNotstartedMISPDF');

Route::get('/expfinalbyDEO/{ac}', 'Expenditure\ACCeoExpenditureController@getfinalbyDEO');
Route::get('/expfinalbyDEOMISEXL/{ac}', 'Expenditure\ACCeoExpenditureController@getfinalbyDEOMISEXL');
Route::get('/expfinalbyDEOMISPDF/{ac}', 'Expenditure\ACCeoExpenditureController@getfinalbyDEOMISPDF');


Route::get('/exppendingatro/{ac}', 'Expenditure\ACCeoExpenditureController@getcandidateListpendingatRO');
Route::get('/exppendingatroEXL/{ac}', 'Expenditure\ACCeoExpenditureController@getcandidateListpendingatROEXL');
Route::get('/exppendingatroPDF/{ac}', 'Expenditure\ACCeoExpenditureController@getcandidateListpendingatROPDF');

Route::get('/exppendingatceo/{ac}', 'Expenditure\ACCeoExpenditureController@getcandidateListpendingatCEO');
Route::get('/exppendingatceoEXL/{ac}', 'Expenditure\ACCeoExpenditureController@getcandidateListpendingatCEOEXL');
Route::get('/exppendingatceoPDF/{ac}', 'Expenditure\ACCeoExpenditureController@getcandidateListpendingatCEOPDF');

Route::get('/expnotintimecandidate/{ac}', 'Expenditure\ACCeoExpenditureController@getnotintimecandidateData');
Route::get('/expnotintimeCandidateMISEXL/{ac}', 'Expenditure\ACCeoExpenditureController@getnotintimecandidateDataEXL');
Route::get('/expnotintimeCandidateMISPDF/{ac}', 'Expenditure\ACCeoExpenditureController@getnotintimecandidateDataPDF');
#########################end by Niraj #############################

// manish end here
 // manoj start 
  // tracking start here 
    /////////////////////////////tracking////////
Route::get('/GetTrackingReportData', 'Expenditure\PCCeoExpenditureController@GetTrackingReportData'); 
Route::get('/editExpenditureData/{id}', 'Expenditure\PCCeoExpenditureController@editExpenditureData'); 
Route::post('/StoreMisExpenseReport', 'Expenditure\ACCeoExpenditureController@StoreMisExpenseReport'); 
Route::get('/updateData', 'Expenditure\ACCeoExpenditureController@updateData'); 
Route::get('/getscrutinyreport','Expenditure\PCCeoExpenditureController@getscrutinyreport');
Route::post('/saveComment','Expenditure\PCCeoExpenditureController@saveComment');
Route::get('/confirmReport','Expenditure\ACCeoExpenditureController@confirmReport');
Route::get('/generatePDF/{id}','Expenditure\ACCeoExpenditureController@generatePDF');



//for graph start here
Route::get('/candidateListBydataentryStartgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBydataentryStartgraph');
Route::get('/candidateListByfinalizeDatagraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByfinalizeDatagraph');
Route::get('/candidateListBylogedaccountgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBylogedaccountgraph');
Route::get('/candidateListBynotintimegraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBynotintimegraph');
Route::get('/candidateListByformatedefectsgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByformatedefectsgraph');
Route::get('/getpendingcandidateListgraph/{pc}', 'Expenditure\PCCeoExpenditureController@getpendingcandidateListgraph');
Route::get('/getpartiallypendingcandidateListgraph/{pc}', 'Expenditure\PCCeoExpenditureController@getpartiallypendingcandidateListgraph');
Route::get('/getdefaultercandidateListgraph/{pc}', 'Expenditure\PCCeoExpenditureController@getdefaultercandidateListgraph');
 
Route::get('/candidateListByunderstatedexpensegraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByunderstatedexpensegraph');
  
Route::get('/candidateListBypartyfundgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBypartyfundgraph');
Route::get('/candidateListByothersfundgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByothersfundgraph');
Route::get('/printScrutinyReport/{id}/{ac_no}',array('as'=>'printScrutinyReport','uses'=>'Expenditure\ACCeoExpenditureController@printScrutinyReport'));
Route::get('/getprofile','Expenditure\ACCeoExpenditureController@getprofile');
Route::get('/editExpenditureReport', 'Expenditure\ACCeoExpenditureController@editExpenditureReport'); 
Route::get('/GetProfileCEO','Expenditure\PCCeoExpenditureController@GetProfileCEO');
Route::get('/view/{id}/{ac_no}','Expenditure\ACCeoExpenditureController@viewByCandidateId');

Route::post('/updateReceived', 'Expenditure\NotificationExpenditureController@updateReceived')->name('updateReceived');
// manoj end
//Shishir sharma
Route::get('/notification', 'Expenditure\NotificationExpenditureController@scrutiny');
Route::get('/allscrutiny', 'Expenditure\NotificationExpenditureController@allscrutiny');
Route::get('/printTrackingStatus/{id}','Expenditure\PCCeoExpenditureController@printTrackingStatus');
 


// new add manoj
Route::get('/view/{id}','Expenditure\ACCeoExpenditureController@viewByCandidateId');
// manoj end
Route::get('/return/{ac}', 'Expenditure\ACCeoExpenditureController@getReturn');
Route::get('/non-return/{ac}', 'Expenditure\ACCeoExpenditureController@getNonReturn');
Route::get('/FinalizedcandidateList', 'Expenditure\ACCeoExpenditureController@getcandidateList')->name('FinalizedcandidateList');
Route::get('/updateStatusReport', 'Expenditure\ACCeoExpenditureController@updateStatusReport');



// for de-finalized candidate 

Route::get('/definalizedcandidate', 'Expenditure\NotificationExpenditureController@definalizedcandidate');
Route::post('/UpdateStatusData', 'Expenditure\NotificationExpenditureController@UpdateStatusData');
Route::get('/get_definalize_data','Expenditure\EciExpenditureController@get_definalize_data');


