@extends('layouts.admin', [
  'page_header' => 'Theory Excel Instructions'
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
    <a href="{{route('theory-excel-instructions.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Add Instruction</a>
  </div>

  <div class="box">

    <div class="box-body table-responsive">
      <table id="coursetopicTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Quiz ID</th>
            <th>Question</th>
            <th>Ans.Exp.</th>
            <th>Ques.Image</th>
            <th>Ques.Video</th>
            <th>Ans.Exp.Image</th>
            <th>Ans.Exp.Video</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($theoryexcelinsdata))
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

      ajax: "{{ route('theory-excel-instructions.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'quiz_id', name: 'quiz_id'},
      {data: 'question', name: 'question'},
      {data: 'answer_explaination', name: 'answer_explaination'},
      {data: 'question_image', name: 'question_image'},
      {data: 'question_video_link', name: 'question_video_link'},
      {data: 'answer_explaination_image', name: 'answer_explaination_image'},
      {data: 'answer_explaination_video_link', name: 'answer_explaination_video_link'},
      {data: 'action', name: 'action',searchable: false,orderable: false}

      ]
    });

  });
  

</script>

@endsection

