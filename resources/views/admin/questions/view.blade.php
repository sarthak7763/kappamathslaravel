@extends('layouts.admin', [
  'page_header' => "View Quiz Questions"
])

@section('content')

<!-- Style for html code -->
  <link type="text/css" rel="stylesheet" href="{{ env('APP_URL') }}css/editor/prism.css" />

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

  <div class="que_wrap">
          
            <div class="que-grp">
            <div class="que-text">
              <label>Question</label>
              <textarea class="textarea" id="textareavalue" style="display: none;">{{html_entity_decode($question->question)}}</textarea>
              <p id="renderer" class="mathrender">
                
              </p>
            </div>
            <div class="ans-table">
            @if($topic->quiz_type==1)
            <div class="ans_op">
              <label>Option A</label>
              @if($question->option_status=="0")
              <textarea class="textarea" id="textareaoptionavalue" style="display: none;">{{html_entity_decode($question->a)}}</textarea>
              <p id="renderer_optiona" class="mathrender"></p>
              @endif
              @if($question->a_image!="")
              <img src="{{url('/')}}/images/questions/options/{{$question->a_image}}" alt="{{url('/')}}/images/questions/options/{{$question->a_image}}">
              @endif
            </div>
            @endif

            @if($topic->quiz_type==1)
            <div class="ans_op">
              <label>Option B</label>
              @if($question->option_status=="0")
              <textarea class="textarea" id="textareaoptionbvalue" style="display: none;">{{html_entity_decode($question->b)}}</textarea>
              <p id="renderer_optionb" class="mathrender"></p>
              @endif
              @if($question->b_image!="")
              <img src="{{url('/')}}/images/questions/options/{{$question->b_image}}" alt="{{url('/')}}/images/questions/options/{{$question->b_image}}">
              @endif
            </div>
            @endif

            @if($topic->quiz_type==1)
            <div class="ans_op">
              <label>Option C</label>
              @if($question->option_status=="0")
              <textarea class="textarea" id="textareaoptioncvalue" style="display: none;">{{html_entity_decode($question->c)}}</textarea>
              <p id="renderer_optionc" class="mathrender"></p>
              @endif
              @if($question->c_image!="")
              <img src="{{url('/')}}/images/questions/options/{{$question->c_image}}" alt="{{url('/')}}/images/questions/options/{{$question->c_image}}">
              @endif
            </div>
            @endif

            @if($topic->quiz_type==1)
            <div class="ans_op">
              <label>Option D</label>
              @if($question->option_status=="0")
              <textarea class="textarea" id="textareaoptiondvalue" style="display: none;">{{html_entity_decode($question->d)}}</textarea>
              <p id="renderer_optiond" class="mathrender"></p>
              @endif
            @if($question->d_image!="")
            <img src="{{url('/')}}/images/questions/options/{{$question->d_image}}" alt="{{url('/')}}/images/questions/options/{{$question->d_image}}">
            @endif
            </div>
            @endif

            @if($topic->quiz_type==1)
            <div class="Correct_ans">
              <label><strong> Correct Answer</strong></label>
              <p>
                {{$question->answer}}
              </p>
            </div>
            @endif

            <div class="result-gp">
              <label>Answer Explaination</label>
              <textarea class="textarea" id="textareaanswerexpvalue" style="display: none;">{{html_entity_decode($question->answer_exp)}}</textarea>
              <p id="renderer_answer_exp" class="mathrender">
                
              </p>
            </div>

          </div>
          </div>
          </div>

  

@endsection

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

@section('scripts')

<script type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>

    <script type="text/javascript">
      $(document).ready(function() {
      var textareavalue=$('#textareavalue').val();
    $("#renderer").empty();
    $("#renderer").append(textareavalue);
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer")[0]])

    var textareaoptionavalue=$('#textareaoptionavalue').val();
    $("#renderer_optiona").empty();
    $("#renderer_optiona").append(textareaoptionavalue);
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_optiona")[0]])

    var textareaoptionbvalue=$('#textareaoptionbvalue').val();
    $("#renderer_optionb").empty();
    $("#renderer_optionb").append(textareaoptionbvalue);
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_optionb")[0]])

    var textareaoptioncvalue=$('#textareaoptioncvalue').val();
    $("#renderer_optionc").empty();
    $("#renderer_optionc").append(textareaoptioncvalue);
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_optionc")[0]])

    var textareaoptiondvalue=$('#textareaoptiondvalue').val();
    $("#renderer_optiond").empty();
    $("#renderer_optiond").append(textareaoptiondvalue);
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_optiond")[0]])

    var textareaanswerexpvalue=$('#textareaanswerexpvalue').val();
    $("#renderer_answer_exp").empty();
    $("#renderer_answer_exp").append(textareaanswerexpvalue);
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_answer_exp")[0]])

});
    </script>
@endsection