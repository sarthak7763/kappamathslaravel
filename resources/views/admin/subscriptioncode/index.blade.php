@extends('layouts.admin', [
  'page_header' => 'Subscription Coupons'
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

  <div class="margin-bottom">
    <a href="{{route('coupon-subscription.create')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Add Coupon</a>
  </div>

  <div class="box">

    <div class="box-body table-responsive">
      <table id="coursetopicTable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Type</th>
            <th>Name</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        @if(isset($subscriptioncoupon))
        <tbody>
         
        </tbody>
        @endif
      </table>
    </div>
  </div>
@endsection
@section('scripts')
<script type="text/javascript">

  $(document).on('click','.changestatusbtn',function(){
    var status=$(this).data('status');
    if(status==1)
    {
      $('.statusvalue').prop('checked',true);
    }
    else{
      $('.statusvalue').prop('checked',false);
    }
    
  });
     

$(function () {

    var filter_start_date="{{Request::input('filter_start_date')}}";
    var filter_end_date="{{Request::input('filter_end_date')}}";
    var table = $('#coursetopicTable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true,

      ajax: {
            url: "{{ route('coupon-subscription.index') }}",
            type: "GET",
            data: {
                "filter_start_date": filter_start_date,
                "filter_end_date": filter_end_date
            }
        },

      columns: [

      {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
      {data: 'coupon_type', name: 'coupon_type'},
      {data: 'coupon_name', name: 'coupon_name'},
      {data: 'coupon_date', name: 'coupon_date'},
      {data: 'coupon_status', name: 'coupon_status'},
      {data: 'action', name: 'action',searchable: false,orderable: false}

      ]
    });

  });
  

</script>

@endsection

