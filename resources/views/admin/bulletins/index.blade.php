@extends('layouts.admin', [
  'page_header' => 'Bulletins'
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
    <a href="{{route('bulletin.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Add Bulletin</a>
  </div>

  <div class="box">

    <div class="box-body table-responsive">
      <table id="coursetopicTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Question</th>
            <th>Answer</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($bulletins))
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


      ajax: "{{ route('bulletin.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'question', name: 'title'},
      {data: 'answer', name: 'price'},
      {data: 'status', name: 'status'},
      {data: 'action', name: 'action',searchable: false,orderable: false}

      ]
    });

  });
  

</script>

@endsection

