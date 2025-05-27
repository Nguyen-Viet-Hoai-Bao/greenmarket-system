@extends('client.client_dashboard')
@section('client')

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Tất Cả Đơn Hàng Của Khách Hàng</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                         
                      </ol>
                  </div>

              </div>
          </div>
      </div>
      <!-- end page title -->

      <div class="row">
          <div class="col-12">
              <div class="card">
                   
                  <div class="card-body">

      <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
          <thead>
          <tr>
            <th>STT</th>
            <th>Ngày</th>
            <th>Hóa Đơn</th>
            <th>Số Tiền</th>
            <th>Phương Thức Thanh Toán</th> 
            <th>Trạng Thái</th>
            <th>Hành Động</th>
          </tr>
          </thead>


          <tbody>
         @foreach ($orderItemGroupData as $key=> $orderitem)
         @php
             $firstItem = $orderitem->first();
             $order = $firstItem->order;
         @endphp  
          <tr>
              <td>{{ $key+1 }}</td>
              <td>{{ $order->order_date }}</td>
              <td>{{ $order->invoice_no }}</td>
              <td>{{ number_format($order->amount, 0, ',', '.') }} VNĐ</td>
              <td>{{ $order->payment_method }}</td>
              <td>
                  @if ($order->status == 'pending')
                  <span class="badge bg-info">Chờ Xử Lý</span>
                  @elseif ($order->status == 'confirm')
                  <span class="badge bg-primary">Đã Xác Nhận</span>
                  @elseif ($order->status == 'processing')
                  <span class="badge bg-warning">Đang Xử Lý</span>
                  @elseif ($order->status == 'delivered')
                  <span class="badge bg-success">Đã Giao</span>
                  @elseif ($order->status == 'cancel_pending')
                  <span class="badge" style="background-color: #f66; color: white;">Đăng ký huỷ</span> {{-- Đỏ nhạt --}}
                  @elseif ($order->status == 'cancelled')
                  <span class="badge bg-danger">Hủy thành công</span>
                  @endif
                 </td>                
             
              
      <td><a href="{{ route('client.order.details',$order->id) }}" class="btn btn-info waves-effect waves-light"> <i class="fas fa-eye"></i> </a> 

              </td> 
          </tr>
          @endforeach    
          
          </tbody>
      </table>

                  </div>
              </div>
          </div> <!-- end col -->
      </div> <!-- end row --> 

       
  </div> <!-- container-fluid -->
</div>

@endsection