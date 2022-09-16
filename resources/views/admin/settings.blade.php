@extends('layouts.admin', [
  'page_header' => 'Settings',
  'dash' => '',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => 'active'
])

@section('content')

  @php
    $setting = $settings[0];
  @endphp

  {!! Form::model($setting, ['method' => 'PATCH', 'action' => ['SettingController@update', $setting->id], 'files' => true]) !!}
  <div class="row">
    <div class="col-md-8">
      <div class="box">
        <div class="box-body settings-block">
          <!-- Project Name -->
          <div class="form-group{{ $errors->has('welcome_txt') ? ' has-error' : '' }}">
            {!! Form::label('welcome_txt', 'Project Name') !!}
            <p class="label-desc">{{ __('Please Enter Your Project Name')}}</p>
            {!! Form::text('welcome_txt', null, ['class' => 'form-control']) !!}
            <small class="text-danger">{{ $errors->first('welcome_txt') }}</small>
          </div>

          <!-- Project URL -->
          <div class="form-group{{ $errors->has('APP_URL') ? ' has-error' : '' }}">
            {!! Form::label('APP_URL', 'Project URL') !!}
            <p class="label-desc">{{ __('Please Enter Your Project URL')}}</p>
            <input class="form-control" type="text" name="APP_URL" value="{{env('APP_URL')? env('APP_URL') : ''}}" placeholder="Please enter your App URL eg: https://yourdomain.com/">
            <small class="text-danger">{{ $errors->first('APP_URL') }}</small>
          </div>

          <div class="row">
            <!-- Logo -->
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('logo') ? ' has-error' : '' }}">
                {!! Form::label('logo', 'Logo Select') !!}
                <p class="label-desc"> {{ __('Please Select Logo') }} </p>
                {!! Form::file('logo') !!}
                <small class="text-danger">{{ $errors->first('logo') }}</small>
              </div>
              <div class="logo-block">
                <img src="{{asset('/images/logo/'. $setting->logo)}}" class="img-responsive"  alt="{{$setting->welcome_txt}}">
              </div>
            </div>

            <!-- Favicon -->
            <div class="col-md-6">
              <div class="form-group{{ $errors->has('favicon') ? ' has-error' : '' }}">
                {!! Form::label('favicon', 'Favicon Select') !!}
                <p class="label-desc"> {{ __('Please Select Favicon') }} </p>
                {!! Form::file('favicon') !!}
                <small class="text-danger">{{ $errors->first('favicon') }}</small>
              </div>
            </div>

            <!-- Default Email -->
            <div class="col-md-6">
               <div class="form-group{{ $errors->has('w_email') ? ' has-error' : '' }}">
                  {!! Form::label('w_email', 'Default Email') !!}
                   <p class="label-desc">Please enter your default email</p>
                  {!! Form::email('w_email', null, ['class' => 'form-control', 'placeholder' => 'eg: foo@bar.com','required']) !!}
                  <small class="text-danger">{{ $errors->first('w_email') }}</small>
              </div>
            </div>

            <!-- Currency Code -->
            <div  class="col-md-6">
              <div class="form-group{{ $errors->has('currency_code') ? ' has-error' : '' }}">
                {!! Form::label('currency_code', 'Currency Code') !!}
                 <p class="label-desc">- Please enter your curreny code</p>
                {!! Form::text('currency_code', null, ['class' => 'form-control']) !!}
                <small class="text-danger">{{ $errors->first('currency_code') }}</small>
              </div>
            </div>

            <!-- Currency Symbol -->
            <div class="col-md-6">
               <div class="form-group{{ $errors->has('currency_symbol') ? ' has-error' : '' }} currency-symbol-block">
                {!! Form::label('currency_symbol', 'Currency Symbol') !!}
                <p class="label-desc"> - Please select your currency symbol</p>
                  <div class="input-group">
                    {!! Form::text('currency_symbol', null, ['class' => 'form-control currency-icon-picker']) !!}
                    <span class="input-group-addon simple-input"><i class="fa fa-money"></i></span>
                  </div>
                <small class="text-danger">{{ $errors->first('currency_symbol') }}</small>
              </div>
            </div>

            <!-- Welcome Email -->
            <div class="col-md-6">
              <div class="form-group">
               <label for="wel_mail">Welcome email for user:</label>
                <input {{ $setting->wel_mail == 1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="wel_mail" id="wel_mail">
                <label for="wel_mail"></label>
              </div>
            </div>

            <!-- Debug -->
            <div class="col-md-6">
              <div class="form-group">
               <label for="APP_DEBUG">Debug:</label>
               <input type="checkbox" {{env('APP_DEBUG') == true ? "checked" : ""}} name="APP_DEBUG" class="toggle-input" data-size="small" id="APP_DEBUG">
                <label for="APP_DEBUG"></label>
              </div>
            </div>

            <!-- Right Click -->
            <div class="col-md-6">
              <div class="form-group">
               <label for="status">Right Click Disable:</label>
                <input {{ $setting->right_setting == 1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="rightclick" id="rightclick">
                <label for="rightclick"></label>
              </div>
            </div>

            <!-- Inspect element -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="status">Inspect Element Disable:</label>
                    <input {{ $setting->element_setting == 1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="inspect" id="inspect">
                    <label for="inspect"></label>
              </div>
            </div>

            <!-- Coming Soon -->
            <div class="col-md-6">
              <div class="col-sm-5">
                <div class="form-group">
                 <label for="status">Coming Soon:</label>
                  <input {{ $setting->coming_soon == 1 ? "checked" : "" }} type="checkbox" class="toggle-input coming_soon" name="coming_soon" id="coming_soon" onChange ='iscomingsoon()'>
                  <label for="coming_soon"></label>
                </div>
              </div>
              
              <div class="col-sm-7" id="coming_soon_link" style="{{ $setting->coming_soon == '1' ? " " : "display: none" }}">
                <div class="form-group" style="width:100% !important;">
                   <label for="status">Coming Enabled IP:</label>
                  <select class="form-control select2" name="comingsoon_enabled_ip[]" multiple="multiple">
                   @if(isset($setting->comingsoon_enabled_ip) &&  $setting->comingsoon_enabled_ip != NULL)
                      @foreach($setting->comingsoon_enabled_ip as $enable_ip)
                        <option value="{{$enable_ip}}" @if(isset($enable_ip)) selected="" @endif>{{$enable_ip}}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
              </div>
            </div>
            
            <!-- Repeat Quiz -->
            {{-- <div class="col-md-6">
              <div class="form-group">
               <label for="">User can repeat Quiz?</label>
                <select name="userquiz" id="">
                       <option @if($setting->userquiz == 1) selected @endif value="1">Yes</option>
                       <option @if($setting->userquiz == 0) selected @endif value="0">No</option>
                </select>
             </div>
            </div>  --}}             
          </div>

          <!-- Save setting button -->
          {!! Form::submit("Save Setting", ['class' => 'btn btn-wave btn-block']) !!}
        </div>
       
      </div>
    </div>
  </div>
  {!! Form::close() !!}

@endsection

@section('scripts')
  <!---------- comming soon --------->
  <script type="text/javascript">
    function iscomingsoon()
    {
      if($('.coming_soon').is(":checked"))   
        $("#coming_soon_link").show();
      else
        $("#coming_soon_link").hide();
    }
  </script>
@endsection
