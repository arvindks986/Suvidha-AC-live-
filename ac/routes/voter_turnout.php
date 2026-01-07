<?php

use Illuminate\Support\Facades\Route;

Route::get('/publish-vtr', 'Admin\PublishController@update_turnout_index');
Route::get('/publish-vtr-show', 'Admin\PublishController@show_turnout_index');
Route::get('/closed-missed-window', 'Admin\PublishController@closedMissedEntryWindows');
Route::get('/closed-modifed-window', 'Admin\PublishController@closedModifyEntryWindows');
Route::get('/test/publish-vtr', 'Admin\PublishTestController@update_turnout_index');
Route::get('/test/publish-vtr-show', 'Admin\PublishTestController@show_turnout_index');
Route::get('/test/closed-missed-window', 'Admin\PublishTestController@closedMissedEntryWindows');
Route::get('/test/closed-modifed-window', 'Admin\PublishTestController@closedModifyEntryWindows');

Route::group(['middleware' => 'adminsession'], function () {

  //ECI TURNOUT ROUTES STARTS
  Route::group(['prefix' => 'eci', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function () {

    //ECI ELECTION SCHEDULE 
    Route::get('/ElectionScheduleState', 'Admin\ElectionScheduleController@ElectionScheduleState');
    //ECI ELECTION SCHEDULE EXCEL REPORT
    Route::get('/ElectionScheduleState/excel', 'Admin\ElectionScheduleController@ElectionScheduleStateExcel');
    //ECI ELECTION SCHEDULE PDF REPORT
    Route::get('/ElectionScheduleState/pdf', 'Admin\ElectionScheduleController@ElectionScheduleStatePdf');

    Route::get('/ElectionScheduleState/state', 'Admin\ElectionScheduleController@ElectionScheduleAc');
    Route::get('/ElectionScheduleState/state/excel', 'Admin\ElectionScheduleController@ElectionScheduleAcExcel');
    Route::get('/ElectionScheduleState/state/pdf', 'Admin\ElectionScheduleController@ElectionScheduleAcPdf');

    Route::group(['prefix' => 'turnout', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth', 'eci']], function () {


      ####################### Script for the entry data from boothapp to suvidhaac turnout #####################
      Route::get('/update_turnout', 'Admin\turnout\ECITurnoutController@update_turnout_index');
      Route::get('/update_turnout_data', 'Admin\turnout\ECITurnoutController@update_turnout_update');
      ############################################# Script End #################################################

      //MISSED AC STARTS
      Route::get('/list-schedule/state/ac/missed', 'Admin\turnout\MissingTurnoutController@get_missed_ac');
      Route::get('/list-schedule/state/ac/missed/excel', 'Admin\turnout\MissingTurnoutController@export_excel_report_missed');
      Route::get('/list-schedule/state/ac/missed/pdf', 'Admin\turnoutMissingTurnoutController@export_pdf_report_missed');
      Route::get('/get-enable-eci-acs', 'Admin\turnout\MissingTurnoutController@get_enable_acs_for_update');
      Route::get('/get_missed', 'Admin\turnout\MissingTurnoutController@get_missed');
      Route::get('/get_missed/excel', 'Admin\turnout\MissingTurnoutController@export_excel_report_ac_missed');
      Route::get('/get_missed/pdf', 'Admin\turnout\MissingTurnoutController@export_pdf_report_ac_missed');
      //MISSED AC ENDS


      //Estimated Poll Day Turnout Details Starts
      Route::get('/estimate-poll-percent', 'Admin\turnout\PolldayTurnoutController@report_state');
      Route::get('/estimate-poll-percent/excel', 'Admin\turnout\PolldayTurnoutController@export_excel_report_state');
      Route::get('/estimate-poll-percent/pdf', 'Admin\turnout\PolldayTurnoutController@export_pdf_report_state');
      Route::get('/estimate-poll-percent/state/ac', 'Admin\turnout\PolldayTurnoutController@report_ac');
      Route::get('/estimate-poll-percent/state/ac/excel', 'Admin\turnout\PolldayTurnoutController@export_excel_report_ac');
      Route::get('/estimate-poll-percent/state/ac/pdf', 'Admin\turnout\PolldayTurnoutController@export_pdf_report_ac');
      Route::get('/estimate-poll-percent/state/ac/new', 'Admin\turnout\PolldayTurnoutController@report_ac_new');
      Route::get('/estimate-poll-percent/state/ac/new/excel', 'Admin\turnout\PolldayTurnoutController@export_excel_report_ac_new');
      Route::get('/estimate-poll-percent/state/ac/new/pdf', 'Admin\turnout\PolldayTurnoutController@export_pdf_report_ac_new');
      Route::get('/estimate-poll-percent/state/district', 'Admin\turnout\PolldayTurnoutController@report_district');
      Route::get('/estimate-poll-percent/state/district/excel', 'Admin\turnout\PolldayTurnoutController@export_excel_report_district');
      Route::get('/estimate-entry-logs', 'Admin\turnout\PolldayTurnoutController@estimateEntryLogs');
      Route::get('/estimate-entry-logs/excel', 'Admin\turnout\PolldayTurnoutController@export_excel_estimateEntryLogs');
      Route::get('/estimate-entry-logs/pdf', 'Admin\turnout\PolldayTurnoutController@export_pdf_estimateEntryLogs');
      Route::get('/voter-turnout-after-round-percentage-change', 'Admin\turnout\PolldayTurnoutController@voterTurnoutAfterRoundPercentageChangeReport');
      Route::get('/voter-turnout-after-round-percentage-change/excel', 'Admin\turnout\PolldayTurnoutController@voterTurnoutAfterRoundPercentageChangeReportExcel');
      Route::get('/voter-turnout-after-round-percentage-change/pdf', 'Admin\turnout\PolldayTurnoutController@voterTurnoutAfterRoundPercentageChangeReportPdf');

      //Estimated Poll Day Turnout Details Ends
      Route::get('/vt-report-with-old-ac-and-pc-vt-data', 'Admin\turnout\PolldayTurnoutController@voterTurnoutReportWithOldACPCData');
      Route::get('/vt-report-with-old-ac-and-pc-vt-data/excel', 'Admin\turnout\PolldayTurnoutController@voterTurnoutReportWithOldACPCDataExcel');
      Route::get('/vt-report-with-old-ac-and-pc-vt-data/pdf', 'Admin\turnout\PolldayTurnoutController@voterTurnoutReportWithOldACPCDataPDF');

      Route::get('/vt-end-of-poll-close-of-poll', 'Admin\turnout\PolldayTurnoutController@endOfPollCloseOfPollReport');
      Route::get('/vt-end-of-poll-close-of-poll/excel', 'Admin\turnout\PolldayTurnoutController@endOfPollCloseOfPollReportExcel');
      Route::get('/vt-end-of-poll-close-of-poll/pdf', 'Admin\turnout\PolldayTurnoutController@endOfPollCloseOfPollReportPDF');

      //END OF POLL STARTS
      Route::get('/end-of-poll', 'Admin\turnout\PolldayEndOfPollController@report_state');
      Route::get('/end-of-poll/excel', 'Admin\turnout\PolldayEndOfPollController@export_excel_report_state');
      Route::get('/end-of-poll/pdf', 'Admin\turnout\PolldayEndOfPollController@export_pdf_report_state');
      Route::get('/end-of-poll/state/ac', 'Admin\turnout\PolldayEndOfPollController@report_ac');
      Route::get('/end-of-poll/state/ac/excel', 'Admin\turnout\PolldayEndOfPollController@export_excel_report_ac');
      Route::get('/end-of-poll/state/ac/pdf', 'Admin\turnout\PolldayEndOfPollController@export_pdf_report_ac');

      Route::get('/end-of-poll-percent', 'Admin\turnout\EndOfPollPercentController@percent_state');
      Route::get('/end-of-poll-percent/excel', 'Admin\turnout\EndOfPollPercentController@export_excel_percent_state');
      Route::get('/end-of-poll-percent/pdf', 'Admin\turnout\EndOfPollPercentController@export_pdf_percent_state');
      Route::get('/end-of-poll-percent/state/ac', 'Admin\turnout\EndOfPollPercentController@percent_ac');
      Route::get('/end-of-poll-percent/state/ac/excel', 'Admin\turnout\EndOfPollPercentController@export_excel_percent_ac');
      Route::get('/end-of-poll-percent/state/ac/pdf', 'Admin\turnout\EndOfPollPercentController@export_pdf_percent_ac');
      //END OF POLL ENDS

      //ECI END OF POLL FINALSED STARTS
      Route::get('/EndOfPollFinalised/', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalised');
      Route::get('/EndOfPollFinalised/excel', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedExcel');
      Route::get('/EndOfPollFinalised/pdf', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedPdf');
      Route::get('/EndOfPollFinalisedList', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedList');
      Route::get('/EndOfPollFinalisedList/excel', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedListExcel');
      Route::get('/EndOfPollFinalisedList/pdf', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedListPdf');
      //ECI END OF POLL FINALSED ENDS

      Route::get('/EndOfPollDeFinalisedList', 'Admin\turnout\EndOfPollDeFinalisedController@EndOfPollDeFinalisedList');
      Route::get('/EndOfPollDeFinalisedList/finalized/{st_code}/{ac_no}', 'Admin\turnout\EndOfPollDeFinalisedController@EndOfPollDeFinalisedList');


      //ECI AC POLLING STATION STARTS
      Route::get('/get_ac_list/', 'Admin\PollingStation\EciPollingStationController@get_ac_list');
      Route::get('/EciPsWiseDetails/', 'Admin\PollingStation\EciPollingStationController@EciPsWiseDetails');
      Route::get('/EciPsWiseDetails/excel', 'Admin\PollingStation\EciPollingStationController@EciPsWiseDetailsExcel');
      Route::get('/EciPsWiseDetails/pdf', 'Admin\PollingStation\EciPollingStationController@EciPsWiseDetailsPdf');
      Route::get('/EnableClosePollEntry', 'Admin\PollingStation\EciPollingStationController@getEciAcsListForMissedEntry');
      Route::get('/AcECIPSElectoralDefinalzied', 'Admin\PollingStation\EciPollingStationController@AcECIPSElectoralDefinalzied');
      Route::post('/AcECIPSElectoralDefinalziedUpdate', 'Admin\PollingStation\EciPollingStationController@AcECIPSElectoralDefinalziedUpdate');
      Route::get('/fetchElectorsCountPanel', 'Admin\PollingStation\EciPollingStationController@fetchElectorsCountPanel');
      Route::get('/fetchACElectorsCountPanel', 'Admin\PollingStation\EciPollingStationController@fetchACElectorsCountPanel');
      Route::get('/fecthgetGenderWiseElectorsCountForPC', 'EronetController@fecthgetGenderWiseElectorsCountForPC');
      Route::get('/fecthgetGenderWiseElectorsCountForAC', 'EronetController@fecthgetGenderWiseElectorsCountForAC');
      Route::get('/EndOfPollFinalizeReport', 'Admin\PollingStation\EciPollingStationController@EndOfPollFinalizeReport');
      Route::get('/EndOfPollFinalizeReport/excel', 'Admin\PollingStation\EciPollingStationController@EndOfPollFinalizeReportExcel');

      //ECI AC POLLING STATION ENDS

      Route::get('/turnout-log/', 'Admin\turnout\TurnoutLogController@turnout_log');
      //ECI end-of-poll-comparision
      Route::get('/end-of-poll-comparision/state/ac', 'Admin\turnout\PolldayComparisionController@report_comparision');
      Route::get('/end-of-poll-comparision/state/ac/excel', 'Admin\turnout\PolldayComparisionController@export_excel_report_comparision');
      Route::get('/end-of-poll-comparision/state/ac/pdf', 'Admin\turnout\PolldayComparisionController@export_pdf_report_comparision');

      //ECI estimate-poll-percent-comparision
      Route::get('/estimate-poll-percent-comparision/state/ac', 'Admin\turnout\EstimateComparisionController@report_comparision');
      Route::get('/estimate-poll-percent-comparision/state/ac/excel', 'Admin\turnout\EstimateComparisionController@export_excel_report_comparision');
      Route::get('/estimate-poll-percent-comparision/state/ac/pdf', 'Admin\turnout\EstimateComparisionController@export_pdf_report_comparision');
      Route::get('/estimate-poll-percent-comparision/state/ac/pdf-color', 'Admin\turnout\EstimateComparisionController@export_pdf_report_comparision_color');

      //ECI Sent Sms
      Route::get('/send-sms', 'Admin\turnout\SmsSendController@index');
      Route::post('/send-sms', 'Admin\turnout\SmsSendController@send');

      // Update PS Data from Boothapp database
      Route::get('/update-ps-data-boothapp', 'Admin\turnout\PsUpdateController@updatepsdataboothapp');
    });
  });
  //ECI TURNOUT ROUTES ENDS


  //AC-CEO TURNOUT ROUTES STARTS
  Route::group(['prefix' => 'acceo', 'as' => 'acceo::', 'middleware' => ['auth:admin', 'auth']], function () {

    //ACCEO ELECTION SCHEDULE 
    Route::get('/ElectionScheduleState', 'Admin\ElectionScheduleController@ElectionScheduleState');
    //ACCEO ELECTION SCHEDULE EXCEL REPORT
    Route::get('/ElectionScheduleState/excel', 'Admin\ElectionScheduleController@ElectionScheduleStateExcel');
    //ACCEO ELECTION SCHEDULE PDF REPORT
    Route::get('/ElectionScheduleState/pdf', 'Admin\ElectionScheduleController@ElectionScheduleStatePdf');

    Route::get('/ElectionScheduleState/state', 'Admin\ElectionScheduleController@ElectionScheduleAc');
    Route::get('/ElectionScheduleState/state/excel', 'Admin\ElectionScheduleController@ElectionScheduleAcExcel');
    Route::get('/ElectionScheduleState/state/pdf', 'Admin\ElectionScheduleController@ElectionScheduleAcPdf');

    Route::group(['prefix' => 'turnout', 'as' => 'acceo::', 'middleware' => ['auth:admin', 'auth']], function () {

      Route::get('/AcCeoAcElectoralReport', 'Admin\PCCeoReportNewController@AcCeoAcElectoralReport');
      Route::get('/AcCeoPSElectoralDefinalzied', 'Admin\PCCeoReportNewController@AcCeoPSElectoralDefinalzied');
      Route::post('/AcCeoPSElectoralDefinalziedUpdate', 'Admin\PCCeoReportNewController@AcCeoPSElectoralDefinalziedUpdate');
      Route::get('/AcCeoMissedAc', 'Admin\PCCeoReportNewController@AcCeoMissedAc');
      Route::post('/enable-missed-acs', 'Admin\PCCeoReportNewController@enableAcs');
      Route::get('/ACeoMissedAcExcel', 'Admin\PCCeoReportNewController@AcCeoMissedAcExcel');
      Route::get('/AcCeoMissedAcPdf', 'Admin\PCCeoReportNewController@AcCeoMissedAcPdf');
      Route::get('/enable-acs-for-missed-and-modification', 'Admin\PCCeoReportNewController@getAcsListForMissedEntry');
      Route::post('/enable-modification-acs', 'Admin\turnout\MissingTurnoutController@enbale_modified_acs');
      Route::get('/ExemptACWithNoPollingPS', 'Admin\PCCeoReportNewController@ExemptACWithNoPollingPS');
      Route::post('/ExemptACWithNoPollingPS', 'Admin\PCCeoReportNewController@AddExemptACWithNoPollingPS');
      Route::post('/ExemptACWithNoPollingPSRemove', 'Admin\PCCeoReportNewController@RemoveExemptACWithNoPollingPS');

      //Estimate Poll Percentage Starts
      Route::get('/estimate-poll-percent', 'Admin\turnout\PolldayTurnoutController@report_state');
      Route::get('/estimate-poll-percent/excel', 'Admin\turnout\PolldayTurnoutController@export_excel_report_state');
      Route::get('/estimate-poll-percent/pdf', 'Admin\turnout\PolldayTurnoutController@export_pdf_report_state');
      Route::get('/estimate-poll-percent/state/ac', 'Admin\turnout\PolldayTurnoutController@report_ac');
      Route::get('/estimate-poll-percent/state/ac/excel', 'Admin\turnout\PolldayTurnoutController@export_excel_report_ac');
      Route::get('/estimate-poll-percent/state/ac/pdf', 'Admin\turnout\PolldayTurnoutController@export_pdf_report_ac');
      Route::get('/estimate-poll-percent/state/district', 'Admin\turnout\PolldayTurnoutController@report_district');
      Route::get('/estimate-poll-percent/state/district/excel', 'Admin\turnout\PolldayTurnoutController@export_excel_report_district');
      //Estimate Poll Percentage Ends

      Route::get('/AcCeoEndOfPoll/', 'Admin\turnout\AcCeoEndOfPollController@AcCeoEndOfPoll');
      Route::get('/AcCeoEndOfPollExcel/', 'Admin\turnout\AcCeoEndOfPollController@AcCeoEndOfPollExcel');
      Route::get('/AcCeoEndOfPollPdf/', 'Admin\turnout\AcCeoEndOfPollController@AcCeoEndOfPollPdf');
      Route::get('/AcCeoEndOfPollAc/', 'Admin\turnout\AcCeoEndOfPollController@AcCeoEndOfPollAc');
      Route::get('/AcCeoEndOfPollAcExcel/', 'Admin\turnout\AcCeoEndOfPollController@AcCeoEndOfPollAcExcel');
      Route::get('/AcCeoEndOfPollAcPdf/', 'Admin\turnout\AcCeoEndOfPollController@AcCeoEndOfPollAcPdf');
      //END OF POLL ENDS

      //ECI END OF POLL FINALSED STARTS
      //Route::get('EndOfPollFinalised', function(){dd('imher');});
      Route::get('/EndOfPollFinalised/', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalised');
      Route::get('/EndOfPollFinalised/excel', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedExcel');
      Route::get('/EndOfPollFinalised/pdf', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedPdf');
      Route::get('/EndOfPollFinalisedList', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedList');
      Route::get('/EndOfPollFinalisedList/excel', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedListExcel');
      Route::get('/EndOfPollFinalisedList/pdf', 'Admin\turnout\EndOfPollFinalisedController@EndOfPollFinalisedListPdf');
      //ECI END OF POLL FINALSED ENDS


      //ACCEO AC POLLING STATION STARTS
      Route::get('/CeoPsWiseDetails/', 'Admin\PollingStation\CeoPollingStationController@CeoPsWiseDetails');
      Route::get('/CeoPsWiseDetails/excel', 'Admin\PollingStation\CeoPollingStationController@CeoPsWiseDetailsExcel');
      Route::get('/CeoPsWiseDetails/pdf', 'Admin\PollingStation\CeoPollingStationController@CeoPsWiseDetailsPdf');
      Route::post('/CeoPsDefinalizeUpdate/', 'Admin\PollingStation\CeoPollingStationController@CeoPsDefinalizeUpdate');
      Route::post('/CeoPsFinalizeUpdate/', 'Admin\PollingStation\CeoPollingStationController@CeoPsFinalizeUpdate');

      Route::get('/PsWiseElectoralDetails/', 'Admin\PollingStation\CeoPollingStationController@getCeoPsWiseElectoralDetails');
      Route::get('/PsWiseElectoralDetails/excel', 'Admin\PollingStation\CeoPollingStationController@getCeoPsWiseElectoralDetailsExcel');
      Route::get('/PsWiseElectoralDetails/pdf', 'Admin\PollingStation\CeoPollingStationController@getCeoPsWiseElectoralDetailsPdf');

      //ACCEO AC POLLING STATION ENDS
      Route::post('/publish-turnout/', 'Admin\PollingStation\CeoPollingStationController@finalize_turnout');
      Route::post('/publish-all-turnout/', 'Admin\PollingStation\CeoPollingStationController@finalize_all_turnout');
      Route::get('/turnout-publish-status-list/', 'Admin\PollingStation\CeoPollingStationController@getAcFinalizeList');
    });
  });
  //AC-CEO TURNOUT ROUTES ENDS

  // AC-DEO TURNOUT ROUTES
  Route::group(['prefix' => 'acdeo', 'as' => 'acdeo::', 'middleware' => ['auth:admin', 'auth']], function () {
    Route::group(['prefix' => 'turnout', 'as' => 'acdeo::', 'middleware' => ['auth:admin', 'auth', 'deo']], function () {
      Route::get('/estimate-poll-percent/state/ac', 'Admin\turnout\Deo\PolldayTurnoutController@report_ac');
      Route::get('/estimate-poll-percent/state/ac/excel', 'Admin\turnout\Deo\PolldayTurnoutController@export_excel_report_ac');
      Route::get('/estimate-poll-percent/state/ac/pdf', 'Admin\turnout\Deo\PolldayTurnoutController@export_pdf_report_ac');

      Route::get('/AcDeoEndOfPollAc', 'Admin\turnout\Deo\AcCeoEndOfPollController@AcCeoEndofPollAc');
      Route::get('/AcDeoEndOfPollAcExcel', 'Admin\turnout\Deo\AcCeoEndOfPollController@export_excel_report_ac');
      Route::get('/AcDeoEndOfPollAcPdf', 'Admin\turnout\Deo\AcCeoEndOfPollController@AcDeoEndOfPollAcPdf');
      Route::post('/estimated-turnout-change', 'Admin\turnout\Deo\PolldayTurnoutController@estimated_turnout_change');

      Route::get('/DeoPsWiseDetails/', 'Admin\PollingStation\DeoPollingStationController@DeoPsWiseDetails');
      Route::get('/DeoPsWiseDetails/excel', 'Admin\PollingStation\DeoPollingStationController@DeoPsWiseDetailsExcel');
      Route::get('/DeoPsWiseDetails/pdf', 'Admin\PollingStation\DeoPollingStationController@DeoPsWiseDetailsPdf');
      Route::post('/DeoPsWiseDetailsUpdate/', 'Admin\PollingStation\DeoPollingStationController@DeoPsWiseDetailsUpdate');
      Route::post('/DeoPsFinalizeUpdate/', 'Admin\PollingStation\DeoPollingStationController@DeoPsFinalizeUpdate');
      Route::post('/DeoPsDefinalizeUpdate/', 'Admin\PollingStation\DeoPollingStationController@DeoPsDefinalizeUpdate');
    });
  });


  //ROAC TURNOUT ROUTES STARTS SACHCHIDA
  Route::group(['prefix' => 'roac', 'as' => 'roac::', 'middleware' => ['auth:admin', 'auth']], function () {

    Route::group(['prefix' => 'turnout', 'as' => 'roac::', 'middleware' => ['auth:admin', 'auth', 'ro']], function () {


      Route::get('/estimate-turnout-entry', 'Admin\turnout\TurnoutController@estimate_turnout_entry');
      Route::post('/estimated-entry', 'Admin\turnout\TurnoutController@estimated_entry');
      Route::get('/ElectorsDetails', 'Admin\turnout\ElectorsDetailsController@ElectorsDetails');
      Route::post('/ElectorsDetailsUpdate', 'Admin\turnout\ElectorsDetailsController@ElectorsDetailsUpdate');
      Route::get('/polling-station-electors-details', 'Admin\turnout\ElectorsDetailsController@PollingStationElectorsDetails');
      Route::post('/polling-station-electors-details-update', 'Admin\turnout\ElectorsDetailsController@PollingStationElectorsDetailsUpdate');
      Route::post('/polling-station-electors-details-finalized', 'Admin\turnout\ElectorsDetailsController@PollingStationElectorsFinalized');
      Route::post('/polling-station-electors-details-export', 'Admin\turnout\ElectorsDetailsController@PollingStationElectorsDetailsExport');
      Route::post('/polling-station-import', 'Admin\turnout\ElectorsDetailsController@PollingStationImport');

      //ROAC AC POLLING STATION STARTS
      Route::get('/RoPsWiseDetails/', 'Admin\PollingStation\RoPollingStationController@RoPsWiseDetails');
      Route::get('/RoPsWiseDetails/excel', 'Admin\PollingStation\RoPollingStationController@RoPsWiseDetailsExcel');
      Route::get('/RoPsWiseDetails/pdf', 'Admin\PollingStation\RoPollingStationController@RoPsWiseDetailsPdf');
      Route::post('/RoPsWiseDetailsUpdate/', 'Admin\PollingStation\RoPollingStationController@RoPsWiseDetailsUpdate');
      Route::post('/RoPsFinalizeUpdate/', 'Admin\PollingStation\RoPollingStationController@RoPsFinalizeUpdate');
    });
  });
  //ROAC TURNOUT ROUTES ENDS
});
