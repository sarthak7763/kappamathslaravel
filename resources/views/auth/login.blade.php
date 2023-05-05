@extends('layouts.app')

@section('head')
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <script>
    window.Laravel =  <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>
  </script>

  <style type="text/css">
    .field-icon {
  float: right;
  margin-left: -25px;
  margin-top: -40px;
  position: relative;
  z-index: 2;
}
  </style>
@endsection

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
  $email_error="";
  $password_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp
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
  @endif
    <div class="container">
      <div class="vertical-center">
      <div class="login-page">
        <div class="logo">
          @if ($setting)
            <a href="{{url('/')}}" title="{{$setting->welcome_txt}}">
              <img src="{{asset('/images/logo/'.$setting->logo)}}" class="login-logo img-responsive" alt="{{$setting->welcome_txt}}">
            </a>
          @endif
        </div>

        <h4 class="user-register-heading text-center">Login</h4>
        <br>

        <form class="form login-form" method="POST" action="{{ route('checkwebuserlogin') }}">
          {{ csrf_field() }}
          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <i class="fa fa-envelope"></i>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter Your Email" autofocus>
            <small class="text-danger">{{$email_error}}</small>
          </div>

          <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password" placeholder="Enter Password" value="{{ old('password') }}">
            <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
            <small class="text-danger">{{$password_error}}</small>
          </div>
          
          <div class="form-group">
            <div class="checkbox remember-me">
              <label>
               Remember Me
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
              <span class="checkmark"></span>
              </label>
            </div>
            
            <a href="{{url('/password/reset')}}" title="Forgot Password">Forgot Password?</a>
       
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-wave"> Login </button>
          </div>
         <p class="login-acc">Don't have an account <a href="https://kappamaths.ezxdemo.com/register" title="don't have an account please sign up" class="text-center btn-block">sign up</a>

          
        </form>
      </div>
    </div>
  </div>    
@endsection

@section('scripts')
  <script>
    $(function () {
      $( document ).ready(function() {
         $('.sessionmodal').addClass("active");
         setTimeout(function() {
             $('.sessionmodal').removeClass("active");
        }, 4500);
      });
    });
  </script>

  <script type="text/javascript">
    $(".toggle-password").click(function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
      input.attr("type", "text");
    } else {
      input.attr("type", "password");
    }
  });
  </script>
@endsection
