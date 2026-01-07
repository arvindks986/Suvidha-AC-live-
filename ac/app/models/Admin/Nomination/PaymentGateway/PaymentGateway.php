<?php

namespace App\models\Admin\Nomination\PaymentGateway;
use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $table = 'payment_gateway_config';
    protected $guarded  = [];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getCeoStatus($st_code='') {

        $pickup_status = PaymentGateway::where(['st_code'=>$st_code])->first();
        return $pickup_status;
    }

    
    public static function getenablestatus($ro, $ceo) {
        if($ro == 1 && $ceo == 1){
            return "Enabled";
        }elseif($ro == 1 && $ceo == 0) {
            return "Disabled";
        }elseif($ro == 0 && $ceo == 1){
            return "Disabled";
        }else{
            return "Disabled";
        }
    }
}