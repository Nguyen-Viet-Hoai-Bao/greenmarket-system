@extends('admin.admin_dashboard')
 @section('admin')
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
 
 <div class="page-content">
     <div class="container-fluid">
 
         <!-- start page title -->
         <div class="row">
             <div class="col-12">
                 <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                     <h4 class="mb-sm-0 font-size-18">Vấn đề của khách hàng </h4>
 
                     
 
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
                <th>Người dùng</th>
                <th>Cửa hàng</th>
                <th>Mã hóa đơn</th>
                <th>Ý kiến</th>
                <th>Loại đánh giá</th>                
                <th>Trạng thái</th> 
                <th>Thao tác</th> 
             </tr>
             </thead>
 
 
             <tbody>
            @foreach ($listReport as $key=> $item)  
            <tr id="review-row-{{ $item->id }}">
                 <td>{{ $key+1 }}</td>
                 <td>{{ $item['order']['user']['name'] }}</td>
                 <td>{{ $item['client']['name'] }}</td>
                 <td>{{ $item['order']['invoice_no'] }}</td>
                 <td>{{ Str::limit($item->content, 50, '...')  }}</td>
                 @php
                    $issueTypes = [
                        'delivery' => 'Giao hàng',
                        'product_quality' => 'Chất lượng sản phẩm',
                        'payment' => 'Thanh toán',
                        'customer_service' => 'Dịch vụ khách hàng',
                        'other' => 'Khác',
                    ];
                @endphp
                <td>{{ $issueTypes[$item->issue_type] ?? 'Không xác định' }}</td>
                 <td> 
                     @if ($item->status == 'pending')
                     <span class="text-danger"><b>Đang chờ</b></span>
                     @else
                     <span class="text-success"><b>Đã giải quyết</b></span>
                     @endif
                 </td>
         <td> 
         <input data-id="{{$item->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Giải quyết" data-off="Chờ" {{ $item->status ? 'checked' : '' }}>
 
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
 
 <script type="text/javascript">
     $(function() {
       $('.toggle-class').change(function() {
           var status = $(this).prop('checked') == true ? 1 : 0; 
           var review_id = $(this).data('id'); 
            
           $.ajax({
               type: "GET",
               dataType: "json",
               url: '/changeOrderReport',
               data: {'status': status, 'review_id': review_id},
               success: function(data){
                const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        icon: 'success', 
                        showConfirmButton: false,
                        timer: 3000 
                })
                if ($.isEmptyObject(data.error)) {
                    Toast.fire({
                    type: 'success',
                    title: data.success, 
                    })
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }else{
                    Toast.fire({
                        type: 'error',
                        title: data.error, 
                    })
                }
                }
           });
       })
     })
   </script>
    
 
 
 @endsection