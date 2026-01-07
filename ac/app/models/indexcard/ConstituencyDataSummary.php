<?php
namespace App\models\indexcard;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use DB;

class ConstituencyDataSummary implements WithMultipleSheets
{
	use Exportable;
	public $finalArraynew;
    function __construct($finalArraynew) {

        $this->finalArraynew = $finalArraynew;
    }

	
	 /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach($this->finalArraynew as $key => $value){
            foreach($value as $key2 => $val){
				$sheets[] = new ConstituencyDataSummaryMulti($val);
			}
		}

        return $sheets;
    }
		
}