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
		use App\models\Admin\Form7AdetilsModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		use Illuminate\Support\Facades\Crypt;

 
class Form7AController extends Controller
{
    //
    public $base    = 'roac';
    public $folder  = 'CandNomination';
    public $action    = 'roac/';
    public $view_path = "admin.candform";
   public function __construct()
        {   
			$this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			 $this->commonModel = new commonModel();
            $this->xssClean = new xssClean;
            $this->formmodel = new Form7AdetilsModel;
			$this->sym = new SymbolMaster();
			if(!Auth::check()){ 
        		return redirect('/officer-login');
        	}
        	$user = Auth::user();
            switch ($user->role_id) {
                case '5':
                    $this->middleware('deo');
                    break;
                case '4':
                    $this->middleware('ceo');
                    break;
                case '19':
                    $this->middleware('ro');
                    break;
                default:
                    $this->middleware('eci');
            }
			 
			$this->middleware('clean_url');
			 
			
		 }

    
    public function update_form7A_details(request $request)
	    {      
	      
		    $user = Auth::user();
		    $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
            $ac=getacname($d->st_code,$d->ac_no);
            
            $st=getstatebystatecode($ele_details->ST_CODE);
            $sech=getschedulebyid($ele_details->ScheduleID);
            $ndate=$sech['LDT_WD_CAN'];

            $year=date("Y", strtotime($ndate));
            $filter = [
					    'st_code' => $ele_details->ST_CODE,
					   // 'election_id' => $ele_details->ELECTION_ID,
					    'ac_no'   =>$d->ac_no,
					 ];
            $state_language=getstatelanguage($ele_details->ST_CODE);
         
		    $record=$this->formmodel->get_records($filter);
                if(!isset($record)){
                  $master=DB::table('m_form7a_details')->where('st_code', $ele_details->ST_CODE)->first();

                  $newdata = array('st_code'=>$ele_details->ST_CODE, 
                          'pc_no'=>0,
			                    'ac_no'=>$d->ac_no, 
			                    'dist_no'=>$d->dist_no ,
			                    'election_id'=>$ele_details->ELECTION_ID, 
			                    'election_typeid'=>$ele_details->ELECTION_TYPEID , 
			                    'const_type'=>"AC",  
			                     'title1'=>$master->title1,
                          'title2'=>$master->title2, 
                          'title3'=>$master->title3,
                          'title4'=>$master->title4." " .$ac->AC_NO."-".ucwords($ac->AC_NAME),
                          'header1'=>$master->header1,  
                          'header2'=>$master->header2,
                          'header3'=>$master->header3,
                          'header4'=>$master->header4,
                          'header5'=>$master->header5,
                          'header6'=>$master->header6,
                          'subheader1'=>$master->subheader1,  
                          'subheader2'=>$master->subheader2, 
                          'subheader3'=>$master->subheader3,
                          'subheader4'=>$master->subheader4,
                          'subheader5'=>$master->subheader5,
                          'subheader6'=>$master->subheader6,
                          'middle_title1'=>$master->middle_title1,
                          'middle_title2'=>$master->middle_title2,
                          'middle_title3'=>$master->middle_title3,
                          'footer1'=>$master->footer1,
                          'footer2'=>$master->footer2."-".date("d-m-Y",strtotime($ndate)),
                          'footer3'=>"( ".$d->name." )", 
                          'footer4'=>$master->footer4,
                          'footer5'=>$ac->AC_NO."-".ucwords($ac->AC_NAME).$master->footer5,
                          'vtitle1'=>$master->vtitle1,
                          'vtitle2'=>$master->vtitle2, 
                          'vtitle3'=>$master->vtitle3,
                          'vtitle4'=>$master->vtitle4.$ac->AC_NO."-".ucwords($ac->AC_NAME),
                          'vheader1'=>$master->vheader1,  
                          'vheader2'=>$master->vheader2,
                          'vheader3'=>$master->vheader3,
                          'vheader4'=>$master->vheader4,
                          'vheader5'=>$master->vheader5,
                          'vheader6'=>$master->vheader6,
                          'vsubheader1'=>$master->vsubheader1,  
                          'vsubheader2'=>$master->vsubheader2, 
                          'vsubheader3'=>$master->vsubheader3,
                          'vsubheader4'=>$master->vsubheader4,
                          'vsubheader5'=>$master->vsubheader5,
                          'vsubheader6'=>$master->vsubheader6,
                          'vmiddle_title1'=>$master->vmiddle_title1,
                          'vmiddle_title2'=>$master->vmiddle_title2,
                          'vmiddle_title3'=>$master->vmiddle_title3,
                         'vfooter1'=>$master->vfooter1,
                         'vfooter2'=>$master->vfooter2.date("d-m-Y",strtotime($ndate)),
                          'vfooter3'=>"( ".$d->name." )", 
                          'vfooter4'=>$master->vfooter4,
                          'vfooter5'=>$ac->AC_NO."-".ucwords($ac->AC_NAME).$master->vfooter5,
			                    'added_created_at'=>date("Y-m-d"),
			                    'created_at'=>date("Y-m-d H:i:s"),
			                    'created_by'=>$d->officername,
                        ); 
                       
                	     insertData('candidate_form7a_detail', $newdata);
                		}
                	 
		         $record=$this->formmodel->get_records($filter);

		                $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['record']=$record;
                    $data['st_name']=$st->ST_NAME;
                    $data['ac_no']=$ac->AC_NO;
		                $data['ac_name']=$ac->AC_NAME;   //
                    $data['state_language']=$state_language;  
	              
	              return view($this->view_path.'.form7a', $data); 

	    }  // end index function  
    function updated_form7A(Request $request) {
             $user = Auth::user(); 
             $d=$this->commonModel->getunewserbyuserid($user->id);
             $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,'AC');
             //dd($request->input());

