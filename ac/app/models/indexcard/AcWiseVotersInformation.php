<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class AcWiseVotersInformation implements FromView, ShouldAutoSize
{
	public $electorsdata;
	public $electorsdata_total;

    function __construct($electorsdata,$electorsdata_total) {

        $this->electorsdata = $electorsdata;
        $this->electorsdata_total = $electorsdata_total;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.ac-wise-voters-information',[
            'electorsdata' => $this->electorsdata,
            'electorsdata_total' => $this->electorsdata_total
        ]);
    }
}