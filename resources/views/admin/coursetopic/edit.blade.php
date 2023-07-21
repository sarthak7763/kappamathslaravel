@extends('layouts.admin', [
  'page_header' => 'Course Sub Topics'
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
  $description_error="";
  $topic_title_error="";
  $sort_order_error="";
  $video_error="";
  $image_error="";
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

      @if($validationmessage!="" && isset($validationmessage['title']))
      @php $topic_title_error=$validationmessage['title']; @endphp
      @else
      @php $topic_title_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['sort_order']))
      @php $sort_order_error=$validationmessage['sort_order']; @endphp
      @else
      @php $sort_order_error=""; @endphp
      @endif 

      @if($validationmessage!="" && isset($validationmessage['topic_video_id']))
      @php $video_error=$validationmessage['topic_video_id']; @endphp
      @else
      @php $video_error=""; @endphp
      @endif 

      @if($validationmessage!="" && isset($validationmessage['image']))
      @php $image_error=$validationmessage['image']; @endphp
      @else
      @php $image_error=""; @endphp
      @endif
  @endif
  
  <div class="box">
    <div class="box-body">
        <h3>Edit SubTopic: {{ $coursetopic->topic_name }}
          <a href="{{ route('course-topic.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> {{ __('Back')}}
          </a>
        </h3>
      <hr>
    
      {!! Form::model($coursetopic, ['method' => 'PATCH', 'enctype'=>'multipart/form-data','action' => ['CoursetopicController@update', $coursetopic->id]]) !!}
            
        <div class="row">
          <div class="col-md-12">

            <div class="row">
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('course') ? ' has-error' : '' }}">
                    <label for="">Course: </label>
                    <span class="required">*</span>
                   <select class="form-control" name="course" id="course">
                    <option value="">Select</option>
                    @foreach($subjectlist as $list)
                      <option {{ $coursetopic->subject ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['title']}}</option>
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
                      <option {{ $coursetopic->category ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['category_name']}}</option>
                    @endforeach
                   </select>
                    <small class="text-danger">{{ $topic_error }}</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    {!! Form::label('title', 'Topic Title') !!}
                    <span class="required">*</span>
                    {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title', 'required' => 'required']) !!}
                    <small class="text-danger">{{ $errors->first('title') }}</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                    {!! Form::label('sort_order', 'Sort Order') !!}
                    <span class="required">*</span>
                    {!! Form::number('sort_order', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Sort Order','min'=>'1']) !!}
                    <small class="text-danger">{{ $sort_order_error }}</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('topic_video_id') ? ' has-error' : '' }}">
                    {!! Form::label('topic_video_id', 'Topic Video ID') !!}
                    <span class="required">*</span>
                    {!! Form::text('topic_video_id', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Vimeo Video ID']) !!}
                    <small class="text-danger">{{ $video_error }}</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('topic_img') ? ' has-error' : '' }}">
                    {!! Form::label('topic_img', 'Add Image') !!}
                    {!! Form::file('topic_img') !!}
                    <small class="text-danger">{{ $image_error }}</small>
                    <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                  </div>
                  @if($coursetopic->topic_image!="")
                  <div id="preview_image_div">
                    <img id="preview-image" src="/images/topics/{{ $coursetopic->topic_image }}" style="height: auto;width: 50%;">
                  </div>
                  @else
                  <div id="preview_image_div">
                    <img id="preview-image" src="/images/noimage.jpg" style="height: auto;width: 20%;">
                  </div>
                  @endif
                </div>
                <div class="col-md-6">
                   <label for="">Status: </label>
                   <input {{ $coursetopic->topic_status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
                   <label for="toggle2"></label>
                </div>

                <div class="col-md-12">
                  <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                    {!! Form::label('description', 'Description') !!}
                    {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
                    <small class="text-danger">{{ $description_error }}</small>
                  </div>
                </div>

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

  $(document).on('change','#topic_img',function(){
    let reader = new FileReader();
    reader.onload = (e) => { 
      $('#preview-image').attr('src', e.target.result); 
    }
    reader.readAsDataURL(this.files[0]);
  });

  $(document).on('change','#course',function(){
    var course=$(this).val();
    if(course!="")
    {
      $.ajax({
            'url':'{{url("/")}}/admin/course-topic/getsubjectcategorylist',
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

  </script>

  <script>
  CKEDITOR.replace( 'description' );
</script>

@endsection