@extends('layouts.admin', [
  'page_header' => 'Course'
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
        <h3>Edit Course: {{ $subject->title }}
          <a href="{{ route('subject.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> {{ __('Back')}}
          </a>
        </h3>
      <hr>
    
      {!! Form::model($subject, ['method' => 'PATCH','enctype'=>'multipart/form-data', 'action' => ['SubjectController@update', $subject->id]]) !!}
            
        <div class="row">

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Course Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>
            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
                <small class="text-danger">{{ $errors->first('description') }}</small>
              </div>
          </div>  

            <div class="col-md-6">
               <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                {!! Form::label('image', 'Add Image') !!}
                {!! Form::file('image') !!}
                <small class="text-danger">{{ $errors->first('image') }}</small>
                <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
              </div>
               <div id="preview_image_div">
              @if($subject->image!="")
              <img id="preview-image" src="/images/subjects/{{ $subject->image }}" style="height: auto;width: 50%;">
              @else
              <img id="preview-image" src="/images/noimage.jpg" style="height: auto;width: 50%;">
              @endif
            </div>
            <label for="">Status: </label>
             <input {{ $subject->status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
             <label for="toggle2"></label>
          </div>
          <div class="col-md-12">
             <div class="btn-group pull-right">
          {!! Form::submit("Update", ['class' => 'btn btn-wave']) !!}
        </div>
          </div>
          </div>
        </div>
       
      {!! Form::close() !!}
  </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).on('change','#image',function(){
    let reader = new FileReader();
    reader.onload = (e) => { 
      $('#preview-image').attr('src', e.target.result); 
    }
    reader.readAsDataURL(this.files[0]);
  });

</script>

@endsection