<?php

namespace App\models\Affidavit;

use Illuminate\Database\Eloquent\Model;

class AffPendingCriminalCase extends Model
{
    protected $table = 'aff_pending_criminal_cases';
    protected $primaryKey ='id';
    protected $fillable = ['affidavit_id','candidate_id','fir_no','st_code','dist_no','police_station','police_station_address','appeal_application','acts','sections','offence_description','name_court_cognizance','case_no','date_cognizance','framed_charge','name_court_charges','not_applicable','date_charges','modified_on'];
}