@extends('admin.admin_dashboard')
 @section('admin') 
 
 <div class="page-content">
     <div class="container-fluid">
 
         <!-- start page title -->
         <div class="row">
             <div class="col-12">
                 <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                     <h4 class="mb-sm-0 font-size-18">Tìm kiếm tất cả đơn hàng theo ngày</h4>
 
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
         <h3 class="text-danger">Tìm kiếm theo ngày: {{ $formatDate }}</h3>
         <div class="row mb-3">
            <div class="col-md-4">
                <div class="alert alert-primary">
                    <strong>Tổng Doanh Thu:</strong> {{ number_format($totalAmount, 0, ',', '.') }}đ
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-success">
                    <strong>Lợi Nhuận Thực Tế:</strong> {{ number_format($totalRevenue, 0, ',', '.') }}đ
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-warning">
                    <strong>Doanh thu của Admin:</strong> {{ number_format($totalServiceFee, 0, ',', '.') }}đ
                </div>
            </div>
        </div>
         <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
             <thead>
             <tr>
                <th>STT</th>
                <th>Ngày</th>
                <th>Chi nhánh</th>
                <th>Hóa đơn</th>
                <th>Số tiền</th>
                <th>Phí dịch vụ</th>
                <th>Phương thức thanh toán</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
             </tr>
             </thead>
 
 
             <tbody>
            @foreach ($orderDate as $key=> $item)  
            @php
                $firstItem = $item->OrderItems->first();
                $market = $firstItem ? \App\Models\Client::find($firstItem->client_id) : null;
            @endphp
             <tr>
                 <td>{{ $key+1 }}</td>
                 <td>{{ $item->order_date }}</td>
                 <td>{{ $market->name ?? 'Không xác định' }}</td>
                 <td>{{ $item->invoice_no }}</td>
                 <td>{{ $item->amount }}</td>
                 <td>{{ $item->service_fee }}</td>
                 <td>{{ $item->payment_method }}</td>
                 <td>
                    @switch($item->status)
                      @case('pending')
                        <span class="badge bg-warning text-dark">{{ $item->status }}</span>
                        @break
                      @case('confirm')
                        <span class="badge bg-info text-white">{{ $item->status }}</span>
                        @break
                      @case('processing')
                        <span class="badge bg-secondary">{{ $item->status }}</span>
                        @break
                      @case('delivered')
                        <span class="badge bg-success">{{ $item->status }}</span>
                        @break
                      @default
                          <span class="badge bg-secondary">{{ $item->status }}</span>
                    @endswitch
                  </td>
                
                 
         <td><a href="#" class="btn btn-info waves-effect waves-light"> <i class="fas fa-eye"></i> </a> 
          {{--  --}}
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