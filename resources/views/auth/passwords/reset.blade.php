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
    margin-left: -21px;
    margin-top: -28px;
    position: relative;
    z-index: 2;
    margin-right: 5px;
  }

  .field-icon-password {
    float: right;
    margin-left: -21px;
    margin-top: -28px;
    position: relative;
    z-index: 2;
    margin-right: 5px;
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
  $password_error="";
  $password_confirm_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp
      @if($validationmessage!="" && isset($validationmessage['password']))
      @php $password_error=$validationmessage['password']; @endphp
      @else
      @php $password_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['password_confirmation']))
      @php $password_confirm_error=$validationmessage['password_confirmation']; @endphp
      @else
      @php $password_confirm_error=""; @endphp
      @endif
  @endif

  <div style="margin-top: -25px;" class="">
    <div class="container">
      <div class="login-page">
        <div class="logo">
          @if ($setting)
            <a href="{{url('/')}}" title="{{$setting->welcome_txt}}"><img src="{{asset('/images/logo/'.$setting->logo)}}" class="login-logo img-responsive" alt="{{$setting->welcome_txt}}"></a>
          @endif
        </div>
        <h4 class="user-register-heading text-center">Admin Reset Password</h4>
      
         <form class="form-horizontal" method="POST" action="{{ route('adminresetpassword') }}">
                        {{ csrf_field() }}
                        
                        <input type="hidden" name="forgot_token" value="{{$forgot_token}}">

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                          <div class="row">
                             <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-8">
                                <input id="password" type="password" class="form-control" name="password">
                                <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                               <small class="text-danger">{{$password_error}}</small>
                            </div>
                          </div>
                           
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                          <div class="row">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-8">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                                <span toggle="#password-confirm" class="fa fa-fw fa-eye field-icon-password toggle-password-confirm"></span>
                                <small class="text-danger">{{$password_confirm_error}}</small>
                            </div>
                          </div>
                            
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
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

    $(".toggle-password-confirm").click(function() {
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
