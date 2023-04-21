@extends('layouts.admin', [
  'page_header' => "Objective Questions / {$topic->title} "
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

  <!-- Buttons -->
  <div class="margin-bottom">
    <!-- Add Question -->
    <a href="{{route('questions.create', Request::segment(3))}}" ><button type="button" class="btn btn-wave">
      {{ __('Add Question')}}
    </button></a>

    <!-- Back Button -->
    <a href="{{route('questions.index')}}" class="btn btn-wave pull-right">
      <i class="fa fa-arrow-left"></i> 
      {{ __('Back') }}
    </a>
  </div>

  <!-- Index Table -->
  <div class="box">
    <div class="box-body table-responsive">
      <table id="questionsTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Questions</th>
            <th>A - Option</th>
            <th>B - Option</th>
            <th>C - Option</th>
            <th>D - Option</th>
            <th>Correct Answer</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
         @if ($questions)
        <tbody>
          
        </tbody>
        @endif
      </table>
    </div>
  </div>

@endsection

@section('scripts')

<script type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>

  <script>

    $(function () {

    var table = $('#questionsTable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('questions.show', $topic->id) }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'question', name: 'question'},
      {data: 'a', name: 'a'},
      {data: 'b', name: 'b'},
      {data: 'c', name: 'c'},
      {data: 'd', name: 'd'},
      {data: 'answer', name: 'answer'},
      {data: 'question_status', name: 'status'},
      {data: 'action', name: 'action',searchable: false,orderable: false}

      ]
    }).on( 'init', function () {
       $('[id^=textareavalue_]').each(function(){
        var textareavalue = $(this).val();
        var id = $(this).attr("id").replace('textareavalue_','');
        $("#renderer_"+id).empty();
        $("#renderer_"+id).append(textareavalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_"+id)[0]])
      });

      $('[id^=textareaoptionavalue_]').each(function(){
        var textareaoptionavalue = $(this).val();
        var id = $(this).attr("id").replace('textareaoptionavalue_','');
        $("#rendereroptiona_"+id).empty();
        $("#rendereroptiona_"+id).append(textareaoptionavalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#rendereroptiona_"+id)[0]])
      });

      $('[id^=textareaoptionbvalue_]').each(function(){
        var textareaoptionbvalue = $(this).val();
        var id = $(this).attr("id").replace('textareaoptionbvalue_','');
        $("#rendereroptionb_"+id).empty();
        $("#rendereroptionb_"+id).append(textareaoptionbvalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#rendereroptionb_"+id)[0]])
      });

      $('[id^=textareaoptioncvalue_]').each(function(){
        var textareaoptioncvalue = $(this).val();
        var id = $(this).attr("id").replace('textareaoptioncvalue_','');
        $("#rendereroptionc_"+id).empty();
        $("#rendereroptionc_"+id).append(textareaoptioncvalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#rendereroptionc_"+id)[0]])
      });

      $('[id^=textareaoptiondvalue_]').each(function(){
        var textareaoptiondvalue = $(this).val();
        var id = $(this).attr("id").replace('textareaoptiondvalue_','');
        $("#rendereroptiond_"+id).empty();
        $("#rendereroptiond_"+id).append(textareaoptiondvalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#rendereroptiond_"+id)[0]])
      });
    });

    $(document).on("click", ".paginate_button a", function(){  
      
      $('[id^=textareavalue_]').each(function(){
        var textareavalue = $(this).val();
        var id = $(this).attr("id").replace('textareavalue_','');
        $("#renderer_"+id).empty();
        $("#renderer_"+id).append(textareavalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_"+id)[0]])
      });

      $('[id^=textareaoptionavalue_]').each(function(){
        var textareaoptionavalue = $(this).val();
        var id = $(this).attr("id").replace('textareaoptionavalue_','');
        $("#rendereroptiona_"+id).empty();
        $("#rendereroptiona_"+id).append(textareaoptionavalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#rendereroptiona_"+id)[0]])
      });

      $('[id^=textareaoptionbvalue_]').each(function(){
        var textareaoptionbvalue = $(this).val();
        var id = $(this).attr("id").replace('textareaoptionbvalue_','');
        $("#rendereroptionb_"+id).empty();
        $("#rendereroptionb_"+id).append(textareaoptionbvalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#rendereroptionb_"+id)[0]])
      });

      $('[id^=textareaoptioncvalue_]').each(function(){
        var textareaoptioncvalue = $(this).val();
        var id = $(this).attr("id").replace('textareaoptioncvalue_','');
        $("#rendereroptionc_"+id).empty();
        $("#rendereroptionc_"+id).append(textareaoptioncvalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#rendereroptionc_"+id)[0]])
      });

      $('[id^=textareaoptiondvalue_]').each(function(){
        var textareaoptiondvalue = $(this).val();
        var id = $(this).attr("id").replace('textareaoptiondvalue_','');
        $("#rendereroptiond_"+id).empty();
        $("#rendereroptiond_"+id).append(textareaoptiondvalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#rendereroptiond_"+id)[0]])
      });

    });


  });

  

  </script>
@endsection
