@extends('layouts.admin', [
  'page_header' => "Add Objective Quiz Questions / {$quiztopicdata->title}"
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
  $optiona_image_error="";
  $optionb_image_error="";
  $optionc_image_error="";
  $optiond_image_error="";
  $option_status_value=0;

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

      @if($validationmessage!="" && isset($validationmessage['option_status']))
      @php 
      $option_status_value=$validationmessage['option_status'];
      @endphp
      @else
      @php $option_status_value=0; 
      @endphp
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

      @if($validationmessage!="" && isset($validationmessage['optiona_image']))
      @php $optiona_image_error=$validationmessage['optiona_image']; @endphp
      @else
      @php $optiona_image_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['optionb_image']))
      @php $optionb_image_error=$validationmessage['optionb_image']; @endphp
      @else
      @php $optionb_image_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['optionc_image']))
      @php $optionc_image_error=$validationmessage['optionc_image']; @endphp
      @else
      @php $optionc_image_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['optiond_image']))
      @php $optiond_image_error=$validationmessage['optiond_image']; @endphp
      @else
      @php $optiond_image_error=""; @endphp
      @endif
  @endif
  


  <!-- Add Question Modal -->
        <form method="post" action="{{route('storeobjectivequiz')}}" enctype="multipart/form-data" id="add-form">
        {{ csrf_field() }}
            <div class="row">

              <div class="col-md-12">
                {!! Form::hidden('topic_id', $quiztopicdata->id) !!}
                <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">                  
                  {!! Form::label('question', 'Question') !!}
                  <span class="required">*</span>
                  {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Question', 'rows'=>'8']) !!}
                  <small class="text-danger">{{ $question_error }}</small>
                </div>

          <div style="display: none;">
          <textarea class="form-control" rows="10" cols="30" id="get_question_preview" name="get_question_preview"></textarea>

          <textarea class="form-control" rows="10" cols="30" id="get_question_preview_latex" name="get_question_preview_latex"></textarea>
        </div>

      </div>


      <div class="col-md-12">  
            <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
              <label for="">Images Only No Text: </label>
               <input type="checkbox" class="toggle-input" name="option_status" id="option_status" value="{{$option_status_value}}">
               <label for="option_status"></label>
              <br>
            </div>

            <input type="hidden" name="checkboxvalue" id="checkboxvalue" value="{{$option_status_value}}">
          </div> 

              <div id="optionswithtext">
              <div class="col-md-6">
                <div class="form-group{{ $errors->has('a') ? ' has-error' : '' }}">
                  {!! Form::label('a_option', 'A - Option') !!}
                  <span class="required">*</span>
                  {!! Form::textarea('a_option', null, ['class' => 'form-control', 'placeholder' => 'Please Enter A Option']) !!}
                  <small class="text-danger options_error">{{ $a_error }}</small>
                </div>

                <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_a_option_preview" name="get_a_option_preview"></textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_a_option_preview_latex" name="get_a_option_preview_latex"></textarea>
              </div>

              </div>

              <div class="col-md-6">
                <div class="form-group{{ $errors->has('b') ? ' has-error' : '' }}">
                  {!! Form::label('b_option', 'B - Option') !!}
                  <span class="required">*</span>
                  {!! Form::textarea('b_option', null, ['class' => 'form-control', 'placeholder' => 'Please Enter B Option']) !!}
                  <small class="text-danger options_error">{{ $b_error }}</small>
                </div>

                <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_b_option_preview" name="get_b_option_preview"></textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_b_option_preview_latex" name="get_b_option_preview_latex"></textarea>
              </div>

              </div>

              <div class="col-md-6">
                <div class="form-group{{ $errors->has('c') ? ' has-error' : '' }}">
                  {!! Form::label('c_option', 'C - Option') !!}
                  <span class="required">*</span>
                  {!! Form::textarea('c_option', null, ['class' => 'form-control', 'placeholder' => 'Please Enter C Option']) !!}
                  <small class="text-danger options_error">{{ $c_error }}</small>
                </div>

                 <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_c_option_preview" name="get_c_option_preview"></textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_c_option_preview_latex" name="get_c_option_preview_latex"></textarea>
              </div>

              </div>

              <div class="col-md-6">
                <div class="form-group{{ $errors->has('d') ? ' has-error' : '' }}">
                  {!! Form::label('d_option', 'D - Option') !!}
                  <span class="required">*</span>
                  {!! Form::textarea('d_option', null, ['class' => 'form-control', 'placeholder' => 'Please Enter D Option']) !!}
                  <small class="text-danger options_error">{{ $d_error }}</small>
                </div>

                 <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_d_option_preview" name="get_d_option_preview"></textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_d_option_preview_latex" name="get_d_option_preview_latex"></textarea>
              </div>

              </div>
          </div>

          <div id="optionswithnewdivimage"></div>

              <div class="col-md-12">
                <div class="form-group{{ $errors->has('answer_exp') ? ' has-error' : '' }}">
                  {!! Form::label('answer_exp', 'Answer Explanation') !!}
                  {!! Form::textarea('answer_exp', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Answer Explanation', 'rows' => '4']) !!}
                  <small class="text-danger">{{ $answer_exp_error }}</small>
                </div>

                <div style="display: none;">
                <textarea class="form-control" rows="10" cols="30" id="get_answer_exp_preview" name="get_answer_exp_preview"></textarea>

                <textarea class="form-control" rows="10" cols="30" id="get_answer_exp_preview_latex" name="get_answer_exp_preview_latex"></textarea>
              </div>

              </div>

              <div class="col-md-12">  
                <div class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
                    {!! Form::label('answer', 'Correct Answer') !!}
                    <span class="required">*</span>
                    {!! Form::select('answer', array('A'=>'A', 'B'=>'B', 'C'=>'C', 'D'=>'D'),null, ['class' => 'form-control select2', 'placeholder'=>'']) !!}
                    <small class="text-danger">{{ $answer_error }}</small>
                </div>
              </div>
              
              <div class="col-md-6">
              <div class="extras-block bg-whte">
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
                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('question_img') ? ' has-error' : '' }}">
                        {!! Form::label('question_img', 'Add Image To Question') !!}
                        {!! Form::file('question_img') !!}
                        <small class="text-danger">{{ $question_img_error}}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                      <div id="preview_image_question_div">
                      <img id="preview-image-question" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                    </div>

                    </div>
                  </div>
                </div>
                </div>

                <div class="col-md-6">
              <div class="extras-block bg-whte">
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

                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('answer_explaination_img') ? ' has-error' : '' }}">
                        {!! Form::label('answer_explaination_img', 'Add Image To Answer Explaination') !!}
                        {!! Form::file('answer_explaination_img') !!}
                        <small class="text-danger">{{ $answer_explaination_img_error}}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                      <div id="preview_image_answer_div">
                      <img id="preview-image-answer" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                    </div>
                    </div>

                  </div>
                </div>
                </div>

                <div id="optionswithimages">
                <div class="row">
                  <div class="col-md-12">

                    <div class="col-md-6">
              <div class="extras-block bg-whte">
                  <h4 class="extras-heading">Option A Image</h4>
                <div class="row">
                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('optiona_image') ? ' has-error' : '' }}">
                        {!! Form::label('optiona_image', 'Option A Image') !!}
                        {!! Form::file('optiona_image') !!}
                        <small class="text-danger options_error_image">{{$optiona_image_error}}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                      <div id="preview_image_optiona_div">
                      <img id="preview-image-optiona" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                    </div>
                    </div>

                  </div>
                </div>
                </div>

                <div class="col-md-6">
              <div class="extras-block bg-whte">
                  <h4 class="extras-heading">Option B Image</h4>
                <div class="row">
                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('optionb_image') ? ' has-error' : '' }}">
                        {!! Form::label('optionb_image', 'Option B Image') !!}
                        {!! Form::file('optionb_image') !!}
                        <small class="text-danger options_error_image">{{$optionb_image_error}}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                      <div id="preview_image_optionb_div">
                      <img id="preview-image-optionb" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                    </div>
                    </div>

                  </div>
                </div>
                </div>
                    
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-12">

                    <div class="col-md-6">
              <div class="extras-block bg-whte">
                  <h4 class="extras-heading">Option C Image</h4>
                <div class="row">
                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('optionc_image') ? ' has-error' : '' }}">
                        {!! Form::label('optionc_image', 'Option C Image') !!}
                        {!! Form::file('optionc_image') !!}
                        <small class="text-danger options_error_image">{{$optionc_image_error}}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                      <div id="preview_image_optionc_div">
                      <img id="preview-image-optionc" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                    </div>
                    </div>

                  </div>
                </div>
                </div>

                <div class="col-md-6">
              <div class="extras-block bg-whte">
                  <h4 class="extras-heading">Option D Image</h4>
                <div class="row">
                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('optiond_image') ? ' has-error' : '' }}">
                        {!! Form::label('optiond_image', 'Option D Image') !!}
                        {!! Form::file('optiond_image') !!}
                        <small class="text-danger options_error_image">{{$optiond_image_error}}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                      <div id="preview_image_optiond_div">
                      <img id="preview-image-optiond" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                    </div>
                    </div>

                  </div>
                </div>
                </div>
                    
                  </div>
                </div>
                </div> 

                <div class="col-md-12">
                      <div class="btn-group pull-right mt-4">
                        <button class="btn btn-wave submitbtn" type="button">Add</button>
                      </div>
                    </div>

              </div>

              

            
            <div class="row">
            
          </div>

        {!! Form::close() !!}

  <!-- Add Question Modal End -->

  

