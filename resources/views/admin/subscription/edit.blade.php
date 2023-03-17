@extends('layouts.admin', [
  'page_header' => 'Subscription Plans'
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
  $title_error="";
  $price_error="";
  $subscription_tenure_error="";
  $subscription_plan_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp
      @if($validationmessage!="" && isset($validationmessage['title']))
      @php $title_error=$validationmessage['title']; @endphp
      @else
      @php $title_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['price']))
      @php $price_error=$validationmessage['price']; @endphp
      @else
      @php $price_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['subscription_tenure']))
      @php $subscription_tenure_error=$validationmessage['subscription_tenure']; @endphp
      @else
      @php $subscription_tenure_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['subscription_plan']))
      @php $subscription_plan_error=$validationmessage['subscription_plan']; @endphp
      @else
      @php $subscription_plan_error=""; @endphp
      @endif

      
  @endif
  <div class="box">
    <div class="box-body">
        <h3>Edit Subscription Plan
          <a href="{{ route('subscription.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::model($subscription, ['method' => 'PATCH','enctype'=>'multipart/form-data', 'action' => ['SubscriptionController@update', $subscription->id]]) !!}

      <div class="row">

          <div class="col-md-6">
             <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{ $title_error }}</small>
            </div>
          </div>  
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
              {!! Form::label('price', 'Price') !!}
              <span class="required">*</span>
              {!! Form::text('price', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Price']) !!}
              <small class="text-danger">{{ $price_error }}</small>
            </div>
          </div>
          <div class="col-md-6">  
            <div class="form-group{{ $errors->has('subscription_plan') ? ' has-error' : '' }}">
              <label for="">Subscription Plan: </label>
              <span class="required">*</span>
             <select class="form-control" name="subscription_plan">
              <option value="">Select</option>
              @foreach($montharray as $key=>$list)
                <option <?php if($subscription->subscription_plan==$key){echo "selected";} ?> value="{{$key}}">{{$list}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $errors->first('subscription_plan_error') }}</small>
            </div>
          </div> 

          <div class="col-md-6">  
            <div class="form-group{{ $errors->has('subscription_tenure') ? ' has-error' : '' }}">
              <label for="">Subscription Tenure: </label>
              <span class="required">*</span>
             {!! Form::text('subscription_tenure', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Subscription Tenure']) !!}
              <small class="text-danger">{{ $subscription_tenure_error }}</small>
            </div>
          </div>
           
           <div class="col-md-6">
            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>
          </div>  
          <div class="col-md-6">  
            <label for="">Status: </label>
             <input {{ $subscription->subscription_status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
             <label for="toggle2"></label>
             
          </div>
        </div>

        <div class="btn-group pull-right">
          {!! Form::submit("Update", ['class' => 'btn btn-wave']) !!}
        </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection

@section('scripts')

<script type="text/javascript">
  $('#price').prop('readonly', true);
</script>

@endsection