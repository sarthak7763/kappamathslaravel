<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TheoryQuizTopicsExport implements FromArray, WithHeadings, WithTitle
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Quiz_id',
            'Title',
            'Course',
            'Course Topic',
            'Course Sub Topic'
        ];
    }

    public function title(): string
    {
        return 'TheoryQuizTopics';
    }
}
