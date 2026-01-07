<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class CandidateDataSummary implements FromView, ShouldAutoSize
{
	public $acdataarray;
	public $dfdataarray;
	public $candatawise;

    function __construct($acdataarray,$dfdataarray,$candatawise) {

        $this->acdataarray = $acdataarray;
        $this->dfdataarray = $dfdataarray;
        $this->candatawise = $candatawise;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.candidate-data-summary',[
            'acdataarray' => $this->acdataarray,
            'dfdataarray' => $this->dfdataarray,
            'candatawise' => $this->candatawise
        ]);
    }
}