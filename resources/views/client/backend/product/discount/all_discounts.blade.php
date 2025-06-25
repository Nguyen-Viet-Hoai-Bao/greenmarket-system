@extends('client.client_dashboard')
@section('client')

{{-- Import SweetAlert2 CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Quản Lý Giảm Giá</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Sản phẩm</a></li>
                            <li class="breadcrumb-item active">Giảm giá</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Danh Sách Các Đợt Giảm Giá</h4>
                        <p class="card-title-desc">Quản lý các đợt giảm giá áp dụng cho toàn bộ sản phẩm.</p>

                        <a href="{{ route('product.discounts.add') }}" class="btn btn-primary waves-effect waves-light mb-3">
                            <i class="fas fa-plus"></i> Thêm Đợt Giảm Giá Mới
                        </a>

                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Phần Trăm Giảm Giá (%)</th>
                                        <th>Giá Giảm Cố Định (VNĐ)</th>
                                        <th>Thời Gian Bắt Đầu</th>
                                        <th>Thời Gian Kết Thúc</th>
                                        <th>Trạng Thái</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($discounts as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->productNew->productTemplate->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($item->discount_percent)
                                                    {{ $item->discount_percent }}%
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->discount_price)
                                                    {{ number_format($item->discount_price, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->start_at)->format('d-m-Y H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->end_at)->format('d-m-Y H:i') }}</td>
                                            <td>
                                                @if (\Carbon\Carbon::now()->between($item->start_at, $item->end_at))
                                                    <span class="badge bg-success">Đang hoạt động</span>
                                                @elseif (\Carbon\Carbon::now()->greaterThan($item->end_at))
                                                    <span class="badge bg-danger">Đã kết thúc</span>
                                                @else
                                                    <span class="badge bg-warning">Sắp diễn ra</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('product.discounts.edit', $item->id) }}" class="btn btn-info btn-sm" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('product.discounts.delete', $item->id) }}" class="btn btn-danger btn-sm" id="delete" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
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

{{-- Datatables JS --}}
<script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/pages/datatables.init.js') }}"></script>

{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

<script type="text/javascript">
    $(document).ready(function (){
        // Khởi tạo Datatables nếu chưa có
        if (!$.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable();
        }

        // Xử lý sự kiện click nút xóa
        $(document).on('click', '#delete', function(e){
            e.preventDefault();
            var link = $(this).attr("href");

            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Đợt giảm giá này sẽ bị xóa vĩnh viễn!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý xóa!',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            });
        });
    });
</script>

@endsection