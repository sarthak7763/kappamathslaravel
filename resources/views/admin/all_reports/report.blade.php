<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>{{$user->name}}'s Report</title>
	<!-- Latest compiled and minified CSS -->
  {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> --}}
</head>
<body>
  <h1>{{$user->name}}</h1>
  <div class="container">
    <h2 class="text-center main-block-heading">{{$user->name}} ANSWER REPORT</h2>
    <h3 class="text-center main-block-heading">Quiz: {{$topic->title}}</h3>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Question</th>                  
          
          <th style="color: green;">Correct Answer</th>
          <th style="color: blue;">{{$user->name}} Answer</th>
          <th>Answer Explnation</th>
        </tr>
      </thead>
      <tbody>
        @php
          $questions = App\Question::where('topic_id', $topic->id)->get();
          $count_questions = $questions->count();
          $x = $count_questions;               
          $y = 1;
        @endphp

        @foreach($answers as $key=> $a)
        
          @if($a->user_answer != "0" && $topic->id == $a->topic_id)

            <tr>
              <td>{{ $a->question->question }}</td>
              <td>{{ $a->answer }}</td>
              <td>{{ $a->user_answer }}</td>
              <td>{{ $a->question->answer_exp }}</td>
            </tr>
            @php                
              $y++;
              if($y > $x){                 
                break;
              }
            @endphp
          @endif
        @endforeach    
      </tbody>
    </table>

    <table class="table table-bordered result-table">
        <thead>
          <tr>
            <th>Total Questions</th>
            <th>{{$user->name}}'s Marks</th>
            <th>Per Question Mark</th>
            <th>Total Marks</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{$count_questions}}</td>
            <td>
              @php
                $mark = 0;
                $correct = collect();
              @endphp
              @foreach ($answers as $answer)
                @if ($answer->answer == $answer->user_answer)
                  @php
                  $mark++;
                  @endphp
                @endif
              @endforeach
              @php
                $correct = $mark*$topic->per_q_mark;
              @endphp
              {{$correct}}
            </td>
            <td>{{$topic->per_q_mark}}</td>
            <td>{{$topic->per_q_mark*$count_questions}}</td>
          </tr>
        </tbody>
    </table>
  </div>
</body>
</html>