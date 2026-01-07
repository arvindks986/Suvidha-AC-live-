<?php namespace App\models\Admin\Nomination;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Session;
use App\models\Admin\Nomination\UserLogin;


class UserModel extends Model
{

  protected $table = 'user_data';

   public $fillable = ['id', 'mobileno'];

  public static function add_user($data = []){
  //dd($data);
    $user_login = UserLogin::firstorNew([
      'mobile' => $data['mobile']
    ]);
    $user_login->save();

    $sql = UserModel::firstorNew(['mobileno' => $data['mobile']]);
    $sql->user_login_id = $user_login->id;
    $result = $sql->save();
   // dd( $sql);
    if(!$result){
      return false;
    }
    return $sql;
	}

  public static function get_user($filter = []){
    $sql = UserModel::select("id");
    if(!empty($filter['mobileno'])){
      $sql->where('mobileno',$filter['mobileno']);
    }
    if(!empty($filter['email'])){
      $sql->where('email',$filter['email']);
    }
    $query = $sql->first();
    if(!$query){
      return false;
    }
    return $query;
  }

}