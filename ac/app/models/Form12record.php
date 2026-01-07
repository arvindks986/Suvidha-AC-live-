<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Form12record extends Model
{
    

      protected $table = 'form12records';

      public $fillable = ['id','officer_id','st_code','dist','ac_no','pc_no','election_id','applied_postal_ballot','issued_postal_ballot','vote_cast','created_at','updated_at'];

       public static function getrecord_ac($stcode){
      //  $object = Form12record::where('st_code', $stcode)->first();
        $data=DB::table('form12records')
        
                ->select('*')
                ->where('st_code' , $stcode)
                // ->where('ac_no' , $ac_no)
                ->get()->toArray();
        return $data;
        //return $object;
    }

     public static function getrecord_pc($stcode){
      //  $object = Form12record::where('st_code', $stcode)->first();
        $data=DB::table('form12records')
                ->select('*')
                ->where('st_code' , $stcode)
                // ->where('pc_no' , $ac_no)
                ->get()->toArray();
        return $data;
        //return $object;
    }




}
