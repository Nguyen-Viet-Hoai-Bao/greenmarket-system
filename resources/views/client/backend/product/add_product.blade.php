@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Thêm Sản Phẩm</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Bảng thống kê</a></li>
                            <li class="breadcrumb-item active">Thêm Sản Phẩm</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Title -->

        <!-- Form Start -->
        <div class="row">
            <div class="col-xl-12">
                <form id="myForm" action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Menu -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên Menu</label>
                                <select class="form-select" id="menuSelect" name="menu_id">
                                    <option selected="" disabled>Chọn</option>
                                    @foreach ($menus as $men)
                                        <option value="{{ $men->id }}">{{ $men->menu_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Danh Mục</label>
                                <select class="form-select" name="category_id" id="categorySelect">
                                    <option selected="" disabled>Chọn danh mục</option>
                                </select>
                            </div>
                        </div>

                        <!-- Product Template -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mẫu Sản Phẩm</label>
                                <select class="form-select" name="product_template_id" id="productTemplateSelect">
                                    <option selected disabled>Chọn mẫu sản phẩm</option>
                                </select>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <img id="showImage" src="{{ url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}" alt="" class="rounded p-1 bg-primary" width="110">
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Danh Mục:</label>
                                <div id="categoryLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>

                        <!-- Size -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Kích Cỡ:</label>
                                <div id="sizeLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>

                        <!-- Unit -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Đơn Vị:</label>
                                <div id="unitLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>
                        
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Giá Nhập</label>
                                <input 
                                    class="form-control @error('cost_price') is-invalid @enderror" 
                                    type="text" 
                                    name="cost_price" 
                                    placeholder="Giá Nhập" 
                                    value="{{ old('cost_price') }}"
                                >
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Field 1 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Giá Bán</label>
                                <input 
                                    class="form-control @error('price') is-invalid @enderror" 
                                    type="text" 
                                    name="price" 
                                    placeholder="Giá Bán" 
                                    value="{{ old('price') }}"
                                >
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Field 1 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Giá Giảm</label>
                                <input 
                                    class="form-control @error('discount_price') is-invalid @enderror" 
                                    type="text" 
                                    name="discount_price" 
                                    placeholder="Giá Giảm" 
                                    value="{{ old('discount_price') }}"
                                >
                                @error('discount_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Field 2 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Số Lượng</label>
                                <input 
                                    class="form-control @error('qty') is-invalid @enderror" 
                                    type="number" 
                                    name="qty" 
                                    placeholder="Số Lượng" 
                                    value="{{ old('qty') }}"
                                >
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="best_seller" id="formCheck2" value="1">
                        <label for="formCheck2" class="form-check-lable">Sản Phẩm Bán Chạy</label>
                    </div>
                    <br>
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="most_popular" id="formCheck2" value="1">
                        <label for="formCheck2" name="" class="form-check-lable">Sản Phẩm Phổ Biến</label>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Thêm Sản Phẩm</button>
                    </div>

                </form>
            </div>
        </div>
        <!-- End Form -->

    </div>
</div>
<script>
    const productTemplatesByMenu = @json($productTemplates); // Sản phẩm nhóm theo menu_id

    const menuSelect = document.getElementById('menuSelect');
    const categorySelect = document.getElementById('categorySelect');
    const productTemplateSelect = document.getElementById('productTemplateSelect');
    const imagePreview = document.getElementById('showImage');

    let currentTemplates = []; // lưu tạm các template theo menu đang chọn

    // Lắng nghe sự kiện thay đổi Menu
    menuSelect.addEventListener('change', function () {
        const selectedMenuId = this.value;
        console.log("Menu Selected: ", selectedMenuId);

        // Làm mới danh sách Category
        categorySelect.innerHTML = '<option selected disabled>Chọn danh mục</option>';

        const categories = @json($categories);
        const filteredCategories = categories.filter(category => category.menu_id == selectedMenuId);

        console.log("Filtered Categories: ", filteredCategories);

        filteredCategories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.category_name;
            categorySelect.appendChild(option);
        });
    });

    categorySelect.addEventListener('change', function () {
        const selectedCategoryId = this.value;
        console.log("Category Selected: ", selectedCategoryId);

        // Làm mới dropdown sản phẩm
        productTemplateSelect.innerHTML = '<option selected disabled>Chọn mẫu sản phẩm</option>';

        // Lọc sản phẩm theo category_id và menu_id
        const selectedMenuId = menuSelect.value; // Lấy menu_id đã chọn
        const filteredProducts = (productTemplatesByMenu[selectedMenuId] || []).filter(product => product.category_id == selectedCategoryId);

        currentTemplates = filteredProducts;

        console.log("Filtered Products: ", filteredProducts);  // Kiểm tra các sản phẩm sau khi lọc

        filteredProducts.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.name; // Đổi theo tên sản phẩm
            productTemplateSelect.appendChild(option);
        });
    });

    productTemplateSelect.addEventListener('change', function () {
        const selectedId = parseInt(this.value);
        const selectedTemplate = currentTemplates.find(t => t.id === selectedId);

        if (selectedTemplate) {
            // Cập nhật ảnh
            if (selectedTemplate.image) {
                imagePreview.src = '{{ url('/') }}/' + selectedTemplate.image;
            } else {
                imagePreview.src = '{{ url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}';
            }

            // Cập nhật các label mới
            categoryLabel.textContent = selectedTemplate.category.category_name || '-';
            sizeLabel.textContent = selectedTemplate.size || '-';
            unitLabel.textContent = selectedTemplate.unit || '-';
        } else {
            imagePreview.src = '{{ url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}';
            categoryLabel.textContent = '-';
            sizeLabel.textContent = '-';
            unitLabel.textContent = '-';
        }
    });
</script>



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
