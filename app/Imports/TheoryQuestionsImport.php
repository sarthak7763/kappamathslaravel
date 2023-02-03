<?php

namespace App\Imports;

use App\Question;
use App\Quiztopic;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class TheoryQuestionsImport implements OnEachRow, WithHeadingRow
{
   public function onRow(Row $row)
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

            Question::updateOrCreate(
                [
                    'topic_id' => $row['quiz_id'],
                    'question' => $row['question'],
                ],
                [
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
                ]
            );
        }
    }
}
