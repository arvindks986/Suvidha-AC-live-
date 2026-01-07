<?php
    namespace App\adminmodel;
    use Illuminate\Database\Eloquent\Model;

class PCMaster extends Model
{
    protected $table ="m_pc";
	protected $primaryKey ='PC_ID'; 
	protected $fillable = [
     	'ST_CODE',
		'PC_NO',
        'PC_NAME',
     	'PC_TYPE',     
     	'PC_NAME_HI',
     ]; 
	 
    protected $guarded = ['PC_ID'];
   public function state()
        {
        return $this->hasmany('App\model\StateMaster','ST_CODE','ST_CODE');
        }
}
