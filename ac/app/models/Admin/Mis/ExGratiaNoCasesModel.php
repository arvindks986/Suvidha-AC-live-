<?php namespace App\models\Admin\Mis;

use Illuminate\Database\Eloquent\Model;
use DB, Auth;

class ExGratiaNoCasesModel extends Model
{
    
    protected $table = 'mis_exgratia_non_cases';

    public $fillable = ['officer_id'];		
}