<?php

namespace App\Exports;

use App\Question;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;

class TheoryQuestionSampleExport implements FromArray, WithHeadings,WithEvents
{
    protected $questionarray;

    public function __construct(array $questionarray)
    {
        $this->questionarray = $questionarray;
    }

    public function array(): array
    {
        return $this->questionarray;
    }

    public function headings(): array
    {
        return [
            "quiz_id",
            "question",
            "answer_explaination",
            "question_image",
            "question_video_link",
            "answer_explaination_image",
            "answer_explaination_video_link"
        ];
    }

    public function registerEvents(): array
    {
        return [
            // handle by a closure.
            AfterSheet::class => function(AfterSheet $event) {

                // get layout counts (add 1 to rows for heading row)
                $row_count = 20;
                $column_count = 7;

                // set dropdown column
                $column_one='A';
                $column_two='D';
                $column_three='E';
                $column_four='F';
                $column_five='G';
                $column_six='B';
                // set dropdown options
                $options = [
                    'option 1',
                    'option 2',
                    'option 3',
                ];

                // set dropdown list for first data row
                $validation = $event->sheet->getCell("{$column_one}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST );
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setFormula1(sprintf('"%s"',implode(',',$options)));

                // clone validation to remaining rows
                for ($i = 2; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$column_one}{$i}")->setDataValidation(clone $validation);
                }

                // set dropdown list for first data row
                $validation_two = $event->sheet->getCell("{$column_two}2")->getDataValidation();
                $validation_two->setType(DataValidation::TYPE_CUSTOM );
                $validation_two->setErrorStyle(DataValidation::STYLE_STOP);
                $validation_two->setAllowBlank(false);
                $validation_two->setShowInputMessage(true);
                $validation_two->setShowErrorMessage(true);
                $validation_two->setErrorTitle('Input error');
                $validation_two->setError('Please enter valid URL.');
                $validation_two->setFormula1('isurl({$column_two}2)');

                // clone validation two to remaining rows
                for ($i = 2; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$column_two}{$i}")->setDataValidation(clone $validation_two);
                }

                // set dropdown list for first data row
                $validation_three = $event->sheet->getCell("{$column_three}2")->getDataValidation();
                $validation_three->setType(DataValidation::TYPE_WHOLE );
                $validation_three->setOperator(DataValidation::OPERATOR_GREATERTHAN);
                $validation_three->setErrorStyle(DataValidation::STYLE_STOP);
                $validation_three->setAllowBlank(false);
                $validation_three->setShowInputMessage(true);
                $validation_three->setShowErrorMessage(true);
                $validation_three->setErrorTitle('Input error');
                $validation_three->setError('Number is not allowed.');
                $validation_three->setFormula1(10);

                // clone validation two to remaining rows
                for ($i = 2; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$column_three}{$i}")->setDataValidation(clone $validation_three);
                }

                // set dropdown list for first data row
                $validation_four = $event->sheet->getCell("{$column_four}2")->getDataValidation();
                $validation_four->setType(DataValidation::TYPE_CUSTOM );
                $validation_four->setErrorStyle(DataValidation::STYLE_STOP);
                $validation_four->setAllowBlank(false);
                $validation_four->setShowInputMessage(true);
                $validation_four->setShowErrorMessage(true);
                $validation_four->setErrorTitle('Input error');
                $validation_four->setError('Please enter valid URL.');
                $validation_four->setFormula1('isurl({$column_four}2)');

                // clone validation two to remaining rows
                for ($i = 2; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$column_four}{$i}")->setDataValidation(clone $validation_four);
                }

                 // set dropdown list for first data row
                $validation_five = $event->sheet->getCell("{$column_five}2")->getDataValidation();
                $validation_five->setType(DataValidation::TYPE_WHOLE );
                $validation_five->setOperator(DataValidation::OPERATOR_GREATERTHAN);
                $validation_five->setErrorStyle(DataValidation::STYLE_STOP);
                $validation_five->setAllowBlank(false);
                $validation_five->setShowInputMessage(true);
                $validation_five->setShowErrorMessage(true);
                $validation_five->setErrorTitle('Input error');
                $validation_five->setError('Number is not allowed.');
                $validation_five->setFormula1(10);

                // clone validation two to remaining rows
                for ($i = 2; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$column_five}{$i}")->setDataValidation(clone $validation_five);
                }

                  // set dropdown list for first data row
                $validation_six = $event->sheet->getCell("{$column_six}2")->getDataValidation();
                $validation_six->setType(DataValidation::TYPE_TEXTLENGTH );
                $validation_six->setOperator(DataValidation::OPERATOR_NOTEQUAL);
                $validation_six->setErrorStyle(DataValidation::STYLE_STOP);
                $validation_six->setAllowBlank(false);
                $validation_six->setShowInputMessage(true);
                $validation_six->setShowErrorMessage(true);
                $validation_six->setErrorTitle('Input error');
                $validation_six->setError('Please enter question title it cannot be blank.');
                $validation_six->setFormula1("");

                // clone validation two to remaining rows
                for ($i = 2; $i <= $row_count; $i++) {
                    $event->sheet->getCell("{$column_six}{$i}")->setDataValidation(clone $validation_six);
                }

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }


}