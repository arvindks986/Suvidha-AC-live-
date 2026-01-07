<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use \PDF;

ini_set("memory_limit","1500M");
set_time_limit('360');
ini_set("pcre.backtrack_limit", "10000000");


class EciMPartyMasterData extends Controller
{
    public function GetEciMPartyData()
    {
      
        // Total records : 3046
        // Total New : 18
        // Total Modified : 3

        {
        if(Auth::check()){
            $user = Auth::user();
            $uid=$user->id;
            $user_data=$data = DB::table('officer_login')->where('id',$uid )->first();
            //   $d=$this->commonModel->getunewserbyuserid($user->id);
         // $rd='http://10.248.89.234/mpartywcf/Service1.svc/GET_MPARTY';
          $rd='http://164.100.128.76/mpartywcf/Service1.svc/GET_MPARTY';
          $postData = array();
                
                $handler = curl_init();
                curl_setopt($handler, CURLOPT_URL, $rd);
                //curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($postData));		
                curl_setopt($handler, CURLOPT_POST, true);
                curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
                $response_data = curl_exec($handler);
                //dd($response_data);
                $xml = simplexml_load_string($response_data, "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $json_upd = str_replace('{}', '""', $json);
                $array = json_decode($json_upd,TRUE);
            //dd($array['M_PARTY']);
            $api_data = [];

            DB::table('m_party_temp')->delete();

                foreach($array['M_PARTY'] as $data){
                
                    $date = $date2= '';
                    if($data['INSERT_DATE'])
                    {
                        $date=date_create($data['INSERT_DATE']);
                        $insert_date = date_format($date,"Y-m-d H:i:s");
                    }
                    else{
                        $insert_date='';
                    }

                    if($data['UPDATE_DATE'])
                    {
                        $date2=date_create($data['UPDATE_DATE']);
                        $update_date = date_format($date2,"Y-m-d H:i:s");
                    }
                    else{
                        $update_date='';
                    }
                    
                    // echo "<pre>";print_r($data['CCODE']);
                    // echo "<pre>";print_r($data['INSERT_DATE']);
                    
                   

                   
                    $api_data[] = [
                       
                        'CCODE' => $data['CCODE'],
                        'PARTYABBRE' => $data['PARTYABBRE'],
                        'PARTYHABBR' => $data['PARTYHABBR'],
                        'PARTYNAME' =>   $data['PARTYNAME'],
                        'PARTYHNAME' =>  $data['PARTYHNAME'],
                        'PARTYTYPE' =>  $data['PARTYTYPE'],
                        'PARTYSYM' => $data['PARTYSYM'],
                        'PARTYHFOCABBR' => $data['PARTYHFOCABBR'],
                        'PARTYHFOCNAME' => $data['PARTYHFOCNAME'],
                        'deleteflag' => $data['deleteflag'],
                        'party_typeid' => 0,
                        'remarks' => null,
                        'created_at' => $insert_date,
                        'added_created_at' => null,
                        'updated_at' => $update_date,
                        'added_updated_at' => null,
                        'created_by' => null,
                        'updated_by' => null,
                        'party_reg_date' => null
                    ];
                    
                }

                //  die;
        DB::table('m_party_temp')->insert($api_data);
               
        $results = DB::table('m_party_temp')->get()->toArray();
        $from_date=0;
        $from_to=0;
        $rp_str=0;         

        return view('admin.m_party.m_party_data',compact('results','user_data','from_date','from_to','rp_str'));   
                
            }  
        }
    }

    public function searchMPartyData(Request $request)
    {
        $user = Auth::user();
        $uid=$user->id;
        $user_data=$data = DB::table('officer_login')->where('id',$uid )->first();

        $data=$request->all();

        if($request->has('type'))
        {
            $type=$data['type'];
            $rp_str=str_replace("?","",$type);
        }
        else{
            $rp_str=0;
        }
        if($request->has('from'))
        {
            $from_date= date('Y-m-d',strtotime($request->from));
        }
        if($request->has('to'))
        {
            $from_to= date('Y-m-d',strtotime($request->to));
        }
        

        if(isset($from_date))
        {
            $from_date=$from_date;
        }
        else{
            $from_date=0;
        }
        if(isset($from_to))
        {
            $from_to=$from_to;
        }
        else{
            $from_to=0;
        }
        // echo $from_date;die;

        
        // echo "<pre>";print_r($from_date);die;

        if($from_date!='0' && $from_to!='0' && $rp_str=='0')
        {
            
            $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                    ->get()
                    ->toArray();
                   
        }
        else if($from_date!='0' && $from_to!='0' && $rp_str!='0')
        {
            
            $from_date  = date('Y-m-d',strtotime($request->from));
            $from_to    = date('Y-m-d',strtotime($request->to));
            if($rp_str=='modified')
            {
                $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }

            // echo "<pre>";print_r($results);die;
            
        }
        else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
        {
            if($rp_str=='modified')
            {
                $results = DB::table('m_party_temp')
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('m_party_temp')
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
        }
        else{
           
            $results = DB::table('m_party_temp')
                    ->get()
                    ->toArray();
        }

        // echo $from_date;
        // echo $from_to;
    //    echo "<pre>";print_r($data);die;
       
        return view('admin.m_party.m_party_data',compact('results','user_data','from_date','from_to','rp_str'));   
        
    }

    public function m_party_export_to_excel($from_date,$from_to,$rp_str)
    {

        $export_data[] = ['CCODE', 'Party Abbr','Party Abbr (Hindi)','Party name', 'Party name (Hindi)','Party type','Delete flag','Created at','Updated at'];
        $headings[]=[];

        if($from_date=='0' && $from_to=='0' && $rp_str=='0')
        {
            $results = DB::table('m_party_temp')
            ->get()
            ->toArray();
            
        }
        elseif($from_date!='0' && $from_to!='0' && $rp_str!='0')
        {
            
            if($rp_str=='modified')
            {
                $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                // echo "<pre>";print_r($results);die;
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
                         
        }
        else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
        {
            if($rp_str=='modified')
            {
                $results = DB::table('m_party_temp')
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('m_party_temp')
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
        }
        elseif($from_date!='0' && $from_to!='0' && $rp_str=='0')
        {
            
            $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                    ->get()
                    ->toArray();
                   
        }
        
        

        foreach ($results as $val) {

            if($val->created_at!='0000-00-00 00:00:00'){
                $created_at=date('d-m-Y',strtotime($val->created_at));
            }
            else{
                $created_at="";
            }
            if($val->updated_at!='0000-00-00 00:00:00'){
                $updated_at=date('d-m-Y',strtotime($val->updated_at));
            }
            else{
                $updated_at="";
            }
            $export_data[] = [
             $val->CCODE,
             $val->PARTYABBRE,
             $val->PARTYHABBR,
             $val->PARTYNAME,
             $val->PARTYHNAME,
             $val->PARTYTYPE,
             $val->deleteflag,
             $created_at,
             $updated_at,
               
            ];
          }


        $name_excel = 'm_party_data'.date('d-m-Y');
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    }

    public function m_party_export_to_pdf($from_date,$from_to,$rp_str)
    {
        
       if($from_date=='0' && $from_to=='0' && $rp_str=='0')
       {
           $results = DB::table('m_party_temp')
           ->get()
           ->toArray();
           
       }
       elseif($from_date!='0' && $from_to!='0' && $rp_str!='0')
       {
           
           if($rp_str=='modified')
           {
               $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
               ->where('updated_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
               // echo "<pre>";print_r($results);die;
           }
           elseif($rp_str=='new')
           {
               $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
               ->where('created_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
           }
                        
       }
       else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
       {
           if($rp_str=='modified')
           {
               $results = DB::table('m_party_temp')
               ->where('updated_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
               
           }
           elseif($rp_str=='new')
           {
               $results = DB::table('m_party_temp')
               ->where('created_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
           }
       }
       elseif($from_date!='0' && $from_to!='0' && $rp_str=='0')
       {
           
           $results = DB::table('m_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                   ->get()
                   ->toArray();
                  
       }
        // echo $rp_str;die;
        // echo "<pre>";print_r($results);die;
        $pdf = \PDF::loadView('admin.m_party.m_party_data_pdf',compact('results'));
        return $pdf->download('m_party_data'.date('d-m-Y').'.pdf');
    }

    public static function getTotalMPartyRecords()
    {
        $results = DB::table('m_party_temp')
        ->count();
        return $results;
    }
    public static function EciMPartyNewData()
    {
        $results = DB::table('m_party_temp')
        ->where('created_at','<>','0000-00-00 00:00:00')
        ->count();
        return $results;
    }
    public static function EciMPartyModifiedData()
    {
        $results = DB::table('m_party_temp')
        ->where('updated_at','<>','0000-00-00 00:00:00')
        ->count();
        return $results;
    }



    // for d party 
    public function GetEciDPartyData()
    {
      
        // Total records : 69
        // Total New : 0
        // Total Modified : 0

        {
        if(Auth::check()){
            $user = Auth::user();
            $uid=$user->id;
            $user_data=$data = DB::table('officer_login')->where('id',$uid )->first();
            //   $d=$this->commonModel->getunewserbyuserid($user->id);
         // $rd='http://10.248.89.234/mpartywcf/Service1.svc/GET_MPARTY';
          $rd='http://164.100.128.76/mpartywcf/Service1.svc/GET_DPARTY';
          $postData = array();
                
                $handler = curl_init();
                curl_setopt($handler, CURLOPT_URL, $rd);
                //curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($postData));		
                curl_setopt($handler, CURLOPT_POST, true);
                curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
                $response_data = curl_exec($handler);
                //dd($response_data);
                $xml = simplexml_load_string($response_data, "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $json_upd = str_replace('{}', '""', $json);
                $array = json_decode($json_upd,TRUE);
            // dd($array['D_PARTY']);
            $api_data = [];

            DB::table('d_party_temp')->delete();

                foreach($array['D_PARTY'] as $data){
                
                    $date = $date2= '';
                    if($data['INSERT_DATE'])
                    {
                        $date=date_create($data['INSERT_DATE']);
                        $insert_date = date_format($date,"Y-m-d H:i:s");
                    }
                    else{
                        $insert_date='';
                    }

                    if($data['UPDATE_DATE'])
                    {
                        $date2=date_create($data['UPDATE_DATE']);
                        $update_date = date_format($date2,"Y-m-d H:i:s");
                    }
                    else{
                        $update_date='';
                    }
                    
                   
                    // echo "<pre>";print_r($data['INSERT_DATE']);
                    
                   

                   
                    $api_data[] = [
                       
                        'ccode' => $data['ccode'],
                        'PARTYABBRE' => $data['PARTYABBRE'],
                        'ST_CODE' => $data['ST_CODE'],
                        'PARTYSYM' =>   $data['PARTYSYM'],

                   
                        'created_at' => $insert_date,
          
                        'updated_at' => $update_date,
                
                    ];
                    
                    
                 
                    
                }
                // echo "<pre>";print_r($api_data);
                //  die;
                DB::table('d_party_temp')->insert($api_data);
               

                
                
          
        $results = DB::table('d_party_temp')->get()->toArray();
        $from_date=0;
        $from_to=0;
        $rp_str=0;         

        return view('admin.m_party.d_party_data',compact('results','user_data','from_date','from_to','rp_str'));   
                
            }   // end dashboard function
        }




    }

    public function searchDPartyData(Request $request)
    {
        $user = Auth::user();
        $uid=$user->id;
        $user_data=$data = DB::table('officer_login')->where('id',$uid )->first();

        $data=$request->all();

      

        
        
        if($request->has('type'))
        {
            $type=$data['type'];
            $rp_str=str_replace("?","",$type);
        }
        else{
            $rp_str=0;
        }
        if($request->has('from'))
        {
            $from_date= date('Y-m-d',strtotime($request->from));
        }
        if($request->has('to'))
        {
            $from_to= date('Y-m-d',strtotime($request->to));
        }
        

        if(isset($from_date))
        {
            $from_date=$from_date;
        }
        else{
            $from_date=0;
        }
        if(isset($from_to))
        {
            $from_to=$from_to;
        }
        else{
            $from_to=0;
        }
        // echo $from_date;die;

        
        // echo "<pre>";print_r($from_date);die;

        if($from_date!='0' && $from_to!='0' && $rp_str=='0')
        {
            
            $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                    ->get()
                    ->toArray();
                   
        }
        else if($from_date!='0' && $from_to!='0' && $rp_str!='0')
        {
            
            $from_date  = date('Y-m-d',strtotime($request->from));
            $from_to    = date('Y-m-d',strtotime($request->to));
            if($rp_str=='modified')
            {
                $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }

            // echo "<pre>";print_r($results);die;
            
        }
        else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
        {
            if($rp_str=='modified')
            {
                $results = DB::table('d_party_temp')
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('d_party_temp')
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
        }
        else{
           
            $results = DB::table('d_party_temp')
                    ->get()
                    ->toArray();
        }

        // echo $from_date;
        // echo $from_to;
    //    echo "<pre>";print_r($data);die;
       
        return view('admin.m_party.d_party_data',compact('results','user_data','from_date','from_to','rp_str'));   
        
    }

    public static function getTotalDPartyRecords()
    {
        $results = DB::table('d_party_temp')
        ->count();
        return $results;
    }
    public static function EciDPartyNewData()
    {
        $results = DB::table('d_party_temp')
        ->where('created_at','<>','0000-00-00 00:00:00')
        ->count();
        return $results;
    }
    public static function EciDPartyModifiedData()
    {
        $results = DB::table('d_party_temp')
        ->where('updated_at','<>','0000-00-00 00:00:00')
        ->count();
        return $results;
    }

 


    public function d_party_export_to_excel($from_date,$from_to,$rp_str)
    {

        $export_data[] = ['CCODE', 'Party Abbr','ST_CODE','PARTYSYM','Created at','Updated at'];
        $headings[]=[];

        if($from_date=='0' && $from_to=='0' && $rp_str=='0')
        {
            $results = DB::table('d_party_temp')
            ->get()
            ->toArray();
            
        }
        elseif($from_date!='0' && $from_to!='0' && $rp_str!='0')
        {
            
            if($rp_str=='modified')
            {
                $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                // echo "<pre>";print_r($results);die;
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
                         
        }
        else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
        {
            if($rp_str=='modified')
            {
                $results = DB::table('d_party_temp')
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('d_party_temp')
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
        }
        elseif($from_date!='0' && $from_to!='0' && $rp_str=='0')
        {
            
            $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                    ->get()
                    ->toArray();
                   
        }
        
        

        foreach ($results as $val) {

            if($val->created_at!='0000-00-00 00:00:00'){
                $created_at=date('d-m-Y',strtotime($val->created_at));
            }
            else{
                $created_at="";
            }
            if($val->updated_at!='0000-00-00 00:00:00'){
                $updated_at=date('d-m-Y',strtotime($val->updated_at));
            }
            else{
                $updated_at="";
            }

          

            $export_data[] = [
             $val->ccode,
             $val->PARTYABBRE,
             $val->ST_CODE,
             $val->PARTYSYM,
             $created_at,
             $updated_at,
               
            ];
          }


        $name_excel = 'd_party_data'.date('d-m-Y');
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    }

    public function d_party_export_to_pdf($from_date,$from_to,$rp_str)
    {
        
       if($from_date=='0' && $from_to=='0' && $rp_str=='0')
       {
           $results = DB::table('d_party_temp')
           ->get()
           ->toArray();
           
       }
       elseif($from_date!='0' && $from_to!='0' && $rp_str!='0')
       {
           
           if($rp_str=='modified')
           {
               $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
               ->where('updated_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
               // echo "<pre>";print_r($results);die;
           }
           elseif($rp_str=='new')
           {
               $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
               ->where('created_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
           }
                        
       }
       else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
       {
           if($rp_str=='modified')
           {
               $results = DB::table('d_party_temp')
               ->where('updated_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
               
           }
           elseif($rp_str=='new')
           {
               $results = DB::table('d_party_temp')
               ->where('created_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
           }
       }
       elseif($from_date!='0' && $from_to!='0' && $rp_str=='0')
       {
           
           $results = DB::table('d_party_temp')->whereBetween('created_at',[$from_date,$from_to])
                   ->get()
                   ->toArray();
                  
       }
        // echo $rp_str;die;
        // echo "<pre>";print_r($results);die;
        $pdf = \PDF::loadView('admin.m_party.d_party_data_pdf',compact('results'));
        return $pdf->download('d_party_data'.date('d-m-Y').'.pdf');
    }


    //for m symbol 
    public function GetEciMSymbolData()
    {
      
        // Total records : 311
        // Total New : 0
        // Total Modified : 0

        {
        if(Auth::check()){
            $user = Auth::user();
            $uid=$user->id;
            $user_data=$data = DB::table('officer_login')->where('id',$uid )->first();
            //   $d=$this->commonModel->getunewserbyuserid($user->id);
         // $rd='http://10.248.89.234/mpartywcf/Service1.svc/GET_MPARTY';
          $rd='http://164.100.128.76/mpartywcf/Service1.svc/GET_MSYMBOL';
          $postData = array();
                
                $handler = curl_init();
                curl_setopt($handler, CURLOPT_URL, $rd);
                //curl_setopt($handler, CURLOPT_POSTFIELDS, http_build_query($postData));		
                curl_setopt($handler, CURLOPT_POST, true);
                curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
                $response_data = curl_exec($handler);
                //dd($response_data);
                $xml = simplexml_load_string($response_data, "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $json_upd = str_replace('{}', '""', $json);
                $array = json_decode($json_upd,TRUE);
            // dd($array['M_SYMBOL']);
            $api_data = [];

            DB::table('m_symbol_temp')->delete();

                foreach($array['M_SYMBOL'] as $data){
                
                    $date = $date2= '';
                    if($data['INSERT_DATE'])
                    {
                        $date=date_create($data['INSERT_DATE']);
                        $insert_date = date_format($date,"Y-m-d H:i:s");
                    }
                    else{
                        $insert_date='';
                    }

                    if($data['UPDATE_DATE'])
                    {
                        $date2=date_create($data['UPDATE_DATE']);
                        $update_date = date_format($date2,"Y-m-d H:i:s");
                    }
                    else{
                        $update_date='';
                    }
                    
                   
                    // echo "<pre>";print_r($data);
                    
                   

                   
                    $api_data[] = [
                       
                        'SYMBOL_NO' => $data['SYMBOL_NO'],
                        'SYMBOL_DES' => $data['SYMBOL_DES'],
                        'SYMBOL_HDES' => $data['SYMBOL_HDES'],
                        'SYMBOL_BMP' =>   $data['SYMBOL_BMP'],
                        'SYMBOL_HFOCDES'=>$data['SYMBOL_HFOCDES'],
                        'Ind_Symbol'=>$data['Ind_Symbol'],
                        'Symbol_Img'=>$data['Symbol_Img'],
                        'CONTENT_TYPE'=>$data['CONTENT_TYPE'],
                        'created_at' => $insert_date,
                        'updated_at' => $update_date,
                
                    ];
                    
                    
                 
                    
                }
                // echo "<pre>";print_r($api_data);
                //  die;
                DB::table('m_symbol_temp')->insert($api_data);
               

                
                
          
        $results = DB::table('m_symbol_temp')->get()->toArray();
        $from_date=0;
        $from_to=0;
        $rp_str=0;         

        return view('admin.m_party.m_symbol_data',compact('results','user_data','from_date','from_to','rp_str'));   
                
            }   
        }




    }

    public static function getTotalMSymbolRecords()
    {
        $results = DB::table('m_symbol_temp')
        ->count();
        return $results;
    }
    public static function EciMSymbolNewData()
    {
        $results = DB::table('m_symbol_temp')
        ->where('created_at','<>','0000-00-00 00:00:00')
        ->count();
        return $results;
    }
    public static function EciMSymbolModifiedData()
    {
        $results = DB::table('m_symbol_temp')
        ->where('updated_at','<>','0000-00-00 00:00:00')
        ->count();
        return $results;
    }

    public function searchMSymbolData(Request $request)
    {
        $user = Auth::user();
        $uid=$user->id;
        $user_data=$data = DB::table('officer_login')->where('id',$uid )->first();

        $data=$request->all();

      

        
        
        if($request->has('type'))
        {
            $type=$data['type'];
            $rp_str=str_replace("?","",$type);
        }
        else{
            $rp_str=0;
        }
        if($request->has('from'))
        {
            $from_date= date('Y-m-d',strtotime($request->from));
        }
        if($request->has('to'))
        {
            $from_to= date('Y-m-d',strtotime($request->to));
        }
        

        if(isset($from_date))
        {
            $from_date=$from_date;
        }
        else{
            $from_date=0;
        }
        if(isset($from_to))
        {
            $from_to=$from_to;
        }
        else{
            $from_to=0;
        }
        // echo $from_date;die;

        
        // echo "<pre>";print_r($from_date);die;

        if($from_date!='0' && $from_to!='0' && $rp_str=='0')
        {
            
            $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
                    ->get()
                    ->toArray();
                   
        }
        else if($from_date!='0' && $from_to!='0' && $rp_str!='0')
        {
            
            $from_date  = date('Y-m-d',strtotime($request->from));
            $from_to    = date('Y-m-d',strtotime($request->to));
            if($rp_str=='modified')
            {
                $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }

            // echo "<pre>";print_r($results);die;
            
        }
        else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
        {
            if($rp_str=='modified')
            {
                $results = DB::table('m_symbol_temp')
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('m_symbol_temp')
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
        }
        else{
           
            $results = DB::table('m_symbol_temp')
                    ->get()
                    ->toArray();
        }

        // echo $from_date;
        // echo $from_to;
    //    echo "<pre>";print_r($data);die;
       
        return view('admin.m_party.m_symbol_data',compact('results','user_data','from_date','from_to','rp_str'));   
        
    }


    public function MSymbol_export_to_excel($from_date,$from_to,$rp_str)
    {

        $export_data[] = ['SYMBOL_NO', 'SYMBOL_DES','SYMBOL_HDES','SYMBOL_BMP','SYMBOL_HFOCDES','Ind_Symbol','Created at','Updated at'];
        $headings[]=[];

        if($from_date=='0' && $from_to=='0' && $rp_str=='0')
        {
            $results = DB::table('m_symbol_temp')
            ->get()
            ->toArray();
            
        }
        elseif($from_date!='0' && $from_to!='0' && $rp_str!='0')
        {
            
            if($rp_str=='modified')
            {
                $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                // echo "<pre>";print_r($results);die;
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
                         
        }
        else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
        {
            if($rp_str=='modified')
            {
                $results = DB::table('m_symbol_temp')
                ->where('updated_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
                
            }
            elseif($rp_str=='new')
            {
                $results = DB::table('m_symbol_temp')
                ->where('created_at','<>','0000-00-00 00:00:00')
                ->get()
                ->toArray();
            }
        }
        elseif($from_date!='0' && $from_to!='0' && $rp_str=='0')
        {
            
            $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
                    ->get()
                    ->toArray();
                   
        }
        
        

        foreach ($results as $val) {

            if($val->created_at!='0000-00-00 00:00:00'){
                $created_at=date('d-m-Y',strtotime($val->created_at));
            }
            else{
                $created_at="";
            }
            if($val->updated_at!='0000-00-00 00:00:00'){
                $updated_at=date('d-m-Y',strtotime($val->updated_at));
            }
            else{
                $updated_at="";
            }

         


            $export_data[] = [
             $val->SYMBOL_NO,
             $val->SYMBOL_DES,
             $val->SYMBOL_HDES,
             $val->SYMBOL_BMP,
             $val->SYMBOL_HFOCDES,
             $val->Ind_Symbol,
             $created_at,
             $updated_at,
               
            ];
          }


        $name_excel = 'm_symbol_data'.date('d-m-Y');
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    }

    public function MSymbol_export_to_pdf($from_date,$from_to,$rp_str)
    {
        
       if($from_date=='0' && $from_to=='0' && $rp_str=='0')
       {
           $results = DB::table('m_symbol_temp')
           ->get()
           ->toArray();
           
       }
       elseif($from_date!='0' && $from_to!='0' && $rp_str!='0')
       {
           
           if($rp_str=='modified')
           {
               $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
               ->where('updated_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
               // echo "<pre>";print_r($results);die;
           }
           elseif($rp_str=='new')
           {
               $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
               ->where('created_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
           }
                        
       }
       else if($from_date=='0' && $from_to=='0' && $rp_str!='0')
       {
           if($rp_str=='modified')
           {
               $results = DB::table('m_symbol_temp')
               ->where('updated_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
               
           }
           elseif($rp_str=='new')
           {
               $results = DB::table('m_symbol_temp')
               ->where('created_at','<>','0000-00-00 00:00:00')
               ->get()
               ->toArray();
           }
       }
       elseif($from_date!='0' && $from_to!='0' && $rp_str=='0')
       {
           
           $results = DB::table('m_symbol_temp')->whereBetween('created_at',[$from_date,$from_to])
                   ->get()
                   ->toArray();
                  
       }
        // echo $rp_str;die;
        // echo "<pre>";print_r($results);die;
        $pdf = \PDF::loadView('admin.m_party.m_symbol_data_pdf',compact('results'));
        return $pdf->download('m_symbol_data'.date('d-m-Y').'.pdf');
    }

  
}
