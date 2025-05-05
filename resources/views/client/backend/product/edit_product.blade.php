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
                
                        <!-- Menu -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Menu Name</label>
                                <div class="form-control" readonly>
                                    {{ $productTemplateEdit->menu->menu_name ?? '-' }}
                                </div>
                                <input type="hidden" name="menu_id" value="{{ $productTemplateEdit->menu_id }}">
                            </div>
                        </div>
                
                        <!-- Product Template -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Product Template</label>
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
                                <label class="form-label">Category:</label>
                                <div id="categoryLabel" class="form-control" readonly>
                                    {{ $productTemplateEdit->category->category_name ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Size -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Size:</label>
                                <div id="sizeLabel" class="form-control" readonly>
                                    {{ $productTemplateEdit->size ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Unit -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Unit:</label>
                                <div id="unitLabel" class="form-control" readonly>
                                    {{ $productTemplateEdit->unit ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Price</label>
                                <input class="form-control" type="text" name="price" value="{{ $product->price }}" placeholder="Enter price">
                            </div>
                        </div>
                
                        <!-- Discount Price -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Discount Price</label>
                                <input class="form-control" type="text" name="discount_price" value="{{ $product->discount_price }}" placeholder="Enter discount price">
                            </div>
                        </div>
                
                        <!-- Quantity -->
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Quantity QTY</label>
                                <input class="form-control" type="number" name="qty" value="{{ $product->qty }}" placeholder="Enter quantity">
                            </div>
                        </div>
                
                    </div>
                
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="best_seller" id="formCheckBestSeller" value="1" {{ $product->best_seller ? 'checked' : '' }}>
                        <label for="formCheckBestSeller" class="form-check-label">Best Seller</label>
                    </div>
                
                    <br>
                
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="most_popular" id="formCheckMostPopular" value="1" {{ $product->most_popular ? 'checked' : '' }}>
                        <label for="formCheckMostPopular" class="form-check-label">Most Popular</label>
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
