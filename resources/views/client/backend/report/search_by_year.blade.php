@extends('client.client_dashboard')
 @section('client') 
 
 <div class="page-content">
     <div class="container-fluid">
 
         <!-- start page title -->
         <div class="row">
             <div class="col-12">
                 <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                     <h4 class="mb-sm-0 font-size-18">All Search By Month Order</h4>
 
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
         <h3 class="text-danger">Search By Year: {{ $years }}</h3>
         <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
             <thead>
             <tr>
                 <th>Sl</th>
                 <th>Date</th>
                 <th>Invoice</th>
                 <th>Amount</th>
                 <th>Payment</th> 
                 <th>Status</th>
                 <th>Action </th> 
             </tr>
             </thead>
 
 
             <tbody>

                @php $key = 1; @endphp
                @foreach ($orderItemGroupData as $orderGroup) 
                    @foreach ($orderGroup as $item) 
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item->order->order_date }}</td>
                        <td>{{ $item->order->invoice_no }}</td>
                        <td>{{ $item->order->amount }}</td>
                        <td>{{ $item->order->payment_method }}</td>
                        <td>
                            @switch($item->order->status)
                              @case('pending')
                                <span class="badge bg-warning text-dark">{{ $item->order->status }}</span>
                                @break
                              @case('confirm')
                                <span class="badge bg-info text-white">{{ $item->order->status }}</span>
                                @break
                              @case('processing')
                                <span class="badge bg-secondary">{{ $item->order->status }}</span>
                                @break
                              @case('delivered')
                                <span class="badge bg-success">{{ $item->order->status }}</span>
                                @break
                              @default
                                  <span class="badge bg-secondary">{{ $item->order->status }}</span>
                            @endswitch
                          </td>
                        <td><a href="{{ route('client.order_details',$item->order_id) }}" class="btn btn-info waves-effect waves-light"> 
                            <i class="fas fa-eye"></i> </a> 
                        </td> 
                    </tr>
                    @break
                    @endforeach 
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