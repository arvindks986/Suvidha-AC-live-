<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;

class IndexCardReport implements FromView, ShouldAutoSize
{
	
	public $getIndexCardDataCandidatesVotesACWise;
	public $getIndexCardDataACWise;
	public $st_code;
	public $ac;
	public $user_data;
	public $acinfo;

    function __construct($getIndexCardDataCandidatesVotesACWise,$getIndexCardDataACWise,$st_code,$ac,$user_data,$acinfo) {

        $this->getIndexCardDataCandidatesVotesACWise = $getIndexCardDataCandidatesVotesACWise;
        $this->getIndexCardDataACWise = $getIndexCardDataACWise;
        $this->st_code = $st_code;
        $this->ac = $ac;
        $this->user_data = $user_data;
        $this->acinfo = $acinfo;
    }

    public function view(): View
    {	
        return view('IndexCardReports.exports.indexcard-report',[
            'getIndexCardDataCandidatesVotesACWise' => $this->getIndexCardDataCandidatesVotesACWise,
            'getIndexCardDataACWise' => $this->getIndexCardDataACWise,
            'st_code' => $this->st_code,
            'ac' => $this->ac,
            'user_data' => $this->user_data,
            'acinfo' => $this->acinfo
        ]);
    }
}