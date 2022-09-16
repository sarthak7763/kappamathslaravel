@extends('layouts.admin', [
  'page_header' => 'Topic',
  'dash' => '',
  'category'=>'',
  'course-topic'=>'active',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Add Topic
          <a href="{{ route('course-topic.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::open(['method' => 'POST', 'action' => 'CoursetopicController@store','enctype'=>'multipart/form-data']) !!}

      <div class="row">
          <div class="col-md-6">

            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
              <label for="">Category: </label>
              <span class="required">*</span>
             <select class="form-control" name="category">
              <option value="">Select</option>
              @foreach($categorylist as $list)
                <option value="{{$list['id']}}">{{$list['title']}}</option>
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

</script>

@endsection