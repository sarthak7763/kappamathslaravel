@extends('layouts.admin', [
  'page_header' => ' My Course Topics'
])

@section('content')


<style>
.custom-close {
  color: red!important;
}
.custom-input {
  background-color: lightsalmon;
}
</style> 


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
    <a href="{{route('mycoursetopic-add')}}" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Add Topic</a>
  </div>

  <div class="box">
    <div class="box-body">
<form method="POST" action="{{route('mycoursetopic-list')}}" autocomplete="off">
  @csrf
  <div class="row">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
            <label for="">Course: </label>
           <select class="form-control" name="course" id="course" value="{{$subject_search}}">
            <option value="">Select</option>
           @foreach($subject as $mysubject)
           <option value="{{$mysubject->id}}">{{$mysubject->title}}</option>
              @endforeach                    
             </select>
            <small class="text-danger"></small>
          </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
            <label for="">Title: </label>
           <select class="form-control" name="category_title" id="category_title" value="{{$course_search}}">
           </select>
            <small class="text-danger"></small>
          </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
              <label for="">Status: </label>
             <select class="form-control" name="status" id="status">
              <option value="">Select Status</option>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
             </select>
              <small class="text-danger"></small>
            </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
              <label for="">Start Date: </label>
             <input type="text" class="form-control datepicker" id="start_date" name="start_date">
              <small class="text-danger"></small>
            </div>
      </div>
      
      <div class="col-md-6">
        <div class="form-group">
              <label for="">End Date: </label>
              <input type="text" class="form-control datepicker" id="end_date" name="end_date">
              <small class="text-danger"></small>
            </div>
      </div>

    </div>
  </div>
</div>
<div class="btn-group pull-right">
  {!! Form::submit("Submit", ['class' => 'btn btn-wave']) !!}
</div>
{!! Form::close() !!}
</div>
</div>

  <div class="box">
  <div class="box-body table-responsive">
      <table id="datatable" class="table table-hover table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Course</th>
            <th>Title</th>
            <th>Status</th>
            <th>Sort Order</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
        </table>
    </div>
  </div>
@endsection

@section('scripts')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>

$(function table_ajax(subject) {

var table = $('#datatable').DataTable({
  processing: true,
  serverSide: false,
  responsive: true,
  autoWidth: false,
  scrollCollapse: true,
  ajax: {
        url: "{{route('mycoursetopic-list')}}",
        type: "GET",
        data: {_token: "{{ csrf_token() }}",subject:subject},
        datatype :"json",      
    },
  columns: [

  {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false},
  {data: 'subject', name: 'subject'},
  {data: 'category_name', name: 'category_nane'},
  {data: 'category_status', name: 'category_status'},
  {data: 'sort_order', name: 'sort_order'},
  {data: 'action', name: 'action',orderable: false,searchable: false}

  ]
});

});
</script>


<script>

$(document).on('click','.changestatus', function(e)
    { 

      var current_id =$(this).find(".body").attr('span-id');
      var status_class = $(this).attr('class');
      var id = $(this).attr('data-id');
      var status  = $(this).attr('data-status');
      var change_btn = $(this);

      
      var title ='Are you sure to change status?';
      
      if(status_class == "badge badge-danger badge_changestatus")
            {
                var newClass = "badge badge-success badge_changestatus";
                var status = 'Active';
                
            }else
            {
                var newClass = "badge badge-danger badge_changestatus";
                var status = 'Inactive';
            }
            e.preventDefault();  
    swal({
              title: title,
              icon: "warning",
              buttons: [
                'No, cancel it!',
                'Yes, I am sure!'
              ],
              dangerMode: true,
            }).then(function(isConfirm) {
              if(isConfirm){
            $.ajax({
                    type: "POST",
                    url: "{{ route('mycoursetopic-change-status')}}",
                    data: {_token: "{{ csrf_token() }}",id:id,status:status},
                    dataType: "json",
                    success: function (data){
                            if(data.status=='success')
                            {

                              // change_btn.removeClass(status_class).addClass(newClass);


                            } 
                            else
                            {
                              alert('error');

                            }    
                    }         
                })
            } 
           
            }); 
        });   
    

</script>

    <script>
    $("#start_date").datepicker({
    format: "mm/dd/yy"
    });
    </script>

<script>
$("#end_date").datepicker({
format: "mm/dd/yy"
});
</script>


<script>


$( document ).ready(function() {


  $('#course').change(function(){

  var course = $(this).val();


          $.ajax({
                    type: "POST",
                    url: "{{ route('mycoursetopic-get-category')}}",
                    data: {_token: "{{ csrf_token() }}",course:course},
                    dataType: "json",
                    success: function (data){
                      
                      if(data.status=='success')
                      {
                            var my_orders = $("#category_title");
                            $.each(data.message, function(i, order){
                            my_orders.append("<option value="+order.id+"> " + order.category_name + "</option>");

                            });    
                      }
                      else{
                          
                      }
                    }         
              });
          });
      });
</script>



<script> 
		$(document).ready(function () { 

    $( "#myform" ).on( "submit", function( event ) {

          var subject = $('#course').val();
          var course = $('#category_title').val();
          var status = $('#status').val();
          var start_date = $('#start_date').val();
          var end_date = $('#end_date').val();
          event.preventDefault();
          table_ajax(subject);
});
        
}); 
	</script>

@endsection



