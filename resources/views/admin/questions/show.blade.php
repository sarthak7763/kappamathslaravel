@extends('layouts.admin', [
  'page_header' => "Questions / {$topic->title} ",
  'dash' => '',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => 'active',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')

  <!-- Buttons -->
  <div class="margin-bottom">
    <!-- Add Question -->
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#createModal">
      {{ __('Add Question')}}
    </button>

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

  <!-- Add Question Modal -->
  <div id="createModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Question</h4>
        </div>
        {!! Form::open(['method' => 'POST', 'action' => 'QuestionsController@store', 'files' => true]) !!}
          <div class="modal-body">
            <div class="row">
              <div class="col-md-4">
                {!! Form::hidden('topic_id', $topic->id) !!}
                <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">                  
                  {!! Form::label('question', 'Question') !!}
                  <span class="required">*</span>
                  {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Question', 'rows'=>'8', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('question') }}</small>
                </div>
                <div class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
                    {!! Form::label('answer', 'Correct Answer') !!}
                    <span class="required">*</span>
                    {!! Form::select('answer', array('A'=>'A', 'B'=>'B', 'C'=>'C', 'D'=>'D', 'E' => 'E', 'F' => 'F'),null, ['class' => 'form-control select2', 'required' => 'required', 'placeholder'=>'']) !!}
                    <small class="text-danger">{{ $errors->first('answer') }}</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group{{ $errors->has('a') ? ' has-error' : '' }}">
                  {!! Form::label('a', 'A - Option') !!}
                  <span class="required">*</span>
                  {!! Form::text('a', null, ['class' => 'form-control', 'placeholder' => 'Please Enter A Option', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('a') }}</small>
                </div>
                <div class="form-group{{ $errors->has('b') ? ' has-error' : '' }}">
                  {!! Form::label('b', 'B - Option') !!}
                  <span class="required">*</span>
                  {!! Form::text('b', null, ['class' => 'form-control', 'placeholder' => 'Please Enter B Option', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('b') }}</small>
                </div>
                <div class="form-group{{ $errors->has('c') ? ' has-error' : '' }}">
                  {!! Form::label('c', 'C - Option') !!}
                  <span class="required">*</span>
                  {!! Form::text('c', null, ['class' => 'form-control', 'placeholder' => 'Please Enter C Option', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('c') }}</small>
                </div>
                <div class="form-group{{ $errors->has('d') ? ' has-error' : '' }}">
                  {!! Form::label('d', 'D - Option') !!}
                  <span class="required">*</span>
                  {!! Form::text('d', null, ['class' => 'form-control', 'placeholder' => 'Please Enter D Option', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('d') }}</small>
                </div>

                  <div class="form-group{{ $errors->has('e') ? ' has-error' : '' }}">
                  {!! Form::label('e', 'E - Option') !!}
                  {!! Form::text('e', null, ['class' => 'form-control', 'placeholder' => 'Please Enter E Option']) !!}
                  <small class="text-danger">{{ $errors->first('e') }}</small>
                  </div>

                  <div class="form-group{{ $errors->has('f') ? ' has-error' : '' }}">
                  {!! Form::label('f', 'F - Option') !!}
                  {!! Form::text('f', null, ['class' => 'form-control', 'placeholder' => 'Please Enter F Option']) !!}
                  <small class="text-danger">{{ $errors->first('f') }}</small>
                  </div>

              </div>
              <div class="col-md-4">
                <div class="form-group{{ $errors->has('code_snippet') ? ' has-error' : '' }}">
                    {!! Form::label('code_snippet', 'Code Snippets') !!}
                    {!! Form::textarea('code_snippet', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Code Snippets', 'rows' => '5']) !!}
                    <small class="text-danger">{{ $errors->first('code_snippet') }}</small>
                </div>
                <div class="form-group{{ $errors->has('answer_ex') ? ' has-error' : '' }}">
                    {!! Form::label('answer_exp', 'Answer Explanation') !!}
                    {!! Form::textarea('answer_exp', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Answer Explanation', 'rows' => '4']) !!}
                    <small class="text-danger">{{ $errors->first('answer_ex') }}</small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="extras-block">
                  <h4 class="extras-heading">Video And Image For Question</h4>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('question_video_link') ? ' has-error' : '' }}">
                        {!! Form::label('question_video_link', 'Add Video To Question') !!}
                        {!! Form::text('question_video_link', null, ['class' => 'form-control', 'placeholder'=>'https://myvideolink.com/embed/..']) !!}
                        <small class="text-danger">{{ $errors->first('question_video_link') }}</small>
                        <p class="help">YouTube And Vimeo Video Support (Only Embed Code Link)</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group{{ $errors->has('question_img') ? ' has-error' : '' }}">
                        {!! Form::label('question_img', 'Add Image To Question') !!}
                        {!! Form::file('question_img') !!}
                        <small class="text-danger">{{ $errors->first('question_img') }}</small>
                         <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <label for="question_audio">Add Audio Explanation:</label>
                       <div class="form-group{{ $errors->has('question_audio') ? ' has-error' : '' }}">
                          <input type="text" class="form-control" value="" name="question_audio" placeholder="http://">
                       </div>
                    </div>

                  </div>
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
  <!-- Add Question Modal End -->

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
            {!! Form::hidden('topic_id', $topic->id) !!}
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
            <th>E - Option</th>
            <th>F - Option</th>
            <th>Correct Answer</th>
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
      serverSide: true,
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
      {data: 'e', name: 'e'},
      {data: 'f', name: 'f'},
      {data: 'answer', name: 'answer'},
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
