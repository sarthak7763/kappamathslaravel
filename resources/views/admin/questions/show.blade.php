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

    <!-- Import Question -->
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#importQuestions">
      {{ __('Import Questions') }}
    </button>

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


  <!-- Import Questions Modal -->
  <div id="importQuestions" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Import Questions (Excel File With Exact Header of DataBase Field)</h4>
        </div>

        <!-- Modal to import question -->
        {!! Form::open(['method' => 'POST', 'action' => 'QuestionsController@importExcelToDB', 'files' => true]) !!}
          <div class="modal-body">
            <div class="form-group{{ $errors->has('question_file') ? ' has-error' : '' }}">
              {!! Form::label('question_file', 'Import Question Via Excel File', ['class' => 'col-sm-3 control-label']) !!}
              <span class="required">*</span>
              <div class="col-sm-9">
                {!! Form::file('question_file', ['required' => 'required']) !!}
                <p class="help-block">Only Excel File (.CSV and .XLS)</p>
                <small class="text-danger">{{ $errors->first('question_file') }}</small>
              </div>
            </div>
          </div>

          <!-- Instructions for excel sheet -->
          <div class="box box-danger">
            <div class="box-body">
                <p><b>{{__('Follow the instructions carefully before importing the file')}}.</b></p>
                <p>{{__('The columns of the file should be in the following order.')}}</p>
    
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{__('No')}}</th>
                            <th>{{__('Column Name')}}</th>
                            <th>{{__('Description')}}</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><b>{{__('topic_id')}}</b> <span class="text-danger">*</span></td>
                            <td>{{__('Enter the topic_id:')}} {{$topic->id}} {{ __('(Required)')}}</td>
    
                            
                        </tr>
    
                        <tr>
                            <td>2</td>
                            <td><b>{{__('question')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Enter question')}} {{ __('(Required)')}}</td>
                        </tr>
    
                        <tr>
                            <td>3</td>
                            <td><b>{{__('a')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Option A')}} {{ __('(Required)')}}</td>
                        </tr>
    
                        <tr>
                            <td>4</td>
                            <td><b>{{__('b')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Option B')}} {{ __('(Required)')}}</td>
                        </tr>
    
                        <tr>
                            <td>5</td>
                            <td><b>{{__('c')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Option C')}} {{ __('(Required)')}}</td>
                        </tr>
    
                        <tr>
                            <td>6</td>
                            <td><b>{{__('d')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Option D')}} {{ __('(Required)')}}</td>
                        </tr>
    
                        <tr>
                            <td>7</td>
                            <td><b>{{__('e')}}</b> </td>
                            <td>{{__('Option E')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                        <tr>
                            <td>6</td>
                            <td><b>{{__('f')}}</b> </td>
                            <td>{{__('Option F')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                        <tr>
                            <td>7</td>
                            <td><b>{{__('answer')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Please enter correct answer option like:A,B,C...')}} {{ __('(Required)')}}</td>
                        </tr>

                        <tr>
                          <td>8</td>
                          <td><b>{{ __('code_snippet')}}</b></td>
                          <td>{{ __('Enter code snippet if any')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>

                        <tr>
                            <td>9</td>
                            <td><b>{{__('answer_exp')}}</b> </td>
                            <td>{{__('Answer Explanation if any')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                        <tr>
                            <td>10</td>
                            <td><b>{{__('question_video_link')}}</b></td>
                            <td>{{__('Attach question video link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                        <tr>
                            <td>10</td>
                            <td><b>{{__('question_audio')}}</b></td>
                            <td>{{__('Attach question audio link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                    </tbody>
                </table>
            </div>
          </div>
          <!-- Instructions end -->

          <!-- Reset and Import button -->
          <div class="modal-footer">
            <div class="btn-group pull-right">
              {!! Form::reset("Reset", ['class' => 'btn btn-default']) !!}
              {!! Form::submit("Import", ['class' => 'btn btn-wave']) !!}
            </div>
          </div>
        {!! Form::close() !!}
      </div>
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
