@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Edit Product</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Edit Product</li>
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
                        <!-- Category -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Category Name</label>
                                <select class="form-select" name="category_id">
                                    <option>Select</option>
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
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Menu Name</label>
                                <select class="form-select" name="menu_id">
                                    <option selected="" disabled>Select</option>
                                    @foreach ($menu as $men)
                                        <option value="{{ $men->id }}"
                                            {{ $men->id == $product->menu_id ? 'selected' : '' }}>
                                            {{ $men->menu_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Menu -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">City Name</label>
                                <select class="form-select" name="city_id">
                                    <option value="">Select</option>
                                    @foreach ($city as $cit)
                                        <option value="{{ $cit->id }}" 
                                                {{ $cit->id == $product->city_id ? 'selected' : '' }}>
                                            {{ $cit->city_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Product Name -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Product Name</label>
                                <input class="form-control" type="text" name="name" value="{{ $product->name }}" placeholder="Enter product name">
                            </div>
                        </div>

                        <!-- Additional Field 1 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Price</label>
                                <input class="form-control" type="text" name="price" value="{{ $product->price }}" placeholder="Enter price">
                            </div>
                        </div>

                        <!-- Additional Field 1 -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Discount Price</label>
                                <input class="form-control" type="text" name="discount_price" value="{{ $product->discount_price }}" placeholder="Enter discount price">
                            </div>
                        </div>

                        <!-- Additional Field 2 -->
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Size</label>
                                <input class="form-control" type="number" name="size" value="{{ $product->size }}" placeholder="Enter size">
                            </div>
                        </div>

                        <!-- Additional Field 2 -->
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Quantity QTY</label>
                                <input class="form-control" type="number" name="qty" value="{{ $product->qty }}" placeholder="Enter quantity">
                            </div>
                        </div>
                        
                        <!-- Additional Field 2 -->
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Product Image</label>
                                <input class="form-control" type="file" name="image" id="image">
                            </div>
                        </div>
                        
                        <!-- Additional Field 2 -->
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group mb-3">
                                <img id="showImage"
                                    src="{{ asset($product->image) }}" 
                                    alt="" class="rounded-circle p-1 bg-primary" width="110">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="best_seller" id="formCheck2" value="1"
                                {{ $product->best_seller == 1 ? 'checked' : '' }}>
                        <label for="formCheck2" class="form-check-lable">Best Seller</label>
                    </div>
                    <br>
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="most_popular" id="formCheck2" value="1"
                                {{ $product->most_popular == 1 ? 'checked' : '' }}>
                        <label for="formCheck2" name="" class="form-check-lable">Most Popular</label>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
