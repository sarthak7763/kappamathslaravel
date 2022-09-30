@extends('layouts.app')

@section('head')
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <script>
    window.Laravel =  <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>
  </script>
@endsection

@section('content')
  <div class="">
    <div class="container">
      @if (Session::has('error'))
        <div class="alert alert-danger sessionmodal">
          {{session('error')}}
        </div>
      @endif

      <div class="login-page">
        <div class="logo">
          @if ($setting)
            <a href="{{url('/')}}" title="{{$setting->welcome_txt}}">
              <img src="{{asset('/images/logo/'.$setting->logo)}}" class="login-logo img-responsive" alt="{{$setting->welcome_txt}}">
            </a>
          @endif
        </div>

        <h4 class="user-register-heading text-center">Login</h4>
        <div class="row">
          @php
            $fb_status = App\Setting::select('fb_login')->where('id','=',1)->first();
            $g_status = App\Setting::select('google_login')->where('id','=',1)->first();
            $gitlab_status = App\Setting::select('gitlab_login')->where('id','=',1)->first();
          @endphp

          @if($fb_status->fb_login == 1)
            <div class="col-md-12">
              <a onclick="window.open('{{ route('sociallogin','facebook') }}','popup','width=600','height=600')" class="btn btn-facebook btn-block">
                <i class="fa fa-facebook"></i> {{ __('Facebook')}}
              </a>
            </div>
          @endif

          @if($gitlab_status->gitlab_login == 1)
            <div class="gap col-md-12 mt-5">
              <a  onclick="window.open('{{ route('sociallogin','gitlab') }}','popup','width=600','height=600')"  class="btn btn-gitlab btn-block">
              <i class="fa fa-gitlab"></i> {{ __('Gitlab')}}
            </a>
            </div>
          @endif
          
          @if($g_status->google_login == 1)
            <div class="gap col-md-12 mt-5">
              <a onclick="window.open('{{ route('sociallogin','google') }}','popup','width=600','height=600')"  class="btn btn-google btn-block">
                <i class="fa fa-google"></i> Google
              </a>
            </div>
          @endif
        </div>
        <br>

        <form class="form login-form" method="POST" action="{{ route('login') }}">
          {{ csrf_field() }}
          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Enter Your Email" required autofocus>
            @if ($errors->has('email'))
              <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
              </span>
            @endif
          </div>

          <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password" placeholder="Enter Password" required>
            @if ($errors->has('password'))
              <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
              </span>
            @endif
          </div>
          
          <div class="form-group">
            <div class="checkbox remember-me">
              <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
              </label>
               Remember Me
            </div>
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-wave"> Login </button>
          </div>

          <div class="form-group text-center">
            <a href="{{url('/password/reset')}}" title="Forgot Password">Forgot Password?</a>
          </div>
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
@endsection
