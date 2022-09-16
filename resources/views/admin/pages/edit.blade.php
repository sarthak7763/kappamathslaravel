@extends('layouts.admin', [
  'page_header' => 'Dashboard',
  'dash' => 'active',
  'course'=>'',
  'quiz' => '',
  'users' => '',
  'questions' => '',
  'top_re' => '',
  'all_re' => '',
  'sett' => ''
])

@section('content')
  <div class="box">
  <div class="box-body">
      <h3>Edit Page: {{ $page->name }}
        <a href="{{url()->previous()}}" class="btn btn-gray pull-right"><i class="fa fa-arrow-left"></i> Back</a></h3>
    <hr>
    <form class="col-md-8" action="{{ route('pages.update',$page->id) }}" method="POST">
      {{ csrf_field() }}
      {{ method_field('PUT') }}
      <label for="name">Page Title:</label>
      <input required type="text" value="{{ $page->name }}" name="name" class="form-control">
      <br>
      <label for="name">Page Content:</label>
      <textarea name="details" class="form-control">
        {!! $page->details !!}
      </textarea>
      <br>
        <label for="status">Status</label>
        <label for="Status">Status</label>
        <input {{$page->status =="1" ? "checked" : ""}} type="checkbox" class="toggle-input" name="status" id="toggle">
        <label for="toggle"></label>
          <br>

        <label>Show in menu:</label>
        <input {{$page->show_in_menu =="1" ? "checked" : ""}} type="checkbox" class="toggle-input" name="show_in_menu" id="show_in_menu">
        <label for="show_in_menu"></label>
        <p class="help-block">(IF enable it will show as menu item in top menu)</p>
              
      <button type="submit" class="btn btn-success btn-md">
          <i class="fa fa-save"></i> Update
      </button>
    </form> 
  </div>
  


  </div>
@endsection

@section('scripts')
  <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=9z77wjhpwrx6pvh3r3oeiky25krlx0jzd8m69yte73hjrrgg"></script>
  <script>
  tinymce.init({
                selector: 'textarea',
                plugins: 'table,textcolor,image,lists,link,code,wordcount,advlist, autosave',
                theme: 'modern',
                menubar: 'none',
                height : '200',
                toolbar: 'restoredraft,bold italic underline | fontselect |  fontsizeselect | forecolor backcolor |alignleft aligncenter alignright alignjustify| bullist,numlist | link image'
  });</script>
  <script>
  @endsection
