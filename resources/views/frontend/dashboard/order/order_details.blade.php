@extends('frontend.dashboard.dashboard')
 @section('dashboard')
  
 @php
     $id = Auth::user()->id;
     $profileData = App\Models\User::find($id);
 @endphp
 
 <section class="section pt-4 pb-4 osahan-account-page">
     <div class="container">
        <div class="row">
           
         @include('frontend.dashboard.sidebar')
 
 
 <div class="col-md-9">
     <div class="osahan-account-page-right rounded shadow-sm bg-white p-4 h-100">
     <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
             <h4 class="font-weight-bold mt-0 mb-4">Chi tiết đơn hàng</h4>
             
             
     
             <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-2">
     
                 <div class="col">
                     <div class="card">
                      <div class="card-header">
                         <h4>Thông tin giao hàng</h4>
                      </div>
                             
                         <div class="card-body">
             <div class="table-responsive">
                 <table class="table table-bordered border-primary mb-0">
              
                     <tbody>
                         <tr> 
                             <th width="50%">Tên người nhận: </th>
                             <td>{{ $order->name }}</td> 
                         </tr> 
                         <tr> 
                             <th width="50%">Số điện thoại: </th>
                             <td>{{ $order->phone }}</td> 
                         </tr>
                         <tr> 
                             <th width="50%">Email: </th>
                             <td>{{ $order->email }}</td> 
                         </tr>
                         <tr> 
                             <th width="50%">Địa chỉ: </th>
                             <td>{{ $order->address }}</td> 
                         </tr>
                         <tr> 
                             <th width="50%">Ngày đặt hàng: </th>
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
                         <span class="text-danger">Mã hóa đơn: {{ $order->invoice_no }}</span></h4>
                      </div>
                             
                         <div class="card-body">
             <div class="table-responsive">
                 <table class="table table-bordered border-primary mb-0">
              
                     <tbody>
                         <tr> 
                             <th width="50%"> Tên khách hàng: </th>
                             <td>{{ $order->user->name }}</td> 
                         </tr> 
                         <tr> 
                             <th width="50%"> Số điện thoại: </th>
                             <td>{{ $order->user->phone }}</td> 
                         </tr>
                         <tr> 
                             <th width="50%"> Email: </th>
                             <td>{{ $order->user->email }}</td> 
                         </tr>
                         <tr> 
                             <th width="50%">Hình thức thanh toán: </th>
                             <td>{{ $order->payment_method }}</td> 
                         </tr>
                         <tr> 
                             <th width="50%">Mã giao dịch: </th>
                             <td>{{ $order->transaction_id }}</td> 
                         </tr> 
                         <tr> 
                             <th width="50%">Mã hóa đơn: </th>
                             <td class="text-danger">{{ $order->invoice_no }}</td> 
                         </tr> 
                         <tr> 
                             <th width="50%">Tổng tiền: </th>
                             <td>${{ $order->amount }}</td> 
                         </tr> 
                         <tr> 
                             <th width="50%">Trạng thái: </th>
                             <td>
                                @if ($order->status == 'pending')
                                <span class="badge bg-info">Chờ xử lý</span>
                                @elseif ($order->status == 'confirm')
                                <span class="badge bg-primary">Đã xác nhận</span>
                                @elseif ($order->status == 'processing')
                                <span class="badge bg-warning">Đang xử lý</span>
                                @elseif ($order->status == 'deliverd')
                                <span class="badge bg-success">Đã giao hàng</span>
                                @elseif ($order->status == 'cancel_pending')
                                <span class="badge" style="background-color: #f66; color: white;">Đăng ký huỷ</span> {{-- Đỏ nhạt --}}
                                @elseif ($order->status == 'cancelled')
                                <span class="badge bg-danger">Hủy thành công</span>
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
                                        <th>Nhà hàng</th>
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

                        @if (in_array($order->status, ['pending', 'confirm']))
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-danger" id="showCancelForm">
                                    <i class="bi bi-x-circle"></i> Huỷ đơn
                                </button>
                            </div>

                            <form id="cancelForm" action="{{ route('user.order.cancel', $order->id) }}" method="POST" style="display: none;" class="mt-3">
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

      
 
         
         </div> 
         
     </div>
     </div>
 </div>
        </div>
     </div>
  </section> 
 
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