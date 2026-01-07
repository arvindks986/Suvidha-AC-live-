<?php namespace App\Http\Controllers\Admin\Nfd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use DB, Validator, Config, Session, Common, Response;
use \PDF, Auth;
use App\Http\Controllers\Candidate\CommonController;
use App\Helpers\SmsgatewayHelper;
use App\models\Admin\{OfficerModel};
use App\Classes\xssClean;


class NfdAccount extends Controller {

  public $folder        = "nfd";
  public $view          = "admin.nfd";
  public $action        = "nfd";
  public $ac_no         = NULL;
  public $dist_no       = NULL;
  public $st_code       = NULL;
  public $role_id       = 0;
  public $base          = '';
  public $allowed_nfd   = 3;

  public function __construct(){
    $this->xssClean = new xssClean;
    $this->middleware(function ($request, $next) {
      $request_filter   = Common::get_request_filter($request);
      $this->ac_no      = $request_filter['ac_no'];
      $this->st_code    = $request_filter['st_code'];
      $this->dist_no    = $request_filter['dist_no'];
      $this->role_id    = $request_filter['role_id'];
      return $next($request);
    });
  }

  public function list(Request $request){
    $data                   = [];
    $data['heading_title']  = __('message.list_of_nfd');
    $data['results']        = [];
    $data['buttons']        = [];
    $data['buttons'][]      = [
      'href' => Common::generate_url('nfd/account/new'),
      'name' => __('message.add_new_nfd'),
      'target' => false,
    ];
    $results = OfficerModel::get_users([
      'role_id' => [42],
      'st_code' => $this->st_code,
      'dist_no' => $this->dist_no
    ]);
    foreach ($results as $iterate_result) {
      $data['results'][] = [
        'name'    => $iterate_result['name'],
        'mobile'  => $iterate_result['Phone_no'],
        'address' => $iterate_result['ro_address_l1'],
        'href_edit' => Common::generate_url('nfd/account/'.encrypt_string($iterate_result['id'])),
      ];
    }

    $data['user_data']  = Auth::user();
    return view('admin.nfd.account.nfd_list', $data);
  }

  public function add($id, Request $request){
    $data                   = [];
    $data['heading_title']  = __('message.add_new_nfd');
    $data['action'] = Common::generate_url("nfd/account");
    $data['buttons']        = [];
    $data['buttons'][]      = [
      'href' => Common::generate_url('nfd/account'),
      'name' => __('message.list_of_nfd'),
      'target' => false,
    ];

    if($id == 'new'){

    }else{
      try{
        //check if a id is valid crypt else go to catch
        $is_valid_id        = decrypt_string($id);
        $data['encrpt_id']  = $id;
        $id                 = $is_valid_id;
        $object             = OfficerModel::find($is_valid_id);
        if($object){
          $object = $object->toArray();
        }
      }catch(\Exception $e){
        return Redirect::to(Common::generate_url("nfd/account"));
      }
    }

    if($request->old('name')){
      $data['name']  = $request->old('name');
    }else if(isset($object) && $object){
      $data['name']  = $object['name'];
    }else{
      $data['name']  = ''; 
    }

    if($request->old('mobile')){
      $data['mobile']  = $request->old('mobile');
    }else if(isset($object) && $object){
      $data['mobile']  = $object['Phone_no'];
    }else{
      $data['mobile']  = ''; 
    }

    if($request->old('address')){
      $data['address']  = $request->old('address');
    }else if(isset($object) && $object){
      $data['address']  = $object['ro_address_l1'];
    }else{
      $data['address']  = ''; 
    }

    if($request->old('lat')){
      $data['lat']  = $request->old('lat');
    }else if(isset($object) && $object){
      $data['lat']  = $object['lat'];
    }else{
      $data['lat']  = ''; 
    }

    if($request->old('lng')){
      $data['lng']  = $request->old('lng');
    }else if(isset($object) && $object){
      $data['lng']  = $object['lng'];
    }else{
      $data['lng']  = ''; 
    }
    //dd($data);
    $data['user_data']      = Auth::user();
    return view('admin.nfd.account.nfd_form', $data);
  }

  public function post_nfd(Request $request){

    $filter = [
      'st_code' => $this->st_code,
      'dist_no' => $this->dist_no,
      'ac_no'   => $this->ac_no,
    ];
    $data   = [];

    $rules    = [
      'name'    => 'required',
      'pin'     => 'required|pin',
      // 'lat'     => 'required',
      // 'lng'     => 'required',
      'address' => 'required',
      'password'              => 'required|confirmed|cpassword',
      'password_confirmation' => 'required|cpassword'
    ];

    $request->merge([
      'lat' => '',
      'lng' => '',
    ]);

    if($request->has('id')){
      try{
        $id        = decrypt_string($request->id);
      }catch(\Exception $e){
        return Redirect::back();
      }

      $rules['mobile']  = 'required|mobile|unique:officer_login,officername,'.$id;
    }else{
      $rules['mobile']  = 'required|mobile|unique:officer_login,officername';
    }

    $messages = [
        'mobile'            => 'Please enter valid mobile number',
        'mobile.exists'     => 'Mobile does not exists in our database.',
        'confirmed'         =>  "The password and confirm password are not matching",
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails())
    {
      return Redirect::back()->withInput($request->all())->withErrors($validator);
    }

    $total_nfc = OfficerModel::count_nfd($filter);
    if($total_nfc >= $this->allowed_nfd){
      Session::flash('status',0);
      Session::flash('flash-message',"You can only add ".$this->allowed_nfd." NFD.");
      return Redirect::back();
    }

    
      $result   = OfficerModel::add_nfd(array_merge($request->all(),$filter));
    try{ }catch(\Exception $e){
      Session::flash('status',0);
      Session::flash('flash-message',"Please Try Again.");
      return Redirect::back();
    }
    Session::flash('status',1);
    Session::flash('flash-message',sprintf(__('message.success'),'NFD'));
    return Redirect::to(Common::generate_url("nfd/account"));
  }

}