{{-- client.backend.product.discount.edit_discount.blade.php --}}

@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

{{-- Libraries for datetime picker --}}
{{-- Không còn cần Flatpickr nếu dùng input type="datetime-local", nhưng vẫn giữ link CSS cho style chung --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.css">
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/flatpickr.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.9/l10n/vn.js"></script> --}}

{{-- Select2 for product selection --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Chỉnh Sửa Đợt Giảm Giá</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('product.discounts.all') }}">Giảm giá</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Cập nhật thông tin đợt giảm giá</h4>
                        <p class="card-title-desc">Thay đổi các thông tin áp dụng giảm giá cho sản phẩm.</p>

                        {{-- PHP block to format old data or existing data for datetime-local input --}}
                        @php
                            // Lấy giá trị cũ nếu có lỗi validation, nếu không thì lấy từ đối tượng $discount
                            $currentStartAt = old('start_at', $discount->start_at);
                            $currentEndAt = old('end_at', $discount->end_at);

                            // Format sang định dạng YYYY-MM-DDTHH:MM cho input type="datetime-local"
                            $formattedStartAt = $currentStartAt ? \Carbon\Carbon::parse($currentStartAt)->format('Y-m-d\TH:i') : '';
                            $formattedEndAt = $currentEndAt ? \Carbon\Carbon::parse($currentEndAt)->format('Y-m-d\TH:i') : '';
                        @endphp

                        <form method="POST" action="{{ route('product.discounts.update', $discount->id) }}" id="discountForm">
                            @csrf

                            <!-- Chọn sản phẩm -->
                            <div class="mb-3">
                                <label for="product_news_id" class="form-label">Sản Phẩm Áp Dụng</label>
                                <select class="form-select select2 @error('product_news_id') is-invalid @enderror" name="product_news_id" id="product_news_id" required>
                                    <option value="">-- Chọn Sản Phẩm --</option>
                                    @foreach($productNews as $item)
                                        <option value="{{ $item->id }}" {{ (old('product_news_id', $discount->product_news_id) == $item->id) ? 'selected' : '' }}>
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
                                       value="{{ old('discount_percent', $discount->discount_percent) }}" placeholder="Ví dụ: 10 (giảm 10%)">
                                @error('discount_percent')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Chỉ nhập một trong hai loại giảm giá.</small>
                            </div>

                            <!-- Giá giảm cố định -->
                            <div class="mb-3">
                                <label for="discount_price" class="form-label">Giá Giảm Cố Định (VNĐ)</label>
                                <input type="number" step="1000" min="0" name="discount_price" id="discount_price"
                                       class="form-control discount-type-group @error('discount_price') is-invalid @enderror"
                                       value="{{ old('discount_price', $discount->discount_price) }}" placeholder="Ví dụ: 50000 (giảm 50.000 VNĐ)">
                                @error('discount_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Chỉ nhập một trong hai loại giảm giá.</small>
                            </div>
                            {{-- Error cho trường discount_type (kiểm tra ở backend) --}}
                            @error('discount_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <!-- Thời gian bắt đầu -->
                            <div class="mb-3">
                                <label for="start_at" class="form-label">Thời Gian Bắt Đầu</label>
                                <input type="datetime-local" name="start_at" id="start_at"
                                       class="form-control @error('start_at') is-invalid @enderror"
                                       value="{{ $formattedStartAt }}" required>
                                @error('start_at')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Thời gian kết thúc -->
                            <div class="mb-3">
                                <label for="end_at" class="form-label">Thời Gian Kết Thúc</label>
                                <input type="datetime-local" name="end_at" id="end_at"
                                       class="form-control @error('end_at') is-invalid @enderror"
                                       value="{{ $formattedEndAt }}" required>
                                @error('end_at')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Cập Nhật Giảm Giá</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function (){
        // Initialize Select2
        $('#product_news_id').select2({
            placeholder: "-- Chọn Sản Phẩm --",
            allowClear: true
        });

        // Không cần khởi tạo Flatpickr nếu dùng input type="datetime-local" vì trình duyệt tự xử lý

        $('#discountForm').validate({
            rules: {
                product_news_id: {
                    required: true,
                },
                start_at: {
                    required: true,
                    // Định dạng và mối quan hệ sẽ được kiểm tra ở backend Laravel
                },
                end_at: {
                    required: true,
                    // Định dạng và mối quan hệ sẽ được kiểm tra ở backend Laravel
                },
                discount_percent: {
                    number: true,
                    min: 0,
                    max: 100,
                    // require_from_group: [1, ".discount-type-group"] // Bỏ comment nếu muốn dùng
                },
                discount_price: {
                    number: true,
                    min: 0,
                    // require_from_group: [1, ".discount-type-group"] // Bỏ comment nếu muốn dùng
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
                    // require_from_group: 'Vui lòng nhập phần trăm giảm giá HOẶC giá giảm cố định.'
                },
                discount_price: {
                    number: 'Giá giảm cố định phải là số.',
                    min: 'Giá giảm cố định không thể âm.',
                    // require_from_group: 'Vui lòng nhập phần trăm giảm giá HOẶC giá giảm cố định.'
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            errorPlacement: function (error, element) {
                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    // Chỉnh lại errorPlacement để chèn sau input, khớp với cấu trúc mới
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
                    $('<div class="invalid-feedback d-block" data-for="discount_type">Chỉ được phép nhập một trong hai: phần trăm giảm giá HOẶC giá giảm cố định.</div>').insertAfter($('#discount_price'));
                    return false;
                } else {
                    $('#discount_percent').removeClass('is-invalid');
                    $('#discount_price').removeClass('is-invalid');
                    $('div.invalid-feedback[data-for="discount_type"]').remove();
                }

                // --- LOGIC KIỂM TRA THỜI GIAN KẾT THÚC > THỜI GIAN BẮT ĐẦU Ở CLIENT-SIDE ---
                var startDateTimeValue = $('#start_at').val();
                var endDateTimeValue = $('#end_at').val();

                // Chuyển đổi thành đối tượng Date để so sánh
                // Lưu ý: Giá trị từ input type="datetime-local" có thể không tương thích hoàn toàn
                // với Date() constructor ở một số trình duyệt cũ hoặc khi chuỗi không đầy đủ.
                // Tuy nhiên, đối với định dạng YYYY-MM-DDTHH:MM, nó hoạt động tốt trên các trình duyệt hiện đại.
                var startDateTime = new Date(startDateTimeValue);
                var endDateTime = new Date(endDateTimeValue);

                if (endDateTime <= startDateTime) {
                    $('#end_at').addClass('is-invalid');
                    $('div.invalid-feedback[data-for="end_at_after"]').remove(); // Xóa lỗi cũ nếu có
                    $('<div class="invalid-feedback d-block" data-for="end_at_after">Thời gian kết thúc phải sau thời gian bắt đầu.</div>').insertAfter($('#end_at'));
                    return false; // Ngăn chặn form submit
                } else {
                    $('#end_at').removeClass('is-invalid');
                    $('div.invalid-feedback[data-for="end_at_after"]').remove(); // Xóa lỗi nếu hợp lệ
                }
                // --- KẾT THÚC LOGIC KIỂM TRA ---

                // Nếu tất cả các kiểm tra đều qua, submit form
                form.submit();
            }
        });

        // Thêm class group cho các trường giảm giá để jQuery Validate có thể dùng require_from_group (nếu bạn kích hoạt lại)
        $('#discount_percent, #discount_price').addClass('discount-type-group');

        // Bỏ comment dòng này nếu bạn muốn sử dụng Flatpickr cho các trường datetime-local
        // flatpickr("#start_at, #end_at", {
        //     enableTime: true,
        //     dateFormat: "Y-m-d H:i", // Hoặc "Y-m-d\TH:i" nếu bạn muốn hiển thị chữ T
        //     locale: "vn",
        //     allowInput: true,
        // });
    });
</script>

@endsection