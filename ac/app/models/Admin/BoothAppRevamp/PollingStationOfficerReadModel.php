<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\SmsgatewayHelper;

class PollingStationOfficerReadModel extends Model
{
  protected $table = 'polling_station_officer';
  
  protected $connection = 'mysql_new_user_bthapp';

  protected $fillable = ['id', 'name', 'mobile_number', 'email', 'designation', 'role_id', 'role_level', 'lattitude', 'longitude', 'st_code', 'district_no', 'ac_no', 'ps_no', 'address', 'alloted_location', 'pin', 'otp', 'otp_attempt', 'otp_time', 'device_id', 'ip_address', 'session_id', 'api_token', 'is_login', 'login_time', 'logout_time', 'is_active', 'created_at', 'created_by', 'updated_at', 'updated_by', 'role_type', 'pro_override', 'is_testing', 'location_id', 'parent_sm_id', 'election_id', 'election_typeid', 'is_import','sector_id'];

  

}
