@extends('admin.admin_dashboard')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <!-- Tiêu đề trang -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Chỉnh sửa sản phẩm</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Bảng thống kê</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa sản phẩm</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kết thúc tiêu đề trang -->

        <!-- Bắt đầu form -->
        <div class="row">
            <div class="col-xl-12">
                <form id="myForm" action="{{ route('admin.product.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">

                    <div class="row">
                        <!-- Danh mục -->

                        <div class="col-xl-3 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên danh mục</label>
                                <select class="form-select" name="category_id">
                                    <option>Chọn</option>
                                    @foreach ($category as $cat)
                                        <option value="{{ $cat->id }}" 
                                                {{ $cat->id == $product->category_id ? 'selected' : '' }}>
                                            {{ $cat->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Menu -->
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên menu</label>
                                <select class="form-select" name="menu_id">
                                    <option selected="" disabled>Chọn</option>
                                    @foreach ($menu as $men)
                                        <option value="{{ $men->id }}"
                                            {{ $men->id == $product->menu_id ? 'selected' : '' }}>
                                            {{ $men->menu_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Tên sản phẩm -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên sản phẩm</label>
                                <input class="form-control" type="text" name="name" value="{{ $product->name }}" placeholder="Nhập tên sản phẩm">
                            </div>
                        </div>

                        <!-- Kích thước -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Kích thước</label>
                                <input class="form-control" type="number" name="size" value="{{ $product->size }}" placeholder="Nhập kích thước">
                            </div>
                        </div>

                        <!-- Đơn vị -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Đơn vị</label>
                                <input class="form-control" type="text" name="unit" value="{{ $product->unit }}" placeholder="Nhập đơn vị">
                            </div>
                        </div>

                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Chế độ tồn kho</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stock_mode" id="stockModeQuantity" value="quantity" {{ $product->stock_mode == 'quantity' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="stockModeQuantity">Theo Số Lượng (quantity)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stock_mode" id="stockModeUnit" value="unit" {{ $product->stock_mode == 'unit' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="stockModeUnit">Theo Đơn Vị (unit)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hình ảnh sản phẩm -->
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Hình ảnh sản phẩm</label>
                                <input class="form-control" type="file" name="image" id="image">
                            </div>
                        </div>
                        
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <img id="showImage"
                                    src="{{ asset($product->image) }}" 
                                    alt="" class="rounded-circle p-1 bg-primary" width="110">
                            </div>
                        </div>
                    </div>

                    <!-- Chi tiết sản phẩm -->
                    <div class="col-xl-12 col-md-12">
                        <h5>Chi tiết sản phẩm</h5>
                        <div class="form-group mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="4">{{ $product->productDetail->description ?? '' }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Thông tin sản phẩm</label>
                            <textarea class="form-control" name="product_info" rows="4">{{ $product->productDetail->product_info ?? '' }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="note" rows="4">{{ $product->productDetail->note ?? '' }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Xuất xứ</label>
                            <input class="form-control" type="text" name="origin" value="{{ $product->productDetail->origin ?? '' }}" placeholder="Nhập xuất xứ">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Bảo quản</label>
                            <textarea class="form-control" name="preservation" rows="4">{{ $product->productDetail->preservation ?? '' }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Hướng dẫn sử dụng</label>
                            <textarea class="form-control" name="usage_instructions" rows="4">{{ $product->productDetail->usage_instructions ?? '' }}</textarea>
                        </div>

                    </div>
                    
                    <!-- Nút submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>

                </form>
            </div>
        </div>
        <!-- Kết thúc form -->

    </div>
</div>

<!-- Script hiển thị hình ảnh -->
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

<!-- Script kiểm tra form -->
<script>
    $(document).ready(function () {
        $('#myForm').validate({
            rules: {
                name: { required: true, },
                menu_id: { required: true, },
            },
            messages: {
                name: { required: 'Vui lòng nhập tên', },
                menu_id: { required: 'Vui lòng chọn một menu', },
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
