@extends('layouts.admin', [
  'page_header' => 'Manage FAQ'
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
        <h3>Edit FAQ
          <a href="{{ route('faq.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::model($examinformation, ['method' => 'PATCH','enctype'=>'multipart/form-data', 'action' => ['ExamInformationController@update', $examinformation->id]]) !!}

      <div class="row">

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
             <input {{ $examinformation->status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
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