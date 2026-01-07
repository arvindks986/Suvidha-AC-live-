<?php
    namespace App;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
    {
    use Notifiable;
    protected $guard = 'admin';
    protected $table = 'officer_login';
    
    protected $fillable = [
        'officername', 'designation', 'name', 'ST_CODE', 'DIST_NO', 'AC_NO', 'PC_NO', 'password', 'email','Phone_no','officerlevel'
         ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

      public function officer_master()
        {
         return $this->hasOne('App\admin_master'); 
        }
}
