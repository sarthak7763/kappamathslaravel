@extends('layouts.admin', [
  'page_header' => "Questions Import Module"
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

  <!-- Index Table -->
  <div class="box">
    <div class="box-body table-responsive">
      <h4>Theory Questions</h4>
      <!-- Import Question -->
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#importtheoryQuestions">
      {{ __('Import Theory Questions') }}
    </button>

    <div class="btn-group">
          <a href="{{url('/')}}/admin/get_theory_question_sample_export" class="btn btn-wave" id="theory_question_sample_export">Download Sample</button></a>
    </div>

    </div>
  </div>

  <!-- Index Table -->
  <div class="box">
    <div class="box-body table-responsive">
      <h4>Objective Questions</h4>
      <!-- Import Question -->
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#importobjectiveQuestions">
      {{ __('Import Objective Questions') }}
    </button>

    <div class="btn-group">
         <a href="{{url('/')}}/admin/get_objective_question_sample_export" class="btn btn-wave" id="objective_question_sample_export">Download Sample</button></a>
    </div>

    </div>
  </div>


  <!-- Import Questions Modal -->
  <div id="importobjectiveQuestions" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Import Objective Questions (Excel File With Exact Header of DataBase Field)</h4>
        </div>

        <!-- Modal to import question -->
        {!! Form::open(['method' => 'POST', 'action' => 'QuestionsController@importObjectivequestionExcelToDB', 'files' => true]) !!}
          <div class="modal-body">
            <div class="form-group{{ $errors->has('question_file') ? ' has-error' : '' }}">
              {!! Form::label('question_file', 'Import Objective Question Via Excel File', ['class' => 'col-sm-3 control-label']) !!}
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
                            <td><b>{{__('quiz_id')}}</b> <span class="text-danger">*</span></td>
                            <td>{{__('Enter the quiz_id from the quiz topics module')}} {{ __('(Required)')}}</td>
    
                            
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
                            <td><b>{{__('answer')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Please enter correct answer option like:A,B,C...')}} {{ __('(Required)')}}</td>
                        </tr>

                        <tr>
                            <td>8</td>
                            <td><b>{{__('answer_exp')}}</b> </td>
                            <td>{{__('Answer Explanation if any')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                        <tr>
                            <td>9</td>
                            <td><b>{{__('question_video_link')}}</b></td>
                            <td>{{__('Attach question video link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                        <tr>
                            <td>10</td>
                            <td><b>{{__('question_image')}}</b></td>
                            <td>{{__('Attach question image link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>

                        <tr>
                            <td>11</td>
                            <td><b>{{__('answer_explaination_image')}}</b></td>
                            <td>{{__('Attach answer explaination image link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>

                        <tr>
                            <td>12</td>
                            <td><b>{{__('answer_explaination_video_link')}}</b></td>
                            <td>{{__('Attach answer explaination video link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                    </tbody>
                </table>
            </div>
          </div>
          <!-- Instructions end -->

          <!-- Reset and Import button -->
          <div class="modal-footer">
            <div class="btn-group pull-right">
              {!! Form::submit("Import", ['class' => 'btn btn-wave']) !!}
            </div>
          </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>

  <!-- Import Questions Modal -->
  <div id="importtheoryQuestions" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Import Theory Questions (Excel File With Exact Header of DataBase Field)</h4>
        </div>

        <!-- Modal to import question -->
        {!! Form::open(['method' => 'POST', 'action' => 'QuestionsController@importTheoryquestionExcelToDB', 'files' => true]) !!}
          <div class="modal-body">
            <div class="form-group{{ $errors->has('question_file') ? ' has-error' : '' }}">
              {!! Form::label('question_file', 'Import Theory Question Via Excel File', ['class' => 'col-sm-3 control-label']) !!}
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
                            <td><b>{{__('quiz_id')}}</b> <span class="text-danger">*</span></td>
                            <td>{{__('Enter the quiz_id from the quiz topics module')}} {{ __('(Required)')}}</td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td><b>{{__('question')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Enter question')}} {{ __('(Required)')}}</td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td><b>{{__('answer_exp')}}</b> </td>
                            <td>{{__('Answer Explanation if any')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                        <tr>
                            <td>4</td>
                            <td><b>{{__('question_video_link')}}</b></td>
                            <td>{{__('Attach question video link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                        <tr>
                            <td>5</td>
                            <td><b>{{__('question_image')}}</b></td>
                            <td>{{__('Attach question image link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>

                        <tr>
                            <td>11</td>
                            <td><b>{{__('answer_explaination_image')}}</b></td>
                            <td>{{__('Attach answer explaination image link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>

                        <tr>
                            <td>12</td>
                            <td><b>{{__('answer_explaination_video_link')}}</b></td>
                            <td>{{__('Attach answer explaination video link')}} {{ __('(Optional can be left empty)') }}</td>
                        </tr>
    
                    </tbody>
                </table>
            </div>
          </div>
          <!-- Instructions end -->

          <!-- Reset and Import button -->
          <div class="modal-footer">
            <div class="btn-group pull-right">
              {!! Form::submit("Import", ['class' => 'btn btn-wave']) !!}
            </div>
          </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>

@endsection
