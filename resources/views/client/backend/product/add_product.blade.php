@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Add Product</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add Product</li>
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
                                <label class="form-label">Menu Name</label>
                                <select class="form-select" name="menu_id">
                                    <option selected="" disabled>Select</option>
                                    @foreach ($menus as $men)
                                        <option value="{{ $men->id }}">{{ $men->menu_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Product Template -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Product Template</label>
                                <select class="form-select" name="product_template_id" id="productTemplateSelect">
                                    <option selected disabled>Select product template</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Additional Field 2 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <img id="showImage"
                                    src="{{ url('upload/no_image.jpg') }}" 
                                    alt="" class="rounded p-1 bg-primary" width="110">
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Category:</label>
                                <div id="categoryLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>

                        <!-- Size -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Size:</label>
                                <div id="sizeLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>

                        <!-- Unit -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Unit:</label>
                                <div id="unitLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>

                        <!-- Additional Field 1 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Price</label>
                                <input class="form-control" type="text" name="price" placeholder="Enter price">
                            </div>
                        </div>

                        <!-- Additional Field 1 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Discount Price</label>
                                <input class="form-control" type="text" name="discount_price" placeholder="Enter discount price">
                            </div>
                        </div>

                        <!-- Additional Field 2 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Quantity QTY</label>
                                <input class="form-control" type="number" name="qty" placeholder="Enter quantity">
                            </div>
                        </div>

                    </div>
                    
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="best_seller" id="formCheck2" value="1">
                        <label for="formCheck2" class="form-check-lable">Best Seller</label>
                    </div>
                    <br>
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="most_popular" id="formCheck2" value="1">
                        <label for="formCheck2" name="" class="form-check-lable">Most Popular</label>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>

                </form>
            </div>
        </div>
        <!-- End Form -->

    </div>
</div>

<script>
    const productTemplatesByMenu = @json($productTemplates);

    const menuSelect = document.querySelector('select[name="menu_id"]');
    const productTemplateSelect = document.getElementById('productTemplateSelect');
    const imagePreview = document.getElementById('showImage');

    let currentTemplates = []; // lưu tạm các template theo menu đang chọn

    menuSelect.addEventListener('change', function () {
        const selectedMenuId = this.value;

        // Xóa options cũ
        productTemplateSelect.innerHTML = '<option selected disabled>Select product template</option>';
        currentTemplates = productTemplatesByMenu[selectedMenuId] || [];

        currentTemplates.forEach(template => {
            const option = document.createElement('option');
            option.value = template.id;
            option.textContent = template.name; // đổi theo tên cột bạn muốn
            productTemplateSelect.appendChild(option);
        });

        imagePreview.src = '{{ url('upload/no_image.jpg') }}'; // reset ảnh nếu đổi menu
    });

    productTemplateSelect.addEventListener('change', function () {
        const selectedId = parseInt(this.value);
        const selectedTemplate = currentTemplates.find(t => t.id === selectedId);

        if (selectedTemplate) {
            // Cập nhật ảnh
            if (selectedTemplate.image) {
                imagePreview.src = '{{ url('/') }}/' + selectedTemplate.image;
            } else {
                imagePreview.src = '{{ url('upload/no_image.jpg') }}';
            }

            // Cập nhật các label mới
            categoryLabel.textContent = selectedTemplate.category.category_name || '-';
            sizeLabel.textContent = selectedTemplate.size || '-';
            unitLabel.textContent = selectedTemplate.unit || '-';
        } else {
            imagePreview.src = '{{ url('upload/no_image.jpg') }}';
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
