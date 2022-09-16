@extends('layouts.admin', [
  'page_header' => 'Quiz',
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
  <div class="margin-bottom">
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#createModal">Add Quiz</button>
  </div>
  <!-- Create Modal -->
  <div id="createModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Quiz</h4>
        </div>
        {!! Form::open(['method' => 'POST', 'action' => 'TopicController@store']) !!}
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                  {!! Form::label('title', 'Quiz Title') !!}
                  <span class="required">*</span>
                  {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Title', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('title') }}</small>
                </div>
                <div class="form-group{{ $errors->has('per_q_mark') ? ' has-error' : '' }}">
                  {!! Form::label('per_q_mark', 'Per Question Mark') !!}
                  <span class="required">*</span>
                  {!! Form::number('per_q_mark', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Per Question Mark', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('per_q_mark') }}</small>
                </div>
                <div class="form-group{{ $errors->has('timer') ? ' has-error' : '' }}">
                  {!! Form::label('timer', 'Quiz Time (in minutes)') !!}
                  {!! Form::number('timer', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Total Time (In Minutes)']) !!}
                  <small class="text-danger">{{ $errors->first('timer') }}</small>
                </div>

                <label for="married_status">Quiz Price:</label>
                {{-- <select name="married_status" id="ms" class="form-control">
                  <option value="no">Free</option>
                  <option value="yes">Paid</option>
                </select> --}}

                <input type="checkbox" class="quizfp toggle-input" name="quiz_price" id="toggle">
                <label for="toggle"></label>
               
                <div style="display: none;" id="doabox">
                   <br>
                  <label for="dob">Choose Quiz Price: </label>
                  <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                <input value="" name="amount" id="doa" type="text" class="form-control"  placeholder="Please Enter Quiz Price">
                 <small class="text-danger">{{ $errors->first('amount') }}</small>
                 </div>
                </div>
                <br>






              <div class="form-group {{ $errors->has('show_ans') ? ' has-error' : '' }}">
                  <label for="">Enable Show Answer: </label>
                 <input type="checkbox" class="toggle-input" name="show_ans" id="toggle2">
                 <label for="toggle2"></label>
                <br>
              </div>
                
              </div>
              <div class="col-md-6">
                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                  {!! Form::label('description', 'Description') !!}
                  {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Description', 'rows' => '8']) !!}
                  <small class="text-danger">{{ $errors->first('description') }}</small>
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
      <table id="topicsTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Quiz Title</th>
            <th>Description</th>
            <th>Per Question Mark</th>
            <th>Time</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($topics))
        <tbody>
         
        </tbody>
        @endif
      </table>
    </div>
  </div>
@endsection
@section('scripts')
<script type="text/javascript">
  
 
  $(function() {
    $('#fb_check').change(function() {
      $('#fb').val(+ $(this).prop('checked'))
    })
  })

 
                  
  $(document).ready(function(){

      $('.quizfp').change(function(){

        if ($('.quizfp').is(':checked')){
            $('#doabox').show('fast');
        }else{
            $('#doabox').hide('fast');
        }

       
      });

  });
                                

                               
  $('#priceCheck').change(function(){
    alert('hi');
  });

  function showprice(id)
  {
    if ($('#toggle2'+id).is(':checked')){
      $('#doabox2'+id).show('fast');
    }else{

      $('#doabox2'+id).hide('fast');
    }
  }
     

$(function () {

    var table = $('#topicsTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,


      ajax: "{{ route('topics.index') }}",
      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'title', name: 'title'},
      {data: 'description', name: 'description'},
      {data: 'per_q_mark', name: 'per_q_mark'},
      {data: 'timer', name: 'timer'},
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

