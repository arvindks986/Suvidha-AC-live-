<?php 
namespace App\Http\Controllers\Admin\mparty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use MPDF;
use App\commonModel;
use App\Helpers\SmsgatewayHelper;
use App\Classes\xssClean;
use DB, Validator, Config, \PDF, Response;
 
use App\models\Common\{StateModel, AcModel, DistrictModel,  ElectionModel};
use App\models\Admin\mparty\{MPartyModel,MpartyLogModel,SymbolModel};  
   
class DashboardController extends Controller
{
    public $upload_folder   = 'uploads1';
    public $base            = '/mparty';
    public $folder  		= 'mparty';
    public $action    		= 'mparty/';
    public $view_path 		= "admin.mparty";

    public function __construct()
    {   
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('mparty');
        $this->commonModel = new commonModel();
        
        $this->xssClean = new xssClean;
        if(!Auth::check()){ 
            return redirect('/officer-login');
        }
    }

   public function index(Request $request){
      $data = [];
      $data['user_data'] = Auth::user();
      $data['totalparties']=MPartyModel::countpartiesbytype();
      $data['national']=MPartyModel::countpartiesbytype('N');
      $data['state']=MPartyModel::countpartiesbytype('S');
      $data['unreconized']=MPartyModel::countpartiesbytype('U');
      
      $filter='';
      	$filter = [
		        'freesymbol' =>'',
		        'symbol_img' =>'', 
		        ];
		$lists=SymbolModel::get_allsymbol($filter);
		$data['totalsymbol']=count($lists);
		$filter = [
		        'freesymbol' =>'PARTY',
		        'symbol_img' =>'', 
		        ];
		$lists=SymbolModel::symbolallotedtoparty($filter);
		$data['allotedtoparties']=SymbolModel::symbolallotedtoparty();
      
		$filter='';
		$filter = [   
		  'symbol_img' =>'',
		  'freesymbol'=>'T',
		  ];
		//$lists=SymbolModel::get_allfreesymbol($filter);
		$data['freesymbol']=SymbolModel::countfreesymbol($filter);//count($lists);
		$filter = [   
		  'symbol_img' =>'',
		  'freesymbol'=>'F',
		  ];
		//$lists=SymbolModel::get_allfreesymbol($filter);
		$data['reservesymbol']=SymbolModel::countfreesymbol($filter);
      //dd($data);
      return view($this->view_path.'.dashboard', $data);
   }
}