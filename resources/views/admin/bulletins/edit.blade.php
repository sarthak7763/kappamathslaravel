@extends('layouts.admin', [
  'page_header' => 'Bulletins'
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Edit Bulletin
          <a href="{{ route('bulletin.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::model($bulletins, ['method' => 'PATCH','enctype'=>'multipart/form-data', 'action' => ['BulletinController@update', $bulletins->id]]) !!}

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

             <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
              {!! Form::label('question', 'Question') !!}
              <span class="required">*</span>
              {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Question']) !!}
              <small class="text-danger">{{ $errors->first('question') }}</small>
            </div>
          </div>  
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
              {!! Form::label('answer', 'Answer') !!}
              {!! Form::textarea('answer', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Answer']) !!}
              <small class="text-danger">{{ $errors->first('answer') }}</small>
            </div>
          </div>
          <div class="col-md-6">  

            <label for="">Status: </label>
             <input {{ $bulletins->status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
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