<?php

namespace App\Imports;

use App\Question;
use App\Quiztopic;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class TheoryQuizSheetImport implements ToModel, SkipsEmptyRows,WithValidation, WithHeadingRow, WithStartRow, WithBatchInserts
{

    use Importable;
    protected $quizid_arr;
    protected $intstartrow;

    public function __construct(array $quizid_arr, int $intstartrow)
    {
        $this->quizid_arr = $quizid_arr;
        $this->intstartrow=$intstartrow;
    }

   public function model(array $row)
    {
        $checkquiztopic=Quiztopic::where('id',$row['quiz_id'])->where('quiz_type','2')->get()->first();
        if($checkquiztopic)
        {
            if($row['answer_explaination']!="")
            {
                $answer_exp=$row['answer_explaination'];
            }
            else{
                $answer_exp="";
            }

            // Remote image URL
            if($row['question_image']!="")
            {
                $currentdate=date('Y-m-d H:i:s');
                $question_img = 'question_'.strtotime($currentdate).'.png';
                $filepath = 'images/questions/'.$question_img;
                file_put_contents($filepath, file_get_contents($row['question_image']));
            }
            else{
                $question_img="";
            }

            // Remote image URL
            if($row['answer_explaination_image']!="")
            {
                $currentdate=date('Y-m-d H:i:s');
                $answer_explaination_img = 'answer_'.strtotime($currentdate).'.png';
                $filepath = 'images/questions/'.$answer_explaination_img;
                file_put_contents($filepath, file_get_contents($row['answer_explaination_image']));
            }
            else{
                $answer_explaination_img="";
            }

            if($row['question_video_link']!="")
            {
                $checkvideo=checkvimeovideoid($row['question_video_link']);
                if($checkvideo['code']=="400")
                {
                  $question_video_link="";
                }
                else{
                  $question_video_link=$row['question_video_link'];
                }
            }
            else{
              $question_video_link="";
            }

            if($row['answer_explaination_video_link']!="")
            {
                $checkanswervideo=checkvimeovideoid($row['answer_explaination_video_link']);
                if($checkanswervideo['code']=="400")
                {
                  $answer_explaination_video_link="";
                }
                else{
                  $answer_explaination_video_link=$row['answer_explaination_video_link'];
                }
            }
            else{
              $answer_explaination_video_link="";
            }

              return new Question([
                'topic_id' => $row['quiz_id'],
                'question' => $row['question'],
                'a' => '',
                'b' => '',
                'c' => '',
                'd' => '',
                'answer' => '',
                'code_snippet'=>'',
                'answer_exp' =>$answer_exp,
                'question_img'=>$question_img,
                'question_video_link'=>$question_video_link,
                'answer_explaination_img'=>$answer_explaination_img,
                'answer_explaination_video_link'=>$answer_explaination_video_link,
                'question_status'=>1
            ]);

        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function headingRow(): int
    {
        return 2;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return $this->intstartrow;
    }

    public function rules(): array
    {
        return [
            'quiz_id' => [
                'required',
                Rule::in($this->quizid_arr),
            ],
            'question' => [
                'required',
                'distinct:strict',
                Rule::unique('questions'),
            ],
            'question_image' => [
               'nullable',
               'url'
            ],
            'question_video_link' => [
               'nullable',
               'numeric'
            ],
            'answer_explaination_image' => [
               'nullable',
               'url'
            ],
            'answer_explaination_video_link' => [
               'nullable',
               'numeric'
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'quiz_id.required' => 'Quiz ID is required',
            'quiz_id.in'=>'Invalid Quiz Id Value',
            'question.required' => 'Question is required',
            'question_image.url'=>'Please Enter Valid Question Image URL.',
            'question_video_link.numeric'=>'Question video link field should only contain numbers.',
            'answer_explaination_image.url'=>'Please Enter Valid Answer explaination Image URL.',
            'answer_explaination_video_link.numeric'=>'Answer explaination video link field should only contain numbers.'
        ];
    }

}
