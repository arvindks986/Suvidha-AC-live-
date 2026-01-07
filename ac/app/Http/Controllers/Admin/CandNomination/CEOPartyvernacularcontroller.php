<?php  
		namespace App\Http\Controllers\Admin\CandNomination;
		use Illuminate\Http\Request;
		use App\Http\Controllers\Controller;
		use Session;
		 
		use Illuminate\Support\Facades\Auth;
		use Illuminate\Support\Facades\Input;
		use Illuminate\Support\Facades\Redirect;
		use Carbon\Carbon;
		use DB;
		use Illuminate\Support\Facades\Hash;
		use Validator;
		use Config;
		use \PDF;

		use MPDF;
		use App\commonModel;
		use App\models\Admin\StatepartyModel;
    use App\models\Admin\StateSymbolModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		use Illuminate\Support\Facades\Crypt;

 
class CEOPartyvernacularcontroller extends Controller
{
    //
    public $base    = 'acceo';
    public $folder  = 'CandNomination';
    public $action    = 'acceo/';
    public $view_path = "admin.ac.ceo.candform";
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->commonModel = new commonModel();
            $this->xssClean = new xssClean;
            $this->stateparty = new StatepartyModel;  
            $this->statesymbol = new StateSymbolModel;
			$this->sym = new SymbolMaster();
			if(!Auth::check()){ 
        		return redirect('/officer-login');
        	}
        $this->middleware('ceo');
        $this->middleware('clean_url');
			 }

    
    public function state_party_list(request $request)
	    {      
	      $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
        $st_code=$d->st_code;
        $this->stateparty->insert_party_record($st_code);
	      $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $record=$this->stateparty->getallpartybystate($st_code); 

                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['record']=$record;
                    $data['st_name']=$st->ST_NAME;
                    $data['state_language']=$state_language;  
                
                return view($this->view_path.'.state-party-list', $data); 
       
	    }
     
    function state_party_vernacular(Request $request)
      {    
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $party_vname = $this->xssClean->clean_input(Check_Input($request->input('party_vname')));
            if(empty($party_vname)) {
             \Session::flash('error_messsage', 'enter Party Name vernacular');
                 return Redirect::back()->withInput($request->all());  
              }
              $id = $this->xssClean->clean_input(Check_Input($request->input('id')));
              $st_code =$this->xssClean->clean_input(Check_Input($request->input('st_code')));
               
          
            $up_data = array('party_vname'=>$party_vname,
                              'updated_by'=> $d->officername,
                              'updated_at'=>date("Y-m-d H:i:s"));
             $rec=updatedata('m_state_party','id',$id,$up_data);
             

             \Session::flash('success_mes', 'Update Party Name vernacular Successfully');
              return Redirect::to('/acceo/state-party-list');
             
      }


  public function symbol_list(request $request)
      {      
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
        $st_code=$d->st_code;
        $this->statesymbol->insert_symbol_record($st_code);
        $st=getstatebystatecode($st_code);
        $state_language=getstatelanguage($st_code);
        $record=$this->statesymbol->getallsymbolbystate($st_code); 

                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['record']=$record;
                    $data['st_name']=$st->ST_NAME;
                    $data['state_language']=$state_language;  
                 
                return view($this->view_path.'.state-symbol-list', $data); 
       
      }
     
    function symbol_list_vernacular(Request $request)
      {    
            $user = Auth::user();
            $d=$this->commonModel->getunewserbyuserid($user->id);
            $symbol_vname = $this->xssClean->clean_input(Check_Input($request->input('symbol_vname')));
            if(empty($symbol_vname)) {
             \Session::flash('error_messsage', 'enter Symbol Name vernacular');
                 return Redirect::back()->withInput($request->all());  
              }
              $id = $this->xssClean->clean_input(Check_Input($request->input('id')));
              $st_code =$this->xssClean->clean_input(Check_Input($request->input('st_code')));
               
          
            $up_data = array('symbol_vname'=>$symbol_vname,
                              'updated_by'=> $d->officername,
                              'updated_at'=>date("Y-m-d H:i:s"));
             $rec=updatedata('m_state_symbol','id',$id,$up_data);
             

             \Session::flash('success_mes', 'Update Sumbol Name vernacular Successfully');
              return Redirect::to('/acceo/symbol-list');
             
      }
    
}      
