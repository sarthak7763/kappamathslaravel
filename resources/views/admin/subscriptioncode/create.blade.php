@extends('layouts.admin', [
  'page_header' => 'Subscription Coupon'
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
  $coupon_type_error="";
  $coupon_title_error="";
  $coupon_date_error="";
  $coupon_time_error="";
  $user_type_error="";
  $coupon_users_error="";
  $coupon_user_limit_error="";
  $coupon_use_per_user_error="";
  $coupon_discount_type_error="";
  $coupon_discount_error="";
  $coupon_max_amount_error="";
  $minimum_transaction_amount_error="";
  $coupon_subscription_type_error="";
  $coupon_start_date_error="";
  $coupon_end_date_error="";
  @endphp

  @if (session()->has('valid_error'))
     @php $validationmessage=session()->get('valid_error'); @endphp

      @if($validationmessage!="" && isset($validationmessage['coupon_type']))
      @php $coupon_type_error=$validationmessage['coupon_type']; @endphp
      @else
      @php $coupon_type_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_title']))
      @php $coupon_title_error=$validationmessage['coupon_title']; @endphp
      @else
      @php $coupon_title_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_date']))
      @php $coupon_date_error=$validationmessage['coupon_date']; @endphp
      @else
      @php $coupon_date_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_time']))
      @php $coupon_time_error=$validationmessage['coupon_time']; @endphp
      @else
      @php $coupon_time_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['user_type']))
      @php $user_type_error=$validationmessage['user_type']; @endphp
      @else
      @php $user_type_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_users']))
      @php $coupon_users_error=$validationmessage['coupon_users']; @endphp
      @else
      @php $coupon_users_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_user_limi']))
      @php $coupon_user_limit_error=$validationmessage['coupon_user_limi']; @endphp
      @else
      @php $coupon_user_limit_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_use_per_user']))
      @php $coupon_use_per_user_error=$validationmessage['coupon_use_per_user']; @endphp
      @else
      @php $coupon_use_per_user_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_discount_type']))
      @php $coupon_discount_type_error=$validationmessage['coupon_discount_type']; @endphp
      @else
      @php $coupon_discount_type_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_discount']))
      @php $coupon_discount_error=$validationmessage['coupon_discount']; @endphp
      @else
      @php $coupon_discount_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_max_amount']))
      @php $coupon_max_amount_error=$validationmessage['coupon_max_amount']; @endphp
      @else
      @php $coupon_max_amount_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['minimum_transaction_amount']))
      @php $minimum_transaction_amount_error=$validationmessage['minimum_transaction_amount']; @endphp
      @else
      @php $minimum_transaction_amount_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_subscription_type']))
      @php $coupon_subscription_type_error=$validationmessage['coupon_subscription_type']; @endphp
      @else
      @php $coupon_subscription_type_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_start_date']))
      @php $coupon_start_date_error=$validationmessage['coupon_start_date']; @endphp
      @else
      @php $coupon_start_date_error=""; @endphp
      @endif

      @if($validationmessage!="" && isset($validationmessage['coupon_end_date']))
      @php $coupon_end_date_error=$validationmessage['coupon_end_date']; @endphp
      @else
      @php $coupon_end_date_error=""; @endphp
      @endif

      
  @endif

  <div class="box">
    <div class="box-body">
        <h3>Add Subscription Coupon
          <a href="{{ route('coupon-subscription.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </h3>
      <hr>

      {!! Form::open(['method' => 'POST', 'action' => 'SubscriptionCodeController@store','enctype'=>'multipart/form-data']) !!}

      <div class="row">

        <div class="col-md-6">  
            <div class="form-group{{ $errors->has('coupon_type') ? ' has-error' : '' }}">
              <label for="">Coupon Type: </label>
              <span class="required">*</span>
             <select class="form-control coupon_type" name="coupon_type">
              <option value="">Select</option>
              <option value="1">Voucher</option>
              <option value="2">Coupon</option>
             </select>
              <small class="text-danger">{{ $coupon_type_error }}</small>
            </div>
          </div> 

          <div class="col-md-6">
             <div class="form-group{{ $errors->has('coupon_title') ? ' has-error' : '' }}">
              {!! Form::label('coupon_title', 'Coupon Title') !!}
              <span class="required">*</span>
              {!! Form::text('coupon_title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Coupon Title']) !!}
              <small class="text-danger">{{ $coupon_title_error }}</small>
            </div>
          </div> 

          <div class="col-md-6">  
            <div class="form-group{{ $errors->has('coupon_start_date') ? ' has-error' : '' }}">
              <label for="">Coupon Start Date: </label>
              <span class="required">*</span>
             {!! Form::text('coupon_start_date', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Coupon Start Date','id'=>'coupon_start_date']) !!}
              <small class="text-danger">{{ $coupon_start_date_error }}</small>
            </div>
          </div>

          <div class="col-md-6">  
            <div class="form-group{{ $errors->has('coupon_end_date') ? ' has-error' : '' }}">
              <label for="">Coupon End Date: </label>
              <span class="required">*</span>
             {!! Form::text('coupon_end_date', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Coupon End Date','id'=>'coupon_end_date']) !!}
              <small class="text-danger">{{ $coupon_end_date_error }}</small>
            </div>
          </div> 

          <div class="col-md-6">  
            <div class="form-group{{ $errors->has('user_type') ? ' has-error' : '' }}">
              <label for="">User Type: </label>
              <span class="required">*</span>
             <select class="form-control user_type" name="user_type">
              <option value="">Select</option>
              <option value="1">All Users</option>
              <option value="2">Specific Users</option>
             </select>
              <small class="text-danger">{{ $user_type_error }}</small>
            </div>
          </div>

          <div class="col-md-6" id="coupon_users_div" style="display: none;">  
            <div class="form-group{{ $errors->has('coupon_users') ? ' has-error' : '' }}">
              <label for="">Coupon Users: </label>
              <span class="required">*</span>
             <select class="form-control" name="coupon_users[]" multiple="">
              <option value="">Select</option>
              @foreach($userlist as $key=>$list)
                <option value="{{$list['id']}}">{{$list['name']}}({{$list['email']}})</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $coupon_users_error }}</small>
            </div>
          </div>

          <div class="col-md-6" id="coupon_user_limit_div" style="display:none;">
             <div class="form-group{{ $errors->has('coupon_user_limit') ? ' has-error' : '' }}">
              {!! Form::label('coupon_user_limit', 'Coupon User Limit') !!}
              <span class="required">*</span>
              {!! Form::text('coupon_user_limit', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Coupon User Limit']) !!}
              <small class="text-danger">{{ $coupon_user_limit_error }}</small>
            </div>
          </div> 

          <div class="col-md-6">
             <div class="form-group{{ $errors->has('coupon_use_per_user') ? ' has-error' : '' }}">
              {!! Form::label('coupon_use_per_user', 'Coupon Use Per User') !!}
              <span class="required">*</span>
              {!! Form::text('coupon_use_per_user', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Coupon Use Per User']) !!}
              <small class="text-danger">{{ $coupon_use_per_user_error }}</small>
            </div>
          </div> 

          <div class="col-md-6" id="coupon_discount_type_div" style="display: none;">
             <div class="form-group{{ $errors->has('coupon_discount_type') ? ' has-error' : '' }}">
              {!! Form::label('coupon_discount_type', 'Coupon Discount Type') !!}
              <span class="required">*</span>
              <select class="form-control coupon_discount_type" name="coupon_discount_type" id="coupon_discount_type">
                <option value="">Select Type</option>
                <option value="1">Flat Discount</option>
                <option value="2">Percentage Discount</option>
              </select>
              <small class="text-danger">{{ $coupon_discount_type_error }}</small>
            </div>
          </div>

          <div class="col-md-6" id="coupon_discount_div" style="display: none;">
             <div class="form-group{{ $errors->has('coupon_discount') ? ' has-error' : '' }}">
              {!! Form::label('coupon_discount', 'Coupon Discount') !!}
              <span class="required">*</span>
              {!! Form::text('coupon_discount', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Coupon Discount']) !!}
              <small class="text-danger" id="coupon_discount_error">{{ $coupon_discount_error }}</small>
            </div>
          </div> 

          <div class="col-md-6" id="min_trans_amount_div" style="display: none;">
             <div class="form-group{{ $errors->has('minimum_transaction_amount') ? ' has-error' : '' }}">
              {!! Form::label('minimum_transaction_amount', 'Minimum Transaction Amount') !!}
              <span class="required">*</span>
              {!! Form::text('minimum_transaction_amount', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Minimum Transaction Amount']) !!}
              <small class="text-danger">{{ $minimum_transaction_amount_error }}</small>
            </div>
          </div>

          <div class="col-md-6" id="coupon_max_amount_div" style="display: none;">
             <div class="form-group{{ $errors->has('coupon_max_amount') ? ' has-error' : '' }}">
              {!! Form::label('coupon_max_amount', 'Coupon Max Amount') !!}
              <span class="required">*</span>
              {!! Form::text('coupon_max_amount', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Coupon Max Amount']) !!}
              <small class="text-danger">{{ $coupon_max_amount_error }}</small>
            </div>
          </div> 

          <div class="col-md-6">  
            <div class="form-group{{ $errors->has('coupon_subscription_type') ? ' has-error' : '' }}">
              <label for="">Coupon Subscription Type: </label>
              <span class="required">*</span>
             <select class="form-control" name="coupon_subscription_type">
              <option value="">Select</option>
              <option value="0">All</option>
              @foreach($subscriptionlist as $key=>$list)
                <option value="{{$list['id']}}">{{$list['title']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $coupon_subscription_type_error }}</small>
            </div>
          </div>


       

           <div class="col-md-6">
            <div class="form-group{{ $errors->has('coupon_description') ? ' has-error' : '' }}">
              {!! Form::label('coupon_description', 'Coupon Description') !!}
              {!! Form::textarea('coupon_description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>
          </div> 
          <div class="col-md-6">  
            <label for="">Status: </label>
            <input type="checkbox" class="toggle-input" name="status" id="toggle2">
            <label for="toggle2"></label>
          </div>   
          </div>
        </div>

        <div class="btn-group pull-right">
          {!! Form::submit("Save", ['class' => 'btn btn-wave','id'=>'btnsubmit']) !!}
        </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">

  $('.user_type').on('change',function(){
    var user_type=$(this).val();
    if(user_type=="1")
    {
      $('#coupon_users_div').hide();
      $('#coupon_user_limit_div').show();
    }
    else{
      $('#coupon_user_limit_div').hide();
      $('#coupon_users_div').show();
    }
  });

  $('.coupon_discount_type').on('change',function(){
    var coupon_discount_type=$(this).val();
    if(coupon_discount_type=="1")
    {
      $('#coupon_discount').val('');
      $('#coupon_discount_error').html('');
      $('#coupon_max_amount_div').hide();
    }
    else{
      $('#coupon_discount').val('');
      $('#coupon_discount_error').html('');
      $('#coupon_max_amount_div').show();
    }
  });

  $('.coupon_type').on('change',function(){
    var coupon_type=$(this).val();
    if(coupon_type=="1")
    { 
      $('#min_trans_amount_div').hide();
      $('#coupon_discount_type_div').hide();
      $('#coupon_discount_div').hide();
    }
    else{ 
      $('#min_trans_amount_div').show();
      $('#coupon_discount_type_div').show();
      $('#coupon_discount_div').show();
    }
  });


  $('#coupon_discount').keyup(function(){
    var coupon_type=$('#coupon_discount_type').val();
    var value=$(this).val();
    if(coupon_type=="2")
    {
      if(value <= 100) {
        $("#btnsubmit"). attr("disabled", false);
        $('#coupon_discount_error').html('');
        $(this).val(value);
      }
      else{
         $("#btnsubmit"). attr("disabled", true); 
        $('#coupon_discount_error').html('Please enter amount less than or equal to 100.');
      }
    }
    else{
      $("#btnsubmit"). attr("disabled", false);
      $('#coupon_discount_error').html('');
      $(this).val(value);
    }
    
});

</script>

<script>

 $("#coupon_start_date").datepicker({
format: "mm/dd/yy"
});

 $("#coupon_end_date").datepicker({
format: "mm/dd/yy"
});

</script>

<script>
  CKEDITOR.replace( 'description' );
</script>

@endsection