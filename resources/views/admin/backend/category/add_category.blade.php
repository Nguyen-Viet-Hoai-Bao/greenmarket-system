@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Thêm Danh Mục</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Trang chủ</a></li>
                          <li class="breadcrumb-item active">Thêm Danh Mục</li>
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
    <form id="myForm" action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

    <div class="row">
          <div class="col-lg-12">
              <div>
                  <div class="form-group mb-6">
                      <label for="example-text-input" class="form-label">Tên Danh Mục</label>
                      <input class="form-control" type="text" name="category_name" value="" id="example-text-input">
                  </div>
              </div>
          </div>
            <div class="col-lg-12">
                <div class="form-group mb-6">
                    <label for="menu_id" class="form-label">Chọn Menu</label>
                    <select name="menu_id" id="menu_id" class="form-control" required>
                        <option value="" disabled selected>-- Chọn Menu --</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}">{{ $menu->menu_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
          <div class="col-lg-12">
            <div class="form-group mb-6">
              <label for="example-text-input" class="form-label">Ảnh Danh Mục</label>
              <input class="form-control" type="file" name="image" id="image">
            </div> </br>
            
            <div class="mb-3">
              <img id="showImage"
                  src="{{ url('upload/no_image.jpg')}}" 
                    alt="" class="rounded-circle p-1 bg-primary" width="110">
            </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-primary waves-effect waves-light">Lưu Thay Đổi</button>
            </div>
          </div>
      </div>
    </form>
  </div>            
</div>

        </div>
    </div> <!-- container-fluid -->
</div>
</div>

<script>
    function bannerEdit(id){
        $.ajax({
            type: 'GET',
            url: '/edit/banner/'+id,
            dataType: 'json',

            success:function(data){
                $('#banner_url').val(data.url);
                $('#bannerImage').attr('src', data.image);
                $('#banner_id').val(data.id);
            }
        })
    }
</script>

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
