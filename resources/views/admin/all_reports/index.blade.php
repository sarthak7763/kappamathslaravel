@extends('layouts.admin', [
  'page_header' => 'Manage Quiz Results'
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

  @if($filter_start_date!="")
  @php
    $filter_start_date=date('m/d/Y',strtotime($filter_start_date));
  @endphp
  @endif

  @if($filter_end_date!="")
  @php
    $filter_end_date=date('m/d/Y',strtotime($filter_end_date));
  @endphp
  @endif

  <div class="row">
  	<div class="col-md-12">
  		<form method="post" action="{{url('/admin/all_reports/')}}" autocomplete="off">
        @csrf
        <div class="row">  
      		<div class="col-md-4">
            <div class="form-group">
              <label for="">User Name: </label>
              <select class="form-control" name="filter_user" id="filter_user">
                <option value="">Select</option>
                @foreach($usernamelist as $row)
                <option {{ $filter_user ==$row['id'] ? "selected" : "" }} value="{{$row['id']}}">{{$row['name']}} ({{$row['username']}})</option>
                @endforeach
              </select>
              <small class="text-danger"></small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="">Start Date: </label>
             	<input type="text"  name="filter_result_date[]" id="datepicker_start" class="form-control" value="{{$filter_start_date}}">
              <small class="text-danger"></small>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="">End Date: </label>
              <input type="text" name="filter_result_date[]" value="{{$filter_end_date}}" id="datepicker_end" class="form-control">
              <small class="text-danger"></small>
            </div>
          </div>
          <div class="col-md-1">
          	<div class="btn-group pull-right" style="margin-top: 26px;">
    		      <input class="btn btn-wave" type="submit" value="Submit">
    		    </div>
          </div>

          @if($clear_filter=="1")
          <div class="col-md-1">
            <div class="btn-group pull-right" style="margin-top: 26px;">
              <button class="btn btn-wave clearfilterbtn" type="button">Clear Filter</button>
            </div>
          </div>
          @endif

        </div>  
  		</form>
  	</div>
  </div>

  <div class="row">
    <div class="col-md-12">
  	  <div class="content-block box">
      <div class="box-body">
        <div class="table-responsive">
          <table id="manageresultTable" class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Topic</th>
                <th>SubTopic</th>
                <th>Result Date</th>
                <th>Marks(%)</th>
                <th>Total Questions</th>
                <th>Correct Answers</th>
                <th>Incorrect Answers</th>
              </tr>
            </thead>
             @if ($result_data)
            <tbody>
              @php $i=1; @endphp
              @foreach($result_data as $list)
              <tr>
                <td>{{$i}}</td>
                <td>{{$list['name']}} ({{$list['username']}})</td>
                <td>{{$list['topic_title']}}</td>
                <td>{{$list['sub_topic_title']}}</td>
                <td>{{$list['result_date']}}</td>
                <td>{{$list['total_score']}}</td>
                <td>{{$list['total_questions']}}</td>
                <td>{{$list['correct_questions']}}</td>
                <td>{{$list['incorrect_questions']}}</td>
              </tr>
              @php $i++; @endphp
              @endforeach
            </tbody>
             @endif
          </table>
        </div>
      </div>
      </div>
    </div>    
  </div>
@endsection

@section('scripts')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>

$(document).on('click','.clearfilterbtn',function(){
  window.location.href="{{url('/')}}/admin/all_reports";
});

 $("#datepicker_start").datepicker({
format: "mm/dd/yy"
});

 $("#datepicker_end").datepicker({
format: "mm/dd/yy"
});

 $(function () {
    var table = $('#manageresultTable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true
    });

  });

</script>
@endsection
