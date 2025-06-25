@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Nhập Kho Sản Phẩm</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Bảng thống kê</a></li>
                            <li class="breadcrumb-item active">Nhập Kho Sản Phẩm</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <form id="stockEntryForm" action="{{ route('product.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tên Menu</label>
                                <select class="form-select" id="menuSelect" name="menu_id">
                                    <option selected="" disabled>Chọn</option>
                                    @foreach ($menus as $men)
                                        <option value="{{ $men->id }}">{{ $men->menu_name }}</option>
                                    @endforeach
                                </select>
                                @error('menu_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Danh Mục</label>
                                <select class="form-select" name="category_id" id="categorySelect">
                                    <option selected="" disabled>Chọn danh mục</option>
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mẫu Sản Phẩm</label>
                                <select class="form-select" name="product_template_id" id="productTemplateSelect">
                                    <option selected disabled>Chọn mẫu sản phẩm</option>
                                </select>
                                @error('product_template_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <img id="showImage" src="{{ url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}" alt="" class="rounded p-1 bg-primary" width="110">
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Danh Mục:</label>
                                <div id="categoryLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Kích Cỡ:</label>
                                <div id="sizeLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Đơn Vị:</label>
                                <div id="unitLabel" class="form-control" readonly>-</div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Chế độ kho:</label>
                                <div id="stockModeLabel" class="form-control" readonly>-</div>
                                {{-- Input ẩn để gửi stock_mode đến controller --}}
                                <input type="hidden" name="current_stock_mode" id="currentStockModeInput">
                            </div>
                        </div>

                        <hr class="mt-4 mb-4">

                        {{-- Phần nhập liệu động --}}
                        <div id="quantityModeFields" style="display: none;">
                            <h5 class="mb-3">Thông tin nhập kho (Theo Số Lượng)</h5>
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Số Lượng Nhập (batch_qty)</label>
                                        <input class="form-control @error('batch_qty') is-invalid @enderror" type="number" name="batch_qty" placeholder="Số lượng" value="{{ old('batch_qty') }}">
                                        @error('batch_qty')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Giá Nhập</label>
                                        <input class="form-control @error('cost_price_quantity') is-invalid @enderror" type="text" name="cost_price_quantity" placeholder="Giá nhập" value="{{ old('cost_price_quantity') }}">
                                        @error('cost_price_quantity')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Hạn Sử Dụng</label>
                                        <input type="date" class="form-control @error('expiry_date_quantity') is-invalid @enderror" name="expiry_date_quantity" value="{{ old('expiry_date_quantity') }}" autocomplete="off"/>
                                        @error('expiry_date_quantity')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Số Ngày Sử Dụng</label>
                                        <input type="number" class="form-control @error('shelf_life_days') is-invalid @enderror" name="shelf_life_days" value="{{ old('shelf_life_days') }}" autocomplete="off"/>
                                        @error('shelf_life_days')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div> {{-- End quantityModeFields --}}

                        <div id="unitModeFields" style="display: none;">
                            <h5 class="mb-3">Thông tin nhập kho (Theo Từng Khay/Đơn Vị)</h5>
                            <button type="button" class="btn btn-primary btn-sm mb-3" id="addUnitRow">Thêm khay/đơn vị</button>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="unitTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cân nặng (kg/..)</th>
                                            <th>Giá Nhập</th>
                                            <th>Hạn Sử Dụng</th>
                                            <th>Số Ngày Sử Dụng</th>
                                            <th>Hành Động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Dòng mẫu, sẽ được thêm bằng JS --}}
                                        {{-- <tr>
                                            <td>1</td>
                                            <td><input type="number" name="units[0][weight]" class="form-control" step="0.01" min="0.01"></td>
                                            <td><input type="text" name="units[0][cost_price_unit]" class="form-control"></td>
                                            <td><input type="text" name="units[0][expiry_date_unit]" class="form-control datepicker"></td>
                                            <td><input type="text" name="units[0][shelf_life_days]" class="form-control"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm removeUnitRow">Xóa</button></td>
                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>
                             @error('units.*.weight')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('units.*.cost_price_unit')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('units.*.expiry_date_unit')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div> {{-- End unitModeFields --}}
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Lưu Nhập Kho</button>
                    </div>

                </form>
            </div>
        </div>
        </div>
</div>

<script>
    // JSON data từ controller
    const productTemplatesGrouped = @json($productTemplatesGrouped);
    const productStockModes = @json($productStockModes);

    const menuSelect = document.getElementById('menuSelect');
    const categorySelect = document.getElementById('categorySelect');
    const productTemplateSelect = document.getElementById('productTemplateSelect');
    const imagePreview = document.getElementById('showImage');
    const categoryLabel = document.getElementById('categoryLabel');
    const sizeLabel = document.getElementById('sizeLabel');
    const unitLabel = document.getElementById('unitLabel');
    const stockModeLabel = document.getElementById('stockModeLabel');
    const currentStockModeInput = document.getElementById('currentStockModeInput');

    const quantityModeFields = document.getElementById('quantityModeFields');
    const unitModeFields = document.getElementById('unitModeFields');
    const unitTableBody = document.querySelector('#unitTable tbody');
    const addUnitRowBtn = document.getElementById('addUnitRow');

    let currentSelectedProductTemplate = null; // Biến lưu trữ product template hiện tại

    // Lắng nghe sự kiện thay đổi Menu
    menuSelect.addEventListener('change', function () {
        const selectedMenuId = this.value;
        
        // Reset Category và Product Template
        categorySelect.innerHTML = '<option selected disabled>Chọn danh mục</option>';
        productTemplateSelect.innerHTML = '<option selected disabled>Chọn mẫu sản phẩm</option>';
        resetProductDetails(); // Reset các label và ẩn/hiện form nhập liệu

        const categories = @json($categories);
        const filteredCategories = categories.filter(category => category.menu_id == selectedMenuId);

        filteredCategories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.category_name;
            categorySelect.appendChild(option);
        });
    });

    // Lắng nghe sự kiện thay đổi Category
    categorySelect.addEventListener('change', function () {
        const selectedCategoryId = this.value;
        const selectedMenuId = menuSelect.value; 

        // Reset Product Template
        productTemplateSelect.innerHTML = '<option selected disabled>Chọn mẫu sản phẩm</option>';
        resetProductDetails(); // Reset các label và ẩn/hiện form nhập liệu

        const filteredProducts = (productTemplatesGrouped[selectedMenuId] || []).filter(product => product.category_id == selectedCategoryId);
        
        filteredProducts.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.name;
            productTemplateSelect.appendChild(option);
        });
    });

    // Lắng nghe sự kiện thay đổi Product Template
    productTemplateSelect.addEventListener('change', function () {
        const selectedId = parseInt(this.value);
        const selectedMenuId = menuSelect.value;
        const selectedCategoryId = categorySelect.value;

        // Tìm product template từ productTemplatesGrouped
        currentSelectedProductTemplate = (productTemplatesGrouped[selectedMenuId] || [])
            .find(t => t.id === selectedId && t.category_id == selectedCategoryId);

        updateProductDetails(currentSelectedProductTemplate);
    });

    function updateProductDetails(template) {
        if (template) {
            imagePreview.src = template.image ? '{{ url('/') }}/' + template.image : '{{ url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}';
            categoryLabel.textContent = template.category ? template.category.category_name : '-';
            sizeLabel.textContent = template.size || '-';
            unitLabel.textContent = template.unit || '-';
            stockModeLabel.textContent = (template.stock_mode === 'quantity' ? 'Theo Số Lượng' : (template.stock_mode === 'unit' ? 'Theo Đơn Vị' : '-'));
            currentStockModeInput.value = template.stock_mode || '';

            // Ẩn/hiện các trường nhập liệu dựa trên stock_mode
            if (template.stock_mode === 'quantity') {
                quantityModeFields.style.display = 'block';
                unitModeFields.style.display = 'none';
                unitTableBody.innerHTML = ''; // Xóa các dòng cũ
            } else if (template.stock_mode === 'unit') {
                quantityModeFields.style.display = 'none';
                unitModeFields.style.display = 'block';
                // Đảm bảo có ít nhất một dòng khi chuyển sang unit mode
                if (unitTableBody.rows.length === 0) {
                    addUnitRow();
                } else {
                    initializeDatepickersForUnits(); // Khởi tạo lại datepicker nếu đã có dòng
                }
            } else {
                quantityModeFields.style.display = 'none';
                unitModeFields.style.display = 'none';
                unitTableBody.innerHTML = '';
            }
        } else {
            resetProductDetails();
        }
    }

    function resetProductDetails() {
        imagePreview.src = '{{ url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}';
        categoryLabel.textContent = '-';
        sizeLabel.textContent = '-';
        unitLabel.textContent = '-';
        stockModeLabel.textContent = '-';
        currentStockModeInput.value = '';

        quantityModeFields.style.display = 'none';
        unitModeFields.style.display = 'none';
        unitTableBody.innerHTML = ''; // Clear rows
        currentSelectedProductTemplate = null;
    }

    // --- Logic cho Unit Mode (Thêm/Xóa dòng) ---
    let unitRowCounter = 0; // Để tạo tên input độc đáo

    addUnitRowBtn.addEventListener('click', addUnitRow);

    function addUnitRow() {
        const newRow = unitTableBody.insertRow();
        newRow.innerHTML = `
            <td>${unitRowCounter + 1}</td>
            <td>
                <input type="number" name="units[${unitRowCounter}][weight]" class="form-control @error('units.*.weight') is-invalid @enderror" step="0.001" min="0.001" value="{{ old('units.${unitRowCounter}.weight') }}" required>
                @error('units.*.weight')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </td>
            <td>
                <input type="text" name="units[${unitRowCounter}][cost_price_unit]" class="form-control @error('units.*.cost_price_unit') is-invalid @enderror" value="{{ old('units.${unitRowCounter}.cost_price_unit') }}" required>
                @error('units.*.cost_price_unit')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </td>
            <td>
                <input type="date" name="units[${unitRowCounter}][expiry_date_unit]" class="form-control @error('units.*.expiry_date_unit') is-invalid @enderror" value="{{ old('units.${unitRowCounter}.expiry_date_unit') }}" autocomplete="off"/>
                @error('units.*.expiry_date_unit')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </td>
            <td>
                <input type="number" name="units[${unitRowCounter}][shelf_life_days]" class="form-control @error('units.*.shelf_life_days') is-invalid @enderror" value="{{ old('units.${unitRowCounter}.shelf_life_days') }}" required>
                @error('units.*.shelf_life_days')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeUnitRow">Xóa</button>
            </td>
        `;
        unitRowCounter++;
        initializeDatepickersForUnits(); // Khởi tạo datepicker cho dòng mới
    }

    // Xử lý sự kiện click trên nút "Xóa"
    unitTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('removeUnitRow')) {
            if (unitTableBody.rows.length > 1) { // Đảm bảo luôn có ít nhất 1 dòng
                e.target.closest('tr').remove();
                updateUnitRowNumbers();
            } else {
                alert('Phải có ít nhất một khay/đơn vị.');
            }
        }
    });

    function updateUnitRowNumbers() {
        const rows = unitTableBody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1; // Cập nhật số thứ tự
            // Cập nhật lại name attribute của input để giữ đúng index
            row.querySelectorAll('input').forEach(input => {
                const name = input.name;
                const newName = name.replace(/units\[\d+\]/, `units[${index}]`);
                input.name = newName;
            });
            // Cập nhật lại id của datepicker
            row.querySelector('.input-group.date').id = `datepickerUnit${index}`;
            row.querySelector('.datetimepicker-input').dataset.target = `#datepickerUnit${index}`;
            row.querySelector('.input-group-append').dataset.target = `#datepickerUnit${index}`;
        });
        unitRowCounter = rows.length; // Cập nhật lại counter
    }

    // --- Validation (sử dụng jQuery Validate) ---
    $(document).ready(function () {
        $('#stockEntryForm').validate({
            rules: {
                product_template_id: { required: true },
                // Rules for quantity mode
                batch_qty: { 
                    required: function(element) {
                        return currentSelectedProductTemplate && currentSelectedProductTemplate.stock_mode === 'quantity';
                    }, 
                    min: 1 
                },
                cost_price_quantity: {
                    required: function(element) {
                        return currentSelectedProductTemplate && currentSelectedProductTemplate.stock_mode === 'quantity';
                    },
                    min: 0
                },
                expiry_date_quantity: { dateISO: true }, // Kiểm tra định dạng ngày YYYY-MM-DD
            },
            messages: {
                product_template_id: { required: 'Vui lòng chọn mẫu sản phẩm.' },
                batch_qty: { 
                    required: 'Vui lòng nhập số lượng nhập.', 
                    min: 'Số lượng phải lớn hơn 0.' 
                },
                cost_price_quantity: {
                    required: 'Vui lòng nhập giá nhập.',
                    min: 'Giá nhập không thể âm.'
                },
                expiry_date_quantity: { dateISO: 'Hạn sử dụng không đúng định dạng (YYYY-MM-DD).' },
            },
            errorElement: 'div', // Đổi errorElement từ span sang div
            errorClass: 'invalid-feedback d-block', // Thêm d-block để hiển thị lỗi ngay lập tức
            errorPlacement: function (error, element) {
                if (element.hasClass('select2-hidden-accessible')) { // Đối với select2 (nếu có)
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.attr("name").includes("units")) {
                    error.insertAfter(element); // Đặt lỗi ngay sau input trong bảng
                } else if (element.closest('.input-group.date').length) {
                    error.insertAfter(element.closest('.input-group.date')); // Cho datepicker
                }
                 else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            // Logic validation cho các trường động (unit mode)
            // Cần một hàm custom validator hoặc xử lý thủ công cho các input mảng
            submitHandler: function(form) {
                // Thêm validation tùy chỉnh cho unit mode
                if (currentSelectedProductTemplate && currentSelectedProductTemplate.stock_mode === 'unit') {
                    let isValidUnits = true;
                    $('#unitTable tbody tr').each(function() {
                        const weightInput = $(this).find('input[name$="[weight]"]');
                        const costPriceInput = $(this).find('input[name$="[cost_price_unit]"]');
                        const expiryDateInput = $(this).find('input[name$="[expiry_date_unit]"]');

                        // Xóa các lỗi cũ
                        weightInput.next('.invalid-feedback').remove();
                        costPriceInput.next('.invalid-feedback').remove();
                        expiryDateInput.closest('.input-group.date').next('.invalid-feedback').remove();
                        
                        weightInput.removeClass('is-invalid');
                        costPriceInput.removeClass('is-invalid');
                        expiryDateInput.removeClass('is-invalid');


                        if (!weightInput.val() || parseFloat(weightInput.val()) <= 0) {
                            weightInput.addClass('is-invalid').after('<div class="invalid-feedback d-block">Cân nặng phải lớn hơn 0.</div>');
                            isValidUnits = false;
                        }
                        if (!costPriceInput.val() || parseFloat(costPriceInput.val()) < 0) {
                            costPriceInput.addClass('is-invalid').after('<div class="invalid-feedback d-block">Giá nhập không thể âm.</div>');
                            isValidUnits = false;
                        }
                        if (expiryDateInput.val() && !/^\d{4}-\d{2}-\d{2}$/.test(expiryDateInput.val())) {
                            expiryDateInput.addClass('is-invalid').after('<div class="invalid-feedback d-block">Hạn sử dụng không đúng định dạng (YYYY-MM-DD).</div>');
                            isValidUnits = false;
                        }
                    });

                    if (!isValidUnits) {
                        return false; // Ngăn chặn form submit nếu có lỗi
                    }
                }
                form.submit();
            }
        });
    });

</script>

@endsection