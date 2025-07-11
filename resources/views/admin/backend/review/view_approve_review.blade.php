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
                     <h4 class="mb-sm-0 font-size-18">Duyệt Đánh Giá Cửa Hàng</h4>
 
                     
 
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
                <th>Ý kiến</th>
                <th>Đánh giá</th>                
                <th>Trạng thái</th> 
                <th>Yêu cầu ẩn</th>
                <th>Thao tác</th> 
             </tr>
             </thead>
 
 
             <tbody>
            @foreach ($approveReview as $key=> $item)  
            <tr id="review-row-{{ $item->id }}">
                 <td>{{ $key+1 }}</td>
                 <td>{{ $item['user']['name'] }}</td>
                 <td>{{ $item['client']['name'] }}</td>
                 <td>{{ Str::limit($item->comment, 50, '...')  }}</td>
                 <td>
                     @for ($i = 1; $i <= 5; $i++)
                         <i class="bx bxs-star {{ $i <= $item->rating ? 'text-warning' : 'text-secondary' }}"></i>
                     @endfor 
                </td> 
                 <td> 
                     @if ($item->status == 1)
                     <span class="text-success"><b>Hoạt động</b></span>
                     @else
                     <span class="text-danger"><b>Không hoạt động</b></span>
                     @endif
                 </td>
                 <td>
                    @if ($item->reviewReport)
                        <span class="text-danger">{{ $item->reviewReport->reason }}</span>
                    @else
                        <span class="text-muted"></span>
                    @endif
                </td>
         <td> 
         <input data-id="{{$item->id}}" class="toggle-class" type="checkbox" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="Inactive" {{ $item->status ? 'checked' : '' }}>
 
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
               url: '/reviewchangeStatus',
               data: {'status': status, 'review_id': review_id},
               success: function(data){
                 // console.log(data.success)
                
                if (status == 0) {
                  $('#review-row-' + review_id).fadeOut(500, function() {
                      $(this).remove();
                  });
                }
                   // Start Message 
   
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
   
               }else{
                  
              Toast.fire({
                       type: 'error',
                       title: data.error, 
                       })
                   }
   
                 // End Message   
   
   
               }
           });
       })
     })
   </script>
    
 
 
 @endsection