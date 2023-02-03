@extends('layouts.admin', [
  'page_header' => 'Quiz'
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
  
  <div class="margin-bottom">
    <a href="{{route('quiz-topics.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-wave btn-floating">Add Quiz</a>
  </div>

  <div class="box">
    <div class="box-body table-responsive">
      <table id="topicsTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Course</th>
            <th>Topic</th>
            <th>Sub Topic</th>
            <th>Quiz ID</th>
            <th>Quiz Type</th>
            <th>Quiz Title</th>
            <th>Description</th>
            <th>Per Question Mark</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($topics))
        <tbody>
         
        </tbody>
        @endif
      </table>
    </div>
  </div>
@endsection
@section('scripts')
<script type="text/javascript">
  
  $(document).on('click','.changestatusbtn',function(){
    var status=$(this).data('status');
    if(status==1)
    {
      $('.statusvalue').prop('checked',true);
    }
    else{
      $('.statusvalue').prop('checked',false);
    }
    
  });
  

$(function () {

    var filter_start_date="{{Request::input('filter_start_date')}}";
    var filter_end_date="{{Request::input('filter_end_date')}}";
    var table = $('#topicsTable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,

      ajax: {
            url: "{{ route('quiz-topics.index') }}",
            type: "GET",
            data: {
                "filter_start_date": filter_start_date,
                "filter_end_date": filter_end_date
            }
        },

      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'subject', name: 'subject'},
      {data: 'category', name: 'category'},
      {data: 'course_topic', name: 'course_topic'},
      {data: 'quiz_id', name: 'quiz_id'},
      {data: 'quiz_type', name: 'quiz_type'},
      {data: 'title', name: 'title'},
      {data: 'description', name: 'description'},
      {data: 'per_q_mark', name: 'per_q_mark'},
      {data: 'timer', name: 'timer'},
      {data: 'quiz_status', name: 'quiz_status'},
      {data: 'action', name: 'action',searchable: false,orderable: false}

      ]
    });

  });
  

</script>

@endsection

