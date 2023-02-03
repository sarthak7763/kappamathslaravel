<?php

namespace App\Exports;

use App\Question;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TheoryQuestionSampleExport implements FromArray, WithHeadings
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
}