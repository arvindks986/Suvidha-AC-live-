<?php

namespace App\models\Admin\Nomination\PaymentGateway;
use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    protected $table = 'challan_payment';
    protected $guarded  = [];
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

}