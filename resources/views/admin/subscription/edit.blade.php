@extends('layouts.admin', [
  'page_header' => 'Subscription Plans'
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Add Subscription Plan
          <a href="{{ route('subscription.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::model($subscription, ['method' => 'PATCH','enctype'=>'multipart/form-data', 'action' => ['SubscriptionController@update', $subscription->id]]) !!}

      <div class="row">

        @if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('deleted'))
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('added'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ $message }}</strong>
</div>
@endif

          <div class="col-md-6">

             <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>

            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>

            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
              {!! Form::label('price', 'Price') !!}
              <span class="required">*</span>
              {!! Form::text('price', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Price']) !!}
              <small class="text-danger">{{ $errors->first('price') }}</small>
            </div>

            <div class="form-group{{ $errors->has('subscription_date') ? ' has-error' : '' }}">
              <label for="">Subscription Date: </label>
              <span class="required">*</span>
             <select class="form-control" name="subscription_date">
              <option value="">Select</option>
              @for($i=1;$i<=10;$i++)
                <option <?php if($subscription->subscription_date==$i){echo "selected";} ?> value="{{$i}}">{{$i}}</option>
              @endfor
             </select>
              <small class="text-danger">{{ $errors->first('subscription_date') }}</small>
            </div>

            <div class="form-group{{ $errors->has('subscription_plan') ? ' has-error' : '' }}">
              <label for="">Subscription Plan Month: </label>
              <span class="required">*</span>
             <select class="form-control" name="subscription_plan">
              <option value="">Select</option>
              @foreach($montharray as $key=>$list)
                <option <?php if($subscription->subscription_plan==$key){echo "selected";} ?> value="{{$key}}">{{$list}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $errors->first('subscription_plan') }}</small>
            </div>

            <label for="">Status: </label>
             <input {{ $subscription->subscription_status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
             <label for="toggle2"></label>

          </div>
        </div>

        <div class="btn-group pull-right">
          {!! Form::submit("Save", ['class' => 'btn btn-wave']) !!}
        </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection