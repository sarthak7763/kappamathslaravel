@extends('layouts.admin', [
  'page_header' => 'Category',
  'dash' => '',
  'category'=>'active',
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
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#createModal">Add Category</button>
  </div>
  <!-- Create Modal -->
  <div id="createModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Category</h4>
        </div>
        {!! Form::open(['method' => 'POST', 'action' => 'CategoryController@store']) !!}
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                  {!! Form::label('title', 'Title') !!}
                  <span class="required">*</span>
                  {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('title') }}</small>
                </div>


                 <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                  {!! Form::label('description', 'Description') !!}
                  {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description', 'rows' => '8']) !!}
                  <small class="text-danger">{{ $errors->first('description') }}</small>
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
  <div class="box">
    <div class="box-body table-responsive">
      <table id="categoryTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($category))
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

    var table = $('#categoryTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('category.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'title', name: 'title',searchable: true},
      {data: 'description', name: 'description'},
      {data: 'status', name: 'status'},
      {data: 'action', name: 'action',searchable: false}

      ]
    });

  });
  

</script>

@endsection

