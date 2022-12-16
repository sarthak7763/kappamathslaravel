@extends('layouts.admin', [
  'page_header' => 'Edit Theory Quiz Question'
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

  
  <div class="box">
    <div class="box-body">
        <h3>Edit Question
          <a href="{{route('questions.show', $topic->id)}}" class="btn btn-gray pull-right">
            <i class="fa fa-arrow-left"></i> 
            {{ __('Back') }}
          </a>
        </h3>
      <hr>
       {!! Form::model($question, ['method' => 'PATCH', 'action' => ['QuestionsController@updatetheoryquiz', $question->id], 'files' => true]) !!}
                     
        <div class="row">
          <div class="col-md-6">
            {!! Form::hidden('topic_id', $topic->id) !!}
            <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
              {!! Form::label('question', 'Question') !!}
              <span class="required">*</span>
              {!! Form::textarea('question', null, ['class' => 'form-control', 'placeholder' => 'Please Enter Question', 'rows'=>'8']) !!}
              <small class="text-danger">{{ $errors->first('question') }}</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group{{ $errors->has('answer_ex') ? ' has-error' : '' }}">
                {!! Form::label('answer_exp', 'Answer Explanation') !!}
                {!! Form::textarea('answer_exp', null, ['class' => 'form-control',  'placeholder' => 'Please Enter Answer Explanation',  'rows' => '4']) !!}
                <small class="text-danger">{{ $errors->first('answer_ex') }}</small>
            </div>
          </div>
          <div class="col-md-12">
            <div class="extras-block">
              <h4 class="extras-heading">Images And Video For Question</h4>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('question_video_link') ? ' has-error' : '' }}">
                    {!! Form::label('question_video_link', 'Add Video To Question') !!}
                    {!! Form::text('question_video_link', null, ['class' => 'form-control', 'placeholder'=>'https://myvideolink.com/embed/..']) !!}
                    <small class="text-danger">{{ $errors->first('question_video_link') }}</small>
                    <p class="help">YouTube And Vimeo Video Support (Only Embed Code Link)</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('question_img') ? ' has-error' : '' }}">
                    {!! Form::label('question_img', 'Add Image In Question') !!}
                    {!! Form::file('question_img') !!}
                    <small class="text-danger">{{ $errors->first('question_img') }}</small>
                    <p class="help">Please Choose Only .JPG, .JPEG and .PNG</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                    {!! Form::label('sort_order', 'Sort Order') !!}
                    {!! Form::text('sort_order', null, ['class' => 'form-control']) !!}
                    <small class="text-danger">{{ $errors->first('sort_order') }}</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="btn-group pull-right">
                    {!! Form::submit("Update", ['class' => 'btn btn-wave']) !!}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    
    
        
    
    {!! Form::close() !!}
  </div>
</div>
@endsection

@section('scripts')
  <script src="https://cdn.tiny.cloud/1/9z77wjhpwrx6pvh3r3oeiky25krlx0jzd8m69yte73hjrrgg/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

  <script type="text/javascript">

    const image_upload_handler_callback = (blobInfo, success) => new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', '<?=url('/')?>/admin/postAcceptor');
    
    xhr.upload.onprogress = (e) => {
        progress(e.loaded / e.total * 100);
    };
    
    xhr.onload = () => {
        if (xhr.status === 403) {
            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
            return;
        }
      
        if (xhr.status < 200 || xhr.status >= 300) {
            reject('HTTP Error: ' + xhr.status);
            return;
        }
      
        const json = JSON.parse(xhr.responseText);
      
        if (!json || typeof json.location != 'string') {
            reject('Invalid JSON: ' + xhr.responseText);
            return;
        }
        
        if(json.location!="")
        {
          var fileName = window.location.protocol + '//' + window.location.host+'/'+json.location;
          success(fileName);
        }
        else{
          reject('Something went wrong');
        }
        

    };
    
    xhr.onerror = () => {
      reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
    };
    
    const formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    
    xhr.send(formData);
});
    
tinymce.init({
  selector: 'textarea',
  width: 600,
  height: 300,
  plugins: [
    'advlist autolink link image lists charmap print preview hr anchor pagebreak',
    'searchreplace wordcount visualblocks code fullscreen insertdatetime media nonbreaking',
    'table emoticons template paste help'
  ],
  toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
    'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
    'forecolor backcolor emoticons | help | image code',

    images_upload_url: '<?=url('/')?>/admin/postAcceptor',
    images_upload_handler: image_upload_handler_callback,

  menubar: 'favs file edit view insert format tools table help',
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
});


  </script>
  @endsection
