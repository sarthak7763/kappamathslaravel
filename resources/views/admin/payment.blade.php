@extends('layouts.admin', [
  'page_header' => 'Payment History',
  'dash' => '',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => '',
  'pay' => 'active'
])

@section('content')
  <div class="content-block box">
    <div class="box-body table-responsive">
      <table id="example1" class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Topic</th>
            <th>Amount</th>
            <th>Payment ID</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
        @if ($data)
          @if ($auth->role != 'A')
            @php($n = 1)
            @foreach ($data as $key => $item)
              <tr>
                <td>
                  {{$n}}
                  @php($n++)
                </td>
                <td>{{$auth->name}}</td>
                <td>{{$item->title}}</td>
                <td><i class="{{$setting->currency_symbol}}"></i> {{$item->pivot->amount}}</td>
                <td>{{$item->pivot->transaction_id}}</td>
                <td>{{$item->pivot->status == 1 ? 'Successful' : 'Unsuccessful'}}</td>
                <td>{{$item->pivot->created_at->toDateString()}}</td>
              </tr>
            @endforeach 
          @else
            @php($n = 1)
            @foreach ($data as $key => $item)
              @foreach($item->user()->get() as $pivot)
                <tr>
                  <td>
                    {{$n}}
                    @php($n++)
                  </td>
                  <td>{{$pivot->name}}</td>
                  <td>{{$item->title}}</td>
                  <td><i class="{{$setting->currency_symbol}}"></i> {{$pivot->pivot->amount}}</td>
                  <td>{{$pivot->pivot->transaction_id}}</td>
                  <td>{{$pivot->pivot->status == 1 ? 'Successful' : 'Unsuccessful'}}</td>
                  <td>{{$pivot->pivot->created_at->toDateString()}}</td>
                </tr>
              @endforeach
            @endforeach
          @endif
        @endif
        </tbody>
      </table>
    </div>
  </div>
@endsection
