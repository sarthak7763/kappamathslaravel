@extends('layouts.admin', [
  'page_header' => 'Edit Objective Quiz Question'
])

@section('content')

<!-- Style for html code -->
  <link type="text/css" rel="stylesheet" href="{{ env('APP_URL') }}css/editor/prism.css" />

  <style>
  .tox-tinymce {
    width: 100% !important;
  }
  .btn.btn-wave {
    margin-bottom: 50px;
  }

  .wrs_btn {
  background-color: #4d4d4d;
  border: 0;
  border-radius: 4px;
  color: #FEFEFE;
  cursor: pointer;
  font-size: 20px;
  margin-top: 10px;
  outline:0;
  padding: 0px 12px;
  padding-right:15px;
  text-align: center;
  transition: background-color 0.2s ease;
  line-height: 2.4;
  vertical-align: middle;
  white-space: nowrap;
}

.wrs_btn:hover {
  background-color: #3d3d3d;
}

.wrs_btn_large {
  height: 46px;
  width: 160px;
}

</style>

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
  $a_error="";
  $b_error="";
  $c_error="";
  $d_error="";
  $answer_error="";
  $question_img_error="";
  $answer_exp_error="";
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

      @if($validationmessage!="" && isset($validationmessage['answer']))
      @php $answer_error=$validationmessage['answer']; @endphp
      @else
      @php $answer_error=""; @endphp
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
       {!! Form::model($question, ['method' => 'PATCH', 'id'=>'edit-form', 'action' => ['QuestionsController@update', $question->id], 'files' => true]) !!}
                     
        <div class="row">
          <div class="col-md-6">
            {!! Form::hidden('topic_id', $topic->id) !!}
            <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
              {!! Form::label('question', 'Question') !!}
              <span class="required">*</span>
              <p id="previeweditquestion" style="display:none;">{{html_entity_decode($question->question)}}</p>
              
              <textarea class="form-control" placeholder="Please Enter Question" rows="8" name="question" cols="50" id="question"></textarea>
              <small class="text-danger">{{ $question_error }}</small>
            </div>

          <div style="display: none;">
            <textarea class="form-control" rows="10" cols="30" id="get_question_preview" name="get_question_preview">{{html_entity_decode($question->question)}}</textarea>

            <textarea class="form-control" rows="10" cols="30" id="get_question_preview_latex" name="get_question_preview_latex">{{html_entity_decode($question->question_latex)}}</textarea>
          </div>

          </div>  
        
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('a') ? ' has-error' : '' }}">
              {!! Form::label('a_option', 'A - Option') !!}
              <span class="required">*</span>
              <p id="previeweditoptiona" style="display:none;">{{html_entity_decode($question->a)}}</p>

              {!! Form::textarea('a_option', null, ['class' => 'form-control', 'placeholder' => 'Please Enter A Option']) !!}
              <small class="text-danger">{{ $a_error }}</small>
            </div>

            <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_a_option_preview" name="get_a_option_preview">{{html_entity_decode($question->a)}}</textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_a_option_preview_latex" name="get_a_option_preview_latex">{{html_entity_decode($question->a_latex)}}</textarea>
              </div>

          </div>
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('b') ? ' has-error' : '' }}">
              {!! Form::label('b_option', 'B - Option') !!}
              <span class="required">*</span>
              <p id="previeweditoptionb" style="display:none;">{{html_entity_decode($question->b)}}</p>

              {!! Form::textarea('b_option', null, ['class' => 'form-control', 'placeholder' => 'Please Enter B Option']) !!}
              <small class="text-danger">{{ $b_error }}</small>
            </div>

            <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_b_option_preview" name="get_b_option_preview">{{html_entity_decode($question->b)}}</textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_b_option_preview_latex" name="get_b_option_preview_latex">{{html_entity_decode($question->b_latex)}}</textarea>
              </div>

          </div>
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('c') ? ' has-error' : '' }}">
              {!! Form::label('c_option', 'C - Option') !!}
              <span class="required">*</span>
              <p id="previeweditoptionc" style="display:none;">{{html_entity_decode($question->c)}}</p>

              {!! Form::textarea('c_option', null, ['class' => 'form-control', 'placeholder' => 'Please Enter C Option']) !!}
              <small class="text-danger">{{ $c_error }}</small>
            </div>

            <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_c_option_preview" name="get_c_option_preview">{{html_entity_decode($question->c)}}</textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_c_option_preview_latex" name="get_c_option_preview_latex">{{html_entity_decode($question->c_latex)}}</textarea>
              </div>

          </div>
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('d') ? ' has-error' : '' }}">
              {!! Form::label('d_option', 'D - Option') !!}
              <span class="required">*</span>

              <p id="previeweditoptiond" style="display:none;">{{html_entity_decode($question->d)}}</p>

              {!! Form::textarea('d_option', null, ['class' => 'form-control', 'placeholder' => 'Please Enter D Option']) !!}
              <small class="text-danger">{{ $d_error }}</small>
            </div>

            <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_d_option_preview" name="get_d_option_preview">{{html_entity_decode($question->d)}}</textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_d_option_preview_latex" name="get_d_option_preview_latex">{{html_entity_decode($question->d_latex)}}</textarea>
              </div>

          </div>
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
                {!! Form::label('answer', 'Correct Answer') !!}
                <span class="required">*</span>
                {!! Form::select('answer', array('A'=>'A', 'B'=>'B', 'C'=>'C', 'D'=>'D'),null, ['class' => 'form-control select2', 'placeholder'=>'']) !!}
                <small class="text-danger">{{ $answer_error }}</small>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('answer_ex') ? ' has-error' : '' }}">
                {!! Form::label('answer_exp', 'Answer Explanation') !!}

                <p id="previeweditanswerexp" style="display:none;">{{html_entity_decode($question->answer_exp)}}</p>

                <textarea class="form-control" placeholder="Please Enter Answer Explanation" rows="8" name="answer_exp" cols="50" id="answer_exp"></textarea>
                <small class="text-danger">{{ $answer_exp_error }}</small>
            </div>

             <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_answer_exp_preview" name="get_answer_exp_preview">{{html_entity_decode($question->answer_exp)}}</textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_answer_exp_preview_latex" name="get_answer_exp_preview_latex">{{html_entity_decode($question->answer_exp_latex)}}</textarea>
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
              <button class="btn btn-wave submitbtn" type="button">Update</button>
            </div>
          </div>

      </div>
 
    
    
        
    
    {!! Form::close() !!}
  </div>
