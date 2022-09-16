@extends('layouts.admin', [
  'page_header' => 'FAQ',
  'dash' => '',
  'course'=>'',
  'quiz' => 'active',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
<div class="box">
    <div class="box-body">
      <!-- Button trigger modal -->
      <div class="margin-bottom">
      <a href="{{ route('faq.add') }}" class="btn btn-md btn-primary">+ Create FAQ</a>
        
      </div>

      <table class="table table-bordered" id="faqTable">
        <thead>
          <tr>
            <th>SN</th>
            <th>Title</th>
            <th>Details</th>
            <th>Action</i>
            </th>
          </tr>
        </thead>
        @if(isset($faqs))
        <tbody>

        </tbody>
        @endif
      </table>
    </div>
    

  </div>
@endsection
@section('scripts')
<script>
  $(function () {

    var table = $('#faqTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('faq.index') }}",
      columns: [

	      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
	      {data: 'title', name: 'title'},
	      {data: 'details', name: 'details'},
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
