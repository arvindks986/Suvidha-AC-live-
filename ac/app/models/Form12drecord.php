<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Form12drecord extends Model
{
    

      protected $table = 'form12drecords';

      public $fillable = ['id','officer_id','st_code','dist','ac_no','pc_no','election_id','date','total_elector','distributed','rejected','pb_issued','recieved' ,'phase_id' ,'elector_type','created_at','updated_at'];

      
     public static function getrecord_ac($stcode){
      //  $object = Form12record::where('st_code', $stcode)->first();
        $data=DB::table('form12drecords')
                  
                ->select('*')
                ->where('st_code' , $stcode)
               //  ->where('ac_no' , $ac_no)
                ->get()->toArray();
        return $data;
        //return $object;
    }
    public static function getrecord_ac_st($stcode,$ac_no){
      //  $object = Form12record::where('st_code', $stcode)->first();
        $data=DB::table('form12drecords')
                  
                ->select('*')
                ->where('st_code' , $stcode)
                 ->where('ac_no' , $ac_no)
                ->get()->toArray();
        return $data;
        //return $object;
    }

    
     public static function getrecord_pbcast_ac($stcode){
      //  $object = Form12record::where('st_code', $stcode)->first();
        $data=DB::table('pb_vote_cast')
                 // ->crossJoin('pb_vote_cast')
                ->select('*')
                ->where('st_code' , $stcode)
                // ->where('ac_no' , $ac_no)
                ->get()->toArray();
        return $data;
        //return $object;
    }

     public static function getrecord_pc($stcode){
     
        $data=DB::table('form12drecords')
                ->select('*')
                ->where('st_code' , $stcode)
               //  ->where('pc_no' , $ac_no)
                ->get()->toArray();
        return $data;
        
    }


     public static function getrecord_pc_st($stcode,$ac_no){
      
        $data=DB::table('form12drecords')
                  
                ->select('*')
                ->where('st_code' , $stcode)
                 ->where('ac_no' , $ac_no)
                ->get()->toArray();
        return $data;
        
    }




              

}
