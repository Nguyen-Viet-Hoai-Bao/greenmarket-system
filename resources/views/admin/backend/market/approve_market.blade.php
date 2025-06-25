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
                  <h4 class="mb-sm-0 font-size-18">Cửa hàng đã duyệt</h4>
              </div>
          </div>
      </div>
      <!-- end page title -->

      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-body">

<table id="datatable" class="table table-bordered dt-responsive w-100">
    <thead>
    <tr>
        <th>STT</th>
        <th>Hình Ảnh</th>
        <th>Tên Cửa Hàng</th>
        <th>Email</th>
        <th>Số Điện Thoại</th>
        <th>Trạng Thái</th>
        <th>Hành Động</th>
    </tr>
    </thead>


    <tbody>
      @foreach ($client as $key=>$item)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>
              <img src="{{ (!empty($item->photo)) 
                            ? url($item->photo)
                            : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png')}}" 
                    alt=""
                    style="width: 70px; heigh:40px;"></td>
            <td><a href="{{ route('client.details', $item->id) }}" >{{ $item->name }}</a></td>
            <td>{{ $item->email }}</td>
            <td>{{ $item->phone }}</td>
            <td class="status-text-{{ $item->id }}">
                @if ($item->status == 1)
                    <span class="text-success"><b>Đang hoạt động</b></span>
                @else
                    <span class="text-danger"><b>Không hoạt động</b></span>
                @endif
            </td>
            <td class="d-flex align-items-center gap-1">
              <input data-id="{{ $item->id }}" 
                  class="toggle-class" type="checkbox" 
                  data-toggle="toggle" 
                  data-onstyle="success" 
                  data-offstyle="danger" 
                  data-on="Khóa" 
                  data-off="Mở khóa" 
                  {{ $item->status ? 'checked' : '' }}>
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
<!-- End Page-content -->


<script type="text/javascript">
  $(function() {
    $('.toggle-class').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 3; 
        var client_id = $(this).data('id'); 
         
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/clientChangeStatus',
            data: {'status': status, 'client_id': client_id},
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

                    $('#datatable').load(location.href + " #datatable>*", "");

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