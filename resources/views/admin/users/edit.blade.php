@extends('layouts.admin', [
  'page_header' => 'Users'
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
  $email_error="";
  $password_error="";
  $username_error="";
  $mobile_error=""; 
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp
      @if($validationmessage!="" && isset($validationmessage['name']))
      @php $name_error=$validationmessage['name']; @endphp
      @else
      @php $name_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['email']))
      @php $email_error=$validationmessage['email']; @endphp
      @else
      @php $email_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['password']))
      @php $password_error=$validationmessage['password']; @endphp
      @else
      @php $password_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['username']))
      @php $username_error=$validationmessage['username']; @endphp
      @else
      @php $username_error=""; @endphp
      @endif
  @endif

 <div class="box">
    <div class="box-body">
        <h3>Edit User: {{ $user->name }}
          <a href="{{ route('users.index') }}" class="btn btn-gray pull-right"><i class="fa fa-arrow-left"></i> Back</a></h3>
      <hr>

      {!! Form::model($user, ['files' => true, 'method' => 'PATCH', 'action' => ['UsersController@update', $user->id]]) !!}
        
          <div class="row">
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                {!! Form::label('name', 'Name') !!}
                <span class="required">*</span>
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter your name']) !!}
                <small class="text-danger">{{$name_error}}</small>
              </div>
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                {!! Form::label('email', 'Email address') !!}
                <span class="required">*</span>
                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'eg: info@example.com','readonly'=>'readonly']) !!}
                <small class="text-danger">{{$email_error}}</small>
              </div>
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                {!! Form::label('password', 'Password') !!}
                <span class="required">*</span>
                {!! Form::password('password', ['class' => 'form-control', 'placeholder'=>'Change Your Password','readonly'=>'readonly']) !!}
                <small class="text-danger">{{$password_error}}</small>
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
                <small class="text-danger">{{$mobile_error}}</small>
              </div>
              <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    {!! Form::label('username', 'Enter Username') !!}
                    <span class="required">*</span>
                    {!! Form::text('username', null, ['class' => 'form-control', 'placeholder'=>'Enter Your Username']) !!}
                    <small class="text-danger">{{$username_error}}</small>
                  </div>

                  <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                  <label for="">Status: </label>
                 <input {{ $user->status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
                 <label for="toggle2"></label>
                <br>
              </div>

                @if($user->image !="")
                <img title="Current image" class="img-circle" width="100px" height="100px" src="{{ url('images/user/'.$user->image) }}" alt="">
                @else
                  <img title="Current image" class="img-circle" width="100px" height="100px" src="{{ url('images/user/default.png') }}" alt="">
                @endif
              
            </div>
          </div>
        
          <div class="btn-group pull-right">
            {!! Form::submit("Update", ['class' => 'btn btn-wave']) !!}
          </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection