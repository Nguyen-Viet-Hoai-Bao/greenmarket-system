@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

{{-- Thêm thư viện jQuery Validate --}}
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>

{{-- Thêm thư viện Datepicker (Nếu chưa có) --}}
{{-- CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
{{-- JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

{{-- Thêm thư viện Select2 (Nếu chưa có và bạn muốn dùng) --}}
{{-- CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- JS (sau jQuery) --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


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
                                <select class="form-select select2 @error('menu_id') is-invalid @enderror" id="menuSelect" name="menu_id">
                                    <option value="" selected disabled>Chọn</option>
                                    @foreach ($menus as $men)
                                        <option value="{{ $men->id }}" {{ old('menu_id') == $men->id ? 'selected' : '' }}>{{ $men->menu_name }}</option>
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
                                <select class="form-select select2 @error('category_id') is-invalid @enderror" name="category_id" id="categorySelect">
                                    <option value="" selected disabled>Chọn danh mục</option>
                                    {{-- Categories sẽ được load bằng JS --}}
                                    @if(old('menu_id'))
                                        @php
                                            $selectedMenuId = old('menu_id');
                                            $filteredCategories = $categories->where('menu_id', $selectedMenuId);
                                        @endphp
                                        @foreach ($filteredCategories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mẫu Sản Phẩm</label>
                                <select class="form-select select2 @error('product_template_id') is-invalid @enderror" name="product_template_id" id="productTemplateSelect">
                                    <option value="" selected disabled>Chọn mẫu sản phẩm</option>
                                    {{-- Product Templates sẽ được load bằng JS --}}
                                    @if(old('category_id') && old('menu_id'))
                                        @php
                                            $selectedMenuId = old('menu_id');
                                            $selectedCategoryId = old('category_id');
                                            $filteredTemplates = ($productTemplatesGrouped[$selectedMenuId] ?? collect())->where('category_id', $selectedCategoryId);
                                        @endphp
                                        @foreach ($filteredTemplates as $template)
                                            <option value="{{ $template->id }}" {{ old('product_template_id') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('product_template_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <img id="showImage" src="{{ old('product_template_id') ? asset($productTemplates->firstWhere('id', old('product_template_id'))->image ?? 'https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}" alt="" class="rounded p-1 bg-primary" width="110">
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Danh Mục:</label>
                                <div id="categoryLabel" class="form-control" readonly>
                                    @if(old('product_template_id'))
                                        {{ $productTemplates->firstWhere('id', old('product_template_id'))->category->category_name ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Kích Cỡ:</label>
                                <div id="sizeLabel" class="form-control" readonly>
                                    @if(old('product_template_id'))
                                        {{ $productTemplates->firstWhere('id', old('product_template_id'))->size ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Đơn Vị:</label>
                                <div id="unitLabel" class="form-control" readonly>
                                    @if(old('product_template_id'))
                                        {{ $productTemplates->firstWhere('id', old('product_template_id'))->unit ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Chế độ kho:</label>
                                <div id="stockModeLabel" class="form-control" readonly>
                                    @if(old('product_template_id'))
                                        @php
                                            $selectedTemplate = $productTemplates->firstWhere('id', old('product_template_id'));
                                        @endphp
                                        {{ $selectedTemplate->stock_mode === 'quantity' ? 'Theo Số Lượng' : ($selectedTemplate->stock_mode === 'unit' ? 'Theo Đơn Vị' : '-') }}
                                    @else
                                        -
                                    @endif
                                </div>
                                {{-- Input ẩn để gửi stock_mode đến controller (Controller đã tìm ProductTemplate để lấy mode, nên input này có thể không cần thiết nếu logic ổn định) --}}
                                <input type="hidden" name="current_stock_mode" id="currentStockModeInput" value="{{ old('current_stock_mode') }}">
                            </div>
                        </div>

                        <hr class="mt-4 mb-4">

                        {{-- Phần nhập liệu động --}}
                        <div id="quantityModeFields" style="{{ (old('current_stock_mode') === 'quantity') ? 'display: block;' : 'display: none;' }}">
                            <h5 class="mb-3">Thông tin nhập kho (Theo Số Lượng)</h5>
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Số Lượng Nhập (batch_qty)</label>
                                        <input class="form-control @error('batch_qty') is-invalid @enderror" type="number" name="batch_qty" placeholder="Số lượng" value="{{ old('batch_qty') }}" min="1">
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
                                        <label class="form-label">Số Ngày Hạn Sử Dụng Tổng Cộng (ban đầu)</label>
                                        <input class="form-control @error('shelf_life_days') is-invalid @enderror" type="number" name="shelf_life_days" placeholder="VD: 365" value="{{ old('shelf_life_days') }}" min="0">
                                        @error('shelf_life_days')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div> {{-- End quantityModeFields --}}

                        <div id="unitModeFields" style="{{ (old('current_stock_mode') === 'unit') ? 'display: block;' : 'display: none;' }}">
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
                                            <th>Số Ngày Sử Dụng (Tổng)</th>
                                            <th>Hành Động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Dòng mẫu, sẽ được thêm bằng JS hoặc load từ old input --}}
                                        @if(old('units'))
                                            @foreach(old('units') as $key => $unit)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>
                                                        <input type="number" name="units[{{ $key }}][weight]" class="form-control @error('units.' . $key . '.weight') is-invalid @enderror" step="0.001" min="0.001" value="{{ $unit['weight'] ?? '' }}" required>
                                                        @error('units.' . $key . '.weight')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="units[{{ $key }}][cost_price_unit]" class="form-control @error('units.' . $key . '.cost_price_unit') is-invalid @enderror" value="{{ $unit['cost_price_unit'] ?? '' }}" required>
                                                        @error('units.' . $key . '.cost_price_unit')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="date" name="units[{{ $key }}][expiry_date_unit]" class="form-control @error('units.' . $key . '.expiry_date_unit') is-invalid @enderror" value="{{ $unit['expiry_date_unit'] ?? '' }}" autocomplete="off"/>
                                                        @error('units.' . $key . '.expiry_date_unit')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="units[{{ $key }}][shelf_life_days]" class="form-control @error('units.' . $key . '.shelf_life_days') is-invalid @enderror" value="{{ $unit['shelf_life_days'] ?? '' }}" min="0">
                                                        @error('units.' . $key . '.shelf_life_days')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm removeUnitRow">Xóa</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
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
    // productTemplatesGrouped sẽ là một object mà key là menu_id, value là mảng các product templates
    // Categories cũng cần được truyền nguyên mảng để lọc
    const productTemplatesGrouped = @json($productTemplatesGrouped);
    const allCategories = @json($categories); // Đổi tên biến để tránh nhầm lẫn

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

    // Initialize Select2
    $(document).ready(function() {
        $('.select2').select2();

        // Khởi tạo datepicker cho các trường hiện có (nếu có lỗi validation và old input)
        initializeDatepickersForInputs();
        initializeDatepickersForUnits();

        // Xử lý khi có old input được chọn sẵn
        if (menuSelect.value) {
            populateCategories(menuSelect.value, '{{ old('category_id') }}');
            if (categorySelect.value) {
                populateProductTemplates(menuSelect.value, categorySelect.value, '{{ old('product_template_id') }}');
                if (productTemplateSelect.value) {
                    const selectedId = parseInt(productTemplateSelect.value);
                    const selectedMenuId = menuSelect.value;
                    const selectedCategoryId = categorySelect.value;
                    currentSelectedProductTemplate = (productTemplatesGrouped[selectedMenuId] || [])
                        .find(t => t.id === selectedId && t.category_id == selectedCategoryId);
                    updateProductDetails(currentSelectedProductTemplate);
                }
            }
        }
    });

    // Hàm chung để khởi tạo datepicker
    function initializeDatepickersForInputs() {
        // Cho quantity mode
        $('input[name="expiry_date_quantity"]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom auto"
        });
    }

    function initializeDatepickersForUnits() {
        // Cho unit mode, các trường trong bảng
        $('#unitTable tbody input[name$="[expiry_date_unit]"]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom auto"
        });
    }


    // Lắng nghe sự kiện thay đổi Menu
    menuSelect.addEventListener('change', function () {
        const selectedMenuId = this.value;
        populateCategories(selectedMenuId);
        // Reset Product Template và thông tin chi tiết
        productTemplateSelect.innerHTML = '<option value="" selected disabled>Chọn mẫu sản phẩm</option>';
        resetProductDetails();
    });

    function populateCategories(menuId, selectedCategoryId = null) {
        categorySelect.innerHTML = '<option value="" selected disabled>Chọn danh mục</option>';
        const filteredCategories = allCategories.filter(category => category.menu_id == menuId);

        filteredCategories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.category_name;
            if (selectedCategoryId && category.id == selectedCategoryId) {
                option.selected = true;
            }
            categorySelect.appendChild(option);
        });
        $(categorySelect).trigger('change.select2'); // Cập nhật Select2
    }

    // Lắng nghe sự kiện thay đổi Category
    categorySelect.addEventListener('change', function () {
        const selectedCategoryId = this.value;
        const selectedMenuId = menuSelect.value;
        populateProductTemplates(selectedMenuId, selectedCategoryId);
        resetProductDetails(); // Reset thông tin chi tiết
    });

    function populateProductTemplates(menuId, categoryId, selectedProductTemplateId = null) {
        productTemplateSelect.innerHTML = '<option value="" selected disabled>Chọn mẫu sản phẩm</option>';
        const filteredProducts = (productTemplatesGrouped[menuId] || []).filter(product => product.category_id == categoryId);

        filteredProducts.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.name;
            if (selectedProductTemplateId && product.id == selectedProductTemplateId) {
                option.selected = true;
            }
            option.setAttribute('data-stock-mode', product.stock_mode); // Lưu stock_mode vào data attribute
            option.setAttribute('data-category-name', product.category ? product.category.category_name : '-');
            option.setAttribute('data-size', product.size || '-');
            option.setAttribute('data-unit', product.unit || '-');
            option.setAttribute('data-image', product.image || ''); // Lưu đường dẫn ảnh
            productTemplateSelect.appendChild(option);
        });
        $(productTemplateSelect).trigger('change.select2'); // Cập nhật Select2
    }


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
            imagePreview.src = template.image ? `{{ url('/') }}/${template.image}` : '{{ url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}';
            categoryLabel.textContent = template.category ? template.category.category_name : '-';
            sizeLabel.textContent = template.size || '-';
            unitLabel.textContent = template.unit || '-';
            stockModeLabel.textContent = (template.stock_mode === 'quantity' ? 'Theo Số Lượng' : (template.stock_mode === 'unit' ? 'Theo Đơn Vị' : '-'));
            currentStockModeInput.value = template.stock_mode || '';

            // Ẩn/hiện các trường nhập liệu dựa trên stock_mode
            if (template.stock_mode === 'quantity') {
                quantityModeFields.style.display = 'block';
                unitModeFields.style.display = 'none';
                unitTableBody.innerHTML = ''; // Xóa các dòng cũ khi chuyển sang quantity mode
                // Clear validation messages for unit mode
                $('#unitTable tbody .invalid-feedback').remove();
                $('#unitTable tbody input').removeClass('is-invalid');
            } else if (template.stock_mode === 'unit') {
                quantityModeFields.style.display = 'none';
                unitModeFields.style.display = 'block';
                // Đảm bảo có ít nhất một dòng khi chuyển sang unit mode và chưa có dòng nào
                if (unitTableBody.rows.length === 0) {
                    addUnitRow();
                } else {
                    initializeDatepickersForUnits(); // Khởi tạo lại datepicker nếu đã có dòng (cho old input)
                }
                // Clear validation messages for quantity mode
                $('#quantityModeFields .invalid-feedback').remove();
                $('#quantityModeFields input').removeClass('is-invalid');

            } else {
                // Nếu stock_mode không xác định hoặc không có template
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

        // Clear input values when mode changes
        $('#quantityModeFields input').val('');
        $('#unitTable input').val('');

        // Clear validation messages
        $('.invalid-feedback').remove();
        $('input, select').removeClass('is-invalid is-valid');
    }

    // --- Logic cho Unit Mode (Thêm/Xóa dòng) ---
    let unitRowCounter = {{ old('units') ? count(old('units')) : 0 }}; // Bắt đầu counter từ số lượng old input nếu có

    // Nếu có old input, đảm bảo unitRowCounter được set đúng
    if (unitRowCounter > 0) {
        // Cập nhật lại unitRowCounter để tránh trùng lặp index khi thêm dòng mới
        // Sau khi reload trang với old input, các index của old input đã tồn tại.
        // Cần tìm max index và tăng lên 1
        let maxExistingIndex = -1;
        $('#unitTable tbody tr').each(function() {
            const nameAttr = $(this).find('input[name$="[weight]"]').attr('name');
            const match = nameAttr.match(/units\[(\d+)\]/);
            if (match && match[1]) {
                const index = parseInt(match[1]);
                if (index > maxExistingIndex) {
                    maxExistingIndex = index;
                }
            }
        });
        unitRowCounter = maxExistingIndex + 1;
    }


    addUnitRowBtn.addEventListener('click', addUnitRow);

    function addUnitRow() {
        const newRow = unitTableBody.insertRow();
        newRow.innerHTML = `
            <td>${unitTableBody.rows.length + 1}</td>
            <td>
                <input type="number" name="units[${unitRowCounter}][weight]" class="form-control" step="0.001" min="0.001" required>
            </td>
            <td>
                <input type="text" name="units[${unitRowCounter}][cost_price_unit]" class="form-control" required>
            </td>
            <td>
                <input type="date" name="units[${unitRowCounter}][expiry_date_unit]" class="form-control" autocomplete="off"/>
            </td>
            <td>
                <input type="number" name="units[${unitRowCounter}][shelf_life_days]" class="form-control" min="0">
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
            // Cập nhật lại name attribute của input để giữ đúng index trong mảng
            row.querySelectorAll('input').forEach(input => {
                const name = input.name;
                // Thay thế index cũ bằng index mới trong tên input (vd: units[0][weight] -> units[1][weight])
                const newName = name.replace(/units\[\d+\]/, `units[${index}]`);
                input.name = newName;

                // Đồng thời xóa các lớp invalid và thông báo lỗi cũ nếu có
                $(input).removeClass('is-invalid');
                $(input).next('.invalid-feedback').remove();
            });
            // Re-initialize datepickers if they are bound to IDs or complex selectors
            // Simpler approach here is to ensure they are initialized on new rows
        });
        unitRowCounter = rows.length; // Cập nhật lại counter
    }

    // --- Validation (sử dụng jQuery Validate) ---
    $(document).ready(function () {
        $('#stockEntryForm').validate({
            rules: {
                menu_id: { required: true },
                category_id: { required: true },
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
                    number: true, // Đảm bảo là số
                    min: 0
                },
                expiry_date_quantity: {
                    dateISO: true // Kiểm tra định dạng ngày YYYY-MM-DD
                },
                shelf_life_days: {
                    number: true,
                    min: 0
                }
            },
            messages: {
                menu_id: { required: 'Vui lòng chọn menu.' },
                category_id: { required: 'Vui lòng chọn danh mục.' },
                product_template_id: { required: 'Vui lòng chọn mẫu sản phẩm.' },
                batch_qty: {
                    required: 'Vui lòng nhập số lượng nhập.',
                    min: 'Số lượng phải lớn hơn hoặc bằng 1.'
                },
                cost_price_quantity: {
                    required: 'Vui lòng nhập giá nhập.',
                    number: 'Giá nhập phải là số.',
                    min: 'Giá nhập không thể âm.'
                },
                expiry_date_quantity: {
                    dateISO: 'Hạn sử dụng không đúng định dạng (YYYY-MM-DD).'
                },
                shelf_life_days: {
                    number: 'Số ngày hạn sử dụng phải là số.',
                    min: 'Số ngày hạn sử dụng không thể âm.'
                }
            },
            errorElement: 'div', // Đổi errorElement từ span sang div
            errorClass: 'invalid-feedback d-block', // Thêm d-block để hiển thị lỗi ngay lập tức
            errorPlacement: function (error, element) {
                if (element.hasClass('select2-hidden-accessible')) { // Đối với select2
                    error.insertAfter(element.next('.select2-container'));
                } else if (element.attr("name").startsWith("units[")) {
                    error.insertAfter(element); // Đặt lỗi ngay sau input trong bảng
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            // Custom submit handler để xử lý validation cho các trường động trong unit mode
            submitHandler: function(form) {
                let isValid = true;

                // Validate unit mode fields manually
                if (currentSelectedProductTemplate && currentSelectedProductTemplate.stock_mode === 'unit') {
                    if (unitTableBody.rows.length === 0) {
                        alert('Vui lòng thêm ít nhất một đơn vị sản phẩm.');
                        isValid = false;
                        return false; // Ngăn chặn submit
                    }

                    $('#unitTable tbody tr').each(function() {
                        const weightInput = $(this).find('input[name$="[weight]"]');
                        const costPriceInput = $(this).find('input[name$="[cost_price_unit]"]');
                        const expiryDateInput = $(this).find('input[name$="[expiry_date_unit]"]');
                        const shelfLifeDaysInput = $(this).find('input[name$="[shelf_life_days]"]');


                        // Clear existing errors
                        weightInput.next('.invalid-feedback').remove();
                        costPriceInput.next('.invalid-feedback').remove();
                        expiryDateInput.next('.invalid-feedback').remove();
                        shelfLifeDaysInput.next('.invalid-feedback').remove();

                        weightInput.removeClass('is-invalid');
                        costPriceInput.removeClass('is-invalid');
                        expiryDateInput.removeClass('is-invalid');
                        shelfLifeDaysInput.removeClass('is-invalid');

                        if (!weightInput.val() || parseFloat(weightInput.val()) <= 0) {
                            weightInput.addClass('is-invalid').after('<div class="invalid-feedback d-block">Cân nặng phải lớn hơn 0.</div>');
                            isValid = false;
                        }
                        if (!costPriceInput.val() || parseFloat(costPriceInput.val()) < 0) {
                            costPriceInput.addClass('is-invalid').after('<div class="invalid-feedback d-block">Giá nhập không thể âm.</div>');
                            isValid = false;
                        }
                        if (expiryDateInput.val() && !/^\d{4}-\d{2}-\d{2}$/.test(expiryDateInput.val())) {
                            expiryDateInput.addClass('is-invalid').after('<div class="invalid-feedback d-block">Hạn sử dụng không đúng định dạng (YYYY-MM-DD).</div>');
                            isValid = false;
                        }
                        if (shelfLifeDaysInput.val() && (isNaN(shelfLifeDaysInput.val()) || parseInt(shelfLifeDaysInput.val()) < 0)) {
                             shelfLifeDaysInput.addClass('is-invalid').after('<div class="invalid-feedback d-block">Số ngày hạn sử dụng phải là số nguyên không âm.</div>');
                            isValid = false;
                        }
                    });
                }

                if (isValid) {
                    form.submit();
                } else {
                    // Nếu có lỗi, cuộn đến lỗi đầu tiên
                    $('html, body').animate({
                        scrollTop: $('.is-invalid:first').offset().top - 100
                    }, 500);
                }
            }
        });
    });

</script>

@endsection