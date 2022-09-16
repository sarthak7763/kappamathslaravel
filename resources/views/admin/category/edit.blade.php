@extends('layouts.admin', [
  'page_header' => 'Category',
  'dash' => '',
  'category'=>'active',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Edit Category: {{ $category->title }}
          <a href="{{ route('topics.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> {{ __('Back')}}
          </a>
        </h3>
      <hr>
    
      {!! Form::model($category, ['method' => 'PATCH', 'action' => ['CategoryController@update', $category->id]]) !!}
            
        <div class="row">
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Topic Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>

              <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>
             
             <label for="">Status: </label>
             <input {{ $category->status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
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