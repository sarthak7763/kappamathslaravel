<?php

namespace App\Imports;

use App\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Question([
            'topic_id' => $row['topic_id'], 
            'question' => $row['question'], 
            'a' => $row['a'], 
            'b' => $row['b'], 
            'c' => $row['c'], 
            'd' => $row['d'],
            'e' => $row['e'] != '' ? $row['e'] : NULL,
            'f' => $row['f'] !='' ? $row['f'] : NULL, 
            'answer' => $row['answer'], 
            'code_snippet' => $row['code_snippet'] != '' ? $row['code_snippet'] : '-', 
            'answer_exp' => $row['answer_exp'] != '' ? $row['answer_exp'] : '-',
            'question_video_link' => $row['question_video_link'] != '' ? $row['question_video_link'] : NULL,
            'question_audio' => $row['question_audio'] != '' ? $row['question_audio'] : NULL
        ]);
    }
}
