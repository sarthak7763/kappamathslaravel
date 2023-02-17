<?php

namespace App\Imports;

use App\Question;
use App\Quiztopic;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;

class TheoryQuestionsImport implements WithMultipleSheets
{
    use WithConditionalSheets;

	protected $quizid_arr;
	protected $intstartrow;

	public function __construct(array $quizid_arr, int $intstartrow)
    {
        $this->quizid_arr = $quizid_arr;
        $this->intstartrow=$intstartrow;
    }

   
    public function conditionalSheets(): array
    {
        return [
            'TheoryQuizSample' => new TheoryQuizSheetImport($this->quizid_arr,$this->intstartrow),
        ];
    }

}
