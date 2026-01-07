<?php 
namespace App\models\Counting;
use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class CountingPrintlogModel extends Model
{
    
    protected $table = 'counting_print_log';
    public static function clone_record($results){
         
         CountingPrintlogModel::insert($results);
        }
 
}