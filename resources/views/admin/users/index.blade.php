@extends('layouts.admin', [
  'page_header' => 'Students'
])

@section('content')
  @if ($auth->role == 'A')
    <div class="margin-bottom">
      <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#createModal">Add Student</button>
      <!-- <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#AllDeleteModal">Delete All Students</button> -->
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
    <!-- Create Modal -->
    <div id="createModal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Student</h4>
          </div>
          {!! Form::open(['files' => true,'method' => 'POST', 'action' => 'UsersController@store']) !!}
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    {!! Form::label('name', 'Student Name') !!}
                    <span class="required">*</span>
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Your Name']) !!}
                    <small class="text-danger">{{ $errors->first('name') }}</small>
                  </div>
                  <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    {!! Form::label('email', 'Email address') !!}
                    <span class="required">*</span>
                    {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'eg: info@examlpe.com']) !!}
                    <small class="text-danger">{{ $errors->first('email') }}</small>
                  </div>
                  <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    {!! Form::label('password', 'Password') !!}
                    <span class="required">*</span>
                    {!! Form::password('password', ['class' => 'form-control', 'placeholder'=>'Enter Your Password']) !!}
                    <small class="text-danger">{{ $errors->first('password') }}</small>
                  </div>
                  
                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                  <label for="image">Choose Profile Picture:</label>
                  <input type="file" class="form-control" name="image">
                </div>

                </div>
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                    {!! Form::label('mobile', 'Mobile No.') !!}
                    {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => 'eg: +91-123-456-7890']) !!}
                    <small class="text-danger">{{ $errors->first('mobile') }}</small>
                  </div>
                  <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    {!! Form::label('username', 'Enter Username') !!}
                    {!! Form::text('username', null, ['class' => 'form-control', 'placeholder'=>'Enter Your Username']) !!}
                    <small class="text-danger">{{ $errors->first('username') }}</small>
                  </div>
                  <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                  <label for="">Status: </label>
                 <input type="checkbox" class="toggle-input" name="status" id="toggle2">
                 <label for="toggle2"></label>
                <br>
              </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div class="btn-group pull-right">
                {!! Form::reset("Reset", ['class' => 'btn btn-default']) !!}
                {!! Form::submit("Add", ['class' => 'btn btn-wave']) !!}
              </div>
            </div>
          {!! Form::close() !!}
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
                <th>User Image</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Mobile No.</th>
                <th>User Role</th>
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

    var table = $('#usersTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('users.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'image', name: 'image',searchable: false},
      {data: 'name', name: 'name'},
      {data: 'email', name: 'email'},
      {data: 'username', name: 'username'},
      {data: 'mobile', name: 'mobile'},
      {data: 'role', name: 'role'},
      {data: 'status', name: 'status'},
      {data: 'action', name: 'action',searchable: false}
      ]
    });

  });
</script>
@endsection
