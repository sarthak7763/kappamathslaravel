<?php

namespace App\Exports;

use App\Question;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use Maatwebsite\Excel\Concerns\WithTitle;

class ObjectiveQuizQuestionsSampleExport implements FromArray, WithHeadings,WithEvents,WithTitle
{
    protected $questionarray;
    protected $quizid_arr;

    public function __construct(array $questionarray,array $quizid_arr)
    {
        $this->questionarray = $questionarray;
        $this->quizid_arr = $quizid_arr;
    }

    public function array(): array
    {
        return $this->questionarray;
    }

    public function headings(): array
    {
        $headers1 = ['Instructions: DO NOT DELETE, ALTER, OR REMOVE THE HEADERS OR COLUMNS IN THIS WORKSHEET BEFORE UPLOADING.Add your data beginning in cell A9 below.Pay attention to the descriptions and restrictions for data in each of the columns.'];
        $headers2= [
            "quiz_id",
            "question",
            "a",
            "b",
            "c",
            "d",
            "correct_answer",
            "answer_explaination",
            "question_image",
            "question_video_link",
            "answer_explaination_image",
            "answer_explaination_video_link"
        ];

        return [$headers1,$headers2]; 
    }

    public function title(): string
    {
        return 'ObjectiveQuizSample';
    }

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function(AfterSheet $event) {

                // get layout counts (add 1 to rows for heading row)
                $column_count = 12;
                
                // set dropdown options
                $options = $this->quizid_arr;

                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('A1')->getFont()
                ->setSize(20)
                ->setBold(true)
                ->getColor()->setRGB('000000');

                $sheet->getStyle('A2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('A2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');

                $sheet->getStyle('B2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('B2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');

                $sheet->getStyle('C2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('C2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');

                $sheet->getStyle('D2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('D2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');

                $sheet->getStyle('E2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('E2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');

                $sheet->getStyle('F2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('F2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');

                $sheet->getStyle('G2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('G2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');  

                $sheet->getStyle('H2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('H2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');

                $sheet->getStyle('I2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('I2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60'); 

                $sheet->getStyle('J2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('J2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');

                $sheet->getStyle('K2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('K2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60'); 

                $sheet->getStyle('L2')->getFont()
                ->setSize(12)
                ->setBold(true)
                ->getColor()->setRGB('ffffff');

                $sheet->getStyle('L2')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('042a60');     

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    if($column=='A')
                    {
                        $event->sheet->getColumnDimension($column)->setAutoSize(false);
                        $event->sheet->getColumnDimension($column)->setWidth('8');
                    }
                    else{
                        $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            },
        ];
    }


}