@extends('layouts.admin', [
  'page_header' => 'Course Sub Topics'
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Add SubTopic
          <a href="{{ route('course-topic.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::open(['method' => 'POST', 'action' => 'CoursetopicController@store','enctype'=>'multipart/form-data']) !!}

      <div class="row">
          <div class="col-md-6">


             <div class="form-group{{ $errors->has('course') ? ' has-error' : '' }}">
              <label for="">Course: </label>
              <span class="required">*</span>
             <select class="form-control" name="course" id="course">
              <option value="">Select</option>
              @foreach($subjectlist as $list)
                <option value="{{$list['id']}}">{{$list['title']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $errors->first('course') }}</small>
            </div>

            <div class="form-group{{ $errors->has('topic') ? ' has-error' : '' }}">
              <label for="">Topics: </label>
              <span class="required">*</span>
             <select class="form-control" name="topic" id="subject_category">
              
             </select>
              <small class="text-danger">{{ $errors->first('topic') }}</small>
            </div>

             <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Topic Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>

            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>

             <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
              {!! Form::label('sort_order', 'Sort Order') !!}
              <span class="required">*</span>
              {!! Form::text('sort_order', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Sort Order']) !!}
              <small class="text-danger">{{ $errors->first('sort_order') }}</small>
            </div>

            <div class="form-group{{ $errors->has('topic_video_id') ? ' has-error' : '' }}">
              {!! Form::label('topic_video_id', 'Topic Video ID') !!}
              <span class="required">*</span>
              {!! Form::text('topic_video_id', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Vimeo Video ID']) !!}
              <small class="text-danger">{{ $errors->first('topic_video_id') }}</small>
            </div>

            <div class="form-group{{ $errors->has('topic_img') ? ' has-error' : '' }}">
            {!! Form::label('topic_img', 'Add Image') !!}
            {!! Form::file('topic_img') !!}
            <small class="text-danger">{{ $errors->first('topic_img') }}</small>
            <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
          </div>

          <div id="preview_image_div">
            <img id="preview-image" src="/images/noimage.jpg" style="height: auto;width: 50%;">
          </div>

            <label for="">Status: </label>
             <input type="checkbox" class="toggle-input" name="status" id="toggle2">
             <label for="toggle2"></label>

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

@endsection