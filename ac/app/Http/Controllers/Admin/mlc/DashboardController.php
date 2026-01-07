<?php 
namespace App\Http\Controllers\Admin\mlc;
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
 
use App\models\Common\{StateModel, FileModel, PcModel, AcModel, DistrictModel, PartyModel, SymbolModel, ElectionModel};
 
   
class DashboardController extends Controller
{
    public $upload_folder   = 'uploads1';
    public $base            = '/mlc';
    public $folder  		= 'mlc';
    public $action    		= 'mlc/';
    public $view_path 		= "admin.mlc";

    public function __construct()
    {   
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('mlc');
        $this->commonModel = new commonModel();
        
        $this->xssClean = new xssClean;
        if(!Auth::check()){ 
            return redirect('/officer-login');
        }
    }

   public function index(Request $request){
      $data = [];
      $data['user_data'] = Auth::user();
     // dd($data);
      return view($this->view_path.'.dashboard', $data);
   }
}