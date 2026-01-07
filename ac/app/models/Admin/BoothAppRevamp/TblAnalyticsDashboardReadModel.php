<?php namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;

class TblAnalyticsDashboardReadModel extends Model
{
  protected $table = 'tbl_analytics_dashboard';
	
  // for local connection
  //protected $connection = 'booth_revamp';
  
  // for live connection
  protected $connection = 'booth_revamp_test_write';
  
  
  
  



}
