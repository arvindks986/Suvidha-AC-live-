<?php
namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ExcelExport implements FromCollection, WithHeadings, WithEvents {

    public $heading;
    public $data;

    function __construct($heading, $data) {

        $this->heading = $heading;
        $this->data = $data;
    }

    // set the headings
    public function headings(): array
    {
        return $this->heading;
      
    }

    // freeze the first row with headings
    public function registerEvents(): array
    {
        return [            
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->freezePane('A2', 'A2');
            },
        ];
    }

    public function collection()
    {
        return  collect($this->data);
       
    }
}