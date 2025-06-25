@extends('admin.admin_dashboard')
@section('admin')

<!-- jQuery & Bootstrap Toggle -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<style>
    .status-select {
        padding: 6px 12px;
        font-weight: bold;
        color: white;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        outline: none;
        transition: background-color 0.3s ease;
    }
    .status-pheduyet {
        background-color: #28a745 !important; /* xanh */
    }
    .status-khongduyet {
        background-color: #dc3545 !important; /* đỏ */
    }
    .status-default {
        background-color: #3ecf8e  !important; /* xám nhạt */
    }
</style>

<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Cửa hàng chờ duyệt</h4>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <table id="pending-table" class="table table-bordered dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên Cửa Hàng</th>
                                    <th>Email</th>
                                    <th>Điện thoại</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($client as $key => $item)
                                    <tr id="row-{{ $item->id }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <img src="{{ !empty($item->photo) 
                                                        ? url($item->photo) 
                                                        : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}"
                                                 style="width: 70px; height: 40px;">
                                        </td>
                                        <td><a href="{{ route('client.details', $item->id) }}" >{{ $item->name }}</a></td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->phone }}</td>
                                        <td class="status-text-{{ $item->id }}">
                                            <span class="text-warning"><b>Đang chờ duyệt</b></span>
                                        </td>
                                        <td>
                                            <select class="status-select" data-id="{{ $item->id }}">
                                                <option value="">-- Duyệt/Không --</option>
                                                <option value="1">Phê duyệt</option>
                                                <option value="2">Không duyệt</option>
                                            </select>
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

<!-- JS xử lý cập nhật và reload dòng -->
<script>
    $(document).ready(function () {
        function updateSelectColor(select) {
            const status = $(select).val();
            $(select).removeClass('status-pheduyet status-khongduyet status-default');

            if (status === "1") {
                $(select).addClass('status-pheduyet');
            } else if (status === "2") {
                $(select).addClass('status-khongduyet');
            } else {
                $(select).addClass('status-default');
            }
        }

        $('.status-select').each(function () {
            updateSelectColor(this);
        });

        $('.status-select').on('change', function () {
            const status = $(this).val();
            const client_id = $(this).data('id');

            if (status === "") {
                return; // Không làm gì nếu chưa chọn
            }

            const rowSelector = `#row-${client_id}`;

            $.ajax({
                url: '/clientChangeStatus',
                method: 'GET',
                data: { status, client_id },
                dataType: 'json',
                success: function (data) {
                    if (!data.error) {
                        $(rowSelector).fadeOut(300, function () {
                            $(this).remove();
                        });
                        alert(data.success);
                    } else {
                        alert(data.error);
                    }
                },
                error: function () {
                    alert('Đã xảy ra lỗi khi gửi yêu cầu.');
                }
            });

            updateSelectColor(this);
        });

    });
</script>

@endsection
