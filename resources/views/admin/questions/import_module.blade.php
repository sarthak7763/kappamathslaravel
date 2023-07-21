<style>
  .w-100 {
    width: 100%;
}

table.que_table.table-striped tr  > * {
    font-size: 15px;
    padding: 13px 10px;
    border-right: 1px solid #bbb8b8;
}

table.que_table thead {
    background: #9e9e9e24;
}
.ox-auto {
    overflow-x: auto;
}
table.que_table.table-striped {
    background: #eff4ff;
    margin-bottom: 15px;
    border: 1px solid #b1b2b7;
}
.ques_table {
    display: inline-block;
    width: 100%;
    padding-bottom: 50px;
}
.ox-auto::-webkit-scrollbar, .ox-auto::-webkit-scrollbar-thumb, .ox-auto::-webkit-scrollbar-thumb {
    display: none;
}
.ques_table .btn-primary {
    background: #112a60;
    border: 1px solid #112a60;
    float: right;
    padding: 3px 18px;
    font-size: 19px;
    text-transform: capitalize;
    margin: 10px 0;
    transition: all .3s;
}

.ques_table .btn-primary:hover {
    background: #3ab1da;
    border-color: #3ab1da;
}
@media(max-width: 767px){
  table.que_table.table-striped tr > * {
    font-size: 13px;
    white-space: nowrap;
}
}
</style>
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

    <div class="col-md-12">  
            <div class="form-group">
              <label for="">Options With Images Only: </label>
               <input type="checkbox" class="toggle-input" name="option_status" id="option_status" value="0">
               <label for="option_status"></label>
              <br>
            </div>

            <input type="hidden" name="checkboxvalue" id="checkboxvalue" value="0">
    </div>

    <div class="box-body table-responsive" id="importdivwithtextonly">

      <h4>Objective Questions (Options Images Optional)</h4>
      <!-- Import Question -->
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#importobjectiveQuestions">
      {{ __('Import Objective Questions') }}
    </button>

    <div class="btn-group">
         <a href="{{url('/')}}/admin/get_objective_question_sample_export" class="btn btn-wave" id="objective_question_sample_export">Download Sample</a>
    </div>

    </div>

    <div class="box-body table-responsive" id="importdivwithimagesonly" style="display: none;">

      <h4>Objective Questions (Options Images Required)</h4>
      <!-- Import Question -->
    <button type="button" class="btn btn-wave" data-toggle="modal" data-target="#importobjectiveQuestionsImages">
      {{ __('Import Objective Questions') }}
    </button>

    <div class="btn-group">
         <a href="{{url('/')}}/admin/get_objective_question_images_sample_export" class="btn btn-wave" id="objective_question_images_sample_export">Download Sample</a>
    </div>

    </div>
  </div>

  @if(count($questionslistarray) > 0)
  <div class="ques_table">
  <form method="post" action="{{route('submitimporttempquestions')}}">
    @csrf
  <div class="ox-auto">

  <table class="table-striped w-100 que_table">
    <thead>
      <tr>
        <th>#</th>
        <th>Questions </th>
      </tr>
    </thead>
    <tbody>
      @php $i=1; @endphp
      @foreach($questionslistarray as $list)
      <tr>
        <td>{{$i}}</td>
        <td>
          <textarea class="textarea" id="textareavalue_{{$list['question_id']}}" style="display: none;">{{html_entity_decode($list['question'])}}</textarea>

          <p id="renderer_{{$list['question_id']}}" class="mathrender" style="color: black;font-size: 20px;"></p>

          <input type="hidden" name="question_id[]" id="importquestion_{{$list['question_id']}}" value="{{$list['question_id']}}">

        </td>
      </tr>
      @php $i++; @endphp
      @endforeach
    </tbody>
  </table>
  </div>
  <button type="submit" class="btn-primary ">  submit</button>
</form>
</div>
@endif

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

                        <tr>
                            <td>13</td>
                            <td><b>{{__('a_image')}}</b></td>
                            <td>{{__('Option A Image')}} {{ __('(Optional can be left empty)')}}</td>
                        </tr>
    
                        <tr>
                            <td>14</td>
                            <td><b>{{__('b_image')}}</b></td>
                            <td>{{__('Option B Image')}} {{ __('(Optional can be left empty)')}}</td>
                        </tr>
    
                        <tr>
                            <td>15</td>
                            <td><b>{{__('c_image')}}</b></td>
                            <td>{{__('Option C Image')}} {{ __('(Optional can be left empty)')}}</td>
                        </tr>
    
                        <tr>
                            <td>16</td>
                            <td><b>{{__('d_image')}}</b></td>
                            <td>{{__('Option D Image')}} {{ __('(Optional can be left empty)')}}</td>
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


  <!-- Import Questions Images Modal -->
  <div id="importobjectiveQuestionsImages" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Import Objective Questions (Excel File With Exact Header of DataBase Field)</h4>
        </div>

        <!-- Modal to import question -->
        {!! Form::open(['method' => 'POST', 'action' => 'QuestionsController@importObjectivequestionImageExcelToDB', 'files' => true]) !!}
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
                            <td><b>{{__('a_image')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Option A Image')}} {{ __('(Required)')}}</td>
                        </tr>
    
                        <tr>
                            <td>4</td>
                            <td><b>{{__('b_image')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Option B Image')}} {{ __('(Required)')}}</td>
                        </tr>
    
                        <tr>
                            <td>5</td>
                            <td><b>{{__('c_image')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Option C Image')}} {{ __('(Required)')}}</td>
                        </tr>
    
                        <tr>
                            <td>6</td>
                            <td><b>{{__('d_image')}}</b> <span class="text-danger">*</span> </td>
                            <td>{{__('Option D Image')}} {{ __('(Required)')}}</td>
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

@section('scripts')

<script type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>

<script type="text/javascript">
  var checkboxvalueonload=$('#option_status').val();
  $('#checkboxvalue').val(checkboxvalueonload);

  if(checkboxvalueonload==1)
  {
    $('#option_status').attr('checked','checked');
    $('#importdivwithimagesonly').show();
    $('#importdivwithtextonly').hide();
  }
  else{
    $('#option_status').removeAttr('checked');
    $('#importdivwithimagesonly').hide();
    $('#importdivwithtextonly').show();
  }

  $('#option_status').on('change', function(){
       var checkboxvalue = this.checked ? 1 : 0;
       $('#checkboxvalue').val(checkboxvalue);

       if(checkboxvalue==1)
       {
          $('#importdivwithimagesonly').show();
          $('#importdivwithtextonly').hide();
       }
       else{
            $('#importdivwithimagesonly').hide();
            $('#importdivwithtextonly').show();
       }
     });

</script>

<script type="text/javascript">
  $('[id^=textareavalue_]').each(function(){
      var textareavalue = $(this).val();
      var id = $(this).attr("id").replace('textareavalue_','');
      $("#renderer_"+id).empty();
      $("#renderer_"+id).append(textareavalue);
      MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer_"+id)[0]])
    });

</script>

@endsection
