@extends('layouts.admin', [
  'page_header' => 'Filter Quiz Topics'
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


<div class="box">
    <div class="box-body">
<form method="post" action="{{route('questionsindex')}}" autocomplete="off">
  <input name="_token" type="hidden" value="{!! csrf_token() !!}" />
<div class="row">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group{{ $errors->has('course') ? ' has-error' : '' }}">
            <label for="">Course: </label>
           <select class="form-control" name="course" id="course">
            <option value="">Select</option>
            @foreach($subjectlist as $list)
              @if($list['id']==$subject)
                @php $selected="selected"; @endphp
              @else
                @php $selected=""; @endphp
              @endif
              <option {{$selected}} value="{{$list['id']}}">{{$list['title']}}</option>
            @endforeach
           </select>
            <small class="text-danger">{{ $errors->first('course') }}</small>
          </div>
      </div>
      <div class="col-md-6">
        <div class="form-group{{ $errors->has('topic') ? ' has-error' : '' }}">
            <label for="">Topics: </label>
           <select class="form-control" name="topic" id="subject_category">
           </select>
            <small class="text-danger">{{ $errors->first('topic') }}</small>
          </div>
      </div>
      <div class="col-md-6">
        <div class="form-group{{ $errors->has('sub_topic') ? ' has-error' : '' }}">
              <label for="">Sub Topics: </label>
             <select class="form-control" name="sub_topic" id="course_topic">
              
             </select>
              <small class="text-danger">{{ $errors->first('sub_topic') }}</small>
            </div>
      </div>
      <div class="col-md-6">
        <div class="form-group{{ $errors->has('quiz_type') ? ' has-error' : '' }}">
              <label for="">Quiz Type: </label>
             <select class="form-control" name="quiz_type" id="quiz_type">
              <option value="">Select</option>
                <option @if($quiz_type==1){{'selected'}} @endif value="1">Objective Quiz</option>
                <option @if($quiz_type==2){{'selected'}} @endif value="2">Theory Quiz</option>
             </select>
              <small class="text-danger">{{ $errors->first('quiz_type') }}</small>
            </div>
      </div>
    </div>
  </div>
</div>
<div class="btn-group pull-right">
  {!! Form::submit("Submit", ['class' => 'btn btn-wave']) !!}
</div>
{!! Form::close() !!}
</div>
</div>

<div class="box">
  <h3>Questions By Topic Wise</h3>
    <div class="box-body">
<div class="row">
  @if ($topics)
  @foreach ($topics as $key => $topic)
  <div class="col-md-4">
          <div class="quiz-card">
            <h3 class="quiz-name">{{$topic['title']}}</h3>
            <div class="row">
              <div class="col-xs-6 pad-0">
                <ul class="topic-detail">
                  <li>Per Question Mark <i class="fa fa-long-arrow-right"></i></li>
                  <li>Total Marks <i class="fa fa-long-arrow-right"></i></li>
                  <li>Total Questions <i class="fa fa-long-arrow-right"></i></li>
                  <li>Total Time <i class="fa fa-long-arrow-right"></i></li>
                </ul>
              </div>
              <div class="col-xs-6">
                <ul class="topic-detail right">
                  <li>{{$topic['per_q_mark']}}</li>
                  <li>@php $qu_count=$topic['qu_count']; @endphp
                    {{$topic['per_q_mark']*$qu_count}}</li>
                  <li>{{$topic['qu_count']}}</li>
                  <li>{{$topic['timer']}} minutes</li>
                </ul>
              </div>
            </div>
            @if($topic['quiz_type']=="1")
            <a href="{{route('questions.show', $topic['id'])}}" class="btn btn-wave">Add Questions</a>
            @else
            <a href="{{route('questions.showquiz', $topic['id'])}}" class="btn btn-wave">Add Questions</a>
            @endif
            </div>
        </div>
        @endforeach
        @endif
</div>
</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
  $(document).ready(function(){
    var subject="{{$subject}}";
    var category="{{$category}}";
    var coursetopic="{{$course}}";

    if(subject!="")
    {
      $.ajax({
            'url':'{{url("/")}}/admin/quiz-topics/getsubjectcategorylist',
            'data':{"_token": "{{ csrf_token() }}","course":subject,'category':category},
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
                getsubjectcategoryoptionhtml(data.message,category);
              }
              else{
                alert(data.message);
              }
            }
        });
    }

    if(subject!="" && category!="")
    {
      $.ajax({
            'url':'{{url("/")}}/admin/quiz-topics/getcoursetopiclist',
            'data':{"_token": "{{ csrf_token() }}","topic":category,"course":subject},
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
                getcoursetopicoptionhtml(data.message,coursetopic);
              }
              else{
                alert(data.message);
              }
            }
        });
    }
  });

  $(document).on('change','#course',function(){
    var course=$(this).val();
    var category="";
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
                getsubjectcategoryoptionhtml(data.message,category);
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
    var coursetopic="";
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
                getcoursetopicoptionhtml(data.message,coursetopic);
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
      alert('Please choose topic');
    }
    else{
      alert('Please choose course and topic');
    }

  });

  function getsubjectcategoryoptionhtml(data,category)
  {
      var optionhtml='<option value="">Select</option>';
      for(emp in data)
      {
          var category_name=data[emp].category_name;
          var categoryid=data[emp].id;

          if(categoryid==category)
          {
            var selected="selected";
          }
          else{
            var selected="";
          }

          optionhtml+='<option '+selected+' value="'+categoryid+'">'+category_name+'</option>'
      }

      $('#subject_category').html(optionhtml);
  }

  function getcoursetopicoptionhtml(data,coursetopic)
  {
      var optionhtml='<option value="">Select</option>';
      for(emp in data)
      {
          var topic_name=data[emp].topic_name;
          var topicid=data[emp].id;

          if(coursetopic==topicid)
          {
            var selected="selected";
          }
          else{
            var selected="";
          }

          optionhtml+='<option '+selected+' value="'+topicid+'">'+topic_name+'</option>'
      }

      $('#course_topic').html(optionhtml);
  }
                              
  </script>
@endsection
