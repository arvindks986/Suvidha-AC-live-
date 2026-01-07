<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class ListOfSuccessfulCandidates implements FromView, ShouldAutoSize
{
	public $dataCaddidateWise;
    public $dataPartyWise;

    function __construct($dataCaddidateWise, $dataPartyWise) {

        $this->dataCaddidateWise = $dataCaddidateWise;
        $this->dataPartyWise = $dataPartyWise;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.list-of-successful-candidates',[
            'dataCaddidateWise' => $this->dataCaddidateWise,
            'dataPartyWise' => $this->dataPartyWise
        ]);
    }
}