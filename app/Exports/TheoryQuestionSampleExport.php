<?php

namespace App\Exports;

use App\Question;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TheoryQuestionSampleExport implements FromArray, WithMultipleSheets
{
    protected $questionarray;
    protected $quiz_topic_arr;
    protected $quizid_arr;

    public function __construct(array $questionarray,array $quiz_topic_arr,array $quizid_arr)
    {
        $this->questionarray = $questionarray;
        $this->quiz_topic_arr = $quiz_topic_arr;
        $this->quizid_arr = $quizid_arr;
    }

    public function array(): array
    {
        return $this->questionarray;
    }

    public function sheets(): array
    {
        $sheets = [
            new TheoryQuizQuestionsSampleExport($this->questionarray,$this->quizid_arr),
            new TheoryQuizTopicsExport($this->quiz_topic_arr),
        ];

        return $sheets;
    }


}