@extends('layouts.admin', [
  'page_header' => 'Students'
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

  @if ($auth->role == 'A')
    <div class="margin-bottom">
       <a href="{{route('users.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Add User</a>
    </div>
    <!-- All Delete Button -->
    <div id="AllDeleteModal" class="delete-modal modal fade" role="dialog">
      <!-- All Delete Modal -->
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="delete-icon"></div>
          </div>
          <div class="modal-body text-center">
            <h4 class="modal-heading">Are You Sure ?</h4>
            <p>Do you really want to delete "All these records"? This process cannot be undone.</p>
          </div>
          <div class="modal-footer">
            {!! Form::open(['method' => 'POST', 'action' => 'DestroyAllController@AllUsersDestroy']) !!}
                {!! Form::reset("No", ['class' => 'btn btn-gray', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit("Yes", ['class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>

    <div class="content-block box">
      <div class="box-body">
        <div class="table-responsive">
          <table id="usersTable" class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Mobile No.</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
             @if ($users)
            <tbody>

            </tbody>
             @endif
          </table>
        </div>
      </div>
    </div>
  @endif
@endsection
@section('scripts')
<script>

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
    var table = $('#usersTable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,

      ajax: {
            url: "{{ route('users.index') }}",
            type: "GET",
            data: {
                "filter_start_date": filter_start_date,
                "filter_end_date": filter_end_date
            }
        },

      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'name', name: 'name'},
      {data: 'email', name: 'email'},
      {data: 'username', name: 'username'},
      {data: 'mobile', name: 'mobile'},
      {data: 'status', name: 'status',},
      {data: 'action', name: 'action',searchable: false,orderable: false}
      ]
    });

  });
</script>
@endsection
