<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class Annxure implements FromView, ShouldAutoSize
{
	public $postalvoteNew;
	public $actypecountNew;

    function __construct($postalvoteNew,$actypecountNew) {

        $this->postalvoteNew = $postalvoteNew;
        $this->actypecountNew = $actypecountNew;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.annxure',[
            'postalvoteNew' => $this->postalvoteNew,
            'actypecountNew' => $this->actypecountNew
        ]);
    }
}