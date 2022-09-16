@extends('layouts.admin', [
  'page_header' => 'Topics',
  'dash' => '',
  'category'=>'',
  'course-topic'=>'active',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
  <div class="margin-bottom">
    <a href="{{route('course-topic.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><button type="button" class="btn btn-wave">Add Topic</button></a>
  </div>

  <div class="box">
    <div class="box-body table-responsive">
      <table id="coursetopicTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Category</th>
            <th>Title</th>
            <th>Image</th>
            <th>Status</th>
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
      {data: 'category', name: 'category',searchable: true},
      {data: 'topic_name', name: 'title',searchable: true},
      {data: 'topic_image', name: 'image',width: '45%',},
      {data: 'topic_status', name: 'status'},
      {data: 'action', name: 'action',searchable: false}

      ]
    });

  });
  

</script>

@endsection

