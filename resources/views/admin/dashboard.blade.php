@extends('layouts.admin', [
  'page_header' => 'Dashboard'
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
  
<!---->
  <div class="dashboard-block dashboard_page">
    <div class="row">

      <div class="col-md-12">
        <div class="row">
          <form method="post" class="form-block" action="{{url('/admin')}}" autocomplete="off">
            @csrf
          
            <div class="form-group">
              <label>Start Date</label>
              <input type="text" name="date_filter_start" id="datepicker_start" value="{{$new_filter_date_start}}" class="form-control">
            </div>
          
        
            <div class="form-group">
              <label>End Date</label>
              <input type="text" name="date_filter_end" id="datepicker_end" value="{{$new_filter_date_end}}" class="form-control">
            </div>
          
         
            <div class="mt-4 d-inline-block dashb_submit">
              <input type="submit" name="submit" class="form-control btn btn-primary" value="Submit">
            </div>
            <div class="filter_button">
              @if($clear_filter=="1")
                <button type="button" class="form-control clearfilterbtn">Clear Filter</button>
              @endif
            </div>
        
         </form>
        </div>
      </div>

      <div class="col-md-12">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3>{{$user}}</h3>
                <p>Total Users</p>
              </div>
              <div class="icon">
                <img src="https://kappamaths.ezxdemo.com/images/dashboard/users.png" class="img-responsive" alt="users">
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

          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-purple">
              <div class="inner">
                <h3>{{$topic}}</h3>
                <p>Total Topics</p>
              </div>
              <div class="icon">
                <img src="https://kappamaths.ezxdemo.com/images/dashboard/Topics.png" class="img-responsive" alt="users">
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

          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3>{{$subtopic}}</h3>
                <p>Total Sub-topics</p>
              </div>
              <div class="icon">
               <img src="https://kappamaths.ezxdemo.com/images/dashboard/Topics.png" class="img-responsive" alt="users">
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

          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-red">
              <div class="inner">
                <h3>{{$quiz}}</h3>
                <p>Total Quiz</p>
              </div>
              <div class="icon">
                <img src="https://kappamaths.ezxdemo.com/images/dashboard/quiz.png" class="img-responsive" alt="users">
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

          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h3>{{$subscription}}</h3>
                <p>Total Subscription</p>
              </div>
              <div class="icon">
                <img src="https://kappamaths.ezxdemo.com/images/dashboard/subscription.png" class="img-responsive" alt="users">
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

          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
            <div class="small-box bg-blue">
              <div class="inner">
                <h3>{{$revenue}}</h3>
                <p>Total Revenue</p>
              </div>
              <div class="icon">
                <img src="https://kappamaths.ezxdemo.com/images/dashboard/increase.png" class="img-responsive" alt="users">
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

<script src="https://kappamaths.ezxdemo.com/mathml2latex-master/dist/mathml2latex.js"></script>

<script>

  $(document).on('click','.clearfilterbtn',function(){
  window.location.href="{{url('/')}}/admin";
});

 $("#datepicker_start").datepicker({
format: "mm/dd/yy"
});

 $("#datepicker_end").datepicker({
format: "mm/dd/yy"
});

</script>

<script type="text/javascript">
  // const mathmlHtml = '<math xmlns="http://www.w3.org/1998/Math/MathML"><mi>U</mi><mo>=</mo><mfenced open="{" close="}"><mrow><mn>2</mn><mo>,</mo><mn>3</mn><mo>,</mo><mn>45</mn><mo>,</mo><mn>6</mn><mo>,</mo><mn>7</mn><mo>,</mo><mn>8</mn><mo>,</mo><mn>9</mn></mrow></mfenced><mfenced open="{" close="}"><mspace/><mspace/></mfenced><mi>P</mi><mo>=</mo><mfenced open="{" close="}"><mrow><mi>x</mi><mo>:</mo><mi>x</mi><mo>&lt;</mo><mn>6</mn></mrow></mfenced><mo>=</mo><mfenced open="{" close="}"><mrow><mn>2</mn><mo>,</mo><mn>3</mn><mo>,</mo><mn>4</mn><mo>,</mo><mn>5</mn></mrow></mfenced><mfenced open="{" close="}"><mspace/><mspace/></mfenced><mi>Q</mi><mo>=</mo><mfenced open="{" close="}"><mrow><mi>y</mi><mo>:</mo><mn>3</mn><mo>&lt;</mo><mi>y</mi><mo>&lt;</mo><mn>8</mn></mrow></mfenced><mo>=</mo><mfenced open="{" close="}"><mrow><mn>4</mn><mo>,</mo><mn>5</mn><mo>,</mo><mn>6</mn><mo>,</mo><mn>7</mn></mrow></mfenced><mfenced open="{" close="}"><mspace/><mspace/></mfenced><mi>R</mi><mo>=</mo><mfenced open="{" close="}"><mrow><mi>e</mi><mi>v</mi><mi>e</mi><mi>n</mi><mo>&#xA0;</mo><mi>n</mi><mi>u</mi><mi>m</mi><mi>b</mi><mi>e</mi><mi>r</mi><mi>s</mi></mrow></mfenced><mo>=</mo><mfenced open="{" close="}"><mrow><mn>2</mn><mo>,</mo><mn>4</mn><mo>,</mo><mn>6</mn><mo>,</mo><mn>8</mn></mrow></mfenced><mfenced open="{" close="}"><mspace/><mspace/></mfenced><mi>P</mi><mo>&#x2229;</mo><mi>Q</mi><mo>&#x2229;</mo><mi>R</mi><mo>=</mo><mfenced open="{" close="}"><mn>4</mn></mfenced></math>';

 const mathmlHtml='<math xmlns="http://www.w3.org/1998/Math/MathML"><mi>t</mi><mi>e</mi><mi>s</mi><mi>t</mi><mo> </mo><mi>o</mi><mi>p</mi><mi>t</mi><mi>i</mi><mi>o</mi><mi>n</mi><mi>a</mi><mspace linebreak="newline"/><mi>t</mi><mi>e</mi><mi>s</mi><mi>t</mi><mo> </mo><mi>n</mi><mi>e</mi><mi>w</mi><mo> </mo><mi>o</mi><mi>p</mi><mi>t</mi><mi>i</mi><mi>o</mi><mi>n</mi><mi>a</mi></math>';

mathmlHtmlpreview = mathmlHtml.replace(/<mspace linebreak="newline"\/>/g,'<mfenced open="{" close="}"><mspace/><mspace/></mfenced>');

var latex = MathML2LaTeX.convert(mathmlHtmlpreview);
console.log(latex,'latex');
latexpreview = latex.replace(/\\left\\{\\:\,\\:\\right\\}/g,'\\\\');

console.log(latexpreview,'latexpreview');

</script>
@endsection
