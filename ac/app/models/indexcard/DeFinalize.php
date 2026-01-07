<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class DeFinalize implements FromView, ShouldAutoSize
{
	public $results;

    function __construct($results) {

        $this->results = $results;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.de-finalize',[
            'results' => $this->results
        ]);
    }
}