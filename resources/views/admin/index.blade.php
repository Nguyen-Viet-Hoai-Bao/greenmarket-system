@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Bảng thống kê</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                          <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                          <li class="breadcrumb-item active">Bảng thống kê</li>
                      </ol>
                  </div>

              </div>
          </div>
      </div>
      <!-- end page title -->

    <div class="row">
        <!-- Ví của tôi -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate"> Tiền kiếm được</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ number_format($totalRevenue / 1000000, 2) }}">
                                    {{ number_format($totalRevenue / 1000000, 2) }}
                                </span> triệu
                            </h4>
                        </div>
                        <div class="col-6">
                            <div id="mini-chart1" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">
                            {{ $revenueDiff >= 0 ? '+' : '' }}{{ number_format($revenueDiff / 1000000, 2) }} triệu
                        </span>
                        <span class="ms-1 text-muted font-size-13">Từ tuần trước</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Số giao dịch -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Số giao dịch</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $transactionCount }}">
                                    {{ $transactionCount }}
                                </span>
                            </h4>
                        </div>
                        <div class="col-6">
                            <div id="mini-chart2" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-danger-subtle text-danger">
                            {{ $transactionDiff >= 0 ? '+' : '' }}{{ $transactionDiff }} giao dịch
                        </span>
                        <span class="ms-1 text-muted font-size-13">Từ tuần trước</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Số tiền đầu tư -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Tổng số tiền vốn</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ number_format($profit / 1000000, 2) }}">
                                    {{ number_format($profit / 1000000, 2) }}
                                </span> triệu
                            </h4>
                        </div>
                        <div class="col-6">
                            <div id="mini-chart4" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">
                            {{ $investmentDiff >= 0 ? '+' : '' }}{{ number_format($investmentDiff / 1000000, 2) }} triệu
                        </span>
                        <span class="ms-1 text-muted font-size-13">Từ tuần trước</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lợi nhuận -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Lợi nhuận</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ number_format($investment / 1000000, 2) }}">
                                    {{ number_format($investment / 1000000, 2) }}
                                </span> triệu
                            </h4>
                        </div>
                        <div class="col-6">
                            <div id="mini-chart3" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                        </div>
                    </div>
                    <div class="text-nowrap">
                        <span class="badge bg-success-subtle text-success">
                            {{ $profitDiff >= 0 ? '+' : '' }}{{ number_format($profitDiff / 1000000, 2) }} triệu
                        </span>
                        <span class="ms-1 text-muted font-size-13">Từ tuần trước</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <div class="row">
        <div class="col-xl-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">Top 5 Sản phẩm bán chạy nhất</h5>
                    <ul class="list-group list-group-flush">
                        @forelse ($topProducts as $item)
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset($item->product->productTemplate->image ?? 'default.png') }}" alt="{{ $item->product->productTemplate->name }}" style="width:40px; height:auto; margin-right:10px;">
                                    <div>
                                        <strong>{{ $item->product->productTemplate->name ?? 'N/A' }}</strong><br>
                                        <small>Giá bán: {{ number_format($item->product->price) }}đ | Đã bán: {{ $item->total_qty }}</small>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-muted">No products sold yet.</p>
                        @endforelse

                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">Top 5 Đơn hàng giá trị nhất</h5>
                    <ul class="list-group list-group-flush">
                        @forelse ($topOrders as $order)
                            <li class="list-group-item">
                                <div>
                                    <strong>Order #{{ $order->id }}</strong> — {{ $order->name }} ({{ $order->email }})<br>
                                    <small>
                                        SDT: {{ $order->phone }} |
                                        Ngày: {{ $order->order_date }} |
                                        Trạng thái: {{ ucfirst($order->status) }}
                                    </small>
                                </div>
                                <span class="badge bg-success rounded-pill float-end">${{ number_format($order->total_amount, 2) }}</span>
                            </li>
                        @empty
                            <p class="text-muted">No orders yet.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

  </div>
  <!-- container-fluid -->
</div>

@endsection