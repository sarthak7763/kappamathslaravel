@extends('layouts.admin', [
  'page_header' => 'Contact Subject'
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
  $name_error="";
  $description_error="";
  $image_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp
      @if($validationmessage!="" && isset($validationmessage['name']))
      @php $name_error=$validationmessage['name']; @endphp
      @else
      @php $name_error=""; @endphp
      @endif

    
  @endif


  <div class="box">
    <div class="box-body">
        <h3>Add Subject
          <a href="{{ route('contact-subject.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::open(['method' => 'POST', 'action' => 'ContactController@store','enctype'=>'multipart/form-data']) !!}

      <div class="row">

          <div class="col-md-12">

             <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
              {!! Form::label('name', 'Name') !!}
              <span class="required">*</span>
              {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Name']) !!}
              <small class="text-danger">{{ $name_error }}</small>
            </div>

            <label for="">Status: </label>
             <input type="checkbox" class="toggle-input" name="status" id="toggle2">
             <label for="toggle2"></label>

          </div>
        </div>

        <div class="btn-group pull-right">
          {!! Form::submit("Save", ['class' => 'btn btn-wave']) !!}
        </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection