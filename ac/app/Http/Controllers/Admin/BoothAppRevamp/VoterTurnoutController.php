<?php 
namespace App\Http\Controllers\Admin\BoothAppRevamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use App\models\Admin\BoothAppRevamp\{TblPollSummaryModel};
use App\Classes\xssClean;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

//current
class VoterTurnoutController extends Controller {

  public $folder          = 'booth-app-revamp';
  public $view            = "admin.booth-app-revamp";
  public $action          = "booth-app-revamp";

  public function update_voter_turnout(Request $request){

    $date = date('Y-m-d');
    $hour = date('H');
    $results = TblPollSummaryModel::get_voters_for_suvidha_update();
    $i = 0;
    $data_sync = [];
    foreach($results as $iterate_res){
    if(in_array($iterate_res['ac_no'],[10,15,22,27,36,40,41,46,55,64,64])){
      $data_sync[] = $iterate_res;
    }
    }
 


    foreach($data_sync as $iterate_res){

      $connection = DB::table("pd_scheduledetail");
    
      $pd_scheduledetails = $connection->select("electors_total")->where('st_code',$iterate_res['st_code'])->where('ac_no',$iterate_res['ac_no'])->first();

        $round = '';
        if($hour<=9){
          $round = 1;
        }
        if($hour<=11 && $hour>9){
          $round = 2;
        }
        if($hour<=13 && $hour>11){
          $round = 3;
        }
        if($hour<=15 && $hour>13){
          $round = 4;
        }
        if($hour<=17 && $hour>15){
          $round = 5;
        }





          $percentage = 0;
          if($pd_scheduledetails->electors_total > 0 && $pd_scheduledetails->electors_total > $iterate_res['voter']){
            $percentage = round(($iterate_res['voter']/$pd_scheduledetails->electors_total)*100,2);
          }

          if($percentage>0){
          $connection->where('st_code',$iterate_res['st_code'])->where('ac_no',$iterate_res['ac_no'])->update([
            'close_of_poll' => $percentage,
            'est_turnout_total' => $percentage,
            'est_voters' => $iterate_res['voter']
          ]);

          echo $iterate_res['voter'].' / '.$pd_scheduledetails->electors_total."<br>";

      if($percentage>0){
        echo $i.' - '.$iterate_res['ac_no']."<br>";
    }else{
        echo $i.' - '.$iterate_res['ac_no']." No Electors or Voters<br>";
      }
      $i++;
    }
    


   
    //     if($round!=''){
    //       $percentage = 0;
    //       if($pd_scheduledetails->electors_total > 0 && $pd_scheduledetails->electors_total > $iterate_res['voter']){
    //         $percentage = round(($iterate_res['voter']/$pd_scheduledetails->electors_total)*100,2);
    //       }

    //       $connection->where('st_code',$iterate_res['st_code'])->where('ac_no',$iterate_res['ac_no'])->update([
    //         'est_turnout_round'.$round => $percentage,
    //         'est_turnout_total' => $percentage,
    //         'est_voters' => $iterate_res['voter']
    //       ]);

    //       echo $iterate_res['voter'].' / '.$pd_scheduledetails->electors_total."<br>";

    //   if($percentage>0){
    //     echo $i.' - '.$iterate_res['ac_no']."<br>";
    // }else{
    //     echo $i.' - '.$iterate_res['ac_no']." No Electors or Voters<br>";
    //   }
    //   $i++;
    //     }




    echo $i."<br>";
    
    }
  }

}  // end class