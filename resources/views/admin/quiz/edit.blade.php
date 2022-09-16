@extends('layouts.admin', [
  'page_header' => 'Quiz',
  'dash' => '',
  'course'=>'',
  'quiz' => 'active',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Edit Topic: {{ $topic->title }}
          <a href="{{ route('topics.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> {{ __('Back')}}
          </a>
        </h3>
      <hr>
    
      {!! Form::model($topic, ['method' => 'PATCH', 'action' => ['TopicController@update', $topic->id]]) !!}
            
        <div class="row">
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Topic Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Title', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>
            <div class="form-group{{ $errors->has('per_q_mark') ? ' has-error' : '' }}">
              {!! Form::label('per_q_mark', 'Per Question Mark') !!}
              <span class="required">*</span>
              {!! Form::number('per_q_mark', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Per Question Mark', 'required' => 'required']) !!}
              <small class="text-danger">{{ $errors->first('per_q_mark') }}</small>
            </div>
            <div class="form-group{{ $errors->has('timer') ? ' has-error' : '' }}">
              {!! Form::label('timer', 'Quiz Time (in minutes)') !!}
              {!! Form::number('timer', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Total Time (In Minutes)']) !!}
              <small class="text-danger">{{ $errors->first('timer') }}</small>
            </div>

             
             <label for="">Enable Show Answer: </label>
             <input {{ $topic->show_ans ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="show_ans" id="toggle{{ $topic->id }}">
             <label for="toggle{{ $topic->id }}"></label>
            
             <label for="">Quiz Price:</label>
             <input onchange="showprice('{{ $topic->id }}')" {{ $topic->amount !=NULL  ? "checked" : ""}} type="checkbox" class="toggle-input " name="pricechk" id="toggle2{{ $topic->id }}">
             <label for="toggle2{{ $topic->id }}"></label>
          
            
          </div>

          <div class="col-md-6">
            <div style="{{ $topic->amount == NULL ? "display: none" : "" }}" id="doabox2{{ $topic->id }}">
               
              <label for="doba">Choose Quiz Price: </label>
              <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
               <input value="{{ $topic->amount }}" name="amount" id="doa" type="text" class="form-control"  placeholder="Please Enter Quiz Price">
               <small class="text-danger">{{ $errors->first('amount') }}</small>
              </div>
            </div>
            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Quiz Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>
          </div>
        </div>
        <div class="btn-group pull-right">
          {!! Form::submit("Update", ['class' => 'btn btn-wave']) !!}
        </div>
      {!! Form::close() !!}
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  
 
  $(function() {
    $('#fb_check').change(function() {
      $('#fb').val(+ $(this).prop('checked'))
    })
  })

 
                  
  $(document).ready(function(){

      $('.quizfp').change(function(){

        if ($('.quizfp').is(':checked')){
            $('#doabox').show('fast');
        }else{
            $('#doabox').hide('fast');
        }

       
      });

  });
                                

                               
  $('#priceCheck').change(function(){
    alert('hi');
  });

  function showprice(id)
  {
    if ($('#toggle2'+id).is(':checked')){
      $('#doabox2'+id).show('fast');
    }else{

      $('#doabox2'+id).hide('fast');
    }
  }
  </script>
@endsection