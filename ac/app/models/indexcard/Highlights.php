<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class Highlights implements FromView, ShouldAutoSize
{
	public $candidates;

    function __construct($candidates) {

        $this->candidates = $candidates;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.highlight',[
            'candidates' => $this->candidates
        ]);
    }
}