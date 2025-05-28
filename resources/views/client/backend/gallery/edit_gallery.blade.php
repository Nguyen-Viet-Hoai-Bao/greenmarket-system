@extends('client.client_dashboard')
@section('client')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Chỉnh Sửa Thư Viện Ảnh</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Bảng thống kê</a></li>
                          <li class="breadcrumb-item active">Chỉnh Sửa Thư Viện Ảnh</li>
                      </ol>
                  </div>

              </div>
          </div>
      </div>
      <!-- end page title -->

      <div class="row">
          <div class="col-xl-9 col-lg-8">

<div class="card">
  <div class="card-body p-4">
    <form id="myForm" action="{{ route('gallery.update') }}" method="POST" enctype="multipart/form-data">
      @csrf
    <input type="hidden" name="id" value="{{ $gallery->id }}">
    <div class="row">
          <div class="col-lg-12">
            <div class="form-group mb-6">
              <label for="example-text-input" class="form-label">Ảnh Thư Viện</label>
              <input class="form-control" type="file" name="gallery_img" id="image">
            </div> </br>
            
            <div class="mb-3">
              <img id="showImage"
                  src="{{ asset($gallery->gallery_img) }}" 
                    alt="" class="rounded-circle p-1 bg-primary" width="110">
            </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-primary waves-effect waves-light">Lưu Thay Đổi</button>
            </div>
          </div>
      </div>
    </form>
  </div>            

                <!-- end tab content -->
            </div>
            <!-- end col -->

            <!-- end col -->
        </div>
        <!-- end row -->
        
    </div> <!-- container-fluid -->
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#image').change(function (e) {
            var reader = new FileReader();
            reader.onload = function(e){
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        })
    })

</script>


@endsection