@endsection

@section('scripts')
<script type="text/javascript" src="{{ env('APP_URL') }}generic_wiris/wirisplugin-generic.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_optiona.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_optionb.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_optionc.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_optiond.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib_answer.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}mathml2latex-master/dist/mathml2latex.js"></script>

<!-- Prism JS script to beautify the HTML code -->
    <script type="text/javascript" src="{{ env('APP_URL') }}js/prism.js"></script>

    <script type="text/javascript">

    var checkboxvalueonload=$('#option_status').val();
    $('#checkboxvalue').val(checkboxvalueonload);
   if(checkboxvalueonload==1)
   {
      $('.options_error').html('');
   		$('#option_status').attr('checked','checked');
   		$('#optionswithtext').hide();

   		$(document).on('click','.submitbtn',function(){
	    	updateQuestionFunction();
	        updateanswerFunction();
	    	$('#add-form').submit();
    	});
   }
   else{
      $('.options_error_image').html('');
   		$('#optionswithtext').show();
   		var a_onload = $('#optionswithnewdivimage').html();
   		if(a_onload)
   		{
   		 var b_onload = $('#optionswithimages').html(a_onload);
			$('#optionswithnewdivimage').html('');
   		}
   		else{
   			$('#optionswithimages').show();
   		}
		
   		$(document).on('click','.submitbtn',function(){
	    	updateQuestionFunction();
	        updateoptionaFunction();
	        updateoptionbFunction();
	        updateoptioncFunction();
	        updateoptiondFunction();
	        updateanswerFunction();
	    	$('#add-form').submit();
    	});
   }


      $('#option_status').on('change', function(){
		   var checkboxvalue = this.checked ? 1 : 0;
		   $('#checkboxvalue').val(checkboxvalue);
       
		   if(checkboxvalue==1)
		   {
          $('.options_error').html('');
		   		$('#optionswithtext').hide();
		   		var a = $('#optionswithimages').html();
				var b = $('#optionswithnewdivimage').html(a);
				$('#optionswithimages').html('');

		   		$(document).on('click','.submitbtn',function(){
			    	updateQuestionFunction();
			        updateanswerFunction();
			    	$('#add-form').submit();
		    	});
		   }
		   else{
          $('.options_error_image').html('');
		   		$('#optionswithtext').show();
		   		var a = $('#optionswithnewdivimage').html();
		   		if(a)
		   		{
		   		 var b = $('#optionswithimages').html(a);
					$('#optionswithnewdivimage').html('');
		   		}
		   		else{
		   			$('#optionswithimages').show();
		   		}
				
		   		$(document).on('click','.submitbtn',function(){
			    	updateQuestionFunction();
			        updateoptionaFunction();
			        updateoptionbFunction();
			        updateoptioncFunction();
			        updateoptiondFunction();
			        updateanswerFunction();
			    	$('#add-form').submit();
		    	});
		   }
		   
		}).change();

    	
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

<script type="text/javascript">

  $(document).on('change','#optiona_image',function(){
    let optionareader = new FileReader();
    optionareader.onload = (e) => { 
      $('#preview-image-optiona').attr('src', e.target.result); 
    }
    optionareader.readAsDataURL(this.files[0]);
  });

</script>

<script type="text/javascript">

  $(document).on('change','#optionb_image',function(){
    let optionbreader = new FileReader();
    optionbreader.onload = (e) => { 
      $('#preview-image-optionb').attr('src', e.target.result); 
    }
    optionbreader.readAsDataURL(this.files[0]);
  });

</script>

<script type="text/javascript">

  $(document).on('change','#optionc_image',function(){
    let optioncreader = new FileReader();
    optioncreader.onload = (e) => { 
      $('#preview-image-optionc').attr('src', e.target.result); 
    }
    optioncreader.readAsDataURL(this.files[0]);
  });

</script>

<script type="text/javascript">

  $(document).on('change','#optiond_image',function(){
    let optiondreader = new FileReader();
    optiondreader.onload = (e) => { 
      $('#preview-image-optiond').attr('src', e.target.result); 
    }
    optiondreader.readAsDataURL(this.files[0]);
  });

</script>

@endsection