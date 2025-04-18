@extends('admin.admin_dashboard')
@section('admin')

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
                                        <span class="badge bg-success">
                                            @if ($order->status == 'pending')
                                                Chờ xác nhận
                                            @elseif ($order->status == 'confirm')
                                                Đã xác nhận
                                            @elseif ($order->status == 'processing')
                                                Đang xử lý
                                            @elseif ($order->status == 'delivered')
                                                Đã giao hàng
                                            @else
                                                Không xác định
                                            @endif
                                        </span>
                                    </td>
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
            <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="col-md-1">
                                <label>Hình ảnh</label>
                            </td>
                            <td class="col-md-1">
                                <label>Tên sản phẩm</label>
                            </td>
                            <td class="col-md-1">
                                <label>Tên nhà hàng</label>
                            </td>
                            <td class="col-md-1">
                                <label>Mã sản phẩm</label>
                            </td>
                            <td class="col-md-1">
                                <label>Số lượng</label>
                            </td>
                            <td class="col-md-1">
                                <label>Giá</label>
                            </td> 
                        </tr>
    @foreach ($orderItem as $item)
    <tr>
        <td class="col-md-1">
            <label>
                <img src="{{ asset($item->product->image) }}" style="width:50px; height:50px">
            </label>
        </td>
        <td class="col-md-2">
            <label>
                {{ $item->product->name }}
            </label>
        </td>
        @if ($item->client_id == NULL)
        <td class="col-md-2">
            <label>
                Chủ sở hữu
            </label>
        </td>
        @else
        <td class="col-md-2">
            <label>
                {{ $item->product->client->name }}
            </label>
        </td>
        @endif
        <td class="col-md-2">
            <label>
                {{ $item->product->code }}
            </label>
        </td>
        <td class="col-md-2">
            <label>
                {{ $item->qty }}
            </label>
        </td>
        <td class="col-md-2">
            <label>
                {{ number_format(($item->price), 0, ',', '.') }} 
                <br>
                Tổng = {{ number_format(($item->price * $item->qty), 0, ',', '.') }} VNĐ
            </label>
        </td> 
    </tr> 
    @endforeach 
                    </tbody>
                </table>
        <div>
            <h6>Tổng cộng: {{ number_format($totalPrice, 0, ',', '.') }} VNĐ</h6>
        </div>
        <div>
            <h4  class="text-success">Tổng thanh toán: {{ number_format($totalAmount, 0, ',', '.') }} VNĐ</h4>
        </div>
    
            </div>
    
            </div>
        </div>
    </div>
    






  </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

@endsection