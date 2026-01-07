<?php
namespace App\adminmodel;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class NodalUser extends Authenticatable
{
     use HasApiTokens, Notifiable;
     protected $table = 'authority_login';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'authority_id','name', 'email', 'password','mobile','remember_token','role_id','otp','otp_attempt','login_flag','isActive','device_type','device_id','verify_otp','app_id','app_version','fcm_id',
    ];
 
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	/*public function user_master()
	{
		return $this->hasOne('App\user_master'); 
	}*/
}
