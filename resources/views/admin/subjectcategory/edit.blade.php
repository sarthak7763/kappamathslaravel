@extends('layouts.admin', [
  'page_header' => 'Subject Category'
])

@section('content')
  <div class="box">
    <div class="box-body">
        <h3>Edit Category: {{ $subjectcategory->category_name }}
          <a href="{{ route('course-category.index') }}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> {{ __('Back')}}
          </a>
        </h3>
      <hr>
    
      {!! Form::model($subjectcategory, ['method' => 'PATCH', 'enctype'=>'multipart/form-data','action' => ['SubjectcategoryController@update', $subjectcategory->id]]) !!}
            
        <div class="row">
          <div class="col-md-6">

            <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
              <label for="">Subject: </label>
              <span class="required">*</span>
             <select class="form-control" name="subject">
              <option value="">Select</option>
              @foreach($subjectlist as $list)
                <option {{ $subjectcategory->subject ==$list['id'] ? "selected" : "" }} value="{{$list['id']}}">{{$list['title']}}</option>
              @endforeach
             </select>
              <small class="text-danger">{{ $errors->first('subject') }}</small>
            </div>

            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
              {!! Form::label('title', 'Title') !!}
              <span class="required">*</span>
              {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Title']) !!}
              <small class="text-danger">{{ $errors->first('title') }}</small>
            </div>

              <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
              {!! Form::label('description', 'Description') !!}
              {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Description']) !!}
              <small class="text-danger">{{ $errors->first('description') }}</small>
            </div>

            <div class="form-group{{ $errors->has('category_img') ? ' has-error' : '' }}">
            {!! Form::label('category_img', 'Add Image') !!}
            {!! Form::file('category_img') !!}
            <small class="text-danger">{{ $errors->first('category_img') }}</small>
            <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
          </div>

          <div id="preview_image_div">
            @if($subjectcategory->category_image!="")
            <img id="preview-image" src="/images/subjectcategory/{{ $subjectcategory->category_image }}" style="height: auto;width: 50%;">
            @else
            <img id="preview-image" src="/images/noimage.jpg" style="height: auto;width: 50%;">
            @endif
          </div>
             
             <label for="">Status: </label>
             <input {{ $subjectcategory->category_status ==1 ? "checked" : "" }} type="checkbox" class="toggle-input" name="status" id="toggle2">
             <label for="toggle2"></label>
            
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

  $(document).on('change','#category_img',function(){
    let reader = new FileReader();
    reader.onload = (e) => { 
      $('#preview-image').attr('src', e.target.result); 
    }
    reader.readAsDataURL(this.files[0]);
  });

</script>

@endsection