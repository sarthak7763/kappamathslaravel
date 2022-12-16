@extends('layouts.admin', [
  'page_header' => 'Dashboard'
])

@section('content')
<!---->
  <div class="dashboard-block">
    <div class="row">

      <div class="col-md-12">
        <div class="row">
          <form method="post" class="form-block" action="{{url('/admin')}}">
            @csrf
          <div class="col-md-4">
            <div class="form-group">
              <label>Start Date</label>
              <input type="text" name="date_filter_start" id="datepicker_start" class="form-control">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>End Date</label>
              <input type="text" name="date_filter_end" id="datepicker_end" class="form-control">
            </div>
          </div>
          <div class="col-md-4" style="margin-top: 24px;">
              <input type="submit" name="submit" class="form-control" value="Submit">
          </div>
        </form>
        </div>
      </div>

      <div class="col-md-12">
        <div class="row">
          <div class="col-md-3">
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3>{{$user}}</h3>
                <p>Total Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              @if($new_filter_date_start!="" && $new_filter_date_end=="")
              @php $hrefurl=url('/').'/admin/users?filter_start_date='.$new_filter_date_start; @endphp

              @elseif($new_filter_date_start=="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/users?filter_end_date='.$new_filter_date_end; @endphp

              @elseif($new_filter_date_start!="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/users?filter_start_date='.$new_filter_date_start.'&filter_end_date='.$new_filter_date_end; @endphp

              @else
              @php $hrefurl=url('/').'/admin/users'; @endphp
              @endif

              <a href="{{$hrefurl}}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-md-3">
            <div class="small-box bg-purple">
              <div class="inner">
                <h3>{{$topic}}</h3>
                <p>Total Topics</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>

              @if($new_filter_date_start!="" && $new_filter_date_end=="")
              @php $hrefurl=url('/').'/admin/course-category?filter_start_date='.$new_filter_date_start; @endphp

              @elseif($new_filter_date_start=="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/course-category?filter_end_date='.$new_filter_date_end; @endphp

              @elseif($new_filter_date_start!="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/course-category?filter_start_date='.$new_filter_date_start.'&filter_end_date='.$new_filter_date_end; @endphp

              @else
              @php $hrefurl=url('/').'/admin/course-category'; @endphp
              @endif

              <a href="{{$hrefurl}}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-md-3">
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3>{{$subtopic}}</h3>
                <p>Total Sub-topics</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>

              @if($new_filter_date_start!="" && $new_filter_date_end=="")
              @php $hrefurl=url('/').'/admin/course-topic?filter_start_date='.$new_filter_date_start; @endphp

              @elseif($new_filter_date_start=="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/course-topic?filter_end_date='.$new_filter_date_end; @endphp

              @elseif($new_filter_date_start!="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/course-topic?filter_start_date='.$new_filter_date_start.'&filter_end_date='.$new_filter_date_end; @endphp

              @else
              @php $hrefurl=url('/').'/admin/course-topic'; @endphp
              @endif

              <a href="{{$hrefurl}}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-md-3">
            <div class="small-box bg-red">
              <div class="inner">
                <h3>{{$quiz}}</h3>
                <p>Total Quiz</p>
              </div>
              <div class="icon">
                <i class="fa fa-question-circle-o"></i>
              </div>

              @if($new_filter_date_start!="" && $new_filter_date_end=="")
              @php $hrefurl=url('/').'/admin/quiz-topics?filter_start_date='.$new_filter_date_start; @endphp

              @elseif($new_filter_date_start=="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/quiz-topics?filter_end_date='.$new_filter_date_end; @endphp

              @elseif($new_filter_date_start!="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/quiz-topics?filter_start_date='.$new_filter_date_start.'&filter_end_date='.$new_filter_date_end; @endphp

              @else
              @php $hrefurl=url('/').'/admin/quiz-topics'; @endphp
              @endif

              <a href="{{$hrefurl}}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-md-3">
            <div class="small-box bg-green">
              <div class="inner">
                <h3>{{$subscription}}</h3>
                <p>Total Subscription</p>
              </div>
              <div class="icon">
                <i class="fa fa-question-circle-o"></i>
              </div>

              @if($new_filter_date_start!="" && $new_filter_date_end=="")
              @php $hrefurl=url('/').'/admin/subscription?filter_start_date='.$new_filter_date_start; @endphp

              @elseif($new_filter_date_start=="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/subscription?filter_end_date='.$new_filter_date_end; @endphp

              @elseif($new_filter_date_start!="" && $new_filter_date_end!="")
              @php $hrefurl=url('/').'/admin/subscription?filter_start_date='.$new_filter_date_start.'&filter_end_date='.$new_filter_date_end; @endphp

              @else
              @php $hrefurl=url('/').'/admin/subscription'; @endphp
              @endif

              <a href="{{$hrefurl}}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-md-3">
            <div class="small-box bg-blue">
              <div class="inner">
                <h3>{{$revenue}}</h3>
                <p>Total Revenue</p>
              </div>
              <div class="icon">
                <i class="fa fa-question-circle-o"></i>
              </div>
              @if($new_filter_date_start!="")
               <a href="{{url('/admin/payment?filter_date=')}}{{$new_filter_date_start}}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
              </a>
              @else
              <a href="{{url('/admin/payment')}}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
              </a>
              @endif
            </div>
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

</script>
@endsection
