<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
 
class ReconizedpartyExport implements FromCollection,WithHeadings, ShouldAutoSize,WithHeadingRow,WithEvents {

    public $heading;
    public $data;

    function __construct($heading, $data) {

        $this->heading = $heading;
        $this->data = $data;
    }

    // set the headings
    public function headings(): array
    {
        //return $this->heading;
        return  [  [$this->heading],
                   ['Sr. No.',
                    'Party Abbree',
                    'Party Name',
                    'State Name',
                     ]
                ];
      
    }

    // freeze the first row with headings
    public function registerEvents(): array {
         $styleArray=['font'=>['bold'=>true,'size'=>12,],
               'borders' => [
               'allborders' => [
               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
               'color' => ['argb' => 'FFB3B3B3'],],],];
       return [
           AfterSheet::class => function(AfterSheet $event) use ($styleArray) {          
            $event->sheet->mergeCells('A1:D1');
            $event->sheet->getStyle('A1:D1')->getAlignment()->setWrapText(true);        
            $event->sheet->getStyle('A1:D1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $event->sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $event->sheet->getRowDimension('2')->setRowHeight(30);
             
            $event->sheet->getStyle('A2:D2')->getAlignment()->setWrapText(true);        
            $event->sheet->getStyle('A2:D2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $event->sheet->getStyle('A2:D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $event->sheet->getRowDimension('2')->setRowHeight(30);
            },
       ];
         
    }

    public function collection()
    {
        return  collect($this->data);
       
    }
}