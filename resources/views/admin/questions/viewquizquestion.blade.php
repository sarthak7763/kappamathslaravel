<style>
    .container{padding:12px 15px;display:block;margin:0 auto;}
    .cms-content h1{font-size:28px;padding:0;margin:0;color:#000;font-weight:600;font-family:'Poppins';}
    .cms-content h2{font-size:24px;font-weight:500;color:#000;padding:0;margin-bottom:5px;font-family:'Poppins';}
    .cms-content p{font-size:60px;color:white;font-weight:400;padding:0;margin:0;line-height:26px;font-family:'Poppins';}
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

::-webkit-scrollbar {
  width: 20px;
}

/* Track */
::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey; 
  border-radius: 10px;
}
 
/* Handle */
::-webkit-scrollbar-thumb {
  background: red; 
  border-radius: 10px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #b30000; 
}
    }
</style>
<main class="main" role="main">
    <section class="section-padding-30 cms-content">
       <div class="container">

        <textarea class="textarea" id="textareavalue" placeholder="Your formula here" style="display: none;">{{html_entity_decode($question->question)}}</textarea>
        <p id="renderer" class="mathrender">
                
        </p>
        </section>
</main>

<script src="{{asset('js/jquery.min.js')}}"></script>

<script type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>

    <script type="text/javascript">
      $(document).ready(function() {
      var textareavalue=$('#textareavalue').val();
    $("#renderer").empty();
    $("#renderer").append(textareavalue);
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, $("#renderer")[0]])

});
    </script>
   