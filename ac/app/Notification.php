<?php
namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
class Notification extends Authenticatable
{
  use HasApiTokens, Notifiable;
  use SoftDeletes;

/**
* The attributes that are mass assignable.
*
* @var array
*/

protected $timestamp=true;
protected $fillable = [
'authority_login_id','user_login_id', 'text','complaint_id','fcm_id', 'photo', 'type', 'title', 'status', 'data', 'marked'
];
protected $dates = ['deleted_at'];
/**
* The attributes that should be hidden for arrays.
*
* @var array
*/
}