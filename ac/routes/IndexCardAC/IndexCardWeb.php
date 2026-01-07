<?php


Route::group(['prefix' => 'eci', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function(){
	Route::any('/test',function(){
		echo "hello!"; die;
	});
        	
	 /****************** Jitendra Code Start******* *********************/
        
         //Route::get('index-card','IndexCardReportsAC\IndexCard\IndexCardController@indexcard');
        Route::get('ajaxpccall','IndexCardReportsAC\IndexCard\IndexCardController@ajaxpccall');
        //Route::post('getindexcarddata','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        Route::any('index-card','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        Route::any('indexcardacpdf/{ac}/{st_code}','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
		Route::get('de-finalize-log', 'IndexCardReportsAC\DeFinalizeIndexCard\DeFinalizeLogController@deFinalizeLogs');
		Route::get('de-finalize-log/pdf', 'IndexCardReportsAC\DeFinalizeIndexCard\DeFinalizeLogController@deFinalizeLogs');
		Route::get('de-finalize-log/excel', 'IndexCardReportsAC\DeFinalizeIndexCard\DeFinalizeLogController@deFinalizeLogs');
		Route::any('indexcardacexcel/{ac}/{st_code}','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        
    /****************** Jitendra Code END******* *********************/	
});

Route::group(['prefix' => 'eci-index', 'as' => 'eci-index::', 'middleware' => ['auth:admin', 'auth']], function(){
	Route::any('/test',function(){
		echo "hello!"; die;
	});
        	
	 /****************** Jitendra Code Start******* *********************/
        
        Route::get('ajaxpccall','IndexCardReportsAC\IndexCard\IndexCardController@ajaxpccall');
        Route::any('index-card','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        Route::any('indexcardacpdf/{ac}/{st_code}','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
		
		Route::any('indexcardacexcel/{ac}/{st_code}','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
				
		Route::get('bye-election-verify-report', 'IndexCardReportsAC\IndexCardReport\StatisticalReportListingController@indexcardreportlist');
		
		Route::get('bye-report-listing-verify-checkbox', 'IndexCardReportsAC\IndexCardReport\StatisticalReportListingController@byeverifyreportcheckbox');
		
		
		// Other Abbreviations and Description
		Route::any('other-abbreviations-and-description/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@otherAbbreviationsAndDescription');
		Route::any('other-abbreviations-and-description-pdf/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@otherAbbreviationsAndDescription');
		Route::any('other-abbreviations-and-description-xls/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@otherAbbreviationsAndDescription');
		
		// List of Successful Candidates
		Route::any('list-of-successful-candidates/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@listOfSuccessfulCandidates');
		Route::any('list-of-successful-candidates-pdf/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@listOfSuccessfulCandidates');
		Route::any('list-of-successful-candidates-xls/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@listOfSuccessfulCandidates');
		
		// List Of Political Parties Participated
		Route::any('list-of-political-parties-participated/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@listOfPoliticalPartiesParticipated');
		Route::any('list-of-political-parties-participated-pdf/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@listOfPoliticalPartiesParticipated');
		Route::any('list-of-political-parties-participated-xls/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@listOfPoliticalPartiesParticipated');
		
		// Candidate Data Summary
		Route::any('candidate-data-summary/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@candidateDataSummary');
		Route::any('candidate-data-summary-pdf/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@candidateDataSummary');
		Route::any('candidate-data-summary-xls/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@candidateDataSummary');
		
		// Detailed Results
		Route::any('detailed-results/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@detailedResults');
		Route::any('detailed-results-pdf/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@detailedResults');
		Route::any('detailed-results-xls/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@detailedResults');
		
		// Performance of Women Candidates
		Route::any('performance-of-women-candidates/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@performanceOfWomenCandidates');
		Route::any('performance-of-women-candidates-pdf/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@performanceOfWomenCandidates');
		Route::any('performance-of-women-candidates-xls/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@performanceOfWomenCandidates');
		
		// Performance of Political Parties
		Route::any('performance-of-political-parties/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@performanceOfPoliticalParties');
		Route::any('performance-of-political-parties-pdf/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@performanceOfPoliticalParties');
		Route::any('performance-of-political-parties-xls/{st_code}','IndexCardReportsAC\IndexCardReport\IndexCardReportController@performanceOfPoliticalParties');
		
		
		
		//Reports  11- AC Wise Number Of Electors 
		Route::get('ac-wise-no-of-electors/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@noofelectors'); 
		Route::get('ac-wise-no-of-electors-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@noofelectors'); 
		Route::get('ac-wise-no-of-electors-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@noofelectors'); 
		
        //Reports  12- AC Wise Voters Information 
		Route::get('ac-wise-voters-information/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@votersinformation'); 
		Route::get('ac-wise-voters-information-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@votersinformation'); 
		Route::get('ac-wise-voters-information-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@votersinformation'); 
				
		//Reports  13- AC Wise Candidate data Summary 
		Route::get('ac-wise-candidate-data-summary/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@acwisecandidatedatasummary'); 
		Route::get('ac-wise-candidate-data-summary-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@acwisecandidatedatasummary'); 
		Route::get('ac-wise-candidate-data-summary-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@acwisecandidatedatasummary'); 
 
 
 
		 //Reports  15- Constituency wise detailed Result 
		Route::get('constituency-wise-detailed-result/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@constituencywisedetailedresult'); 
		Route::get('constituency-wise-detailed-result-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@constituencywisedetailedresult'); 
		Route::get('constituency-wise-detailed-result-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@constituencywisedetailedresult'); 
				
		//Reports  16- List Of the Successful Candidate (B) 
		Route::get('list-of-successful-candidates-b/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@listofsuccessfulcandidatesb'); 
		Route::get('list-of-successful-candidates-b-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@listofsuccessfulcandidatesb'); 
		Route::get('list-of-successful-candidates-b-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\IndexCardReportController@listofsuccessfulcandidatesb'); 
 
 
 
 

	//Route::any('statistical-report-listing', 'IndexCardReportsAC\IndexCardReport\StatisticalReportListingController@statisticalreportlist');
	
	Route::any('statistical-report-listing', 'IndexCardReportsAC\IndexCardReport\StatisticalReportListingController@statisticalreportlist');
	
	Route::get('statistical-report-listing-verify', 'IndexCardReportsAC\IndexCardReport\StatisticalReportListingController@verifyreport');
    Route::get('statistical-report-listing-verify-checkbox', 'IndexCardReportsAC\IndexCardReport\StatisticalReportListingController@verifyreportcheckbox');
	
	
    Route::get('statistical-report-listing-verify-all-report', 'IndexCardReportsAC\IndexCardReport\StatisticalReportListingController@verifyallreport');

	//Reports  4-Highlights
	Route::get('highlights/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@highlight'); 
	Route::get('highlights-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@highlight'); 
	Route::get('highlights-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@highlight'); 

	 

	//Reports  6-Electors Data Summary 
	Route::get('electorsdatasummary/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@electorsdatasummary'); 
	Route::get('electorsdatasummary-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@electorsdatasummary'); 
	Route::get('electorsdatasummary-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@electorsdatasummary'); 

	

	//Reports  8-Constituency Data Summary 

	Route::get('constituency-data-summary/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@constituencydatasummary'); 
	Route::get('constituency-data-summary-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@constituencydatasummary'); 
	Route::get('constituency-data-summary-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@constituencydatasummary'); 


	//Reports  ANNXURE

	Route::get('annxure/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@annxure'); 
	Route::get('annxure-pdf/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@annxure'); 
	Route::get('annxure-excel/{st_code}', 'IndexCardReportsAC\IndexCardReport\ReportIndexCardController@annxure'); 
        
    /******************  END******* *********************/
	
	
	
	Route::group(['prefix' => 'indexcard'], function(){
    //complain  add eci
    Route::get('get-indexcard-eci', 'IndexCardReportsAC\DeFinalizeIndexCard\IndexcardController@get_indexcard_for_eci');
    Route::get('de-finalize-acs','IndexCardReportsAC\DeFinalizeIndexCard\IndexCardDeFinalizeController@get_complains_list');
	Route::get('de-finalize-acs/pdf', 'IndexCardReportsAC\DeFinalizeIndexCard\IndexCardDeFinalizeController@deFinalizeAcs');
	Route::get('de-finalize-acs/excel', 'IndexCardReportsAC\DeFinalizeIndexCard\IndexCardDeFinalizeController@deFinalizeAcs');

    Route::post('de-finalize-acs/post','IndexCardReportsAC\DeFinalizeIndexCard\IndexCardDeFinalizeController@definalize_indexcard');
    Route::get('de-finalize-acs/post',function(){
      return redirect('/eci-index/indexcard/de-finalize-acs');
    });
    
    Route::post('definalize-nomination','IndexCardReportsAC\DeFinalizeIndexCard\IndexCardDeFinalizeController@definalize_nomination');
    Route::get('definalize-nomination',function(){
      return redirect('/eci-index/indexcard/de-finalize-acs');
    });
    
    Route::post('definalize-counting','IndexCardReportsAC\DeFinalizeIndexCard\IndexCardDeFinalizeController@definalize_counting');
    Route::get('definalize-counting',function(){
      return redirect('/eci-index/indexcard/de-finalize-acs');
    });
    
    
  });
	
	
});

Route::group(['prefix' => 'acceo', 'as' => 'acceo::', 'middleware' => ['auth:admin', 'auth']], function(){
	Route::any('/test',function(){
		echo "hello!"; die;
	});
        	
	 /****************** Jitendra Code Start******* *********************/
 
        Route::any('index-card','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        Route::any('indexcardacpdf/{ac}','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        
		Route::any('indexcardacexcel/{ac}','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
    /****************** Jitendra Code END******* *********************/	
});

Route::group(['prefix' => 'acdeo', 'as' => 'acdeo::', 'middleware' => ['auth:admin', 'auth']], function(){
	Route::any('/test',function(){
		echo "hello!"; die;
	});
        	
	 /****************** Jitendra Code Start******* *********************/
 
        Route::any('index-card','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        Route::any('indexcardacpdf/{ac}','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
		
		Route::any('indexcardacexcel/{ac}','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
				
		
        
    /****************** Jitendra Code END******* *********************/	
});



Route::group(['prefix' => 'roac', 'as' => 'roac::', 'middleware' => ['auth:admin', 'auth']], function(){
        	
	 /****************** Jitendra Code Start******* *********************/
        
		Route::get('index-card','IndexCardReportsAC\IndexCard\IndexCardController@indexcard');
        Route::post('getindexcarddata','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        Route::any('indexcardacpdf','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
		
		Route::any('indexcardacexcel','IndexCardReportsAC\IndexCard\IndexCardController@getindexcarddata');
        
    /****************** Jitendra Code END******* *********************/	
});


?>