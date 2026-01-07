<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class ConstituencyWiseDetailedResult implements FromView, ShouldAutoSize
{
	public $dataArr;

    function __construct($dataArr) {

        $this->dataArr = $dataArr;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.constituency-wise-detailed-result',[
            'dataArr' => $this->dataArr
        ]);
    }
}