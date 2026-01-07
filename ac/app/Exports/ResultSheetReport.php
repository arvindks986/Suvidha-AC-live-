<?php
namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;
 
class ResultSheetReport implements FromView, ShouldAutoSize
{
	public $result;

    function __construct($result) {

        $this->result = $result;
    }

    public function view(): View
    {	
        return view('admin.ResultSheet.result-report-excel',[
            'result' => $this->result
        ]);
    }
}