@extends('layouts.admin', [
  'page_header' => 'Students',
  'dash' => '',
  'course'=>'',
  'quiz' => '',
  'users' => 'active',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
 <div class="box">
    <div class="box-body">
        <h3>Edit User: {{ $user->name }}
          <a href="{{url()->previous()}}" class="btn btn-gray pull-right"><i class="fa fa-arrow-left"></i> Back</a></h3>
      <hr>

      {!! Form::model($user, ['files' => true, 'method' => 'PATCH', 'action' => ['UsersController@update', $user->id]]) !!}
        
          <div class="row">
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                {!! Form::label('name', 'Name') !!}
                <span class="required">*</span>
                {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter your name']) !!}
                <small class="text-danger">{{ $errors->first('name') }}</small>
              </div>
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                {!! Form::label('email', 'Email address') !!}
                <span class="required">*</span>
                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'eg: info@example.com', 'required' => 'required']) !!}
                <small class="text-danger">{{ $errors->first('email') }}</small>
              </div>
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                {!! Form::label('password', 'Password') !!}
                <span class="required">*</span>
                {!! Form::password('password', ['class' => 'form-control', 'placeholder'=>'Change Your Password']) !!}
                <small class="text-danger">{{ $errors->first('password') }}</small>
              </div>
              <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                  {!! Form::label('role', 'User Role') !!}
                  <span class="required">*</span>
                  {!! Form::select('role', ['S' => 'Student', 'A'=>'Administrator'], null, ['class' => 'form-control select2', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('role') }}</small>
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
              <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                {!! Form::label('city', 'Enter City') !!}
                {!! Form::text('city', null, ['class' => 'form-control', 'placeholder'=>'Enter Your City']) !!}
                <small class="text-danger">{{ $errors->first('city') }}</small>
              </div>

              <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                {!! Form::label('address', 'Address') !!}
                {!! Form::textarea('address', null, ['class' => 'form-control', 'rows'=>'5', 'placeholder' => 'Enter Your Address']) !!}
                <small class="text-danger">{{ $errors->first('address') }}</small>
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