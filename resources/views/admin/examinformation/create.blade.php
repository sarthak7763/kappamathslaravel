@extends('layouts.admin', [
  'page_header' => 'Exam Information'
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Add Exam Information
          <a href="{{ route('exam-information.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::open(['method' => 'POST', 'action' => 'ExamInformationController@store','enctype'=>'multipart/form-data']) !!}

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

          <div class="col-md-12">

             <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
              {!! Form::label('question', 'Question') !!}
              <span class="required">*</span>
              {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Question']) !!}
              <small class="text-danger">{{ $errors->first('question') }}</small>
            </div>

            <div class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
              {!! Form::label('answer', 'Answer') !!}
              {!! Form::textarea('answer', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Answer']) !!}
              <small class="text-danger">{{ $errors->first('answer') }}</small>
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