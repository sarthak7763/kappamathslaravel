@extends('layouts.admin', [
  'page_header' => 'Students'
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
        <h3>Add User
          <a href="{{ route('users.index') }}" class="btn btn-gray pull-right"><i class="fa fa-arrow-left"></i> Back</a></h3>
      <hr>

      {!! Form::open(['files' => true,'method' => 'POST', 'action' => 'UsersController@store']) !!}
        
          <div class="row">
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                {!! Form::label('name', 'Name') !!}
                <span class="required">*</span>
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter your name']) !!}
                <small class="text-danger">{{ $errors->first('name') }}</small>
              </div>
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                {!! Form::label('email', 'Email address') !!}
                <span class="required">*</span>
                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'eg: info@example.com']) !!}
                <small class="text-danger">{{ $errors->first('email') }}</small>
              </div>
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                {!! Form::label('password', 'Password') !!}
                <span class="required">*</span>
                {!! Form::password('password', ['class' => 'form-control', 'placeholder'=>'Change Your Password']) !!}
                <small class="text-danger">{{ $errors->first('password') }}</small>
              </div>

               <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                <label for="image">Choose Profile Picture:</label>
                <input type="file" class="form-control" name="image">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                {!! Form::label('mobile', 'Mobile No.') !!}
                {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => 'eg: +91-123-456-7890']) !!}
                <small class="text-danger">{{ $errors->first('mobile') }}</small>
              </div>
              <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    {!! Form::label('username', 'Enter Username') !!}
                    <span class="required">*</span>
                    {!! Form::text('username', null, ['class' => 'form-control', 'placeholder'=>'Enter Your Username']) !!}
                    <small class="text-danger">{{ $errors->first('username') }}</small>
                  </div>

                  <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                  <label for="">Status: </label>
                 <input type="checkbox" class="toggle-input" name="status" id="toggle2">
                 <label for="toggle2"></label>
                <br>
              </div>

                 <div>
                    <img title="Current image" class="img-circle" width="100px" height="100px" src="{{ url('images/user/default.png') }}" alt="">
                  </div>

            </div>
          </div>
        
          <div class="btn-group pull-right">
            {!! Form::submit("Submit", ['class' => 'btn btn-wave']) !!}
          </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection