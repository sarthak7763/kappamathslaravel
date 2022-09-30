@extends('layouts.admin', [
  'page_header' => 'Course Topics'
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Edit Topic: {{ $coursetopic->topic_name }}
          <a href="{{ route('topics.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> {{ __('Back')}}
          </a>
        </h3>
      <hr>
    
      {!! Form::model($coursetopic, ['method' => 'PATCH', 'enctype'=>'multipart/form-data','action' => ['CoursetopicController@update', $coursetopic->id]]) !!}
            
        <div class="row">
          <div class="col-md-6">

            <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
              <label for="">Subject: </label>
              <span class="required">*</span>
             <select class="form-control" name="subject">
              <option value="">Select</option>
              @foreach($subjectlist as $list)
                <option {{ $coursetopic->subject ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['title']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $errors->first('subject') }}</small>
            </div>

            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
              <label for="">Category: </label>
              <span class="required">*</span>
             <select class="form-control" name="category">
              <option value="">Select</option>
              @foreach($subjectcategorylist as $list)
                <option {{ $coursetopic->category ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['category_name']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $errors->first('category') }}</small>
            </div>

            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Topic Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>

              <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
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

          @if($coursetopic->topic_image!="")
          <div id="preview_image_div">
            <img id="preview-image" src="/images/topics/{{ $coursetopic->topic_image }}" style="height: auto;width: 50%;">
          </div>
          @else
          <div id="preview_image_div">
            <img id="preview-image" src="/images/noimage.jpg" style="height: auto;width: 50%;">
          </div>
          @endif
             
             <label for="">Status: </label>
             <input {{ $coursetopic->topic_status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
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

  $(document).on('change','#subject',function(){
    var subject=$(this).val();
    if(subject!="")
    {
      $.ajax({
            'url':'{{url("/")}}/admin/course-topic/getsubjectcategorylist',
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