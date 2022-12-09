@extends('layouts.admin', [
  'page_header' => 'Course'
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
    <a href="{{route('subject.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Add Course</a>
  </div>

  <div class="box">

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('deleted'))
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('added'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ $message }}</strong>
</div>
@endif

    <div class="box-body table-responsive">
      <table id="categoryTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>SNo</th>
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
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('subject.index') }}",
      columns: [

      {data: 'id', name: 'id',orderable: false, searchable: false},
      {data: 'title', name: 'title'},
      {data: 'description', name: 'description'},
      {data: 'status', name: 'status'},
      {data: 'action', name: 'action',searchable: false,orderable: false}

      ]
    });

  });
  

</script>

@endsection

