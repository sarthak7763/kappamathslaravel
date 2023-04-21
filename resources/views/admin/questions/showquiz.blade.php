@extends('layouts.admin', [
  'page_header' => "Theory Questions / {$topic->title} "
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
    <a href="{{route('questions.create', Request::segment(4))}}" ><button type="button" class="btn btn-wave">
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


      ajax: "{{ route('questions.showquiz', $topic->id) }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'question', name: 'question'},
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
     });

    $(document).on("click", ".paginate_button a", function(){    
      $('[id^=textareavalue_]').each(function(){
        var textareavalue = $(this).val();
        var id = $(this).attr("id").replace('textareavalue_','');
        $("#renderer_"+id).empty();
        $("#renderer_"+id).append(textareavalue);
        MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_"+id)[0]])
      });
    });

  });
  
  </script>
@endsection
