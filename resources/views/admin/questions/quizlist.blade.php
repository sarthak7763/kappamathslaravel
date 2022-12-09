@extends('layouts.admin', [
  'page_header' => 'Questions By Topic Wise'
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

<div class="row">
  <div class="col-md-4">
    <div class="quiz-card">
      <h3 class="quiz-name">Filters</h3>
    <div class="row">
              <div class="col-xs-6 pad-0">
                <ul class="topic-detail">
                  <li>Course <i class="fa fa-long-arrow-right"></i></li>
                  <li>Topic <i class="fa fa-long-arrow-right"></i></li>
                  <li>Sub Topic <i class="fa fa-long-arrow-right"></i></li>
                  <li>Quiz Type <i class="fa fa-long-arrow-right"></i></li>
                </ul>
              </div>
              <div class="col-xs-6">
                <ul class="topic-detail right">
                  <li>{{$filterarray['subjectname']}}</li>
                  <li>
                    {{$filterarray['categoryname']}}
                  </li>
                  <li>
                    {{$filterarray['coursename']}}
                  </li>
                  <li>
                    {{$filterarray['quiz_typename']}}
                  </li>
                </ul>
              </div>
            </div>
          </div>

  </div>
</div>

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
                  <li>
                    @php $qu_count=$topic['qu_count']; @endphp
                    {{$topic['per_q_mark']*$qu_count}}
                  </li>
                  <li>
                    {{$topic['qu_count']}}
                  </li>
                  <li>
                    {{$topic['timer']}} minutes
                  </li>
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
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).on('change','#subject',function(){
    var subject=$(this).val();
    if(subject!="")
    {
      $.ajax({
            'url':'{{url("/")}}/admin/quiz-topics/getsubjectcategorylist',
            'data':{"_token": "{{ csrf_token() }}","subject":subject},
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
      alert('Please choose subject');
    }
  });

  $(document).on('change','#subject_category',function(){
    var subject=$('#subject').val();
    var category=$(this).val();
    if(subject!="" && category!="")
    {
      $.ajax({
            'url':'{{url("/")}}/admin/quiz-topics/getcoursetopiclist',
            'data':{"_token": "{{ csrf_token() }}","category":category,"subject":subject},
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
    else if(subject!="" && category==""){
      alert('Please choose category');
    }
    else if(subject=="" && category!=""){
      alert('Please choose subject');
    }
    else{
      alert('Please choose subject and category');
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
