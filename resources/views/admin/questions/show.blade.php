@extends('layouts.admin', [
  'page_header' => "Objective Questions / {$topic->title} "
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

  <!-- Buttons -->
  <div class="margin-bottom">
    <!-- Add Question -->
    <a href="{{route('questions.create', Request::segment(3))}}" ><button type="button" class="btn btn-wave">
      {{ __('Add Question')}}
    </button></a>

    <!-- Back Button -->
    <a href="{{route('questions.index')}}" class="btn btn-wave pull-right">
      <i class="fa fa-arrow-left"></i> 
      {{ __('Back') }}
    </a>
  </div>

  <!-- Index Table -->
  <div class="box">
    <div class="box-body table-responsive">
      <table id="questionsTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Questions</th>
            <th>A - Option</th>
            <th>B - Option</th>
            <th>C - Option</th>
            <th>D - Option</th>
            <th>Correct Answer</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
         @if ($questions)
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

    var table = $('#questionsTable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('questions.show', $topic->id) }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'question', name: 'question'},
      {data: 'a', name: 'a'},
      {data: 'b', name: 'b'},
      {data: 'c', name: 'c'},
      {data: 'd', name: 'd'},
      {data: 'answer', name: 'answer'},
      {data: 'question_status', name: 'status'},
      {data: 'action', name: 'action',searchable: false,orderable: false}

      ]
    });

  });
  
  </script>
@endsection
