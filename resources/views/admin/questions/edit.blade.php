@extends('layouts.admin', [
  'page_header' => 'Question',
  'dash' => '',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => 'active',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Edit Question
          <a href="{{route('questions.show', $topic->id)}}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> 
            {{ __('Back') }}
          </a>
        </h3>
      <hr>
       {!! Form::model($question, ['method' => 'PATCH', 'action' => ['QuestionsController@update', $question->id], 'files' => true]) !!}
                     
        <div class="row">
          <div class="col-md-4">
            {!! Form::hidden('topic_id', $topic->id) !!}
            <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
              {!! Form::label('question', 'Question') !!}
              <span class="required">*</span>
              {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Question', 'rows'=>'8', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('question') }}</small>
            </div>
            <div class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
                {!! Form::label('answer', 'Correct Answer') !!}
                <span class="required">*</span>
                {!! Form::select('answer', array('A'=>'A', 'B'=>'B', 'C'=>'C', 'D'=>'D','E'=>'E','F'=>'F'),null, ['class' => 'form-control select2', 'required' => 'required', 'placeholder'=>'']) !!}
                <small class="text-danger">{{ $errors->first('answer') }}</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group{{ $errors->has('a') ? ' has-error' : '' }}">
              {!! Form::label('a', 'A - Option') !!}
              <span class="required">*</span>
              {!! Form::text('a', null, ['class' => 'form-control', 'placeholder' => 'Please Enter A Option', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('a') }}</small>
            </div>
            <div class="form-group{{ $errors->has('b') ? ' has-error' : '' }}">
              {!! Form::label('b', 'B - Option') !!}
              <span class="required">*</span>
              {!! Form::text('b', null, ['class' => 'form-control', 'placeholder' => 'Please Enter B Option', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('b') }}</small>
            </div>
            <div class="form-group{{ $errors->has('c') ? ' has-error' : '' }}">
              {!! Form::label('c', 'C - Option') !!}
              <span class="required">*</span>
              {!! Form::text('c', null, ['class' => 'form-control', 'placeholder' => 'Please Enter C Option', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('c') }}</small>
            </div>
            <div class="form-group{{ $errors->has('d') ? ' has-error' : '' }}">
              {!! Form::label('d', 'D - Option') !!}
              <span class="required">*</span>
              {!! Form::text('d', null, ['class' => 'form-control', 'placeholder' => 'Please Enter D Option', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('d') }}</small>
            </div>

            <div class="form-group{{ $errors->has('e') ? ' has-error' : '' }}">
              {!! Form::label('e', 'E - Option') !!}
              {!! Form::text('e', null, ['class' => 'form-control', 'placeholder' => 'Please Enter E Option']) !!}
              <small class="text-danger">{{ $errors->first('e') }}</small>
            </div>

            <div class="form-group{{ $errors->has('f') ? ' has-error' : '' }}">
              {!! Form::label('f', 'F - Option') !!}
              {!! Form::text('f', null, ['class' => 'form-control', 'placeholder' => 'Please Enter F Option']) !!}
              <small class="text-danger">{{ $errors->first('f') }}</small>
            </div>

          </div>
          <div class="col-md-4">
            <div class="form-group{{ $errors->has('code_snippet') ? ' has-error' : '' }}">
                {!! Form::label('code_snippet', 'Code Snippets') !!}
                {!! Form::textarea('code_snippet', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Code Snippets', 'rows' => '5']) !!}
                <small class="text-danger">{{ $errors->first('code_snippet') }}</small>
            </div>
            <div class="form-group{{ $errors->has('answer_ex') ? ' has-error' : '' }}">
                {!! Form::label('answer_exp', 'Answer Explanation') !!}
                {!! Form::textarea('answer_exp', null, ['class' => 'form-control',  'placeholder' => 'Please Enter Answer Explanation',  'rows' => '4']) !!}
                <small class="text-danger">{{ $errors->first('answer_ex') }}</small>
            </div>
          </div>
          <div class="col-md-12">
            <div class="extras-block">
              <h4 class="extras-heading">Images And Video For Question</h4>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('question_video_link') ? ' has-error' : '' }}">
                    {!! Form::label('question_video_link', 'Add Video To Question') !!}
                    {!! Form::text('question_video_link', null, ['class' => 'form-control', 'placeholder'=>'https://myvideolink.com/embed/..']) !!}
                    <small class="text-danger">{{ $errors->first('question_video_link') }}</small>
                    <p class="help">YouTube And Vimeo Video Support (Only Embed Code Link)</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('question_img') ? ' has-error' : '' }}">
                    {!! Form::label('question_img', 'Add Image In Question') !!}
                    {!! Form::file('question_img') !!}
                    <small class="text-danger">{{ $errors->first('question_img') }}</small>
                    <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="question_audio">Add Audio Explanation:</label>
                   <div class="form-group{{ $errors->has('question_audio') ? ' has-error' : '' }}">
                      <input type="text" value="{{ $question->question_audio }}" placeholder="http://" class="form-control" name="question_audio">
                   </div>
                   
                </div>
              </div>
            </div>
          </div>
        </div>
    
    
        <div class="btn-group pull-right">
          {!! Form::submit("Update", ['class' => 'btn btn-wave']) !!}
        </div>
    
    {!! Form::close() !!}
  </div>
</div>
@endsection