</div>
@endsection

@section('scripts')

<script type="text/javascript" src="{{ env('APP_URL') }}generic_wiris/wirisplugin-generic.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_optiona.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_optionb.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_optionc.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_optiond.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_answer.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/prism.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}mathml2latex-master/dist/mathml2latex.js"></script>


    <script type="text/javascript">
      $(document).ready(function() {
      var textareaquestionvalue=$('#previeweditquestion').text();
      $("#question").append(textareaquestionvalue);

      var textareaoptionavalue=$('#previeweditoptiona').text();
      $("#a_option").append(textareaoptionavalue);

      var textareaoptionbvalue=$('#previeweditoptionb').text();
      $("#b_option").append(textareaoptionbvalue);

      var textareaoptioncvalue=$('#previeweditoptionc').text();
      $("#c_option").append(textareaoptioncvalue);

      var textareaoptiondvalue=$('#previeweditoptiond').text();
      $("#d_option").append(textareaoptiondvalue);

      var textareaanswerexpvalue=$('#previeweditanswerexp').text();
      $("#answer_exp").append(textareaanswerexpvalue);
	});
    </script>

    <script type="text/javascript">
      $(document).on('click','.submitbtn',function(){
        updateQuestionFunction();
        updateoptionaFunction();
        updateoptionbFunction();
        updateoptioncFunction();
        updateoptiondFunction();
        updateanswerFunction();
        $('#edit-form').submit();
      });
    </script>



@endsection
