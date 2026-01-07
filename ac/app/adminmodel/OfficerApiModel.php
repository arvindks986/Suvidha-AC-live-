<?php

namespace App\adminmodel;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OfficerApiModel extends Model
{
    use HasApiTokens, Notifiable;
    protected $table = 'officer_login';
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
}
