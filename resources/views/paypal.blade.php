<html>
<head>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    
    <div class="w3-container">
        @if ($message = Session::get('success'))
        <div class="w3-panel w3-green w3-display-container">
            <span onclick="this.parentElement.style.display='none'"
            class="w3-button w3-green w3-large w3-display-topright">&times;</span>
            <p>{!! $message !!}</p>
        </div>
        <?php Session::forget('success');?>
        @endif

        @if ($message = Session::get('error'))
        <div class="w3-panel w3-red w3-display-container">
            <span onclick="this.parentElement.style.display='none'"
            class="w3-button w3-red w3-large w3-display-topright">&times;</span>
            <p>{!! $message !!}</p>
        </div>
        <?php Session::forget('error');?>
        @endif

      <form method="POST" id="payment-form"
          action="{!! URL::to('paypal') !!}">
        {{ csrf_field() }}
       <input class="w3-input w3-border" id="amount" type="text" name="amount" value="10"></p>
        <button>Paypal</button>
      </form>
    
</body>
</html>

<script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>

<script>
$(document).ready(function() {
$('#submit_100').trigger('click');
    
});
</script>
</>
