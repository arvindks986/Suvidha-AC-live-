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
 
class MpartyExport implements FromCollection,WithHeadings, ShouldAutoSize,WithHeadingRow,WithEvents {

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
                    'Party Abbree in Hindi',
                    'Party Name In Hindi',
                    'Party type']
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
            $event->sheet->mergeCells('A1:F1');
            $event->sheet->getStyle('A1:F1')->getAlignment()->setWrapText(true);        
            $event->sheet->getStyle('A1:F1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $event->sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $event->sheet->getRowDimension('2')->setRowHeight(30);
             
            $event->sheet->getStyle('A2:F2')->getAlignment()->setWrapText(true);        
            $event->sheet->getStyle('A2:F2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $event->sheet->getStyle('A2:F2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $event->sheet->getRowDimension('2')->setRowHeight(30);
            },
       ];
        // return [            
        //     AfterSheet::class => function(AfterSheet $event) {
        //         $event->sheet->freezePane('A2', 'A2');
        //     },
        // ];
    }

    public function collection()
    {
        return  collect($this->data);
       
    }
}