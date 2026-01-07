<?php namespace App\models\Admin\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;

class UserLogin extends Model
{

  protected $table = 'user_login';

  public $fillable = ['id', 'mobile'];

  

}