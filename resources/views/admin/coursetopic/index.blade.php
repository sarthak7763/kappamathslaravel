@extends('layouts.admin', [
  'page_header' => 'Course Sub Topics'
])

@section('content')
  <div class="margin-bottom">
    <a href="{{route('course-topic.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-wave">Add SubTopic</a>
  </div>

  <div class="box">
    <div class="box-body table-responsive">
      <table id="coursetopicTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Course</th>
            <th>Topic</th>
            <th>Title</th>
            <th>Video ID</th>
            <th>Status</th>
            <th>Sort Order</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($coursetopic))
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

    var table = $('#coursetopicTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('course-topic.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'subject', name: 'subject',searchable: false},
      {data: 'category', name: 'category',searchable: false},
      {data: 'topic_name', name: 'title',searchable: true},
      {data: 'topic_video_id', name: 'topic_video_id',searchable: false},
      {data: 'topic_status', name: 'status'},
      {data: 'sort_order', name: 'sort_order'},
      {data: 'action', name: 'action',searchable: false}

      ]
    });

  });
  

</script>

@endsection

