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

  @php 
  $title_error="";
  $description_error="";
  $image_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp
      @if($validationmessage!="" && isset($validationmessage['title']))
      @php $title_error=$validationmessage['title']; @endphp
      @else
      @php $title_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['image']))
      @php $image_error=$validationmessage['image']; @endphp
      @else
      @php $image_error=""; @endphp
      @endif
  @endif
  
  <div class="box">
    <div class="box-body">
        <h3>Add Course
          <a href="{{ route('subject.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> {{ __('Back')}}
          </a>
        </h3>
      <hr>
    
      {!! Form::open(['method' => 'POST','enctype'=>'multipart/form-data','action' => 'SubjectController@store']) !!}
            
        <div class="row">
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Course Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{$title_error}}</small>
            </div>
            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
                <small class="text-danger">{{$description_error}}</small>
              </div>
          </div>  

            <div class="col-md-6">
               <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                {!! Form::label('image', 'Add Image') !!}
                {!! Form::file('image') !!}
                <small class="text-danger">{{$image_error}}</small>
                <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
              </div>
               <div id="preview_image_div">
              <img id="preview-image" src="/images/noimage.jpg" style="height: auto;width: 50%;">
            </div>
            <label for="">Status: </label>
             <input type="checkbox" class="toggle-input" name="status" id="toggle2">
             <label for="toggle2"></label>
          </div>
          <div class="col-md-12">
             <div class="btn-group pull-right">
          {!! Form::submit("Submit", ['class' => 'btn btn-wave']) !!}
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