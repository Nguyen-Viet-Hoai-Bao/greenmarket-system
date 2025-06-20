@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="container-fluid">

        <div class="row mb-4">
            <div class="col-md-12">
                <h4 class="mb-3">Chi tiết cửa hàng: {{ $client->name }}</h4>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Quay lại danh sách</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-4 text-center mb-3">
                        <img src="{{ !empty($client->photo) 
                                        ? url('upload/client_images/' . $client->photo) 
                                        : url('upload/no_image.jpg') }}"
                             style="width: 200px; height: auto; border-radius: 8px;">
                    </div>

                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>Tên cửa hàng</th>
                                <td>{{ $client->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $client->email }}</td>
                            </tr>
                            <tr>
                                <th>Điện thoại</th>
                                <td>{{ $client->phone }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td>
                                    {{ $client->address ?? '' }}<br>
                                    @if($client->ward)
                                        {{ $client->ward->ward_name }},
                                        {{ $client->ward->district->district_name ?? '' }},
                                        {{ $client->ward->district->city->city_name ?? '' }}
                                    @else
                                        <span class="text-muted">Chưa cập nhật địa chỉ hành chính</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($client->status == 1)
                                        <span class="badge bg-success">Phê duyệt</span>
                                    @elseif($client->status == 2)
                                        <span class="badge bg-danger">Không duyệt</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
