@extends('layouts.admin', [
  'page_header' => 'Your Profile',
  'dash' => '',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
  @if ($auth)

    <!-- Is user is admin -->
    @if ($auth->role == 'A')
      <div class="box">
        <div class="box-body">
          <!-- Form Start -->
          {!! Form::model($auth, ['files' => true,'method' => 'PATCH', 'action' => ['UsersController@update', $auth->id]]) !!}
            <div class="row">

              <div class="col-md-6">
                <!-- Name -->
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                  {!! Form::label('name', 'Name') !!}
                  <span class="required">*</span>
                  {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Your Name']) !!}
                  <small class="text-danger">{{ $errors->first('name') }}</small>
                </div>

                <!-- Email -->
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                  {!! Form::label('email', 'Email address') !!}
                  <span class="required">*</span>
                  {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'eg: info@example.com', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('email') }}</small>
                </div>

                <!-- Password -->
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                  {!! Form::label('password', 'Password') !!}
                  <span class="required">*</span>
                  {!! Form::password('password', ['class' => 'form-control', 'placeholder'=>'Change Your Password']) !!}
                  <small class="text-danger">{{ $errors->first('password') }}</small>
                </div>

                <!-- Role -->
                <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                  {!! Form::label('role', 'Role') !!}
                  <span class="required">*</span>
                  {!! Form::select('role', ['S' => 'Student', 'A'=>'Administrator'], null, ['class' => 'form-control select2', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('role') }}</small>
                </div>

                <!-- User Profile -->
                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                  <label for="image">Choose Profile Picture:</label>
                  <input type="file" class="form-control" name="image">
                </div>
                 @if($auth->image !="")
                  <img title="Current image" class="img-circle" width="100px" height="100px" src="{{ url('images/user/'.$auth->image) }}" alt="user profile">
                @else
                    <img title="Current image" class="img-circle" width="100px" height="100px" src="{{ Avatar::create(ucfirst($auth->name))->toBase64() }}" alt="user profile">
                @endif
              </div>

              <div class="col-md-6 margin-bottom">
                <!-- Mobile Number -->
                <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                  {!! Form::label('mobile', 'Mobile No.') !!}
                  {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => 'eg: +91-123-456-7890']) !!}
                  <small class="text-danger">{{ $errors->first('mobile') }}</small>
                </div>

                <!-- City -->
                <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                  {!! Form::label('city', 'Enter City') !!}
                  {!! Form::text('city', null, ['class' => 'form-control', 'placeholder'=>'Enter Your City']) !!}
                  <small class="text-danger">{{ $errors->first('city') }}</small>
                </div>
                
                <!-- Address -->
                <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                    {!! Form::label('address', 'Address') !!}
                    {!! Form::textarea('address', null, ['class' => 'form-control', 'rows'=>'5', 'placeholder' => 'Enter Your Address']) !!}
                    <small class="text-danger">{{ $errors->first('address') }}</small>
                </div>
                </div>

                <!-- User Profile -->
               
                <br><br>

                <!-- Update Button -->
                <div class="col-md-2">
                  {!! Form::submit('Update', ['class' => 'btn btn-wave btn-block']) !!}
                </div>
              </div>
          {!! Form::close() !!}
          <!-- Form End -->
        </div>
      </div>

    <!-- Is user is Student -->
    @elseif ($auth->role == 'S')
      <div class="box">
        <div class="box-body">
          <!-- Form Start -->
          {!! Form::model($auth, ['files' => true, 'method' => 'PATCH', 'action' => ['UsersController@update', $auth->id]]) !!}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                  {!! Form::label('name', 'Name') !!}
                  {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Enter Your Name']) !!}
                  <small class="text-danger">{{ $errors->first('name') }}</small>
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                  {!! Form::label('email', 'Email address') !!}
                  {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'eg: info@example.com', 'required' => 'required']) !!}
                  <small class="text-danger">{{ $errors->first('email') }}</small>
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                  {!! Form::label('password', 'Password') !!}
                  {!! Form::password('password', ['class' => 'form-control', 'placeholder'=>'Change Your Password']) !!}
                  <small class="text-danger">{{ $errors->first('password') }}</small>
                </div>
                <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                  {!! Form::label('mobile', 'Mobile No.') !!}
                  {!! Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => 'eg: +91-123-456-7890']) !!}
                  <small class="text-danger">{{ $errors->first('mobile') }}</small>
                </div>
                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                  <label for="image">Choose Profile Picture:</label>
                  <input type="file" class="form-control" name="image">
                </div>
              </div>
              <div class="col-md-6 margin-bottom">
                <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                  {!! Form::label('city', 'Enter City') !!}
                  {!! Form::text('city', null, ['class' => 'form-control', 'placeholder'=>'Enter Your City']) !!}
                  <small class="text-danger">{{ $errors->first('city') }}</small>
                </div>
                <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                  {!! Form::label('address', 'Address') !!}
                  {!! Form::textarea('address', null, ['class' => 'form-control', 'rows'=>'8', 'placeholder' => 'Enter Your Address']) !!}
                  <small class="text-danger">{{ $errors->first('address') }}</small>
                </div>
                 @if($auth->image !="")
                                <img title="Current image" class="img-circle" width="100px" height="100px" src="{{ url('images/user/'.$auth->image) }}" alt="">
                                @else
                                  <img title="Current image" class="img-circle" width="100px" height="100px" src="{{ Avatar::create(ucfirst($auth->name))->toBase64() }}" alt="">
                                @endif
                                <br><br>
              </div>
              <div class="col-md-offset-3 col-md-6">
                {!! Form::submit('Update', ['class' => 'btn btn-wave btn-block']) !!}
              </div>
            </div>
          {!! Form::close() !!}
          <!-- Form End -->
        </div>
      </div>
    @endif

  @endif
@endsection
