<?php
namespace App\models\Admin\BoothAppRevamp;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class PollEventReportExcel implements FromView, ShouldAutoSize
{
	public $data;

    function __construct($data) {

        $this->data = $data;
    }

    public function view(): View
    {	
	
	//dd($this->data);
        return view('admin.booth-app-revamp.Reports.poll-event-report-excel',$this->data);
    }
}