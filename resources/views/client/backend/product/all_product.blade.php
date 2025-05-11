@extends('client.client_dashboard')
@section('client')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Tất Cả Sản Phẩm</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                        <a href="{{ route('add.product') }}" 
                            class="btn btn-info waves-effect waves-light">Thêm Sản Phẩm</a>
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

<table id="datatable" class="table table-bordered dt-responsive w-100">
    <thead>
    <tr>
        <th>STT</th>
        <th>Hình ảnh</th>
        <th>Tên Sản Phẩm</th>
        <th>Danh Mục</th>
        <th>Số Lượng</th>
        <th>Giá</th>
        <th>Giá Giảm</th>
        <th>Giảm Giá</th>
        <th>Trạng Thái</th>
        <th>Hành Động</th>
    </tr>
    </thead>


    <tbody>
      @foreach ($product as $key=>$item)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>
              <img src="{{ asset($item['productTemplate']['image']) }}" 
                    alt=""
                    style="width: 70px; heigh:40px;"></td>
            <td>{{ $item['productTemplate']['name'] }}</td>
            <td>{{ $item['productTemplate']['menu']['menu_name'] }}</td>
            <td>{{ $item->qty }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->discount_price }}</td>
            <td>
                @if ($item->discount_price == NULL)
                    <span class="badge bg-danger">Không có giảm giá</span>
                @else
                    @php
                        $amount = $item->price - $item->discount_price;
                        $discount = ($amount / $item->price)*100;
                    @endphp      
                    <span class="badge bg-danger">{{ round($discount) }}%</span>
                @endif    
            </td>
            <td class="status-text-{{ $item->id }}">
                @if ($item->status == 1)
                    <span class="text-success"><b>Hoạt Động</b></span>
                @else
                    <span class="text-danger"><b>Ngừng Hoạt Động</b></span>
                @endif
            </td>
            <td class="d-flex align-items-center gap-1">
<a href="{{ route('edit.product', $item->id) }}"
    class="btn btn-info waves-effect waves-light">
    <i class="fas fa-edit"></i>
</a>
<a href="{{ route('delete.product', $item->id) }}" 
    class="btn btn-danger waves-effect waves-light" id="delete">
    <i class="fas fa-trash"></i>
</a>
<input data-id="{{ $item->id }}" 
    class="toggle-class" type="checkbox" 
    data-toggle="toggle" 
    data-onstyle="success" 
    data-offstyle="danger" 
    data-on="Active" 
    data-off="Inactive" 
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
        var status = $(this).prop('checked') == true ? 1 : 0; 
        var product_id = $(this).data('id'); 
         
        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/changeStatus',
            data: {'status': status, 'product_id': product_id},
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

                    let newHtml = status === 1
                        ? '<span class="text-success"><b>Active</b></span>'
                        : '<span class="text-danger"><b>InActive</b></span>';

                    $('.status-text-' + product_id).html(newHtml);

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