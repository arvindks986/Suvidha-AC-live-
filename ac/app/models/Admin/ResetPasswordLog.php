<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class ResetPasswordLog extends Model
{
    protected $table = 'reset_password_log';

    public static function add_record($data){
	    $object = new ResetPasswordLog();
	    $object->officername = $data['officername'];
	    $object->mobile = $data['mobile'];
	    $object->email = $data['email'];
	    $object->save();
	}

}