             $validator = Validator::make($request->all(), 
              [
               'vtitle1'=> 'required',
               'vtitle2' => 'required',
               'vtitle3' => 'required',
               'vtitle4' => 'required',
               'vheader1'=> 'required',
               'vheader2' => 'required',
               'vheader3' => 'required',
               'vheader4' => 'required',
               'vheader5'=> 'required',
               'vheader6'=> 'required',
               'vsubheader1'=> 'required',
               'vsubheader2' => 'required',
               'vsubheader3' => 'required',
               'vsubheader4' => 'required',
               'vsubheader5'=> 'required',
               'vsubheader6'=> 'required',
               'vmiddle_title1' => 'required',
               'vmiddle_title2' => 'required',
               'vmiddle_title3' => 'required',
               'vfooter1'=> 'required',
               'vfooter2' => 'required',
               'vfooter3' => 'required',
               'vfooter4' => 'required',
               'vfooter5' => 'required', 

               'title1'=> 'required',
               'title2' => 'required',
               'title3' => 'required',
               'title4' => 'required',
               'header1'=> 'required',
               'header2' => 'required',
               'header3' => 'required',
               'header4' => 'required',
               'header5'=> 'required',
               'header6'=> 'required',
               'subheader1'=> 'required',
               'subheader2' => 'required',
               'subheader3' => 'required',
               'subheader4' => 'required',
               'subheader5'=> 'required',
               'subheader6'=> 'required',
               'middle_title1' => 'required',
               'middle_title2' => 'required',
               'middle_title3' => 'required',
               'footer1'=> 'required',
               'footer2' => 'required',
               'footer3' => 'required',
               'footer4' => 'required',
               'footer5' => 'required',   
           ],
           [
            'vtitle1.required' => 'Please enter in Vernacular title1 ',
            'vtitle2.required' => 'Please enter in Vernacular title2',
            'vtitle3.required' => 'Please enter in Vernacular title3',
            'vtitle4.required' => 'Please enter in Vernacular title4',
            'vheader1.required' => 'Please enter in Vernacular header1',
            'vheader2.required' => 'Please enter in Vernacular header2',
            'vheader3.required' => 'Please enter in Vernacular header3',
            'vheader4.required' => 'Please enter in Vernacular header4',
            'vheader5.required' => 'Please enter in Vernacular header5',
            'vsubheader1.required' => 'Please enter in Vernacular subheader1',
            'vsubheader2.required' => 'Please enter in Vernacular subheader2',
            'vsubheader3.required' => 'Please enter in Vernacular subheader3',
            'vsubheader4.required' => 'Please enter in Vernacular subheader4',
            'vsubheader5.required' => 'Please enter in Vernacular subheader5',
            'vmiddle_title1.required' => 'Please enter in Vernacular middle_title1',
            'vmiddle_title2.required' => 'Please enter in Vernacular middle_title2',
            'vmiddle_title3.required' => 'Please enter in Vernacular middle_title3',
            'vfooter1.required' => 'Please enter in Vernacular footer1',
            'vfooter2.required' => 'Please enter in Vernacular footer2',
            'vfooter3.required' => 'Please enter in Vernacular footer3',
            'vfooter4.required' => 'Please enter in Vernacular footer4',
            'vfooter5.required' => 'Please enter in Vernacular footer5',
            'vsubheader6.required' => 'Please enter in Vernacular subheader6',
            'vheader6.required' => 'Please enter in Vernacular header6', 

            'title1.required' => 'Please enter in  title1 ',
            'title2.required' => 'Please enter in  title2',
            'title3.required' => 'Please enter in  title3',
            'title4.required' => 'Please enter in  title4',
            'header1.required' => 'Please enter in  header1',
            'header2.required' => 'Please enter in  header2',
            'header3.required' => 'Please enter in  header3',
            'header4.required' => 'Please enter in  header4',
            'header5.required' => 'Please enter in  header5',
            'subheader1.required' => 'Please enter in  subheader1',
            'subheader2.required' => 'Please enter in  subheader2',
            'subheader3.required' => 'Please enter in  subheader3',
            'subheader4.required' => 'Please enter in  subheader4',
            'subheader5.required' => 'Please enter in  subheader5',
            'middle_title1.required' => 'Please enter in  middle_title1',
            'middle_title2.required' => 'Please enter in  middle_title2',
            'middle_title3.required' => 'Please enter in  middle_title3',
            'footer1.required' => 'Please enter in  footer1',
            'footer2.required' => 'Please enter in  footer2',
            'footer3.required' => 'Please enter in  footer3',
            'footer4.required' => 'Please enter in  footer4',
            'footer5.required' => 'Please enter in  footer5',
            'subheader6.required' => 'Please enter in  subheader6',
            'header6.required' => 'Please enter in  header6', 

        ]); 
       
        if ($validator->fails()) {     
            return redirect::back()
            ->withErrors($validator)
            ->withInput();
          }
             
