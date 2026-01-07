<?php
namespace App\models\indexcard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OtherAbbreviationsAndDescription implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        return view('IndexCardReports.exports.other-abbreviations-and-description');
    }
}