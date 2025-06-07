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
                <form id="bulkForm" action="{{ route('product.store.multi') }}" method="POST">
                    @csrf
                    <div id="product-container">
                        <div class="row product-item mb-2">
                            <div class="col-xl-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Mẫu Sản Phẩm</label>
                                    <select class="form-select product-template-select @error('products.0.product_template_id') is-invalid @enderror" name="products[0][product_template_id]" required>
                                        <option disabled selected>Chọn mẫu sản phẩm</option>
                                        @foreach ($productTemplates->flatten() as $template)
                                            <option value="{{ $template->id }}" {{ old('products.0.product_template_id') == $template->id ? 'selected' : '' }}>
                                                {{ $template->name ?? 'Mẫu #' . $template->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('products.0.product_template_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <div class="form-group mb-3">
                                    <label class="form-label">Số Lượng</label>
                                    <input class="form-control qty @error('products.0.qty') is-invalid @enderror" type="number" name="products[0][qty]" value="{{ old('products.0.qty') }}" required>
                                    @error('products.0.qty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <div class="form-group mb-3">
                                    <label class="form-label">Giá Nhập</label>
                                    <input class="form-control cost-price @error('products.0.cost_price') is-invalid @enderror" type="text" name="products[0][cost_price]" value="{{ old('products.0.cost_price') }}" required>
                                    @error('products.0.cost_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <div class="form-group mb-3">
                                    <label class="form-label">Giá Bán</label>
                                    <input class="form-control price @error('products.0.price') is-invalid @enderror" type="text" name="products[0][price]" value="{{ old('products.0.price') }}" required>
                                    @error('products.0.price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <div class="form-group mb-3">
                                    <label class="form-label">Giá Giảm</label>
                                    <input class="form-control discount-price @error('products.0.discount_price') is-invalid @enderror" type="text" name="products[0][discount_price]" value="{{ old('products.0.discount_price') }}">
                                    @error('products.0.discount_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-secondary" onclick="addProductRow()">+ Thêm Dòng</button>
                        <button type="submit" class="btn btn-primary">Thêm Hàng Loạt</button>
                    </div>
                </form>
            </div>
        </div>


    </div>
</div>

<script>
    let index = 1;

    function addProductRow() {
        const container = document.getElementById('product-container');
        const originalRow = container.querySelector('.product-item');
        const newRow = originalRow.cloneNode(true);

        newRow.querySelectorAll('input, select').forEach(el => {
            // Reset value
            el.value = '';

            // Update name attribute
            if (el.name) {
                el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
            }
        });

        container.appendChild(newRow);
        index++;

        // Re-bind event for new select
        bindTemplateChangeEvent(newRow.querySelector('.product-template-select'));
    }

    function bindTemplateChangeEvent(selectElement) {
        selectElement.addEventListener('change', function () {
            const templateId = this.value;
            const row = this.closest('.product-item');
            const costPrice = row.querySelector('.cost-price');
            const price = row.querySelector('.price');
            const discountPrice = row.querySelector('.discount-price');
            const qty = row.querySelector('.qty');

            if (templateId) {
                fetch(`/product/get-info/${templateId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            costPrice.placeholder = `Giá cũ: ${data.cost_price}`;
                            price.placeholder = `Giá cũ: ${data.price}`;
                            discountPrice.placeholder = data.discount_price ? `Giá cũ: ${data.discount_price}` : 'Không có';
                            qty.placeholder = `Kho: ${data.qty}`;
                        } else {
                            costPrice.placeholder = 'Giá Nhập';
                            price.placeholder = 'Giá Bán';
                            discountPrice.placeholder = 'Giá Giảm';
                            qty.placeholder = 'Số Lượng';
                        }
                    });
            }
        });
    }

    // Gán cho dòng đầu tiên
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.product-template-select').forEach(select => {
            bindTemplateChangeEvent(select);
        });
    });
</script>
@endsection
