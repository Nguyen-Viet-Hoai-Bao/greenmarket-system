@extends('admin.admin_dashboard')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Thêm Mẫu Sản Phẩm</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Bảng thống kê</a></li>
                            <li class="breadcrumb-item active">Thêm Mẫu Sản Phẩm</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <!-- Form Start -->
        <div class="row">
            <div class="col-xl-12">
                <form id="myForm" action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Category -->
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên Danh Mục</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Chọn</option>
                                    @foreach ($category as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Menu -->
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên Menu</label>
                                <select class="form-select" name="menu_id">
                                    <option selected="" disabled>Chọn</option>
                                    @foreach ($menu as $men)
                                        <option value="{{ $men->id }}">{{ $men->menu_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Product Name -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên Sản Phẩm</label>
                                <input class="form-control" type="text" name="name" value="" placeholder="Enter product name">
                            </div>
                        </div>

                        <!-- Additional Field 2 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Kích Cỡ</label>
                                <input class="form-control" type="number" name="size" placeholder="Enter size">
                            </div>
                        </div>

                        <!-- Product Unit -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Đơn Vị</label>
                                <input class="form-control" type="text" name="unit" value="" placeholder="Enter product unit">
                            </div>
                        </div>

                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Chế độ tồn kho</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stock_mode" id="stockModeQuantity" value="quantity" checked>
                                        <label class="form-check-label" for="stockModeQuantity">Theo Số Lượng (quantity)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stock_mode" id="stockModeUnit" value="unit">
                                        <label class="form-check-label" for="stockModeUnit">Theo Đơn Vị (unit)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Field 2 -->
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Ảnh Sản Phẩm</label>
                                <input class="form-control" type="file" name="image" id="image">
                            </div>
                        </div>
                        
                        <!-- Additional Field 2 -->
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <img id="showImage"
                                    src="{{ url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png')}}" 
                                    alt="" class="rounded-circle p-1 bg-primary" width="110">
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Thêm Mẫu Sản Phẩm</button>
                    </div>

                </form>
            </div>
        </div>
        <!-- End Form -->

    </div>
</div>

<!-- Image Preview Script -->
<script>
    $(document).ready(function () {
        $('#image').change(function (e) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>

<!-- Form Validation Script -->
<script>
    $(document).ready(function () {
        $('#myForm').validate({
            rules: {
                name: { required: true, },
                image: { required: true, },
                menu_id: { required: true, },
            },
            messages: {
                name: { required: 'Please Enter Name', },
                image: { required: 'Please Select Image', },
                menu_id: { required: 'Please Select One Menu', },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>

@endsection
