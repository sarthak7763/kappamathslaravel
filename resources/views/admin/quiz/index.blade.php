@extends('layouts.admin', [
  'page_header' => 'Quiz'
])

@section('content')
  <div class="margin-bottom">
    <a href="{{route('quiz-topics.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><button type="button" class="btn btn-wave">Add Topic</button></a>
  </div>

  <div class="box">
    <div class="box-body table-responsive">
      <table id="topicsTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Subject</th>
            <th>Category</th>
            <th>Course Topic</th>
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

    var table = $('#topicsTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('quiz-topics.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'subject', name: 'subject'},
      {data: 'category', name: 'category'},
      {data: 'course_topic', name: 'course_topic'},
      {data: 'quiz_type', name: 'quiz_type'},
      {data: 'title', name: 'title'},
      {data: 'description', name: 'description'},
      {data: 'per_q_mark', name: 'per_q_mark'},
      {data: 'timer', name: 'timer'},
      {data: 'quiz_status', name: 'quiz_status'},
      {data: 'action', name: 'action',searchable: false}

      ]
    });

  });
  

</script>

@endsection

