<?php

namespace App\models\Admin\Ro\Profile;
use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;

class ProfileModel extends Model
{
    protected $table = 'officer_login';
    protected $guarded  = [];
}