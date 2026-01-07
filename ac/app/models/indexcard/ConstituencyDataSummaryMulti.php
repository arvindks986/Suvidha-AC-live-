<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class ConstituencyDataSummaryMulti implements FromView, WithTitle, ShouldAutoSize
{
	
	public $val;

	public function __construct($val)
    {
        $this->val = $val;
    }
	
	
	  public function view(): View
    {	
        return view('IndexCardReports.exports.constituency-data-summary',[
            'val' => $this->val
        ]);
    }
	
	

    /**
     * @return string
     */
     public function title(): string
     {
         return $this->val['st_code'].'-'.$this->val['ac_no'].'-'.$this->val['AC_NAME'];
     }
		
}