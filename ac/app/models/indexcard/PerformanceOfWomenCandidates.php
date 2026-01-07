<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class PerformanceOfWomenCandidates implements FromView, ShouldAutoSize
{
	public $dataArray;
	public $state_name;

    function __construct($dataArray,$state_name) {

        $this->dataArray = $dataArray;
        $this->state_name = $state_name;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.performance-of-women-candidates',[
            'dataArray' => $this->dataArray,
            'state_name' => $this->state_name
        ]);
    }
}