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
  <div class="box">
    <div class="box-body">
      <div class="margin-bottom">
      <a title="Create a new page" href="{{ route('pages.add') }}" class="btn btn-md btn-primary">+ Create Page</a>
        
      </div>
      
      <table id="pagesTable" class="table table-bordered">
        <thead>
          <tr>
            <th>SN</th>
            <th>Title</th>

            <th>URL</th>
            <th>Status</th>
            <th>Action
            </th>
          </tr>
        </thead>
        @if(isset($pages))
        <tbody>




        </tbody>
        @endif
      </table>
    </div>
    <!-- Button trigger modal -->
      

  </div>
@endsection
@section('scripts')
<script>
  $(function () {

    var table = $('#pagesTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('pages.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'name', name: 'name'},
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
