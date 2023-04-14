<style>
    .container{max-width:1170px;padding:20px 15px;display:block;margin:0 auto;}
    .cms-content h1{font-size:28px;padding:0;margin:0;color:#000;font-weight:600;font-family:'Poppins';}
    .cms-content h2{font-size:24px;font-weight:500;color:#000;padding:0;margin-bottom:5px;font-family:'Poppins';}
    .cms-content p{font-size:16px;color:#737373;font-weight:400;padding:0;margin:0;line-height:26px;font-family:'Poppins';}
    .col-6{width:100%;flex:0 0 50%;}
    .error-msg img{width:100%;}
    .error-msg{width: 100%; background:#042a60;display: inline-block;}
    .error-msg-inner{padding:62px 0;display: flex; align-items: center;}
    a.logo{background:#fff;border-radius:34.7561px;width:150px;height:140px;display:flex;align-items:center;justify-content:center;margin: 30px 0 0 50px;}
    a.logo img{max-width:110px;filter:invert(1) brightness(0);}
    .error-msg h2{font-size:16rem;display:block;text-align:center;margin:30px 0 ;line-height:12rem;color:#fff;}
    .text-center{text-align:center;}
    .error-msg p{font-size:29px;color:#fff;line-height:36px;}
    .btn.active.focus, .btn.active:focus, .btn.focus, .btn:active.focus, .btn:active:focus, .btn:focus {outline: none;box-shadow: none;}
    @media (max-width:575px){
        .error-msg-inner {
    flex-flow: column;
}
    }
</style>
<main class="main" role="main">
    <section class="section-padding-30 cms-content">
       <div class="container">

       	@php
			$setting = App\Setting::first();
		@endphp

       	@if($pagedet['status']==1)
       		<h1>{{$pagedet['name']}}</h1>
           <p>{{$pagedet['description']}}</p>
       	@else
       		 <div class="error-msg">
       		 		@if($setting)
       		 			<a class="logo" href="{{url('/')}}" title="{{$setting->welcome_txt}}">
                        <img src="{{asset('/images/logo/'.$setting->logo)}}" class="ad-logo img-responsive" alt="{{$setting->welcome_txt}}">
                    </a>
       		 		@else
       		 			<a class="logo" href="{{url('/')}}" title="kappa maths">
                        <img src="{{asset('/images/logo/logo_1669793364logo.png')}}" class="ad-logo img-responsive" alt="kappa maths">
                    </a>
       		 		@endif
                    
                <div class="error-msg-inner">
                    <div class="col-6">
                        <img src="{{asset('/images/404.png')}}" alt="">
                    </div>
                    <div class="col-6 text-center">                        
                        <h2>404</h2>
                        <p>sorry this page is not found </p>
                    </div>
                </div>
           </div>
       	@endif
                
    </section>
</main>
   