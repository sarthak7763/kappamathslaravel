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

             <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>

            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
              {!! Form::label('message', 'Message') !!}
              <span class="required">*</span>
              {!! Form::textarea('message', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Message']) !!}
              <small class="text-danger">{{ $errors->first('message') }}</small>
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