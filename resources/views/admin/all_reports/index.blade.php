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

  @php
  	$filter_user=Request::input('filter_user');
  	$filter_result_date=Request::input('filter_result_date');
  @endphp


  <div class="row">
  	<div class="col-md-12">
  		<form method="get" action="{{route('all_reports.index')}}" autocomplete="off">
  		<div class="col-md-6">
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

       <div class="col-md-6">
        <div class="form-group">
            <label for="">Result Date: </label>
           	<input type="text"  name="filter_result_date" id="datepicker" class="form-control" value="{{$filter_result_date}}">
            <small class="text-danger"></small>
          </div>
      </div>

      <div class="col-md-6">
      	<div class="btn-group pull-right">
		  <input class="btn btn-wave" type="submit" value="Submit">
		</div>
      </div>
  		</form>
  	</div>
  </div>

  <div class="row">

  	<div class="content-block box">
      <div class="box-body">
        <div class="table-responsive">
          <table id="usersTable" class="table table-striped">
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
@endsection

@section('scripts')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>

 $("#datepicker").datepicker({
format: "mm/dd/yy"
});

</script>
@endsection
