@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
  <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
          <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Đơn hàng đang chờ</h4>

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

<table id="datatable" class="table table-bordered dt-responsive w-100">
    <thead>
    <tr>
      <th>STT</th>
      <th>Ngày đặt hàng</th>
      <th>Mã hóa đơn</th>
      <th>Số tiền</th>
      <th>Phương thức thanh toán</th>
      <th>Trạng thái</th>
      <th>Hành động</th>
    </tr>
    </thead>


    <tbody>
      @foreach ($allData as $key=>$item)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $item->order_date }}</td>
            <td>{{ $item->invoice_no }}</td>
            <td>{{ $item->amount }}</td>
            <td>{{ $item->payment_method }}</td>
            <td>
                @if ($item->status == 'pending')    
                <span class="badge bg-info">Chờ xử lý</span>
                @else
                <span class="badge bg-danger">Không xác định</span>
                @endif
            </td>

            <td class="d-flex align-items-center gap-1">
                <a href="{{ route('admin.order_details', $item->id) }}"
                    class="btn btn-info waves-effect waves-light">
                    <i class="fas fa-eye"></i>
                </a>
                {{-- <input data-id="{{ $item->id }}" 
                    class="toggle-class" type="checkbox" 
                    data-toggle="toggle" 
                    data-onstyle="success" 
                    data-offstyle="danger" 
                    data-on="Active" 
                    data-off="Inactive" 
                    {{ $item->status ? 'checked' : '' }}> --}}
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