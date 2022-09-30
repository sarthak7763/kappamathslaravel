@extends('layouts.admin', [
  'page_header' => 'Subject'
])

@section('content')
  <div class="margin-bottom">
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#createModal">Add Subject</button>
  </div>
  <!-- Create Modal -->
  <div id="createModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Subject</h4>
        </div>
        {!! Form::open(['method' => 'POST','enctype'=>'multipart/form-data','action' => 'SubjectController@store']) !!}
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                  {!! Form::label('title', 'Title') !!}
                  <span class="required">*</span>
                  {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
                  <small class="text-danger">{{ $errors->first('title') }}</small>
                </div>


                 <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                  {!! Form::label('description', 'Description') !!}
                  {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description', 'rows' => '8']) !!}
                  <small class="text-danger">{{ $errors->first('description') }}</small>
                </div>

          <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
            {!! Form::label('image', 'Add Image') !!}
            {!! Form::file('image') !!}
            <small class="text-danger">{{ $errors->first('image') }}</small>
            <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
          </div>

          <div id="preview_image_div">
            <img id="preview-image" src="/images/noimage.jpg" style="height: auto;width: 50%;">
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
        @if(isset($subject))
        <tbody>
         
        </tbody>
        @endif
      </table>
    </div>
  </div>
@endsection
@section('scripts')
<script type="text/javascript">

  $(document).on('change','#image',function(){
    let reader = new FileReader();
    reader.onload = (e) => { 
      $('#preview-image').attr('src', e.target.result); 
    }
    reader.readAsDataURL(this.files[0]);
  });

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


      ajax: "{{ route('subject.index') }}",
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

