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
  <div class="vertical-center">
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

        <h4 class="user-register-heading text-center">Forget Password?</h4>
        @if (session('status'))
          <div class="alert alert-success">
              {{ session('status') }}
          </div>
        @endif

        <form class="form form-login" method="POST" action="{{ route('password.email') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
              <!-- <label for="email" class="control-label text-center">E-Mail Address</label> -->
                <i class="fa fa-envelope"></i>
                    <input id="email" placeholder="Email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div> 
              
                        <div class="form-group text-center">
              <button type="submit" class="btn btn-primary">
                  Send Password Reset Link
              </button>
              <a href="{{url('/login')}}"> <u> <i class="fa fa-arrow-left"></i> Get back to Login Page </u> </a>
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
