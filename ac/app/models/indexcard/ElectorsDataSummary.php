<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class ElectorsDataSummary implements FromView, ShouldAutoSize
{
	public $totalvoteNew;
	public $electorsvotersdataNew;
	public $totalpostalvoteNew;
	public $notavoteNew;
	public $totalpostalvoterejectedNew;

    function __construct($totalvoteNew,$electorsvotersdataNew,$totalpostalvoteNew,$notavoteNew,$totalpostalvoterejectedNew) {

        $this->totalvoteNew = $totalvoteNew;
        $this->electorsvotersdataNew = $electorsvotersdataNew;
        $this->totalpostalvoteNew = $totalpostalvoteNew;
        $this->notavoteNew = $notavoteNew;
        $this->totalpostalvoterejectedNew = $totalpostalvoterejectedNew;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.electors-data-summary',[
            'totalvoteNew' => $this->totalvoteNew,
            'electorsvotersdataNew' => $this->electorsvotersdataNew,
            'totalpostalvoteNew' => $this->totalpostalvoteNew,
            'notavoteNew' => $this->notavoteNew,
            'totalpostalvoterejectedNew' => $this->totalpostalvoterejectedNew
        ]);
    }
}