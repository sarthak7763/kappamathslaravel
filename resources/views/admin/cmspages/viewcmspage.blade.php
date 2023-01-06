<style>

    .container {
        max-width: 1170px;
        padding: 20px 15px;
        display: block;
        margin: 0 auto;
    }    
    .cms-content h1 {
        font-size: 28px;
        padding: 0;
        margin: 0;
        color: #000;
        font-weight: 600;
        font-family: 'Poppins';
    }
    .cms-content h2 {
        font-size: 24px;
        font-weight: 500;
        color: #000;
        padding: 0;
        margin-bottom: 5px;
        font-family: 'Poppins';
    }
    .cms-content p {
        font-size: 16px;
        color: #737373;
        font-weight: 400;
        padding: 0;
        margin: 0;
        line-height: 26px;
        font-family: 'Poppins';
    }
</style>
<main class="main" role="main">
    <section class="section-padding-30 cms-content">
       <div class="container">
           <h1>{{$pagedet['name']}}</h1>
           <p>{{$pagedet['description']}}</p>
       </div>
    </section>
</main>
   