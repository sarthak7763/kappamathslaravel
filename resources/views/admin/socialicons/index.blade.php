@extends('layouts.admin', [
  'page_header' => 'Dashboard',
  'dash' => 'active',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
  <div class="dashboard-block">
    <!-- Button trigger modal -->
    <div class="margin-bottom">
    <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#myModal">
  + Add Social Icon
</button>
  </div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">+ Add Social Icon</h4>
      </div>
      <div class="modal-body">
        <form action="{{ route('social.store') }}" method="POST" enctype="multipart/form-data">
          {{ csrf_field() }}
          <label for="url">Title:</label>
          <input type="text" name="title" value="" placeholder="Enter title" class="form-control"/>
          <br>
          <label for="url">URL:</label>
          <input type="text" name="url" value="" placeholder="ex. http://facebook.com" class="form-control"/>
          <br>
          <label for="url">Choose icon:</label>
          <input type="file" name="icon" value="" class="form-control"/>
          <br>
          <label for="status">Status:</label>
          <input type="checkbox" class="toggle-input" name="status" id="toggle">
          <label for="toggle"></label>
          <br>
          <input type="submit" class="btn btn-md btn-danger" value="+ Add">
        </form>
      </div>

    </div>
  </div>
</div>
    
      <div class="box">
    <div class="box-body table-responsive">
      <table class="table table-bordered" id="socialiconTable">
        <thead>
          <tr>
            <th>SN</th>
            <th>Icon</th>
            <th>Title</th>
            <th>URL</th>
            <th>Status</th>
            <th>Action</th>
           
          </tr>
        </thead>
        @if(isset($social) && $social != NULL)
        <tbody>
        
        </tbody>
        @endif
      </table>
    </div>
  </div>
@endsection
@section('scripts')
<script>
  $(function() {
    $('#toggle-event').change(function() {
      $('#status').val(+ $(this).prop('checked'))
    })
  })

  $(function () {

    var table = $('#socialiconTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('socialicons.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'icon', name: 'icon',searchable: false},
      {data: 'title', name: 'title'},
      {data: 'url', name: 'url'},
      {data: 'status', name: 'status'},
      {data: 'action', name: 'action',searchable: false}

      ],
      dom : 'lBfrtip',
      buttons : [
      'csv','excel','pdf','print'
      ],
      order : [[0,'desc']]
    });

  });
</script>
@endsection