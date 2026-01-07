<?php namespace App\Http\Controllers\IndexCardReportsAC\DeFinalizeIndexCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB, Validator, Session, Redirect;
use App\models\Admin\AcModel;
use App\models\Admin\{ElectionModel, StateModel};
use App\models\Admin\IndexCardUploadModel;
use App\Classes\xssClean;
use App\models\Admin\IndexCardDeFinalizeModel;
use App\models\Admin\IndexCardFinalize;
use App\models\Admin\IndexcardLogModel;
use App\commonModel;
use PDF;
use Excel;

class DeFinalizeLogController extends Controller {

  public $base          = '';
  public $folder        = '';
  public $action        = '';
  public $current_page  = '';
  public $ac_no         = 0;
  public $st_code       = 0;
  public $view_path     = "IndexCardReports.DeFinalizeIndexCard";
  public $definalize_access = false;

  public function __construct(){
    $role_id = 0;
    $this->xssClean = new xssClean;
	$this->commonModel = new commonModel();
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        $role_id = Auth::user()->role_id;
        if($role_id == '27'){
          $this->base         = 'eci-index';
          $this->action       = 'eci-index/de-finalize-log/post';
          $this->current_page = 'eci-index/de-finalize-log';
          $this->view_path    = '';
        } else if($role_id == '7'){
          $this->base         = 'eci';
          $this->action       = 'eci/de-finalize-log/post';
          $this->current_page = 'eci/de-finalize-log';
          $this->view_path    = '';
        }

        if(in_array($role_id,['7','27'])){
          $this->definalize_access = true;
        }

        return $next($request);
    });
  }


  
  public function deFinalizeLogs(Request $request){
	  
	  
	  $data['ac_no']          = NULL;
      $data['st_code']        = NULL;
      $data['candidate_id']    = NULL;
	  
      if($request->has('candidate_id')){
        $data['candidate_id']       = $request->candidate_id;
      }

      if($request->has('ac_no')){
        $data['ac_no']       = $request->ac_no;
      } 

      if($request->has('st_code')){
        $data['st_code']       = $request->st_code;
      }
	  
	  
		$date = DB::table('winning_leading_candidate')->select('result_declared_date')
					->orderBy('result_declared_date', 'DESC')->first();
			
		$indexdate = date('Y-m-d H:i:s', strtotime($date->result_declared_date . ' +1 day'));
			  
		//try{
			$sql = DB::table('candidate_logs as cl')
					->select('st_name','cl.ac_no','ac_name','cand_name','cand_gender','cand_age','cand_category','PARTYNAME as party_name','SYMBOL_DES as symbol_name','log_updated_at','log_updated_by')
					->join('m_state as mst','mst.st_code','cl.st_code')
					->join('m_ac as mac',[['mac.st_code','cl.st_code'],['mac.ac_no','cl.ac_no']])
					->join('m_party as mp','mp.CCODE','cl.party_id')
					->join('m_symbol as ms','ms.SYMBOL_NO','cl.symbol_id');
								
					if(!empty($data['st_code']) && isset($data['st_code'])){
						$sql->where('mst.st_code',$data['st_code']);
					}
					if(!empty($data['ac_no']) && isset($data['ac_no'])){
						$sql->where('mac.ac_no',$data['ac_no']);
					}
					if(!empty($data['candidate_id']) && isset($data['candidate_id'])){
						$sql->where('cl.candidate_id',$data['candidate_id']);
					}
					
					$sql->whereDate('log_updated_at', '>=', $indexdate);
								
			

			//echo '<pre>'; print_r($sql); die;

			
			$data['results'] = 	$sql->get();;				
			$data['states'] = DB::table('candidate_logs as cl')
								->select('mst.st_code','st_name')
								->join('m_state as mst','mst.st_code','cl.st_code')
								->whereDate('log_updated_at', '>=', $indexdate)
								->groupBy('mst.st_code')
								->get();


			$data['acs'] = DB::table('candidate_logs as cl')
								->select('cl.ac_no','ac_name')
								->join('m_ac as mac',[['mac.st_code','cl.st_code'],['mac.ac_no','cl.ac_no']])
								->whereDate('log_updated_at', '>=', $indexdate)
								->groupBy('cl.ac_no')
								->get();

			$data['candidate'] = DB::table('candidate_logs as cl')
								->select('cl.candidate_id','cand_name')
								->join('m_ac as mac',[['mac.st_code','cl.st_code'],['mac.ac_no','cl.ac_no']])
								->whereDate('log_updated_at', '>=', $indexdate)
								->groupBy('cl.candidate_id')
								->get();
								
			
			$data['user_data'] 		= \Auth::user();						
			$data['action']         = url($this->action);
			$data['current_page']   = url($this->current_page);
						
			if($request->path() == "eci/de-finalize-log/pdf"){
				
			$pdf = PDF::loadView('IndexCardReports.DeFinalizeIndexCard.logs.DeFinalizeLogPdf', $data);
							
				return $pdf->download('IndexCard De-Finalize Log Report.pdf');
			
			}else if($request->path() == "eci/de-finalize-log/excel"){
						
			return Excel::create('IndexCard De-Finalize Log Report', function($excel) use ($data) {
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
	  	  
					$sheet->mergeCells('A1:K1');
	  
					$sheet->cell('A1', function($cells) {
						$cells->setValue('IndexCard De-Finalize Log Report');
						$cells->setFont(array('name' => 'Times New Roman','size' => 15,'bold' => true));
                        $cells->setAlignment('center');
					});
					

			
					$sheet->cell('A3', function($cells) {
						$cells->setValue('Sl No.');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
		
					$sheet->cell('B3', function($cells) {
						$cells->setValue('State Name');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$sheet->cell('C3', function($cells) {
						$cells->setValue(' AC No - AC Name ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$sheet->cell('D3', function($cells) {
						$cells->setValue(' Candidate Name ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$sheet->cell('E3', function($cells) {
						$cells->setValue(' Gender ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$sheet->cell('F3', function($cells) {
						$cells->setValue(' Age ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$sheet->cell('G3', function($cells) {
						$cells->setValue(' Category ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$sheet->cell('H3', function($cells) {
						$cells->setValue(' Party Name ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$sheet->cell('I3', function($cells) {
						$cells->setValue(' Symbol ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$sheet->cell('J3', function($cells) {
						$cells->setValue(' Updated By ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$sheet->cell('K3', function($cells) {
						$cells->setValue(' Updated At ');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$i= 4;
									
					if (!empty($data)) {
							
						foreach ($data['results'] as $key => $result){
														
							$sheet->cell('A'.$i, $key+1); 
							$sheet->cell('B'.$i, $result->st_name ); 
							$sheet->cell('C'.$i, $result->ac_no.' - '.$result->ac_name );
							$sheet->cell('D'.$i, $result->cand_name );
							$sheet->cell('E'.$i, ucfirst($result->cand_gender) );
							$sheet->cell('F'.$i, $result->cand_age );
							$sheet->cell('G'.$i, strtoupper($result->cand_category) );
							$sheet->cell('H'.$i, $result->party_name );
							$sheet->cell('I'.$i, $result->symbol_name );
							$sheet->cell('J'.$i, $result->log_updated_by );
							$sheet->cell('K'.$i, date('d-m-Y h:i A', strtotime($result->log_updated_at)) );
							
							$i++;						
						}
					}
					
					$i++;

		
				});
			})->download('xls');
			
			
			
		}else{
			return view('IndexCardReports.DeFinalizeIndexCard.logs.DeFinalizeLog', $data);
		}	
			
		
		
        /* }catch(\Exception $e){
        return Redirect::back();
      }  */
  }
  
}  // end class