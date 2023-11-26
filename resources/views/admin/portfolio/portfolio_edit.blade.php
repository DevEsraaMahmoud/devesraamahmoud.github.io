@extends('admin.admin_master')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="card-title">Protfolio Edit Page </h4>

                            <form method="post" action="{{ route('update.portfolio') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Portfolio title</label>
                                    <div class="col-sm-10">
                                        <input name="title" class="form-control" type="text" id="example-text-input" value="{{$portfolio->title}}">
                                        @error('title')
                                        <span class="text-danger"> {{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">category</label>
                                    <div class="col-sm-10">
                                        <input name="category" class="form-control" type="text" id="example-text-input" value="{{$portfolio->title}}">
                                        @error('category')
                                        <span class="text-danger"> {{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- end row -->

                                <div class="row mb-3">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">project_url </label>
                                    <div class="col-sm-10">
                                        <input name="project_url" class="form-control" type="text" id="example-text-input">

                                        @error('project_url')
                                        <span class="text-danger"> {{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">project_client </label>
                                    <div class="col-sm-10">
                                        <input name="project_client" class="form-control" type="text" id="example-text-input">

                                        @error('project_client')
                                        <span class="text-danger"> {{ $message }} </span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- end row -->



                                <div class="row mb-3">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Portfolio Description </label>
                                    <div class="col-sm-10">
      <textarea id="elm1" name="description">

      </textarea>
                                    </div>
                                </div>

                                <!-- end row -->
                                <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Protfolio Data">
                            </form>



                        </div>
                    </div>
                </div> <!-- end col -->
            </div>



        </div>
        <section class="content">
            <div class="row">

                <div class="col-md-12">
                    <div class="box bt-3 border-info">
                        <div class="box-header">
                            <h4 class="box-title">Product Multiple Image <strong>Update</strong></h4>
                        </div>


                        <form method="post" action="{{ route('update-product-image') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row row-sm">
                                @foreach($multiImgs as $img)
                                    <div class="col-md-3">

                                        <div class="card">
                                            <img src="{{ asset($img->photo_name) }}" class="card-img-top" style="height: 130px; width: 280px;">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="{{ route('product.multiimg.delete',$img->id) }}" class="btn btn-sm btn-danger" id="delete" title="Delete Data"><i class="fa fa-trash"></i> </a>
                                                </h5>
                                                <p class="card-text">
                                                <div class="form-group">
                                                    <label class="form-control-label">Change Image <span class="tx-danger">*</span></label>
                                                    <input class="form-control" type="file" name="multi_img[{{ $img->id }}]">
                                                </div>
                                                </p>

                                            </div>
                                        </div>

                                    </div><!--  end col md 3		 -->
                                @endforeach

                            </div>

                            <div class="text-xs-right">
                                <input type="submit" class="btn btn-rounded btn-primary mb-5" value="Update Image">
                            </div>
                            <br><br>



                        </form>





                    </div>
                </div>



            </div> <!-- // end row  -->

        </section>
        <section class="content">
            <div class="row">

                <div class="col-md-12">
                    <div class="box bt-3 border-info">
                        <div class="box-header">
                            <h4 class="box-title"> Image <strong>Update</strong></h4>
                        </div>


                        <form method="post" action="{{ route('update-image') }}" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="id" value="{{ $portfolio->id }}">
                            <input type="hidden" name="old_img" value="{{ $portfolio->project_image }}">

                            <div class="row row-sm">

                                <div class="col-md-3">

                                    <div class="card">
                                        <img src="{{ asset($portfolio->project_image) }}" class="card-img-top" style="height: 130px; width: 280px;">
                                        <div class="card-body">

                                            <p class="card-text">
                                            <div class="form-group">
                                                <label class="form-control-label">Change Image <span class="tx-danger">*</span></label>
                                                <input type="file" name="project_image" class="form-control" onChange="mainThamUrl(this)"  >
                                                <img src="" id="mainThmb">
                                            </div>
                                            </p>

                                        </div>
                                    </div>

                                </div><!--  end col md 3		 -->


                            </div>

                            <div class="text-xs-right">
                                <input type="submit" class="btn btn-rounded btn-primary mb-5" value="Update Image">
                            </div>
                            <br><br>



                        </form>





                    </div>
                </div>



            </div> <!-- // end row  -->

        </section>
    </div>


    <script type="text/javascript">

        $(document).ready(function(){
            $('#image').change(function(e){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#showImage').attr('src',e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });

    </script>
    <script>

        $(document).ready(function(){
            $('#multiImg').on('change', function(){ //on file input change
                if (window.File && window.FileReader && window.FileList && window.Blob) //check File API supported browser
                {
                    var data = $(this)[0].files; //this file data

                    $.each(data, function(index, file){ //loop though each file
                        if(/(\.|\/)(gif|jpe?g|png)$/i.test(file.type)){ //check supported file type
                            var fRead = new FileReader(); //new filereader
                            fRead.onload = (function(file){ //trigger function on successful read
                                return function(e) {
                                    var img = $('<img/>').addClass('thumb').attr('src', e.target.result) .width(80)
                                            .height(80); //create image element
                                    $('#preview_img').append(img); //append image to output element
                                };
                            })(file);
                            fRead.readAsDataURL(file); //URL representing the file's data.
                        }
                    });

                }else{
                    alert("Your browser doesn't support File API!"); //if File API is absent
                }
            });
        });

    </script>
    <script type="text/javascript">
        function mainThamUrl(input){
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#mainThmb').attr('src',e.target.result).width(80).height(80);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
