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
      <h3>Creating new page</h3>
    <hr>
    <form class="col-md-8" action="{{ route('pages.store') }}" method="POST">
      {{ csrf_field() }}
      <label for="name">Page Title:</label>
      <input required type="text" name="name" class="form-control">
      <br>
      <label for="name">Page Content:</label>
      <textarea name="details" class="form-control"></textarea>
      <br>
      <label for="Status">Status</label>
        
        <input type="checkbox" class="toggle-input" name="status" id="toggle">
        <label for="toggle"></label>
        <br>

        <label>Show in menu:</label>
        <input type="checkbox" class="toggle-input" name="show_in_menu" id="show_in_menu">
        <label for="show_in_menu"></label>
       <p class="help-block">(IF enable it will show as menu item in top menu)</p>
      <button type="submit" class="btn btn-primary btn-md">
        <i class="fa fa-plus"></i> Create
      </button>
      <br>
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
  });
</script>
  <script>
  @endsection
