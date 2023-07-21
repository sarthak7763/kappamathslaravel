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

     <!-- Move button -->
     <button type="button" class="btn btn-wave" id="moveQuestions">
      {{ __('Move')}}
    </button>

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
            <th>&nbsp</th>
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
  <div id="QuestionMovemoadl" class="delete-modal modal fade" role="dialog">
                  <div class="modal-dialog modal-sm">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="delete-icon"></div>
                      </div>

                       <form method="POST" action="{{ route('moveQtnAns') }}">
                       <input name="_token" type="hidden" value="{!! csrf_token() !!}" />
                      <div class="modal-body text-center">
                        <input type="hidden" name="checkmyids" id="checkmyids" value="">
                        <input type="hidden" name="quiz_type" id="quiz_type" value="2">
                        <h4 class="modal-heading">Are You Sure ?</h4>
                        <p>Do you really want to move the questions to some other Quiz.</p>

                        <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Select Quiz</label>
                            <br>
                              <select name="new_quiz_id" class="form control" >
                              @foreach($quiz_records as $key=>$val)
                                <option value="{{ $val['id'] }}">{{ $val['title'] }}</option>
                               @endforeach
                              </select>
                            <br>
                          </div>
                          </div>
                          </div>

                      </div>
                      <div class="modal-footer">
                          
                            <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
                            <button type="submit" class="btn btn-danger">Move</button>
                      </div>
                    </div>
                    </form>
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
      {data: 'check', name: 'check'},
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
  <script>  
   $(document).ready(function() {
          $("#moveQuestions").click(function(){
            if($("input[name='checkMultiple']").is(':checked'))
            {
              $('#QuestionMovemoadl').modal('show');
              var arr = [];
              $.each($("input[name='checkMultiple']:checked"), function(){
                  arr.push($(this).val());
              });

               var check_ids = arr.join(",");
               $('#checkmyids').val(check_ids); 
            }
            else{
              alert('please choose at least one question');
              $('#QuestionMovemoadl').modal('hide');
            }
               
               
          });
      });

    </script>
    
@endsection
