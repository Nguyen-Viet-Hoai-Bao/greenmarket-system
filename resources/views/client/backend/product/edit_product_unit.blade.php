@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Chỉnh Sửa Đơn Vị Sản Phẩm</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Tồn Kho</a></li>
                            <li class="breadcrumb-item active">Chỉnh Sửa Đơn Vị</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Sản Phẩm: {{ $productUnit->productNew->productTemplate->name ?? 'N/A' }}</h4>
                        <p class="card-title-desc">Cập nhật thông tin cho đơn vị sản phẩm này.</p>

                        <form method="POST" action="{{ route('product.unit.update', $productUnit->id) }}" id="myForm">
                            @csrf

                            <div class="row mb-3">
                                <label for="product_name" class="col-sm-2 col-form-label">Tên Sản Phẩm</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" value="{{ $productUnit->productNew->productTemplate->name ?? 'N/A' }}" id="product_name" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="category_name" class="col-sm-2 col-form-label">Danh Mục</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" value="{{ $productUnit->productNew->productTemplate->category->category_name ?? 'N/A' }}" id="category_name" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="stock_mode" class="col-sm-2 col-form-label">Chế Độ Kho</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" value="{{ $productUnit->productNew->productTemplate->stock_mode === 'quantity' ? 'Theo Số Lượng' : 'Theo Đơn Vị' }}" id="stock_mode" readonly>
                                    <input type="hidden" id="raw_stock_mode" value="{{ $productUnit->productNew->productTemplate->stock_mode }}">
                                </div>
                            </div>

                            {{-- Trường Quantity/Weight tùy thuộc vào stock_mode --}}
                            <div class="row mb-3" id="quantity_field" style="{{ $productUnit->productNew->productTemplate->stock_mode === 'quantity' ? '' : 'display: none;' }}">
                                <label for="batch_qty" class="col-sm-2 col-form-label">Số Lượng</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="number" name="batch_qty" value="{{ old('batch_qty', $productUnit->batch_qty) }}" id="batch_qty">
                                    @error('batch_qty')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3" id="weight_field" style="{{ $productUnit->productNew->productTemplate->stock_mode === 'unit' ? '' : 'display: none;' }}">
                                <label for="weight" class="col-sm-2 col-form-label">Cân Nặng ({{ $productUnit->productNew->productTemplate->unit ?? '' }})</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" name="weight" value="{{ old('weight', $productUnit->weight) }}" id="weight" placeholder="VD: 1.5, 200">
                                    @error('weight')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="cost_price" class="col-sm-2 col-form-label">Giá Nhập</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="number" name="cost_price" value="{{ old('cost_price', $productUnit->cost_price) }}" id="cost_price">
                                    @error('cost_price')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="sale_price" class="col-sm-2 col-form-label">Giá Bán</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="number" name="sale_price" value="{{ old('sale_price', $productUnit->sale_price) }}" id="sale_price">
                                    @error('sale_price')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="expiry_date" class="col-sm-2 col-form-label">Hạn Sử Dụng</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="date" name="expiry_date" value="{{ old('expiry_date', $productUnit->expiry_date) }}" id="expiry_date">
                                    @error('expiry_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Cập Nhật Đơn Vị">
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function (){
        $('#myForm').validate({
            rules: {
                @if($productUnit->productNew->productTemplate->stock_mode === 'quantity')
                batch_qty: {
                    required : true,
                    min: 1,
                    digits: true, // Chỉ chấp nhận số nguyên
                },
                @else {{-- unit mode --}}
                weight: {
                    required : true,
                    min: 0.01,
                    number: true, // Chấp nhận số thập phân
                },
                @endif
                cost_price: {
                    required : true,
                    min: 0,
                    number: true,
                },
                sale_price: {
                    required : true,
                    min: 0,
                    number: true,
                },
            },
            messages :{
                @if($productUnit->productNew->productTemplate->stock_mode === 'quantity')
                batch_qty: {
                    required : 'Vui lòng nhập số lượng',
                    min: 'Số lượng phải lớn hơn hoặc bằng 1',
                    digits: 'Số lượng phải là số nguyên',
                },
                @else {{-- unit mode --}}
                weight: {
                    required : 'Vui lòng nhập cân nặng',
                    min: 'Cân nặng phải lớn hơn 0',
                    number: 'Cân nặng phải là một số',
                },
                @endif
                cost_price: {
                    required : 'Vui lòng nhập giá nhập',
                    min: 'Giá nhập không thể âm',
                    number: 'Giá nhập phải là số',
                },
                sale_price: {
                    required : 'Vui lòng nhập giá bán',
                    min: 'Giá bán không thể âm',
                    number: 'Giá bán phải là số',
                },
            },
            errorElement : 'span',
            errorPlacement: function (error,element) {
                error.addClass('invalid-feedback');
                element.closest('.col-sm-10').append(error);
            },
            highlight : function(element, errorClass, validClass){
                $(element).addClass('is-invalid');
            },
            unhighlight : function(element, errorClass, validClass){
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>

@endsection