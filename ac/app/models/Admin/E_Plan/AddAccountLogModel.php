<?php

namespace App\models\Admin\E_Plan;

use Illuminate\Database\Eloquent\Model;

class AddAccountLogModel extends Model
{
    
    protected $table = 'eplan_account_update_log';

    protected $fillable = ['id', 'st_code', 'dist_no', 'account_payment_for','bank_name','account_name','account_mobile','account_email','account_number','account_type','account_ifsc','account_benificeary','updated_by','is_finalised','is_verified','created_at','updated_at'];
    

}