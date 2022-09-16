<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	@php
		$page = App\Setting::first();
	@endphp
	@if(isset($page) && $page->coming_soon == 1)
		<title>Coming Soon</title>
	@else
		<title>503 Service Unavailable</title>
	@endif
	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:700,900" rel="stylesheet">

	<!-- Custom stlylesheet -->
<link href="{{url('css/error.css')}}" rel="stylesheet" type="text/css"/> 



</head>


<body>

	<div id="notfound">
		<div class="notfound">
			@php
				$page = App\Setting::first();
			@endphp
			<div class="notfound-404">
				@if(isset($page) && $page->coming_soon == 1)
				
					<h2 style="top:10%;">{{__('We Are Coming Soon !')}}</h2>
					
				@else
				<h1>503</h1>
				<h2>Service Unavailable</h2>
				@endif
			</div>
			@if(isset($page) && $page->coming_soon == 1)
			@else
				<a href={{url('/')}}>Homepage</a>
			@endif

		</div>
	</div>

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>