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
                             <td><span class="badge bg-success">{{ $order->status }}</span></td> 
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
                                         <label>Nhà hàng</label>
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
                            Chính chủ
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
                            {{ number_format($item->price, 0, ',', '.') }}
                         </label>
                         <label class="text-danger">
                            Tổng: {{ number_format($item->price * $item->qty, 0, ',', '.') }} VNĐ
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
                 
      
 
         
         </div> 
         
     </div>
     </div>
 </div>
        </div>
     </div>
  </section> 
 
 @endsection