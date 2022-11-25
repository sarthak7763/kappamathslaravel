@extends('layouts.admin', [
  'page_header' => 'Notifications'
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Edit Notification
          <a href="{{ route('notifications.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::model($notifications, ['method' => 'PATCH','enctype'=>'multipart/form-data', 'action' => ['NotificationController@update', $notifications->id]]) !!}

      <div class="row">

        @if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('deleted'))
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('added'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ $message }}</strong>
</div>
@endif

          <div class="col-md-6">

             <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
              {!! Form::label('message', 'Message') !!}
              {!! Form::textarea('message', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Message']) !!}
              <small class="text-danger">{{ $errors->first('message') }}</small>
            </div>
          </div>  

          <div class="col-md-6">
            <label for="">Status: </label>
             <input {{ $notifications->status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
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