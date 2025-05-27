@extends('client.client_dashboard')
@section('client')

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Chi tiết đơn hàng</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                      </ol>
                  </div>

              </div>
          </div>
      </div>
      <!-- end page title -->

      <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-2">

        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4>Chi tiết giao hàng</h4>
                </div>
    
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered border-primary mb-0">
                            <tbody>
                                <tr>
                                    <th width="50%">Tên người nhận:</th>
                                    <td>{{ $order->name }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Số điện thoại:</th>
                                    <td>{{ $order->phone }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Email:</th>
                                    <td>{{ $order->email }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Địa chỉ giao hàng:</th>
                                    <td>{{ $order->address }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Ngày đặt hàng:</th>
                                    <td>{{ $order->order_date }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4>Chi tiết đơn hàng
                        <span class="text-danger">Mã hóa đơn: {{ $order->invoice_no }}</span>
                    </h4>
                    <h4>Cửa hàng
                        <span class="text-danger">
                            {{ $orderItem[0]->product->client->name }}
                        </span>
                    </h4>
                </div>
    
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered border-primary mb-0">
                            <tbody>
                                <tr>
                                    <th width="50%">Tên khách hàng:</th>
                                    <td>{{ $order->user->name }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Số điện thoại:</th>
                                    <td>{{ $order->user->phone }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Email:</th>
                                    <td>{{ $order->user->email }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Phương thức thanh toán:</th>
                                    <td>{{ $order->payment_method }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Mã giao dịch:</th>
                                    <td>{{ $order->transaction_id }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Mã hóa đơn:</th>
                                    <td class="text-danger">{{ $order->invoice_no }}</td>
                                </tr>
                                <tr>
                                    <th width="50%">Tổng tiền đơn hàng:</th>
                                    <td>{{ number_format($order->amount, 0, ',', '.') }} VNĐ</td>
                                </tr>
                                <tr>
                                    <th width="50%">Tổng thanh toán:</th>
                                    <td class="text-danger">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                </tr>
                                <tr>
                                    <th width="50%">Trạng thái đơn hàng:</th>
                                    <td>
                                        @if ($order->status == 'pending')
                                        <span class="badge bg-info">Chờ xử lý</span>
                                        @elseif ($order->status == 'confirm')
                                        <span class="badge bg-primary">Đã xác nhận</span>
                                        @elseif ($order->status == 'processing')
                                        <span class="badge bg-warning">Đang xử lý</span>
                                        @elseif ($order->status == 'delivered')
                                        <span class="badge bg-success">Đã giao hàng</span>
                                        @elseif ($order->status == 'cancel_pending')
                                        <span class="badge" style="background-color: #f66; color: white;">Đăng ký huỷ</span> {{-- Đỏ nhạt --}}
                                        @elseif ($order->status == 'cancelled')
                                        <span class="badge bg-danger">Hủy thành công</span>
                                        @else
                                        <span class="badge bg-danger">Không xác định</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    @if($order->status == 'cancel_pending')
                                        <th width="50%">Lý do hủy:</th>
                                        <td class="text-danger">{{ $order->cancel_reason }}</td>
                                    @elseif ($order->status == 'cancelled')
                                        <th width="50%">Lý do hủy:</th>
                                        <td class="text-danger">{{ $order->cancel_reason }}</td>
                                    @endif
                                </tr>
    
                                <tr>
                                    <th width="50%"></th>
                                    <td>
                                        @if($order->status == 'pending')
                                            <a href="{{ route('pening_to_confirm',$order->id) }}" class="btn btn-block btn-success" id="confirmOrder">Xác nhận đơn hàng</a>
                                        @elseif ($order->status == 'confirm')
                                            <a href="{{ route('confirm_to_processing',$order->id) }}" class="btn btn-block btn-success" id="processingOrder">Đang xử lý đơn hàng</a>
                                        @elseif ($order->status == 'processing')
                                            <a href="{{ route('processing_to_delivered',$order->id) }}" class="btn btn-block btn-success" id="deliveredOrder">Giao đơn hàng</a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    
    </div> <!-- end row -->
    



             <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-1">
                <div class="col">
                    <div class="card p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Cửa hàng</th>
                                        <th>Mã sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderItem as $item)
                                    <tr>
                                        <td>
                                            <img src="{{ asset($item->product->productTemplate->image) }}" style="width:50px; height:50px" alt="Product Image">
                                        </td>
                                        <td>
                                            {{ $item->product->productTemplate->name }}
                                        </td>
                                        <td>
                                            {{ $item->client_id == NULL ? 'Chính chủ' : $item->product->client->name }}
                                        </td>
                                        <td>
                                            {{ $item->product->productTemplate->code }}
                                        </td>
                                        <td>
                                            {{ $item->qty }}
                                        </td>
                                        <td>
                                            {{ number_format($item->price, 0, ',', '.') }}
                                            <br>
                                            <small class="text-danger">
                                                Tổng: {{ number_format($item->price * $item->qty, 0, ',', '.') }} VNĐ
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach 
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <h6>Tổng cộng: {{ number_format($totalPrice, 0, ',', '.') }} VNĐ</h6>
                            <h4 class="text-success">Tổng thanh toán: {{ number_format($totalAmount, 0, ',', '.') }} VNĐ</h4>
                        </div>

                        @if (in_array($order->status, ['cancel_pending']))
                            <div class="mt-3">
                                <h5>Lý do huỷ đơn:</h5>
                                <p class="border p-3 bg-light">{{ $order->cancel_reason ?? 'Không có lý do huỷ.' }}</p>

                                <div class="text-end">
                                    <form action="{{ route('client.order.cancel.pending', $order->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="cancel_reason" value="{{ $order->cancel_reason }}">
                                        <button type="submit" class="btn btn-danger">
                                            Xác nhận huỷ đơn
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                        
                        @if (in_array($order->status, ['pending', 'confirm']))
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-danger" id="showCancelForm">
                                    <i class="bi bi-x-circle"></i> Huỷ đơn
                                </button>
                            </div>

                            <form id="cancelForm" action="{{ route('client.order.cancel', $order->id) }}" method="POST" style="display: none;" class="mt-3">
                                @csrf
                                <div class="form-group">
                                    <label for="cancel_reason">Lý do huỷ đơn <span class="text-danger">*</span></label>
                                    <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="3" required placeholder="Nhập lý do huỷ đơn..."></textarea>
                                </div>
                                <div class="text-end mt-2">
                                    <button type="submit" class="btn btn-danger">
                                        Xác nhận huỷ đơn
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>





  </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cancelBtn = document.getElementById('showCancelForm');
        const cancelForm = document.getElementById('cancelForm');

        cancelBtn.addEventListener('click', function () {
            cancelForm.style.display = 'block';
            cancelBtn.style.display = 'none';
            document.getElementById('cancel_reason').focus();
        });
    });
</script>
@endsection