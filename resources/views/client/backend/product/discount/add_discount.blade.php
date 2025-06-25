{{-- client.backend.product.discount.add_discount.blade.php --}}

@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

{{-- Libraries for datetime picker (Flatpickr, although native input type="datetime-local" is used now) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/l10n/vn.js"></script> {{-- For Vietnamese locale --}}

{{-- Select2 for product selection --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Thêm Đợt Giảm Giá Mới</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('product.discounts.all') }}">Giảm giá</a></li>
                            <li class="breadcrumb-item active">Thêm mới</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Điền thông tin đợt giảm giá</h4>
                        <p class="card-title-desc">Áp dụng giảm giá cho sản phẩm của bạn theo thời gian.</p>

                        @php
                            $oldStartAt = old('start_at') ? \Carbon\Carbon::parse(old('start_at'))->format('Y-m-d\TH:i') : '';
                            $oldEndAt = old('end_at') ? \Carbon\Carbon::parse(old('end_at'))->format('Y-m-d\TH:i') : '';
                        @endphp

                        <form method="POST" action="{{ route('product.discounts.store') }}" id="discountForm">
                            @csrf

                            <!-- Chọn sản phẩm -->
                            <div class="mb-3">
                                <label for="product_news_id" class="form-label">Chọn Sản Phẩm</label>
                                <select class="form-select select2 @error('product_news_id') is-invalid @enderror" name="product_news_id" id="product_news_id" required>
                                    <option value="">-- Chọn Sản Phẩm --</option>
                                    @foreach($productNews as $item)
                                        <option value="{{ $item->id }}" {{ old('product_news_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->productTemplate->name ?? 'N/A' }} (ID: {{ $item->id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_news_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phần trăm giảm giá -->
                            <div class="mb-3">
                                <label for="discount_percent" class="form-label">Phần Trăm Giảm Giá (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="discount_percent" id="discount_percent"
                                      class="form-control discount-type-group @error('discount_percent') is-invalid @enderror"
                                      value="{{ old('discount_percent') }}" placeholder="VD: 10">
                                @error('discount_percent')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Giá giảm cố định -->
                            <div class="mb-3">
                                <label for="discount_price" class="form-label">Giá Giảm Cố Định (VNĐ)</label>
                                <input type="number" step="1000" min="0" name="discount_price" id="discount_price"
                                      class="form-control discount-type-group @error('discount_price') is-invalid @enderror"
                                      value="{{ old('discount_price') }}" placeholder="VD: 50000">
                                @error('discount_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            @error('discount_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <!-- Thời gian bắt đầu -->
                            <div class="mb-3">
                                <label for="start_at" class="form-label">Thời Gian Bắt Đầu</label>
                                <input type="datetime-local" name="start_at" id="start_at"
                                      class="form-control @error('start_at') is-invalid @enderror"
                                      value="{{ $oldStartAt }}" required>
                                @error('start_at')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Thời gian kết thúc -->
                            <div class="mb-3">
                                <label for="end_at" class="form-label">Thời Gian Kết Thúc</label>
                                <input type="datetime-local" name="end_at" id="end_at"
                                      class="form-control @error('end_at') is-invalid @enderror"
                                      value="{{ $oldEndAt }}" required>
                                @error('end_at')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Thêm Đợt Giảm Giá</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function (){
        $('#product_news_id').select2({
            placeholder: "-- Chọn Sản Phẩm --",
            allowClear: true
        });

        $('#discountForm').validate({
            rules: {
                product_news_id: {
                    required: true,
                },
                start_at: {
                    required: true,
                },
                end_at: {
                    required: true,
                },
                discount_percent: {
                    number: true,
                    min: 0,
                    max: 100,
                },
                discount_price: {
                    number: true,
                    min: 0,
                }
            },
            messages: {
                product_news_id: {
                    required: 'Vui lòng chọn sản phẩm.',
                },
                start_at: {
                    required: 'Vui lòng chọn thời gian bắt đầu giảm giá.',
                },
                end_at: {
                    required: 'Vui lòng chọn thời gian kết thúc giảm giá.',
                },
                discount_percent: {
                    number: 'Phần trăm giảm giá phải là số.',
                    min: 'Phần trăm giảm giá không thể âm.',
                    max: 'Phần trăm giảm giá không thể lớn hơn 100.',
                },
                discount_price: {
                    number: 'Giá giảm cố định phải là số.',
                    min: 'Giá giảm cố định không thể âm.',
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            errorPlacement: function (error, element) {
                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight : function(element, errorClass, validClass){
                $(element).addClass('is-invalid');
            },
            unhighlight : function(element, errorClass, validClass){
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                // Kiểm tra logic giảm giá (client-side)
                var discountPercent = $('#discount_percent').val();
                var discountPrice = $('#discount_price').val();

                // Đảm bảo chỉ một trong hai trường giảm giá được điền
                if ((discountPercent && discountPrice) || (!discountPercent && !discountPrice)) {
                    $('#discount_percent').addClass('is-invalid');
                    $('#discount_price').addClass('is-invalid');
                    $('div.invalid-feedback[data-for="discount_type"]').remove(); // Xóa thông báo lỗi cũ nếu có
                    $('<div class="invalid-feedback d-block" data-for="discount_type">Chỉ được phép nhập một trong hai: phần trăm giảm giá HOẶC giá giảm cố định.</div>').insertAfter($('#discount_price').closest('.mb-3').find('.form-control').last());
                    return false;
                } else {
                    $('#discount_percent').removeClass('is-invalid');
                    $('#discount_price').removeClass('is-invalid');
                    $('div.invalid-feedback[data-for="discount_type"]').remove();
                }

                var startDateTimeValue = $('#start_at').val();
                var endDateTimeValue = $('#end_at').val();

                // Chuyển đổi thành đối tượng Date để so sánh
                var startDateTime = new Date(startDateTimeValue);
                var endDateTime = new Date(endDateTimeValue);

                if (endDateTime <= startDateTime) {
                    $('#end_at').addClass('is-invalid');
                    // Xóa lỗi cũ nếu có và thêm lỗi mới
                    $('div.invalid-feedback[data-for="end_at_after"]').remove();
                    $('<div class="invalid-feedback d-block" data-for="end_at_after">Thời gian kết thúc phải sau thời gian bắt đầu.</div>').insertAfter($('#end_at'));
                    return false; // Ngăn chặn form submit
                } else {
                    $('#end_at').removeClass('is-invalid');
                    $('div.invalid-feedback[data-for="end_at_after"]').remove(); // Xóa lỗi nếu hợp lệ
                }
                // --- KẾT THÚC LOGIC BỔ SUNG ---

                // Nếu tất cả các kiểm tra đều qua, submit form
                form.submit();
            }
        });

        // Thêm class group cho các trường giảm giá để jQuery Validate có thể dùng require_from_group (nếu bạn kích hoạt lại)
        $('#discount_percent, #discount_price').addClass('discount-type-group');
    });
</script>

@endsection
