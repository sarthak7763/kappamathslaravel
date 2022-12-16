@extends('layouts.admin', [
  'page_header' => 'Home Banner'
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

      {!! Form::open(['method' => 'POST', 'action' => 'HomeBannerController@submithomebannerinfo','enctype'=>'multipart/form-data']) !!}

      <div class="row">

          <div class="col-md-6">

            <div class="form-group{{ $errors->has('banner_type') ? ' has-error' : '' }}">
              <label for="">Banner Type: </label>
              <span class="required">*</span>
             <select class="form-control" name="banner_type">
              <option value="">Select</option>
              <option {{ $homebannerarray['banner_type'] =='Core' ? "selected" : "" }} value="Core">Core</option>
              <option {{ $homebannerarray['banner_type'] =='Elective' ? "selected" : "" }} value="Elective">Elective</option>
             </select>
              <small class="text-danger">{{ $errors->first('banner_type') }}</small>
            </div>

             <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Title') !!}
              <span class="required">*</span>
              <input class="form-control" placeholder="Please Enter Title" name="title" type="text" id="title" value="{{$homebannerarray['title']}}">
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>

            <div class="form-group{{ $errors->has('sub_title') ? ' has-error' : '' }}">
              {!! Form::label('sub_title', 'Sub Title') !!}
              <input class="form-control" placeholder="Please Enter Sub Title" name="sub_title" type="text" id="sub_title" value="{{$homebannerarray['sub_title']}}">

              <small class="text-danger">{{ $errors->first('sub_title') }}</small>
            </div>
          </div>

            <div class="col-md-6">
            <div class="form-group{{ $errors->has('event_date') ? ' has-error' : '' }}">
            {!! Form::label('event_date', 'Event Date') !!}
            <input class="form-control" placeholder="Please Enter Event Date" name="event_date" type="text" id="event_date" value="{{$homebannerarray['event_date']}}">
            <small class="text-danger">{{ $errors->first('event_date') }}</small>
          </div>

          <div class="form-group{{ $errors->has('event_link') ? ' has-error' : '' }}">
              {!! Form::label('event_link', 'Event Link') !!}
              <span class="required">*</span>
              <input class="form-control" placeholder="Please Enter Event Link" name="event_link" type="text" id="event_link" value="{{$homebannerarray['event_link']}}">
              <small class="text-danger">{{ $errors->first('event_link') }}</small>
            </div>

          </div>
        </div>

        <div class="btn-group pull-right">
          {!! Form::submit("Save", ['class' => 'btn btn-wave']) !!}
        </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection

@section('scripts')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>


<script>
  $(function(){
    $( "#event_date" ).datetimepicker({
      changeMonth: true,
      changeYear: true,
      timeFormat: "h:mm TT",
      ampm: true
    });
  });

  </script>

@endsection