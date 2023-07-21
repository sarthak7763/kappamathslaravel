@extends('layouts.admin', [
  'page_header' => "Quiz Editor"
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
  $question_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp

      @if($validationmessage!="" && isset($validationmessage['question']))
      @php $question_error=$validationmessage['question']; @endphp
      @else
      @php $question_error=""; @endphp
      @endif

  @endif
  


  <!-- Add Question Modal -->
            <div class="row">

              <div class="col-md-12">
                <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">                  
                  {!! Form::label('question', 'Question') !!}
                  <span class="required">*</span>
                  {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Question', 'rows'=>'8']) !!}
                  <small class="text-danger">{{ $question_error }}</small>
                </div>

        <div>
          <textarea class="form-control" rows="10" cols="30" id="get_question_preview" name="get_question_preview"></textarea>
        </div>

      </div>

          <div class="col-md-12">
            <div class="btn-group pull-right mt-4">
              <button class="btn btn-wave submitbtn" type="button">Submit</button>
            </div>
        </div>

    </div>

  <!-- Add Question Modal End -->

  

@endsection

@section('scripts')
<script type="text/javascript" src="{{ env('APP_URL') }}generic_wiris/wirisplugin-generic.js"></script>

<script type="text/javascript" src="{{ env('APP_URL') }}js/wirislib.js"></script>

<!-- Prism JS script to beautify the HTML code -->
    <script type="text/javascript" src="{{ env('APP_URL') }}js/prism.js"></script>

    <script type="text/javascript">

      $(document).on('click','.submitbtn',function(){
        updateQuestionFunction();
        var copyText = document.getElementById("get_question_preview");
        console.log('copyText',copyText);

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(decode(copyText.innerHTML));

        // Alert the copied text
        alert("Copied the text:");

      });

    function decode(str) {
      let txt = document.createElement("textarea");
      txt.innerHTML = str;
      return txt.value;
      }

    	
    </script>

@endsection