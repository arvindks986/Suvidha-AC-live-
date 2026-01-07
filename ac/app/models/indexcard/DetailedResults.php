<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class DetailedResults implements FromView, ShouldAutoSize
{
	public $dataArr;
	public $all_state_Data;
	public $state_name;

    function __construct($dataArr,$all_state_Data,$state_name) {

        $this->dataArr = $dataArr;
        $this->all_state_Data = $all_state_Data;
        $this->state_name = $state_name;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.detailed-results',[
            'dataArr' => $this->dataArr,
            'all_state_Data' => $this->all_state_Data,
            'state_name' => $this->state_name
        ]);
    }
}