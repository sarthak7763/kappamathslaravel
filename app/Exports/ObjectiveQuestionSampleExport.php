<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ObjectiveQuestionSampleExport implements FromArray, WithHeadings
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
            "a",
            "b",
            "c",
            "d",
            "answer",
            "answer_explaination",
            "question_image",
            "question_video_link",
            "answer_explaination_image",
            "answer_explaination_video_link"
        ];
    }
}