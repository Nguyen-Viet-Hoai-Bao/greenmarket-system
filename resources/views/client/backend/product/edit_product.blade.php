@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Chỉnh sửa sản phẩm</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa sản phẩm</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <!-- Form Start -->
        <div class="row">
            <div class="col-xl-12">
                <form id="myForm" action="{{ route('product.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                
                    <div class="row">
                
                        <!-- Menu -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên Menu</label>
                                <div class="form-control" readonly>
                                    {{ $productTemplateEdit->menu->menu_name ?? '-' }}
                                </div>
                                <input type="hidden" name="menu_id" value="{{ $productTemplateEdit->menu_id }}">
                            </div>
                        </div>
                
                        <!-- Product Template -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mẫu sản phẩm</label>
                                <div class="form-control" readonly>
                                    {{ $productTemplateEdit->name ?? '-' }}
                                </div>
                                <input type="hidden" name="product_template_id" value="{{ $productTemplateEdit->id }}">
                            </div>
                        </div>
                
                        <!-- Image -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <img id="showImage" 
                                     src="{{ $productTemplateEdit ? asset($productTemplateEdit->image) : url('upload/no_image.jpg') }}" 
                                     alt="" class="rounded p-1 bg-primary" width="110">
                            </div>
                        </div>
                        
                        <!-- Category -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Danh mục:</label>
                                <div id="categoryLabel" class="form-control" readonly>
                                    {{ $productTemplateEdit->category->category_name ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Size -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Kích cỡ:</label>
                                <div id="sizeLabel" class="form-control" readonly>
                                    {{ $productTemplateEdit->size ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Unit -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Đơn vị:</label>
                                <div id="unitLabel" class="form-control" readonly>
                                    {{ $productTemplateEdit->unit ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <!-- Giá nhập -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Giá nhập</label>
                                <input 
                                    class="form-control @error('cost_price') is-invalid @enderror" 
                                    type="text" 
                                    name="cost_price" 
                                    value="{{ old('cost_price', $product->cost_price) }}" 
                                    placeholder="Enter cost price"
                                >
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Giá bán -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Giá bán</label>
                                <input 
                                    class="form-control @error('price') is-invalid @enderror" 
                                    type="text" 
                                    name="price" 
                                    value="{{ old('price', $product->price) }}" 
                                    placeholder="Enter price"
                                >
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Giá giảm giá -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Giá giảm giá</label>
                                <input 
                                    class="form-control @error('discount_price') is-invalid @enderror" 
                                    type="text" 
                                    name="discount_price" 
                                    value="{{ old('discount_price', $product->discount_price) }}" 
                                    placeholder="Enter discount price"
                                >
                                @error('discount_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Số lượng -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Số lượng</label>
                                <input 
                                    class="form-control @error('qty') is-invalid @enderror" 
                                    type="number" 
                                    name="qty" 
                                    value="{{ old('qty', $product->qty) }}" 
                                    placeholder="Enter quantity"
                                >
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                
                    </div>
                
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="best_seller" id="formCheckBestSeller" value="1" {{ $product->best_seller ? 'checked' : '' }}>
                        <label for="formCheckBestSeller" class="form-check-label">Sản phẩm bán chạy</label>
                    </div>
                
                    <br>
                
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="most_popular" id="formCheckMostPopular" value="1" {{ $product->most_popular ? 'checked' : '' }}>
                        <label for="formCheckMostPopular" class="form-check-label">Sản phẩm phổ biến nhất</label>
                    </div>
                
                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                
                </form>
            </div>
        </div>
        <!-- End Form -->

    </div>
</div>

<!-- Form Validation Script -->
<script>
    $(document).ready(function () {
        $('#myForm').validate({
            rules: {
                name: { required: true, },
                menu_id: { required: true, },
            },
            messages: {
                name: { required: 'Please Enter Name', },
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
