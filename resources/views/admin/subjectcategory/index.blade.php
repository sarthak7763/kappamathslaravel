@extends('layouts.admin', [
  'page_header' => 'Course Topics'
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
    <a href="{{route('course-category.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Add Topic</a>
  </div>

  <div class="box">

    <div class="box-body table-responsive">
      <table id="coursetopicTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Course</th>
            <th>Title</th>
            <th>Status</th>
            <th>Sort Order</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($subjectcategory))
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

    var filter_start_date="{{Request::input('filter_start_date')}}";
    var filter_end_date="{{Request::input('filter_end_date')}}";
    var table = $('#coursetopicTable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,
      ajax: {
            url: "{{ route('course-category.index') }}",
            type: "GET",
            data: {
                "filter_start_date": filter_start_date,
                "filter_end_date": filter_end_date
            }
        },

      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'subject', name: 'category'},
      {data: 'category_name', name: 'title'},
      {data: 'category_status', name: 'status'},
      {data: 'sort_order', name: 'sort_order'},
      {data: 'action', name: 'action',orderable: false,searchable: false}

      ]
    });

  });
  

</script>

@endsection