        $nid= $this->xssClean->clean_input($request->input('id'));
        $filter = [
					    'st_code' => $ele_details->ST_CODE,
					   // 'election_id' => $ele_details->ELECTION_ID,
					    'ac_no'   =>$d->ac_no,
				  ];
		$record=$this->formmodel->get_records($filter);   
      DB::beginTransaction();
        try{   
            $data_u = array('vtitle1'=>$this->xssClean->clean_input($request->input('vtitle1')),
                    
                    'vtitle2'=>$this->xssClean->clean_input($request->input('vtitle2')),
                    'vtitle3'=>$this->xssClean->clean_input($request->input('vtitle3')),
                    'vtitle4'=>$this->xssClean->clean_input($request->input('vtitle4')),
                    'vheader1'=>$this->xssClean->clean_input($request->input('vheader1')),
                    'vheader2'=>$this->xssClean->clean_input($request->input('vheader2')),
                    'vheader3'=>$this->xssClean->clean_input($request->input('vheader3')),
                    'vheader4'=>$this->xssClean->clean_input($request->input('vheader4')),
                    'vheader5'=>$this->xssClean->clean_input($request->input('vheader5')),
                    'vheader6'=>$this->xssClean->clean_input($request->input('vheader6')),
                    'vsubheader1'=>$this->xssClean->clean_input($request->input('vsubheader1')),
                    'vsubheader2'=>$this->xssClean->clean_input($request->input('vsubheader2')),
                    'vsubheader3'=>$this->xssClean->clean_input($request->input('vsubheader3')),
                    'vsubheader4'=>$this->xssClean->clean_input($request->input('vsubheader4')),
                    'vsubheader5'=>$this->xssClean->clean_input($request->input('vsubheader5')),
                    'vsubheader6'=>$this->xssClean->clean_input($request->input('vsubheader6')),
                    'vmiddle_title1'=>$this->xssClean->clean_input($request->input('vmiddle_title1')),
                    'vmiddle_title2'=>$this->xssClean->clean_input($request->input('vmiddle_title2')),
                    'vmiddle_title3'=>$this->xssClean->clean_input($request->input('vmiddle_title3')),
                    'vfooter1'=>$this->xssClean->clean_input($request->input('vfooter1')),
                    'vfooter2'=>$this->xssClean->clean_input($request->input('vfooter2')),
                    'vfooter3'=>$this->xssClean->clean_input($request->input('vfooter3')),
                    'vfooter4'=>$this->xssClean->clean_input($request->input('vfooter4')),
                    'vfooter5'=>$this->xssClean->clean_input($request->input('vfooter5')),
                    'title1'=>$this->xssClean->clean_input($request->input('title1')),
                    'title2'=>$this->xssClean->clean_input($request->input('title2')),
                    'title3'=>$this->xssClean->clean_input($request->input('title3')),
                    'title4'=>$this->xssClean->clean_input($request->input('title4')),
                    'header1'=>$this->xssClean->clean_input($request->input('header1')),
                    'header2'=>$this->xssClean->clean_input($request->input('header2')),
                    'header3'=>$this->xssClean->clean_input($request->input('header3')),
                    'header4'=>$this->xssClean->clean_input($request->input('header4')),
                    'header5'=>$this->xssClean->clean_input($request->input('header5')),
                    'header6'=>$this->xssClean->clean_input($request->input('header6')),
                    'subheader1'=>$this->xssClean->clean_input($request->input('subheader1')),
                    'subheader2'=>$this->xssClean->clean_input($request->input('subheader2')),
                    'subheader3'=>$this->xssClean->clean_input($request->input('subheader3')),
                    'subheader4'=>$this->xssClean->clean_input($request->input('subheader4')),
                    'subheader5'=>$this->xssClean->clean_input($request->input('subheader5')),
                    'subheader6'=>$this->xssClean->clean_input($request->input('subheader6')),
                    'middle_title1'=>$this->xssClean->clean_input($request->input('middle_title1')),
                    'middle_title2'=>$this->xssClean->clean_input($request->input('middle_title2')),
                    'middle_title3'=>$this->xssClean->clean_input($request->input('middle_title3')),
                    'footer1'=>$this->xssClean->clean_input($request->input('footer1')),
                    'footer2'=>$this->xssClean->clean_input($request->input('footer2')),
                    'footer3'=>$this->xssClean->clean_input($request->input('footer3')),
                    'footer4'=>$this->xssClean->clean_input($request->input('footer4')),
                    'footer5'=>$this->xssClean->clean_input($request->input('footer5')),

                    'updated_at'=>date("Y-m-d H:i:s"),
			              'updated_by'=>$d->officername); 
                 
                updatedata('candidate_form7a_detail','id',$record->id, $data_u);
              

		    }
			catch(\Exception $e){
			   DB::rollback();

			   \Session::flash('error_mes', 'Please try again');
			   return Redirect::back();
			}
        DB::commit();
             
