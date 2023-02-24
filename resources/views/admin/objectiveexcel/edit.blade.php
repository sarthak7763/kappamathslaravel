@extends('layouts.admin', [
  'page_header' => 'Objective Excel Instructions'
])

@section('content')

@if (session()->has('success'))
    <div class="alert alert-success">
        {!! session()->get('success')!!}        
    </div>
  @endif


  @if (session()->has('error'))
      <div class="alert alert-danger">
          {!! session()->get('error')!!}        
      </div>
  @endif

  @php 
  $quiz_id_error="";
  $question_error="";
  $a_error="";
  $b_error="";
  $c_error="";
  $d_error="";
  $correct_answer_error="";
  $answer_explaination_error="";
  $question_image_error="";
  $question_video_link_error="";
  $answer_explaination_image_error="";
  $answer_explaination_video_link_error="";
  @endphp

  @if (session()->has('valid_error'))
  @php $validationmessage=session()->get('valid_error'); @endphp

      @if($validationmessage!="" && isset($validationmessage['quiz_id']))
      @php $quiz_id_error=$validationmessage['quiz_id']; @endphp
      @else
      @php $quiz_id_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['question']))
      @php $question_error=$validationmessage['question']; @endphp
      @else
      @php $question_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['a']))
      @php $a_error=$validationmessage['a']; @endphp
      @else
      @php $a_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['b']))
      @php $b_error=$validationmessage['b']; @endphp
      @else
      @php $b_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['c']))
      @php $c_error=$validationmessage['c']; @endphp
      @else
      @php $c_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['d']))
      @php $d_error=$validationmessage['d']; @endphp
      @else
      @php $d_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['correct_answer']))
      @php $correct_answer_error=$validationmessage['correct_answer']; @endphp
      @else
      @php $correct_answer_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['answer_explaination']))
      @php $answer_explaination_error=$validationmessage['answer_explaination']; @endphp
      @else
      @php $answer_explaination_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['question_image']))
      @php $question_image_error=$validationmessage['question_image']; @endphp
      @else
      @php $question_image_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['question_video_link']))
      @php $question_video_link_error=$validationmessage['question_video_link']; @endphp
      @else
      @php $question_video_link_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['answer_explaination_image']))
      @php $answer_explaination_image_error=$validationmessage['answer_explaination_image']; @endphp
      @else
      @php $answer_explaination_image_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['answer_explaination_video_link']))
      @php $answer_explaination_video_link_error=$validationmessage['answer_explaination_video_link']; @endphp
      @else
      @php $answer_explaination_video_link_error=""; @endphp
      @endif

  @endif


  <div class="box">
    <div class="box-body">
        <h3>Edit Objective Excel Instructions
          <a href="{{ route('objective-excel-instructions.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::model($objectiveexceldata, ['method' => 'PATCH','enctype'=>'multipart/form-data', 'action' => ['ObjectiveExcelController@update', $objectiveexceldata->id]]) !!}

      <div class="row">

           <div class="col-md-6">
            <div class="form-group{{ $errors->has('quiz_id') ? ' has-error' : '' }}">
              {!! Form::label('quiz_id', 'Quiz ID') !!}
              {!! Form::textarea('quiz_id', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$quiz_id_error}}</small>
            </div>
          </div> 

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
              {!! Form::label('question', 'Question') !!}
              {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$question_error}}</small>
            </div>
          </div> 

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('a') ? ' has-error' : '' }}">
              {!! Form::label('a', 'A') !!}
              {!! Form::textarea('a', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$a_error}}</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('b') ? ' has-error' : '' }}">
              {!! Form::label('b', 'B') !!}
              {!! Form::textarea('b', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$b_error}}</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('c') ? ' has-error' : '' }}">
              {!! Form::label('c', 'C') !!}
              {!! Form::textarea('c', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$c_error}}</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('d') ? ' has-error' : '' }}">
              {!! Form::label('d', 'D') !!}
              {!! Form::textarea('d', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$d_error}}</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('correct_answer') ? ' has-error' : '' }}">
              {!! Form::label('correct_answer', 'Correct Answer') !!}
              {!! Form::textarea('correct_answer', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$correct_answer_error}}</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('answer_explaination') ? ' has-error' : '' }}">
              {!! Form::label('answer_explaination', 'Answer Explaination') !!}
              {!! Form::textarea('answer_explaination', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$answer_explaination_error}}</small>
            </div>
          </div> 

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('question_image') ? ' has-error' : '' }}">
              {!! Form::label('question_image', 'Question Image') !!}
              {!! Form::textarea('question_image', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$question_image_error}}</small>
            </div>
          </div> 

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('question_video_link') ? ' has-error' : '' }}">
              {!! Form::label('question_video_link', 'Question Video Link') !!}
              {!! Form::textarea('question_video_link', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$question_video_link_error}}</small>
            </div>
          </div> 

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('answer_explaination_image') ? ' has-error' : '' }}">
              {!! Form::label('answer_explaination_image', 'Answer Explaination Image') !!}
              {!! Form::textarea('answer_explaination_image', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$answer_explaination_image_error}}</small>
            </div>
          </div> 

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('answer_explaination_video_link') ? ' has-error' : '' }}">
              {!! Form::label('answer_explaination_video_link', 'Answer Explaination Video Link') !!}
              {!! Form::textarea('answer_explaination_video_link', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{$answer_explaination_video_link_error}}</small>
            </div>
          </div> 

          </div>
        </div>

        <div class="btn-group pull-right">
          {!! Form::submit("Save", ['class' => 'btn btn-wave']) !!}
        </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection