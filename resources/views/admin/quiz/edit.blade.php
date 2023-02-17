@extends('layouts.admin', [
  'page_header' => 'Quiz'
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

  @php 
  $course_error="";
  $topic_error="";
  $sub_topic_error="";
  $quizType_error="";
  $title_error="";
  $perQuestionMark_error="";
  $questions_limit_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp

     @if($validationmessage!="" && isset($validationmessage['course']))
      @php $course_error=$validationmessage['course']; @endphp
      @else
      @php $course_error=""; @endphp
      @endif


      @if($validationmessage!="" && isset($validationmessage['topic']))
      @php $topic_error=$validationmessage['topic']; @endphp
      @else
      @php $topic_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['sub_topic']))
      @php $sub_topic_error=$validationmessage['sub_topic']; @endphp
      @else
      @php $sub_topic_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['quiz_type']))
      @php $quizType_error=$validationmessage['quiz_type']; @endphp
      @else
      @php $quizType_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['title']))
      @php $title_error=$validationmessage['title']; @endphp
      @else
      @php $title_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['per_question_mark']))
      @php $perQuestionMark_error=$validationmessage['per_question_mark']; @endphp
      @else
      @php $perQuestionMark_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['questions_limit']))
      @php $questions_limit_error=$validationmessage['questions_limit']; @endphp
      @else
      @php $questions_limit_error=""; @endphp
      @endif 
  @endif

  <div class="box">
    <div class="box-body">
        <h3>Edit Quiz: {{ $quiztopic->title }}
          <a href="{{ route('quiz-topics.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> {{ __('Back')}}
          </a>
        </h3>
      <hr>
    
      {!! Form::model($quiztopic, ['method' => 'PATCH', 'action' => ['QuizTopicController@update', $quiztopic->id]]) !!}
            
        <div class="row">
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('course') ? ' has-error' : '' }}">
              <label for="">Course: </label>
              <span class="required">*</span>
             <select class="form-control" name="course" id="course">
              <option value="">Select</option>
              @foreach($subjectlist as $list)
                <option {{ $quiztopic->subject ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['title']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $course_error }}</small>
            </div>
          </div>  
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('topic') ? ' has-error' : '' }}">
              <label for="">Topics: </label>
              <span class="required">*</span>
             <select class="form-control" name="topic" id="subject_category">
              <option value="">Select</option>
              @foreach($subjectcategorylist as $list)
                <option {{ $quiztopic->category ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['category_name']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $topic_error }}</small>
            </div>
          </div>
          <div class="col-md-6">  
            <div class="form-group{{ $errors->has('sub_topic') ? ' has-error' : '' }}">
              <label for="">Sub Topics: </label>
              <span class="required">*</span>
             <select class="form-control" name="sub_topic" id="course_topic">
              <option value="">Select</option>
              @foreach($subjectcourselist as $list)
                <option {{ $quiztopic->course_topic ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['topic_name']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $sub_topic_error }}</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('quiz_type') ? ' has-error' : '' }}">
              <label for="">Quiz Type: </label>
              <span class="required">*</span>
             <select class="form-control" name="quiz_type" id="quiz_type">
              <option value="">Select</option>
                <option {{ $quiztopic->quiz_type =="1" ? "selected" : "" }}  value="1">Objective Quiz</option>
                <option {{ $quiztopic->quiz_type =="2" ? "selected" : "" }}  value="2">Theory Quiz</option>
             </select>
              <small class="text-danger">{{ $quizType_error }}</small>
            </div>
          </div>  
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Quiz Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Title']) !!}
              <small class="text-danger">{{ $title_error }}</small>
            </div>
          </div>
          <div class="col-md-6">  
            <div class="form-group{{ $errors->has('per_question_mark') ? ' has-error' : '' }}">
              {!! Form::label('per_question_mark', 'Per Question Mark') !!}
              <span class="required">*</span>
              {!! Form::number('per_question_mark', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Per Question Mark','min'=>'0']) !!}
              <small class="text-danger">{{ $perQuestionMark_error }}</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('timer') ? ' has-error' : '' }}">
              {!! Form::label('timer', 'Quiz Time (in minutes)') !!}
              {!! Form::number('timer', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Total Time (In Minutes)','min'=>'0']) !!}
              <small class="text-danger">{{ $errors->first('timer') }}</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('questions_limit') ? ' has-error' : '' }}">
              {!! Form::label('questions_limit', 'Questions Limit') !!}
              {!! Form::number('questions_limit', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Questions limit','min'=>'0']) !!}
              <small class="text-danger">{{ $questions_limit_error }}</small>
            </div>
          </div>
          <div class="col-md-6">  
            <div class="mb-0 form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                <label for="">Status: </label>
               <input {{ $quiztopic->quiz_status =="1" ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
               <label for="toggle2"></label>
              <br>
            </div>
          </div>  
          <div class="col-md-12">
            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>
          </div>
        </div>

        <div class="btn-group pull-right">
          {!! Form::submit("Update", ['class' => 'btn btn-wave']) !!}
        </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">

  $('#course').attr("disabled", true);
  $('#subject_category').attr("disabled", true);
  $('#course_topic').attr("disabled", true);
  $('#quiz_type').attr("disabled", true);

  $(document).on('change','#course',function(){
    var course=$(this).val();
    if(course!="")
    {
      $.ajax({
            'url':'{{url("/")}}/admin/quiz-topics/getsubjectcategorylist',
            'data':{"_token": "{{ csrf_token() }}","course":course},
            'type':'post',
            'dataType':'json',
            error:function()
            {
              alert('Something went wrong');
            },
            success:function(data)
            {
              if(data.code=="200")
              {
                getsubjectcategoryoptionhtml(data.message);
              }
              else{
                alert(data.message);
              }
            }
        });
    }
    else{
      alert('Please choose course');
    }
  });

  $(document).on('change','#subject_category',function(){
    var course=$('#course').val();
    var topic=$(this).val();
    if(course!="" && topic!="")
    {
      $.ajax({
            'url':'{{url("/")}}/admin/quiz-topics/getcoursetopiclist',
            'data':{"_token": "{{ csrf_token() }}","topic":topic,"course":course},
            'type':'post',
            'dataType':'json',
            error:function()
            {
              alert('Something went wrong');
            },
            success:function(data)
            {
              if(data.code=="200")
              {
                getcoursetopicoptionhtml(data.message);
              }
              else{
                alert(data.message);
              }
            }
        });
    }
    else if(course!="" && topic==""){
      alert('Please choose topic');
    }
    else if(course=="" && topic!=""){
      alert('Please choose course');
    }
    else{
      alert('Please choose course and topic');
    }

  });

  function getsubjectcategoryoptionhtml(data)
  {
      var optionhtml='<option value="">Select</option>';
      for(emp in data)
      {
          var category_name=data[emp].category_name;
          var categoryid=data[emp].id;

          optionhtml+='<option value="'+categoryid+'">'+category_name+'</option>'
      }

      $('#subject_category').html(optionhtml);
  }

  function getcoursetopicoptionhtml(data)
  {
      var optionhtml='<option value="">Select</option>';
      for(emp in data)
      {
          var topic_name=data[emp].topic_name;
          var topicid=data[emp].id;

          optionhtml+='<option value="'+topicid+'">'+topic_name+'</option>'
      }

      $('#course_topic').html(optionhtml);
  }
                              
  </script>
@endsection