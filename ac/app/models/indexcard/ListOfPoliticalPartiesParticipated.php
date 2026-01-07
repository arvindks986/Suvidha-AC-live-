<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class ListOfPoliticalPartiesParticipated implements FromView, ShouldAutoSize
{
	public $dataArray;

    function __construct($dataArray) {

        $this->dataArray = $dataArray;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.list-of-political-parties-participated',[
            'dataArray' => $this->dataArray
        ]);
    }
}