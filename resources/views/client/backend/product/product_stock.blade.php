@extends('client.client_dashboard')
@section('client')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Tồn Kho Sản Phẩm</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Bảng thống kê</a></li>
                            <li class="breadcrumb-item active">Tồn Kho Sản Phẩm</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Danh Sách Tồn Kho</h4>
                        <p class="card-title-desc">Dưới đây là tổng quan về tồn kho sản phẩm hiện tại của bạn, bao gồm giá bán cuối cùng sau khi áp dụng các chương trình giảm giá.</p>

                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Danh Mục</th>
                                        <th>Kích Cỡ</th>
                                        <th>Đơn Vị</th>
                                        <th>Chế Độ Kho</th>
                                        <th>Cân Nặng/Số Lượng</th>
                                        <th>Giá Nhập (TB/Đơn vị)</th>
                                        <th>Giá Bán Gốc</th> {{-- Thêm cột này --}}
                                        <th>Giá Bán Cuối Cùng</th> {{-- Thêm cột này --}}
                                        <th>Hạn Sử Dụng</th>
                                        <th>Số Ngày Tồn Kho</th>
                                        <th>Ngày Nhập Kho</th>
                                        <th>Tác Vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockData as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td>{{ $item['category_name'] }}</td>
                                            <td>{{ $item['size'] }}</td>
                                            <td>{{ $item['unit_name'] }}</td>
                                            <td>
                                                @if ($item['total_quantity'] <= 0)
                                                    <span class="badge bg-danger">Đã bán hết</span>
                                                @else
                                                    <span class="badge {{ $item['stock_mode'] === 'Theo Số Lượng' ? 'bg-info' : 'bg-success' }}">
                                                        {{ $item['stock_mode'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item['stock_mode'] === 'Theo Số Lượng')
                                                    {{ $item['total_quantity'] }} {{ $item['unit_name'] }}
                                                @else
                                                    {{ $item['total_weight'] }} {{ $item['unit_name'] }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($item['average_cost_price'], 0, ',', '.') }} VNĐ</td>
                                            <td>{{ number_format($item['original_sale_price'], 0, ',', '.') }} VNĐ</td> {{-- Hiển thị giá gốc --}}
                                            <td>
                                                @if ($item['final_sale_price'] < $item['original_sale_price'])
                                                    <strong class="text-danger">{{ number_format($item['final_sale_price'], 0, ',', '.') }} VNĐ</strong>
                                                    <br><small class="text-muted">(Đã giảm giá)</small>
                                                @else
                                                    {{ number_format($item['final_sale_price'], 0, ',', '.') }} VNĐ
                                                @endif
                                            </td> {{-- Hiển thị giá cuối cùng --}}
                                            <td>
                                                @if ($item['expiry_date'])
                                                    {{ \Carbon\Carbon::parse($item['expiry_date'])->format('d-m-Y') }}
                                                    @php
                                                        $daysRemaining = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($item['expiry_date'])->startOfDay(), false);
                                                    @endphp
                                                    @if ($daysRemaining < 0)
                                                        <span class="badge bg-danger">Hết hạn</span>
                                                    @elseif ($daysRemaining <= 30)
                                                        <span class="badge bg-warning">Sắp hết hạn ({{ $daysRemaining }} ngày)</span>
                                                    @endif
                                                @else
                                                    Không có
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item['import_date'] != 'N/A')
                                                    @php
                                                        $daysInStock = \Carbon\Carbon::parse($item['import_date'])->startOfDay()
                                                                                ->diffInDays(\Carbon\Carbon::now()->startOfDay(), false);
                                                    @endphp
                                                    {{ max(0, $daysInStock) }} ngày
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $item['import_date'] }}</td>
                                            <td>
                                                @if ($item['type'] === 'individual' && $item['product_unit_id'])
                                                    <a href="{{ route('product.unit.edit', $item['product_unit_id']) }}" class="btn btn-warning btn-sm" title="Sửa đơn vị">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('product.unit.delete', $item['product_unit_id']) }}" class="btn btn-danger btn-sm" id="delete" title="Xóa đơn vị">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

<script src="{{ asset('backend/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

<script src="{{ asset('backend/assets/js/pages/datatables.init.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function (){
        $('#datatable').DataTable();
    });
</script>

@endsection