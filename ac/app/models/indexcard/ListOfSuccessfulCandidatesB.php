<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class ListOfSuccessfulCandidatesB implements FromView, ShouldAutoSize
{
	public $arraydata;

    function __construct($arraydata) {

        $this->arraydata = $arraydata;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.list-of-successful-candidates-b',[
            'arraydata' => $this->arraydata
        ]);
    }
}