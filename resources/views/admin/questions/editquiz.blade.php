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
       {!! Form::model($question, ['method' => 'PATCH', 'id'=>'edit-form', 'action' => ['QuestionsController@updatetheoryquiz', $question->id], 'files' => true]) !!}
                     
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
                       <div id="preview_image_question_div">
                      @if($question->question_img!="")
                      <img id="preview-image-question" src="/images/questions/{{ $question->question_img }}" style="height: auto;width: 20%;">
                      @else
                      <img id="preview-image-question" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                      @endif
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
                      <div id="preview_image_answer_div">
                      @if($question->answer_explaination_img!="")
                      <img id="preview-image-answer" src="/images/questions/{{ $question->answer_explaination_img }}" style="height: auto;width: 20%;">
                      @else
                      <img id="preview-image-answer" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                      @endif
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

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_answer.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/prism.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}mathml2latex-master/dist/mathml2latex.js"></script>


    <script type="text/javascript">
      $(document).ready(function() {
      var textareaquestionvalue=$('#previeweditquestion').text();
      $("#question").append(textareaquestionvalue);

      var textareaanswerexpvalue=$('#previeweditanswerexp').text();
      $("#answer_exp").append(textareaanswerexpvalue);
  });
    </script>

    <script type="text/javascript">
      $(document).on('click','.submitbtn',function(){
        updateQuestionFunction();
        updateanswerFunction();
        $('#edit-form').submit();
      });
    </script>

     <script type="text/javascript">

  $(document).on('change','#question_img',function(){
    let questionreader = new FileReader();
    questionreader.onload = (e) => { 
      $('#preview-image-question').attr('src', e.target.result); 
    }
    questionreader.readAsDataURL(this.files[0]);
  });

</script>


<script type="text/javascript">

  $(document).on('change','#answer_explaination_img',function(){
    let answerreader = new FileReader();
    answerreader.onload = (e) => { 
      $('#preview-image-answer').attr('src', e.target.result); 
    }
    answerreader.readAsDataURL(this.files[0]);
  });

</script>

@endsection
