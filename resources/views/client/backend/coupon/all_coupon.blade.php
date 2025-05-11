@extends('client.client_dashboard')
@section('client')


<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Danh sách mã giảm giá</h4>

                  <div class="page-title-right">
                      <ol class="breadcrumb m-0">
                        <a href="{{ route('add.coupon') }}" 
                            class="btn btn-info waves-effect waves-light">Thêm mã giảm giá</a>
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
        <th>Tên mã giảm giá</th>
        <th>Mô tả</th>
        <th>Giảm giá</th>
        <th>Hiệu lực</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>
    </thead>


    <tbody>
      @foreach ($coupon as $key=>$item)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $item->coupon_name }}</td>
            <td>{{ Str::limit($item->coupon_desc, 20) }}</td>
            <td>{{ $item->discount }}</td>
            <td>{{ Carbon\Carbon::parse($item->validity)->format('D, d F Y') }}</td>
            <td>
                @if ($item->validity >= Carbon\Carbon::now()->format('Y-m-d'))
                    <span class="badge rounded-pill bg-success">Còn hiệu lực</span>
                @else
                    <span class="badge rounded-pill bg-danger">Hết hạn</span>
                @endif
            </td>
            <td>
                <a href="{{ route('edit.coupon', $item->id) }}"
                    class="btn btn-info waves-effect waves-light">Chỉnh sửa</a>
                <a href="{{ route('delete.coupon', $item->id) }}" 
                    class="btn btn-danger waves-effect waves-light" id="delete">Xóa</a>
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

@endsection