@extends('layouts.admin', [
  'page_header' => 'CMS Pages'
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
        <h3>Edit CMS Pages
          <a href="{{ route('cms-pages.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::model($cmspages, ['method' => 'PATCH','enctype'=>'multipart/form-data', 'action' => ['CmsPagesController@update', $cmspages->id]]) !!}

      <div class="row">

          <div class="col-md-12">

             <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
              {!! Form::label('name', 'Name') !!}
              <span class="required">*</span>
              {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Name']) !!}
              <small class="text-danger">{{ $errors->first('name') }}</small>
            </div>
          </div>  
          <div class="col-md-12">
            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              <span class="required">*</span>
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>
          </div>  
          <div class="col-md-6">
            <label for="">Status: </label>
             <input {{ $cmspages->status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
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