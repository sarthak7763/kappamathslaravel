@extends('layouts.admin', [
  'page_header' => 'Edit Theory Quiz Question'
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
  $topic_id_error="";
  $question_error="";
  $answer_exp_error="";
  $question_img_error="";
  $answer_explaination_img_error="";
  $question_video_link_error="";
  $answer_explaination_video_link_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp
      @if($validationmessage!="" && isset($validationmessage['topic_id']))
      @php $topic_id_error=$validationmessage['topic_id']; @endphp
      @else
      @php $topic_id_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['question']))
      @php $question_error=$validationmessage['question']; @endphp
      @else
      @php $question_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['answer_exp']))
      @php $answer_exp_error=$validationmessage['answer_exp']; @endphp
      @else
      @php $answer_exp_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['question_img']))
      @php $question_img_error=$validationmessage['question_img']; @endphp
      @else
      @php $question_img_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['answer_explaination_img']))
      @php $answer_explaination_img_error=$validationmessage['answer_explaination_img']; @endphp
      @else
      @php $answer_explaination_img_error=""; @endphp
      @endif

  @endif

  <div class="box">
    <div class="box-body">
        <h3>Edit Question
          <a href="{{route('questions.show', $topic->id)}}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> 
            {{ __('Back') }}
          </a>
        </h3>
      <hr>
       {!! Form::model($question, ['method' => 'PATCH', 'action' => ['QuestionsController@updatetheoryquiz', $question->id], 'files' => true]) !!}
                     
        <div class="row">
          <div class="col-md-6">
            {!! Form::hidden('topic_id', $topic->id) !!}
            <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
              {!! Form::label('question', 'Question') !!}
              <span class="required">*</span>
              {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Question', 'rows'=>'8']) !!}
              <small class="text-danger">{{ $question_error }}</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('answer_ex') ? ' has-error' : '' }}">
                {!! Form::label('answer_exp', 'Answer Explanation') !!}
                {!! Form::textarea('answer_exp', null, ['class' => 'form-control',  'placeholder' => 'Please Enter Answer Explanation',  'rows' => '8']) !!}
                <small class="text-danger">{{ $answer_exp_error }}</small>
            </div>
          </div>

         <div class="extras-block col-md-12">
                <h4 class="extras-heading">Video And Image For Question</h4>
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group{{ $errors->has('question_video_link') ? ' has-error' : '' }}">
                        {!! Form::label('question_video_link', 'Add Video To Question') !!}
                        {!! Form::text('question_video_link', null, ['class' => 'form-control']) !!}
                        <small class="text-danger">{{$question_video_link_error }}</small>
                        <p class="help">Please enter Vimeo Video ID</p>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group{{ $errors->has('question_img') ? ' has-error' : '' }}">
                        {!! Form::label('question_img', 'Add Image To Question') !!}
                        {!! Form::file('question_img') !!}
                        <small class="text-danger">{{ $question_img_error}}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="extras-block col-md-12">
                  <h4 class="extras-heading">Video And Image For Answer Explaination</h4>
                <div class="row">
                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('answer_explaination_video_link') ? ' has-error' : '' }}">
                        {!! Form::label('answer_explaination_video_link', 'Add Video To Answer Explaination') !!}
                        {!! Form::text('answer_explaination_video_link', null, ['class' => 'form-control']) !!}
                        <small class="text-danger">{{$answer_explaination_video_link_error }}</small>
                        <p class="help">Please enter Vimeo Video ID</p>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group{{ $errors->has('answer_explaination_img') ? ' has-error' : '' }}">
                        {!! Form::label('answer_explaination_img', 'Add Image To Answer Explaination') !!}
                        {!! Form::file('answer_explaination_img') !!}
                        <small class="text-danger">{{ $answer_explaination_img_error}}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                    </div>

                  </div>
                </div>

           <div class="col-md-6">
                  <div class="btn-group pull-right">
                    {!! Form::submit("Update", ['class' => 'btn btn-wave']) !!}
                  </div>
                </div>

        </div>
    
    
        
    
    {!! Form::close() !!}
  </div>
</div>
@endsection