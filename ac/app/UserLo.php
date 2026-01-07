<?php
namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserLO extends Authenticatable
{
     use HasApiTokens, Notifiable;
      protected $connection = 'suivhdaaclivetest';
     protected $table = 'user_login';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   protected $guarded = [];
 
    
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
