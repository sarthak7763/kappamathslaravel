@extends('layouts.admin', [
  'page_header' => 'Top Students Report'
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
          <div class="col-md-5">
            <div class="form-group">
              <label for="">Start Date: </label>
              <input type="text"  name="filter_date" id="datepicker_start" class="form-control" value="{{$filter_start_date}}">
              <small class="text-danger"></small>
            </div>
          </div>
          <div class="col-md-2">
            <div class="btn-group pull-right" style="margin-top: 26px;">
              <input class="btn btn-wave" type="submit" value="Submit">
            </div>
          </div>
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