			\Session::flash('success_mes', 'This Records Successfully Saved');
			return Redirect::to('roac/update-form7A-details');

    }  // end function  

   public function download_form7a_english(Request $request)
		    {
			 $st_code=$this->xssClean->clean_input($request->input('st_code'));
			 $ac_no=$this->xssClean->clean_input($request->input('ac_no'));
			  $filter = [
                'st_code'               => $st_code,
                'ac_no'     			=> $ac_no,
                'election_id'           => Auth::user()->election_id,
                'const_type'            => "AC",
               ];
             $filter1 = [
                'st_code'               => $st_code,
                'ac_no'     			=> $ac_no,
                ];
               
              $state_language=getstatelanguage($st_code);

		       $record=$this->formmodel->get_records($filter1);
              
              $a='N'; $a1='S';  $a2='U'; $a3='0';$a4='Z';
              $candn =$this->formmodel->partywiseallcontenestingcandidate($filter);   
    	      $cands = $this->formmodel->partywisecontenestingcandidate($filter,$a,$a1);  
              $candu =$this->formmodel->partywisecontenestingcandidate($filter,$a2,$a3); 
              $candz =$this->formmodel->partywisecontenestingcandidate($filter,$a4,$a4); 
           
		      $ac='';
		        
		        $ac=getacbyacno($st_code,$ac_no);
		        $state=getstatebystatecode($st_code); 
                   
                    $data['heading_title']="Form 7A";
                    $data['st_code']=$st_code;
                    $data['state']=$state;
                    $data['ac']   =$ac;
                    $data['ac_no']=$ac->AC_NO;
		            $data['ac_name']=$ac->AC_NAME;   //$heading_title
                    $data['candn']=$candn;
                    $data['cands']=$cands;
                    $data['candu']=$candu;
                    $data['candz']=$candz;
                    $data['state_language']=$state_language;
                    $data['record']=$record;
	    	     
	        $name_excel = 'Form7Aenglish-'.$st_code."_ac_no".$ac_no.'_'.time();
      		$data['file_name']=$name_excel; 
      		$data['ref_no']  =time();

        // $log_data = array( 'st_code'=>$st_code,
        //                       'election_id'=>$election_id,
        //                       'election_typeid'=>'0', 
        //                       'pc_no'=>'0', 
        //                       'ac_no'=>$ac_no, 
        //                       'ps_no'=>'0',
        //                       'doc_type'=>"Generate From20 PDF",
        //                       'file_name'=>$name_excel.".pdf",
        //                       'table_name'=>$new_table,
        //                       'table_primary_key'=>'0', 
        //                       'log_date_time'=>date('Y-m-d H:i:a'),
        //                       'added_create_at'=>date('Y-m-d'),
        //                       'ref_no'=> $data['ref_no'],
        //                       'created_by'=>\Auth::user()->officername);
            
        //     \App\models\Counting\CountingPrintlogModel::clone_record($log_data);

		      $data['user']=\Auth::user()->officername;
		      $data['print_date']=date('d-m-Y H:i:a');
		            $setting_pdf = [
		                'margin_top'        =>45,  
		                'margin_bottom'     =>30,
		                'show_warnings'     => false,    
		               // 'orientation'       => 'portlet',    
		            ];
				if($data['user']=='ROACS11A006'){
					//return view($this->view_path.'.cantesting-candidate',$data);
					$pdf = \MPDF::loadView($this->view_path.'.cantesting-candidate',$data,[], $setting_pdf);
					return $pdf->download($name_excel.'.pdf'); 
				}else{
					$pdf = \MPDF::loadView($this->view_path.'.cantesting-candidate',$data,[], $setting_pdf);
					return $pdf->download($name_excel.'.pdf'); 
				}
        		
			    
			 
		    }
      public function download_form7a_vernacular(Request $request)
		    {
			 $st_code=$this->xssClean->clean_input($request->input('st_code'));
			 $ac_no=$this->xssClean->clean_input($request->input('ac_no'));
			  $filter = [
                'st_code'               => $st_code,
                'ac_no'     			=> $ac_no,
                'election_id'           => Auth::user()->election_id,
                'const_type'            => "AC",
               ];
             $filter1 = [
                'st_code'               => $st_code,
                'ac_no'     			=> $ac_no,
                ];
               
              $state_language=getstatelanguage($st_code);
              if($st_code=="S10" and $state_language=="KAN")
                 {
                    $data['font_data']="kannad";
                    $data['fonts']="tunga";
                 }
              elseif(($st_code=="S29"||$st_code=="S01") and $state_language=="TEL")
                {
                   $data['font_data']="telugu";
                    $data['fonts']="gautami";
                }
              // elseif(($st_code=="S23"||$st_code=="S25") and $state_language=="BEN")
              //   {
              //      $data['font_data']="bangla";
              //       $data['fonts']="mitra";
              //   }
              else{
                    $data['font_data']="manny";
                    $data['fonts']="freeserif";
                  }
              
  
		       $record=$this->formmodel->get_records($filter1);
              //dd( $record);
              $a='N'; $a1='S';  $a2='U'; $a3='0';$a4='Z';
              $candn =$this->formmodel->partywiseallcontenestingcandidatevernacular($filter);   
    	        $cands = $this->formmodel->partywisecontenestingcandidatevernacular($filter,$a,$a1);  
              $candu =$this->formmodel->partywisecontenestingcandidatevernacular($filter,$a2,$a3); 
              $candz =$this->formmodel->partywisecontenestingcandidatevernacular($filter,$a4,$a4); 
           
		      $ac='';
		        
		        $ac=getacbyacno($st_code,$ac_no);
		        $state=getstatebystatecode($st_code); 
                   
                    $data['heading_title']="Form 7A";
                    $data['st_code']=$st_code;
                    $data['state']=$state;
                    $data['ac']   =$ac;
                    $data['ac_no']=$ac->AC_NO;
		                $data['ac_name']=$ac->AC_NAME;   //$heading_title
                    $data['candn']=$candn;
                    $data['cands']=$cands;
                    $data['candu']=$candu;
                    $data['candz']=$candz;
                    $data['state_language']=$state_language;
                    $data['record']=$record;
	    	     
	        $name_excel = 'Form7A-vernacular-'.$st_code."_ac_no".$ac_no.'_'.time();
      		$data['file_name']=$name_excel; 
      		$data['ref_no']  =time();

        // $log_data = array( 'st_code'=>$st_code,
        //                       'election_id'=>$election_id,
        //                       'election_typeid'=>'0', 
        //                       'pc_no'=>'0', 
        //                       'ac_no'=>$ac_no, 
        //                       'ps_no'=>'0',
        //                       'doc_type'=>"Generate From20 PDF",
        //                       'file_name'=>$name_excel.".pdf",
        //                       'table_name'=>$new_table,
        //                       'table_primary_key'=>'0', 
        //                       'log_date_time'=>date('Y-m-d H:i:a'),
        //                       'added_create_at'=>date('Y-m-d'),
        //                       'ref_no'=> $data['ref_no'],
        //                       'created_by'=>\Auth::user()->officername);
            
        //     \App\models\Counting\CountingPrintlogModel::clone_record($log_data);
        // dd($data);
		      $data['user']=\Auth::user()->officername;
		      $data['print_date']=date('d-m-Y H:i:a');
		            $setting_pdf = [
		                'margin_top'        =>45,  
		                'margin_bottom'     =>30,
		                'show_warnings'     => false,    
		                //'orientation'       => 'portlet',    
		            ];
		        $mpdf = new \Mpdf\Mpdf(['utf-8', 'A4-C']);
        		$pdf = \MPDF::loadView($this->view_path.'.cantesting-candidate-vernacular',$data,[], $setting_pdf);
         		return $pdf->download($name_excel.'.pdf'); 
			    
			 
		    }
   public function download_form7a_bilingual(Request $request)
        {
       $st_code=$this->xssClean->clean_input($request->input('st_code'));
       $ac_no=$this->xssClean->clean_input($request->input('ac_no'));
        $filter = [
                'st_code'         => $st_code,
                'ac_no'           => $ac_no,
                'election_id'     => Auth::user()->election_id,
                'const_type'      => "AC",
               ];
             $filter1 = [
                'st_code'         => $st_code,
                'ac_no'           => $ac_no,
                ];
               
              $state_language=getstatelanguage($st_code);
              if($st_code=="S10" and $state_language=="KAN")
                 {
                    $data['font_data']="kannad";
                    $data['fonts']="tunga";
                 }
              elseif(($st_code=="S29"||$st_code=="S01") and $state_language=="TEL")
                {
                   $data['font_data']="telugu";
                    $data['fonts']="gautami";
                }
               
              else{
                    $data['font_data']="manny";
                    $data['fonts']="freeserif";
                  }
              
  
           $record=$this->formmodel->get_records($filter1);
              //dd( $record);
              $a='N'; $a1='S';  $a2='U'; $a3='0';$a4='Z';
              $candn =$this->formmodel->partywiseallcontenestingcandidatevernacular($filter);   
              $cands = $this->formmodel->partywisecontenestingcandidatevernacular($filter,$a,$a1);  
              $candu =$this->formmodel->partywisecontenestingcandidatevernacular($filter,$a2,$a3); 
              $candz =$this->formmodel->partywisecontenestingcandidatevernacular($filter,$a4,$a4); 
           
          $ac='';
            
            $ac=getacbyacno($st_code,$ac_no);
            $state=getstatebystatecode($st_code); 
                   
                    $data['heading_title']="Form 7A";
                    $data['st_code']=$st_code;
                    $data['state']=$state;
                    $data['ac']   =$ac;
                    $data['ac_no']=$ac->AC_NO;
                    $data['ac_name']=$ac->AC_NAME;   //$heading_title
                    $data['candn']=$candn;
                    $data['cands']=$cands;
                    $data['candu']=$candu;
                    $data['candz']=$candz;
                    $data['state_language']=$state_language;
                    $data['record']=$record;
             
          $name_excel = 'Form7A-bilingual-'.$st_code."_ac_no".$ac_no.'_'.time();
          $data['file_name']=$name_excel; 
          $data['ref_no']  =time();
 
          $data['user']=\Auth::user()->officername;
          $data['print_date']=date('d-m-Y H:i:a');
                $setting_pdf = [
                    'margin_top'        =>60,  
                    'margin_bottom'     =>30,
                    'show_warnings'     => false,       
                ];
            $mpdf = new \Mpdf\Mpdf(['utf-8', 'A4-C']);
            $pdf = \MPDF::loadView($this->view_path.'.cantesting-candidate-bilingual',$data,[], $setting_pdf);
            return $pdf->download($name_excel.'.pdf'); 
          
       
        }
  // excelexport_excel_form20
  // excelexport_excel_form20


  public function download_form7a_english_excel(Request $request){

    // if(!$this->st_code || !$this->ac_no){
    //   return Redirect::back();
    // }
       $st_code=$this->xssClean->clean_input($request->input('st_code'));
       $ac_no=$this->xssClean->clean_input($request->input('ac_no'));
        $filter = [
                'st_code'         => $st_code,
                'ac_no'           => $ac_no,
                'election_id'     => Auth::user()->election_id,
                'const_type'      => "AC",
               ];
             $filter1 = [
                'st_code'         => $st_code,
                'ac_no'           => $ac_no,
                ];
               
            $state_language=getstatelanguage($st_code);

           $record=$this->formmodel->get_records($filter1);
              
              $a='N'; $a1='S';  $a2='U'; $a3='0';$a4='Z';
              $candn =$this->formmodel->partywiseallcontenestingcandidate($filter);   
              $cands = $this->formmodel->partywisecontenestingcandidate($filter,$a,$a1);  
              $candu =$this->formmodel->partywisecontenestingcandidate($filter,$a2,$a3); 
              $candz =$this->formmodel->partywisecontenestingcandidate($filter,$a4,$a4); 
           
          $ac='';
            
            $ac=getacbyacno($st_code,$ac_no);
            $state=getstatebystatecode($st_code); 
                   
                    $data['heading_title']="Form 7A";
                    $data['st_code']=$st_code;
                    $data['state']=$state;
                    $data['ac']   =$ac;
                    $data['ac_no']=$ac->AC_NO;
                    $data['ac_name']=$ac->AC_NAME;   
                    $data['candn']=$candn;
                    $data['cands']=$cands;
                    $data['candu']=$candu;
                    $data['candz']=$candz;
                    $data['state_language']=$state_language;
                    $data['record']=$record;
             
          $name_excel = 'Form7Aenglish-'.$st_code."_ac_no".$ac_no.'_'.time();
          $data['file_name']=$name_excel; 
          

         

          $data['user']=\Auth::user()->officername;
          // $data['print_date']=date('d-m-Y H:i:a');
          //       $setting_pdf = [
          //           'margin_top'        =>45,  
          //           'margin_bottom'     =>30,
          //           'show_warnings'     => false,    
          //          // 'orientation'       => 'portlet',    
          //       ];

       dd($data);
        $GLOBALS['cellarr']=array('0' =>'A8','1' =>'B8','2' =>'C8','3' =>'D8','4' =>'E8','5' =>'F8','6' =>'G8','7' =>'H8',
          '8' =>'I8','9' =>'J8', '10' =>'K8','11' =>'L8','12' =>'M8','13' =>'N8','14' =>'O8',
              '15' =>'P8','16' =>'Q8','17' =>'R8','18' =>'S8','19' =>'T8', '20' =>'U8','21' =>'V8','22' =>'W8','23' =>'X8',
              '24' =>'Y8', '25' =>'Z8', '26' =>'AA8','27' =>'AB8','28' =>'AC8','29' =>'AD8','30' =>'AE8','31' =>'AF8',
              '32' =>'G8','33' =>'H8',
              '34' =>'I8','35' =>'J8', '36' =>'K8','37' =>'L8','38' =>'M8','39' =>'N8','40' =>'O8',
              '41' =>'P8','42' =>'Q8','43' =>'R8','44' =>'S8','45' =>'T8', '46' =>'U8','47' =>'V8','48' =>'W8',
              '49' =>'X8', '50' =>'Y8', '51' =>'Z8',);
   //          $data=[];
   //          $st_code=$this->xssClean->clean_input($this->st_code);
   //          $ac_no=$this->xssClean->clean_input($this->ac_no);
   //          $election_id=Auth::user()->election_id;
   //          $st=getstatebystatecode($st_code);  
   //          $ac=getacbyacno($st_code,$ac_no); 
   //           $data['st_code']        = $st_code;
   //           $data['ac_no']          = $ac_no;
   //           $data['ac_name']        = $ac->AC_NAME;
   //           $data['st_name']        = $st->ST_NAME;

   //          $filter = [
   //              'st_code'       => $st_code,
   //              'election_id'   => $election_id,
   //              'ac_no'         =>$ac_no,
   //              'pc_no'         =>'',
   //              'ps_no'         =>'',
   //              'table'         =>"counting_master_".strtolower($st_code), 
   //          ];
   //         $totalelectors= $this->boothcounting->totalelectors($filter);
   //         $totalcandidate = $this->boothcounting->noofcandidate($filter);
   //         $c=$GLOBALS['cellarr'][$totalcandidate];
            
   //         $columecandidate = $this->boothcounting->getallcandidate($filter);
   //         $GLOBALS['totalcandidate']=$totalcandidate;
   //         $listallac = $this->boothcounting->get_acwisepollingstation($filter);
               
   //         $resultsum = $this->boothcounting->getpsvotessum($filter);
   //         $postaldetails = $this->boothcounting->get_allpostalvotes($filter);

   //         $data['totalcandidate'] = $totalcandidate;
   //         $data['columecandidate'] = $columecandidate;
   //         $data['listallac'] = $listallac;

   //          $j=0; $k=0;
   //         foreach ($listallac as $key => $val) { $i=0; $field="data".$i; $k++;
   //         $data['results'][$j][$field]=$k;  
   //          $i++;
   //          $field="data".$i;
   //         $data['results'][$j][$field]=$val->PS_NO;

   //         $filter_new = [
   //          'st_code'       => $st_code,
   //          'election_id'   => $election_id,
   //          'ac_no'         =>$ac_no,
   //          'pc_no'         =>'',
   //          'ps_no'         =>$val->PS_NO,
   //      ];

   //        $list = $this->boothcounting->getallpsvotes($filter_new);
           
   //      $sum=0; $nota=0; $rejected_vote=0;  $tendered_vote=0; 

         
   //      foreach ( $list as  $new) { $i++; $field="data".$i;
   //      if($new->party_id!='1180'){
   //          $data['results'][$j][$field] =$new->evm_vote;
   //          $sum +=$new->evm_vote;
   //          $rejected_vote=$new->rejected_vote;

   //          $tendered_vote=$new->tendered_vote;
   //      }       
   //      else {
   //         $nota =$new->evm_vote;
   //     }
   // }
      
   // $field="data".$i;
   // if (empty($sum))$sum=0;
   // if (empty($rejected_vote))$rejected_vote=0;
   // if (empty($nota))$nota=0;
   // if (empty($tendered_vote))$tendered_vote=0;
   

   //  $data['results'][$j][$field] = $sum;
   // $i++;
   // $field="data".$i;
   // $data['results'][$j][$field] = $rejected_vote;
   // $i++;
   // $field="data".$i;
   // $data['results'][$j][$field] = $nota;
   // $net=0;
   // $net=$sum+$nota+ $rejected_vote;
   // $i++;
   
   // $field="data".$i;
   // if( $net==0 || ($net)) $data['results'][$j][$field]='0';
   // $data['results'][$j][$field] = $net;

   // $i++;
   // $field="data".$i;
   // $data['results'][$j][$field] = $tendered_vote;
   // $j++;    
   //    }
      
   //     $data['grand_allsum'] = array();
   //    $k=0; $gsum=0;  $grejected_vote=0;  $gtendered_vote=0;  $gnota=0;
   //    foreach ( $resultsum as  $sum) {  
   //      if($sum->party_id!='1180'){
   //        $data['grandsum'][$k]=$sum->evm_vote;
   //        $data['grand_allsum'][$k] =$sum->evm_vote;
   //        $gsum=$gsum+$sum->evm_vote;
   //        $grejected_vote=$sum->rejected_vote;
   //        $gtendered_vote=$sum->tendered_vote;
   //    }       
   //    else {
   //       $gnota =$sum->evm_vote;
   //    }
   //    $k++;
   //    }

   //    $data['grandsum'][$k]=$gsum; 
   //    $data['grand_allsum'][$k] =$gsum;
   //    $k++;  
   //    $data['grandsum'][$k]=$grejected_vote;
   //    $data['grand_allsum'][$k] =$grejected_vote;
   //    $k++;  
   //    $data['grandsum'][$k]=$gnota; 
   //    $data['grand_allsum'][$k] =$gnota; 
   //    $gnet= $gsum+$grejected_vote+$gnota;
   //    $k++;  
   //    $data['grandsum'][$k]=$gnet; 
   //     $data['grand_allsum'][$k] =$gnet;   
   //    $k++;  
   //    $data['grandsum'][$k]=$gtendered_vote;
   //    $data['grand_allsum'][$k] =$gtendered_vote;
     
   //    $data['postal_vote'] = array();
      

   //    $data['colcount'] = $totalcandidate+6;
   //     $k=0; $postalsum=0;  $prejected_votes=0;  $tended_votes=0;  $pnota=0;
   //    foreach ( $postaldetails as  $postal) {  
   //      if($postal->party_id!='1180'){
   //        $data['postal_vote'][$k]=$postal->postalballot_vote;
   //        $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$postal->postalballot_vote;
   //        $postalsum=$postalsum+$postal->postalballot_vote;
   //        $prejected_votes=$postal->rejected_votes;
           
   //    }       
   //    else {
   //       $pnota =$postal->postalballot_vote;
   //    }
   //    $k++;
   //    }
   //    $data['postal_vote'][$k]=$postalsum;
   //    $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$postalsum; 
   //    $k++;  
   //    $data['postal_vote'][$k]=$prejected_votes;
   //    $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$prejected_votes; 
   //    $k++;  
   //    $data['postal_vote'][$k]=$pnota;  
   //     $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$pnota; 
   //    $pnet= $postalsum+$prejected_votes+$pnota;

   //    $k++;  
   //    $data['postal_vote'][$k]=$pnet; 
   //     $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$pnet;   
   //    $k++;  
   //    $data['postal_vote'][$k]=$tended_votes;
   //    $data['grand_allsum'][$k] = $data['grand_allsum'][$k]+$tended_votes; 
     
              $export_data = [];
              $export_data[] = [$data['record']->title1];
              $export_data[] = [$data['record']->title2];
              $export_data[] = [$data['record']->title3];
              $export_data[] = [$data['record']->title4];
              $export_data[] = [$data['record']->header1,$data['record']->header2,$data['record']->header3,];
              $export_data[] = [' Name of  Assembly/segment  ...'. $data['ac_no'].'-'.$data['ac_name'].' Assembly Election'];
                            
            $export_data[] = ['','', 'No of Valid Votes Cast in favour of',' ','', '','',''];
            $i=0;
            $export_data[7][$i] ='Serial No.';
            $i++;
            $export_data[7][$i] ='Serial No. Of Polling Station';  
             $st='';  
                   foreach ($columecandidate as   $val) { $i++;
                         
                              $export_data[7][$i]=$val->candidate_name;
                     }
             $i++;
             $export_data[7][$i]='Total of Valid Votes';
             $i++;
             $export_data[7][$i]='No. Of Rejected Votes';         
             $i++;
             $export_data[7][$i]='NOTA'; 
             $i++;
             $export_data[7][$i]='Total'; 
             $i++;
             $export_data[7][$i]='No. Of Tendered Votes'; 
        
     $i=8; $j=0;
    foreach ($data['results'] as $lists) {

          foreach ($lists as $lis) {
               if($lis==0) $export_data[$i][$j]='0';
               else
               $export_data[$i][$j] =$lis;
               $j++;
            }   // end foreach
        $i++;
        } // end foreach 
        $j=0; 
         $export_data[$i][$j] ='Total EVM ';
         $j++;   
         $export_data[$i][$j] =' Votes ';    
         foreach($data['grandsum'] as $d){ $j++; 
              if($d==0) $export_data[$i][$j]='0'; 
              else  
              $export_data[$i][$j] =$d;
                 
         } 

       $j=0; $i++; 
         $export_data[$i][$j] ='Total Postal Ballot '; 
         $j++;   
         $export_data[$i][$j] =' Votes ';       
         foreach($data['postal_vote'] as $d){ $j++;    
               if($d==0) 
                        $export_data[$i][$j]='0'; 
                else  
                        $export_data[$i][$j] =$d;
                 
         }  
         $j=0; $i++; 
         $export_data[$i][$j] ='Total Votes '; 
         $j++;   
         $export_data[$i][$j] =' Polled ';       
         foreach($data['grand_allsum'] as $d){ $j++;    
               if($d==0) 
                        $export_data[$i][$j]='0'; 
              else  
                        $export_data[$i][$j] =$d;
                 
         }  


    //dd($export_data); 
    $name_excel = 'form20-'.strtolower($data['st_code'])."_".$data['ac_no'].'_'.date('d-m-Y').'_'.time();
    
    \Excel::create($name_excel, function($excel) use($export_data) {
        $excel->sheet('Sheet1', function($sheet) use($export_data) {
          $sheet->mergeCells('A1:J1');
          $sheet->mergeCells('A2:J2');
          $sheet->mergeCells('A3:J3');
          $sheet->mergeCells('A4:J4');
          $sheet->mergeCells('A5:J5');
          $sheet->mergeCells('A6:J6');
         // $sheet->mergeCells('A8:B8');
          $sheet->mergeCells('C7:K7');
          
          $sheet->cell('A1', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->cell('A2', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->cell('A3', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
          $sheet->cell('A4', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
         $sheet->cell('A5', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
         $sheet->cell('A6', function($cell) {
            $cell->setAlignment('center');
            $cell->setFontWeight('bold');
          });
            for($c=0; $c<=$GLOBALS['totalcandidate']+6;$c++){
             $newcell=strtoupper($GLOBALS['cellarr'][$c]);
            
              $sheet->cell($newcell, function($cell) {
                      $cell->setTextRotation(90);
                      $cell->setFontWeight('bold');
             });
           }
          $sheet->fromArray($export_data,null,'A1',false,false);
        });
    })->export('xls');
  }
    

  public function ceo_form7A_details(request $request) {      
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_detailsac($d->st_code,$d->ac_no,$d->dist_no,$d->id,$d->officerlevel);
        $ele_details=$ele_details[0];
         
            $st=getstatebystatecode($ele_details->ST_CODE);
           
            $state_language=getstatelanguage($ele_details->ST_CODE);
         
        
             $record=getById('m_form7a_details','st_code',$ele_details->ST_CODE);

                    $data['user_data']=$d;
                    $data['ele_details']=$ele_details;
                    $data['record']=$record;
                    $data['st_name']=$st->ST_NAME;
                    $data['state_language']=$state_language;  
                
                return view($this->view_path.'.ceoform7a', $data); 

      }  // end index function 

    function ceoupdated_form7A(Request $request) {

             $user = Auth::user(); 
             $d=$this->commonModel->getunewserbyuserid($user->id);
              
             $validator = Validator::make($request->all(), 
              [
               'vtitle1'=> 'required',
               'vtitle2' => 'required',
               'vtitle3' => 'required',
               'vtitle4' => 'required',
               'vheader1'=> 'required',
               'vheader2' => 'required',
               'vheader3' => 'required',
               'vheader4' => 'required',
               'vheader5'=> 'required',
               'vheader6'=> 'required',
               'vsubheader1'=> 'required',
               'vsubheader2' => 'required',
               'vsubheader3' => 'required',
               'vsubheader4' => 'required',
               'vsubheader5'=> 'required',
               'vsubheader6'=> 'required',
               'vmiddle_title1' => 'required',
               'vmiddle_title2' => 'required',
               'vmiddle_title3' => 'required',
               'vfooter1'=> 'required',
               'vfooter2' => 'required',
               'vfooter3' => 'required',
               'vfooter4' => 'required',
               'vfooter5' => 'required', 

               'title1'=> 'required',
               'title2' => 'required',
               'title3' => 'required',
               'title4' => 'required',
               'header1'=> 'required',
               'header2' => 'required',
               'header3' => 'required',
               'header4' => 'required',
               'header5'=> 'required',
               'header6'=> 'required',
               'subheader1'=> 'required',
               'subheader2' => 'required',
               'subheader3' => 'required',
               'subheader4' => 'required',
               'subheader5'=> 'required',
               'subheader6'=> 'required',
               'middle_title1' => 'required',
               'middle_title2' => 'required',
               'middle_title3' => 'required',
               'footer1'=> 'required',
               'footer2' => 'required',
               'footer3' => 'required',
               'footer4' => 'required',
               'footer5' => 'required',   
           ],
           [
            'vtitle1.required' => 'Please enter in Vernacular title1 ',
            'vtitle2.required' => 'Please enter in Vernacular title2',
            'vtitle3.required' => 'Please enter in Vernacular title3',
            'vtitle4.required' => 'Please enter in Vernacular title4',
            'vheader1.required' => 'Please enter in Vernacular header1',
            'vheader2.required' => 'Please enter in Vernacular header2',
            'vheader3.required' => 'Please enter in Vernacular header3',
            'vheader4.required' => 'Please enter in Vernacular header4',
            'vheader5.required' => 'Please enter in Vernacular header5',
            'vsubheader1.required' => 'Please enter in Vernacular subheader1',
            'vsubheader2.required' => 'Please enter in Vernacular subheader2',
            'vsubheader3.required' => 'Please enter in Vernacular subheader3',
            'vsubheader4.required' => 'Please enter in Vernacular subheader4',
            'vsubheader5.required' => 'Please enter in Vernacular subheader5',
            'vmiddle_title1.required' => 'Please enter in Vernacular middle_title1',
            'vmiddle_title2.required' => 'Please enter in Vernacular middle_title2',
            'vmiddle_title3.required' => 'Please enter in Vernacular middle_title3',
            'vfooter1.required' => 'Please enter in Vernacular footer1',
            'vfooter2.required' => 'Please enter in Vernacular footer2',
            'vfooter3.required' => 'Please enter in Vernacular footer3',
            'vfooter4.required' => 'Please enter in Vernacular footer4',
            'vfooter5.required' => 'Please enter in Vernacular footer5',
            'vsubheader6.required' => 'Please enter in Vernacular subheader6',
            'vheader6.required' => 'Please enter in Vernacular header6', 

            'title1.required' => 'Please enter in  title1 ',
            'title2.required' => 'Please enter in  title2',
            'title3.required' => 'Please enter in  title3',
            'title4.required' => 'Please enter in  title4',
            'header1.required' => 'Please enter in  header1',
            'header2.required' => 'Please enter in  header2',
            'header3.required' => 'Please enter in  header3',
            'header4.required' => 'Please enter in  header4',
            'header5.required' => 'Please enter in  header5',
            'subheader1.required' => 'Please enter in  subheader1',
            'subheader2.required' => 'Please enter in  subheader2',
            'subheader3.required' => 'Please enter in  subheader3',
            'subheader4.required' => 'Please enter in  subheader4',
            'subheader5.required' => 'Please enter in  subheader5',
            'middle_title1.required' => 'Please enter in  middle_title1',
            'middle_title2.required' => 'Please enter in  middle_title2',
            'middle_title3.required' => 'Please enter in  middle_title3',
            'footer1.required' => 'Please enter in  footer1',
            'footer2.required' => 'Please enter in  footer2',
            'footer3.required' => 'Please enter in  footer3',
            'footer4.required' => 'Please enter in  footer4',
            'footer5.required' => 'Please enter in  footer5',
            'subheader6.required' => 'Please enter in  subheader6',
            'header6.required' => 'Please enter in  header6', 

        ]); 
       
        if ($validator->fails()) {     
            return redirect::back()
            ->withErrors($validator)
            ->withInput();
          }
             
        $nid= $this->xssClean->clean_input($request->input('id'));
       $record=getById('m_form7a_details','id',$nid); 
       DB::beginTransaction();
        try{   
            $data_u = array('vtitle1'=>$this->xssClean->clean_input($request->input('vtitle1')),
                    
                    'vtitle2'=>$this->xssClean->clean_input($request->input('vtitle2')),
                    'vtitle3'=>$this->xssClean->clean_input($request->input('vtitle3')),
                    'vtitle4'=>$this->xssClean->clean_input($request->input('vtitle4')),
                    'vheader1'=>$this->xssClean->clean_input($request->input('vheader1')),
                    'vheader2'=>$this->xssClean->clean_input($request->input('vheader2')),
                    'vheader3'=>$this->xssClean->clean_input($request->input('vheader3')),
                    'vheader4'=>$this->xssClean->clean_input($request->input('vheader4')),
                    'vheader5'=>$this->xssClean->clean_input($request->input('vheader5')),
                    'vheader6'=>$this->xssClean->clean_input($request->input('vheader6')),
                    'vsubheader1'=>$this->xssClean->clean_input($request->input('vsubheader1')),
                    'vsubheader2'=>$this->xssClean->clean_input($request->input('vsubheader2')),
                    'vsubheader3'=>$this->xssClean->clean_input($request->input('vsubheader3')),
                    'vsubheader4'=>$this->xssClean->clean_input($request->input('vsubheader4')),
                    'vsubheader5'=>$this->xssClean->clean_input($request->input('vsubheader5')),
                    'vsubheader6'=>$this->xssClean->clean_input($request->input('vsubheader6')),
                    'vmiddle_title1'=>$this->xssClean->clean_input($request->input('vmiddle_title1')),
                    'vmiddle_title2'=>$this->xssClean->clean_input($request->input('vmiddle_title2')),
                    'vmiddle_title3'=>$this->xssClean->clean_input($request->input('vmiddle_title3')),
                    'vfooter1'=>$this->xssClean->clean_input($request->input('vfooter1')),
                    'vfooter2'=>$this->xssClean->clean_input($request->input('vfooter2')),
                    'vfooter3'=>$this->xssClean->clean_input($request->input('vfooter3')),
                    'vfooter4'=>$this->xssClean->clean_input($request->input('vfooter4')),
                    'vfooter5'=>$this->xssClean->clean_input($request->input('vfooter5')),
                    'title1'=>$this->xssClean->clean_input($request->input('title1')),
                    'title2'=>$this->xssClean->clean_input($request->input('title2')),
                    'title3'=>$this->xssClean->clean_input($request->input('title3')),
                    'title4'=>$this->xssClean->clean_input($request->input('title4')),
                    'header1'=>$this->xssClean->clean_input($request->input('header1')),
                    'header2'=>$this->xssClean->clean_input($request->input('header2')),
                    'header3'=>$this->xssClean->clean_input($request->input('header3')),
                    'header4'=>$this->xssClean->clean_input($request->input('header4')),
                    'header5'=>$this->xssClean->clean_input($request->input('header5')),
                    'header6'=>$this->xssClean->clean_input($request->input('header6')),
                    'subheader1'=>$this->xssClean->clean_input($request->input('subheader1')),
                    'subheader2'=>$this->xssClean->clean_input($request->input('subheader2')),
                    'subheader3'=>$this->xssClean->clean_input($request->input('subheader3')),
                    'subheader4'=>$this->xssClean->clean_input($request->input('subheader4')),
                    'subheader5'=>$this->xssClean->clean_input($request->input('subheader5')),
                    'subheader6'=>$this->xssClean->clean_input($request->input('subheader6')),
                    'middle_title1'=>$this->xssClean->clean_input($request->input('middle_title1')),
                    'middle_title2'=>$this->xssClean->clean_input($request->input('middle_title2')),
                    'middle_title3'=>$this->xssClean->clean_input($request->input('middle_title3')),
                    'footer1'=>$this->xssClean->clean_input($request->input('footer1')),
                    'footer2'=>$this->xssClean->clean_input($request->input('footer2')),
                    'footer3'=>$this->xssClean->clean_input($request->input('footer3')),
                    'footer4'=>$this->xssClean->clean_input($request->input('footer4')),
                    'footer5'=>$this->xssClean->clean_input($request->input('footer5')),

                    'updated_at'=>date("Y-m-d H:i:s"),
                    'updated_by'=>$d->officername); 
                 
                updatedata('m_form7a_details','id',$record->id, $data_u);
              

        }
      catch(\Exception $e){
         DB::rollback();

         \Session::flash('error_mes', 'Please try again');
         return Redirect::back();
      }
        DB::commit();
             
      \Session::flash('success_mes', 'This Records Successfully Saved');
      return Redirect::to('acceo/ceo-form7A-details');

    }  // end function  

   
}  // end class   
