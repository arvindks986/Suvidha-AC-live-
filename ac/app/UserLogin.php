<?php
namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserLogin extends Authenticatable
{
     use HasApiTokens, Notifiable;
     protected $table = 'user_login';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'candidate_id','authority_id','name','email','mobile','password','remember_token','access_token','role_id', 'otp','otp_attempt','otp_time','login_flag','registration_type','permission_request_status','party_id','login_access','isActive','device_type','device_id','added_at','created_at','added_update_at','updated_at','verify_otp','app_id'
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
