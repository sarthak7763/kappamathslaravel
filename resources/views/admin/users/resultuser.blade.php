@extends('layouts.admin', [
  'page_header' => 'Users'
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

  @if ($auth->role == 'A')

  <div class="margin-bottom">
       <h3>{{$name}} ({{$username}}) Result Details</h3>
    </div>

    <div class="content-block box">
      <div class="box-body">
        <div class="table-responsive">
          <table id="manageuserresultTable" class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Topic</th>
                <th>SubTopic</th>
                <th>Result Date</th>
                <th>Marks(%)</th>
                <th>Total Questions</th>
                <th>Correct Answers</th>
                <th>Incorrect Answers</th>
              </tr>
            </thead>
             @if ($user_result)
            <tbody>
              @php $i=1; @endphp
              @foreach($user_result as $list)
              <tr>
                <td>{{$i}}</td>
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
  @endif
@endsection

@section('scripts')

<script>

 $(function () {
    var table = $('#manageuserresultTable').DataTable({
      processing: true,
      serverSide: false,
      responsive: true,
      autoWidth: false,
      scrollCollapse: true
    });

  });

</script>
@endsection