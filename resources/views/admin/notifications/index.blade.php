@extends('layouts.admin', [
  'page_header' => 'Notifications'
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
    <a href="{{route('notifications.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Add Notification</a>
  </div>

  <div class="box">

    <div class="box-body table-responsive">
      <table id="coursetopicTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Message</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($notifications))
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
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('notifications.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'title', name: 'title'},
      {data: 'message', name: 'price'},
      {data: 'status', name: 'status'},
      {data: 'action', name: 'action',searchable: false,orderable: false}

      ]
    });

  });
  

</script>

@endsection

