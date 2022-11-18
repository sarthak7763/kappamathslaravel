@extends('layouts.admin', [
  'page_header' => 'Quiz'
])

@section('content')
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
              <small class="text-danger">{{ $errors->first('course') }}</small>
            </div>

            <div class="form-group{{ $errors->has('topic') ? ' has-error' : '' }}">
              <label for="">Topics: </label>
              <span class="required">*</span>
             <select class="form-control" name="topic" id="subject_category">
              <option value="">Select</option>
              @foreach($subjectcategorylist as $list)
                <option {{ $quiztopic->category ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['category_name']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $errors->first('topic') }}</small>
            </div>


            <div class="form-group{{ $errors->has('sub_topic') ? ' has-error' : '' }}">
              <label for="">Sub Topics: </label>
              <span class="required">*</span>
             <select class="form-control" name="sub_topic" id="course_topic">
              <option value="">Select</option>
              @foreach($subjectcourselist as $list)
                <option {{ $quiztopic->course_topic ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['topic_name']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $errors->first('sub_topic') }}</small>
            </div>

            <div class="form-group{{ $errors->has('quiz_type') ? ' has-error' : '' }}">
              <label for="">Quiz Type: </label>
              <span class="required">*</span>
             <select class="form-control" name="quiz_type" id="quiz_type">
              <option value="">Select</option>
                <option {{ $quiztopic->quiz_type =="1" ? "selected" : "" }}  value="1">Objective Quiz</option>
                <option {{ $quiztopic->quiz_type =="2" ? "selected" : "" }}  value="2">Theory Quiz</option>
             </select>
              <small class="text-danger">{{ $errors->first('quiz_type') }}</small>
            </div>

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

            <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                  <label for="">Status: </label>
                 <input {{ $quiztopic->quiz_status =="1" ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
                 <label for="toggle2"></label>
                <br>
              </div>

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