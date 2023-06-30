@extends('layouts.admin', [
  'page_header' => 'Notifications'
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
  $message_error="";
  $image_error="";

  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp
      @if($validationmessage!="" && isset($validationmessage['title']))
      @php $title_error=$validationmessage['title']; @endphp
      @else
      @php $title_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['message']))
      @php $message_error=$validationmessage['message']; @endphp
      @else
      @php $message_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['image']))
      @php $image_error=$validationmessage['image']; @endphp
      @else
      @php $image_error=""; @endphp
      @endif

  @endif

  <div class="box">
    <div class="box-body">
        <h3>Add Notification
          <a href="{{ route('notifications.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::open(['method' => 'POST', 'action' => 'NotificationController@store','enctype'=>'multipart/form-data']) !!}

      <div class="row">

          <div class="col-md-12">
            <div class="col-md-6">
             <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{ $title_error}}</small>
            </div>

            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
              {!! Form::label('message', 'Message') !!}
              <span class="required">*</span>
              {!! Form::textarea('message', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Message']) !!}
              <small class="text-danger">{{ $message_error }}</small>
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
            
           </div>

          </div>
        </div>

        <div class="btn-group pull-right">
          {!! Form::submit("Save", ['class' => 'btn btn-wave']) !!}
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