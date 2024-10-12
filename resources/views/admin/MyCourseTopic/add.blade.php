@extends('layouts.admin', [
  'page_header' => ' My Course Topics'
])
@section('content')

<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <!-- Main-body start -->
        <div class="main-body">
            <div class="page-wrapper">
                <!-- Page-header start -->
                <div class="page-header">
                    <div class="row align-items-end">
                        <div class="col-lg-8">
                            <div class="page-header-title">
                                <div class="d-inline">
                                    <h4>Add Page</h4>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="page-header-breadcrumb">
                                <ul class="breadcrumb-title">
                                    <li class="breadcrumb-item">
                                        <a href=""> <i class="feather icon-home"></i> </a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="">Course category List</a> </li>
                                     <li class="breadcrumb-item"><a href="">Course Category Add</a> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page-header end -->

                <div class="page-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">

                                <div class="card-header">
                                    <!-- h5>Hello Card</h5>
                                    <span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
                                    <div class="card-header-right">
                                        <ul class="list-unstyled card-option">
                                            <li><i class="feather icon-maximize full-card"></i></li>
                                            <li><i class="feather icon-minus minimize-card"></i></li>
                                            <li><i class="feather icon-trash-2 close-card"></i></li>
                                        </ul>
                                    </div> -->
                                  <!-- Page-header end -->
                @if ($message = Session::get('success'))
                    <div class="row">
                        <div class="col-md-12">
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                             <label  class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"  style="font-size: 19px;margin-top: -1px;">&times;</span>
                              </label>
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                @endif
                @if($message = Session::get('error'))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                             <label type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true" style="font-size: 19px;margin-top: -1px;">&times;</span>
                              </label>
                                
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                @endif

                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{route('mycoursetopic-save')}}" enctype="multipart/form-data">
                                    @csrf
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label font-weight-bold">Title<span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                               <input type="text" class="form-control  @error('title') is-invalid @enderror" name="title"  value="{{old('title')}}" placeholder="Enter Title">
                                               @error('title')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                        
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                         <div class="form-group row">
                                            <label class="col-sm-2 col-form-label font-weight-bold">Course<span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                            <select class="form-control" name="course">

                                            <option value="">Select</option>
                                            @foreach($subject_data as $subject)
                                            <option value="{{$subject['id']}}">{{$subject['title']}}</option>
                                            @endforeach
                                                </select>
                                            @error('title')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label font-weight-bold">SOrt Order<span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                               <input type="number" class="form-control  @error('sort_order') is-invalid @enderror" name="sort_order"  value="{{old('sort_order')}}" placeholder="Enter Sort Order">
                                               @error('sort_order')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label font-weight-bold">Topic Image<span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                            <input type="file" class="form-control  @error('topic_image') is-invalid @enderror" src="" alt="" name="topic_image" >
                                               @error('topic_image')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}  </strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label font-weight-bold">Description<span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                            <textarea rows="5" cols="3" id="message" name="description" class="ck-editor @error('description') is-invalid @enderror">{{old('description')}}</textarea>
                                            @error('description')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2"></label>
                                            <div class="col-sm-10">
                                            <button type="submit"  class="btn btn-primary m-b-0">Save</button>
                                                                                        </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
CKEDITOR.replace( 'message' );
function clearData(){
    for ( instance in CKEDITOR.instances ){
        CKEDITOR.instances[instance].updateElement();
    }
    CKEDITOR.instances[instance].setData('');
 
}


if ($("#message").hasClass("is-invalid")) {
  $(".invalid-feedback").show();
}
</script>
@endsection