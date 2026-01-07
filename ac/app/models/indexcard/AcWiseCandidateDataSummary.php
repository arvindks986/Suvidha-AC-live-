<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class AcWiseCandidateDataSummary implements FromView, ShouldAutoSize
{
	public $dataAcType;
	public $cat;
	public $st_code;

    function __construct($dataAcType,$cat,$st_code) {

        $this->dataAcType = $dataAcType;
        $this->cat = $cat;
        $this->st_code = $st_code;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.ac-wise-candidate-data-summary',[
            'dataAcType' => $this->dataAcType,
            'cat' => $this->cat,
            'st_code' => $this->st_code
        ]);
    }
}