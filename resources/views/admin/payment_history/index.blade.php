@extends('layouts.admin', [
  'page_header' => 'Payment History'
])
<style>
.example1_wrapper row col-md-4 dt-buttons { display:none;}
</style>
@section('content')
<div class="row">
    <div class="col-md-12">
      <form method="post" action="{{ url('admin/payment') }}" autocomplete="off">
        @csrf
        <div class="row">  
          <div class="col-md-5">
            <div class="form-group">
              <label for="">User Name:</label>
              <select class="form-control" name="user_id">
                <option value="">Select User</option>
                @foreach($users as $value)
                  @if($userid==$value->id)
                    @php $userselected="selected"; @endphp
                    @else
                    @php $userselected=""; @endphp
                    @endif
                  <option {{$userselected}} value="{{ $value->id }}">{{ $value->name }}</option>
                @endforeach
              </select>
              <small class="text-danger"></small>
            </div>
          </div>
          <div class="col-md-2">
            <div class="btn-group pull-right" style="margin-top: 26px;">
              <input class="btn btn-wave" type="submit" value="Submit">
            </div>
          </div>

          @if($clear_filter==1)
          <div class="col-md-2">
            <div class="btn-group pull-right" style="margin-top: 26px;">
              <a href="{{ route('admin.payment')}}"><button class="btn btn-wave clearfilterbtn" type="button">Clear Filter</button></a>
            </div>
          </div>
          @endif
        </div>  
      </form>
    </div>
  </div>

  <div class="content-block box">
    <div class="box-body table-responsive">
      <table id="subscriptiontable" class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Subscription</th>
            <th>Amount</th>
            <th>Payment ID</th>
            <th>Status</th>
            <th>Subscription Start Date</th>
            <th>Subscription End Date</th>
            <th>Coupon/Voucher</th>
            <th>Discount Amount</th>
            <th>Total Amount</th>
          </tr>
        </thead>
        <tbody>
        @if($user_subscription_array)
            @php($n = 1)
            @foreach ($user_subscription_array as $key => $item)
              <tr>
                <td>
                  {{$n}}
                  @php($n++)
                </td>
                <td>{{ $item['username'] }}</td>
                <td>{{ $item['subscriptionname'] }}</td>
                <td>{{$item['subscription_payment']}} GHS</td>
                <td>{{$item['transaction_id']}}</td>
                <td>{{$item['subscription_status'] == 1 ? 'Successful' : 'Unsuccessful'}}</td>
                <td>{{$item['subscription_start'] }}</td>
                <td>{{$item['subscription_end'] }}</td>
                @if($item['couponname']!="")
                <td>{{$item['couponname'] }} ({{$item['coupon_type'] }})</td>
                @else
                <td>N.A.</td>
                @endif
                <td>{{$item['coupon_discount'] }} GHS</td>
                <td>{{$item['total_amount'] }} GHS</td>
              </tr>
            @endforeach 
          @endif
        </tbody>
      </table>
    </div>
  </div>
@endsection

@section('scripts')
<script type="text/javascript">

$(function () {
    var table = $('#subscriptiontable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,
    });

  });
  

</script>

@endsection
