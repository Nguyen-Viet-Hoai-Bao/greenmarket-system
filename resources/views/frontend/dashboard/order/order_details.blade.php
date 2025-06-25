@extends('frontend.dashboard.dashboard')
 @section('dashboard')
  


<style>
    /* CSS cho thanh nối giữa các bước */
    .step-item {
        position: relative;
    }
    .step-connector {
        position: absolute;
        top: 10px; 
        right: 0;
        height: 4px;
        width: 75%;
        background-color: #ddd;
        z-index: 0;
        margin-left: 50%; 
        transform: translateX(50%);
        border-radius: 2px;
        transition: background-color 0.3s ease;
    }
    .step-connector.active {
        background-color: #0d6efd; 
    }
</style>

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
            @php
                // Định nghĩa trạng thái, icon, màu sắc tương ứng (FontAwesome 5)
                $statuses = [
                    'pending' => ['label' => 'Đang chờ', 'icon' => 'fas fa-hourglass-half', 'color' => 'text-warning'],
                    'confirm' => ['label' => 'Đã xác nhận', 'icon' => 'fas fa-check-circle', 'color' => 'text-primary'],
                    'processing' => ['label' => 'Đang xử lý', 'icon' => 'fas fa-sync-alt', 'color' => 'text-warning'],
                    'delivered' => ['label' => 'Đã giao hàng', 'icon' => 'fas fa-truck', 'color' => 'text-success'],
                    'cancel_pending' => ['label' => 'Đăng ký huỷ', 'icon' => 'fas fa-ban', 'color' => 'text-danger'],
                    'cancelled' => ['label' => 'Hủy thành công', 'icon' => 'fas fa-times-circle', 'color' => 'text-danger'],
                ];

                $steps = ['pending', 'confirm', 'processing', 'delivered'];
                $isCancelled = in_array($order->status, ['cancel_pending', 'cancelled']);
                $currentIndex = array_search($order->status, $steps);
            @endphp

            <div class="card mb-4">
                <div class="card-header">
                    <h4>Tiến trình đơn hàng</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center position-relative">
                        @if (!$isCancelled)
                            @foreach ($steps as $index => $step)
                                @php
                                    $active = $currentIndex !== false && $index <= $currentIndex;
                                    $nextStepActive = $currentIndex !== false && ($index + 1) <= $currentIndex;
                                @endphp

                                <div class="text-center flex-fill position-relative step-item" style="min-width: 100px;">
                                    <div class="mb-2">
                                        <i class="{{ $statuses[$step]['icon'] }} fa-2x {{ $active ? $statuses[$step]['color'] : 'text-muted' }}"></i>
                                    </div>
                                    <div class="{{ $active ? 'fw-bold' : 'text-muted' }}">
                                        {{ $statuses[$step]['label'] }}
                                    </div>

                                    {{-- Thanh nối giữa các bước --}}
                                    @if ($index < count($steps) - 1)
                                        <div class="step-connector {{ $nextStepActive ? 'active' : '' }}"></div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            {{-- Hiển thị riêng trạng thái hủy --}}
                            <div class="text-center flex-fill position-relative" style="min-width: 100px;">
                                <div class="mb-2">
                                    <i class="{{ $statuses[$order->status]['icon'] }} fa-2x {{ $statuses[$order->status]['color'] }}"></i>
                                </div>
                                <div class="fw-bold {{ $statuses[$order->status]['color'] }}">
                                    {{ $statuses[$order->status]['label'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
     
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
                                        @php
                                            $ward = \App\Models\Ward::with('district.city')->find($order->ward_id);
                                        @endphp

                                        <tr> 
                                            <th width="50%">Địa chỉ: </th>
                                            <td>
                                                {{ $order->address }}<br>
                                                @if ($ward && $ward->district && $ward->district->city)
                                                    {{ $ward->ward_name }}, {{ $ward->district->district_name }}, {{ $ward->district->city->city_name }}
                                                @else
                                                    Địa chỉ không xác định
                                                @endif
                                            </td> 
                                        </tr>
                                        <tr> 
                                            <th width="50%">Ngày đặt hàng: </th>
                                            <td>{{ $order->order_date }}</td> 
                                        </tr> 
                                        
                                    </tbody>
                                </table>
                            </div> 
                         </div>

                         <div class="card-footer">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($order->status == 'delivered')
                                <div class="mt-3 text-end">
                                    <button type="button" class="btn btn-warning" id="btnReport">
                                        <i class="fas fa-flag"></i> Báo cáo vấn đề
                                    </button>
                                </div>

                                {{-- Form Report ẩn ban đầu {{ route('user.order.report', $order->id) }} --}} 
                                <form id="reportForm" action="{{ route('user.order.report', $order->id) }}" method="POST" style="display:none;" class="mt-3">
                                    @csrf
                                    <div class="form-group">
                                        <label for="issue_type">Loại vấn đề <span class="text-danger">*</span></label>
                                        <select name="issue_type" id="issue_type" class="form-control" required>
                                            <option value="" disabled selected>-- Chọn loại vấn đề --</option>
                                            <option value="delivery">Giao hàng</option>
                                            <option value="product_quality">Chất lượng sản phẩm</option>
                                            <option value="payment">Thanh toán</option>
                                            <option value="customer_service">Dịch vụ khách hàng</option>
                                            <option value="other">Khác</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="report_content">Nội dung báo cáo <span class="text-danger">*</span></label>
                                        <textarea name="report_content" id="report_content" class="form-control" rows="4" required placeholder="Mô tả vấn đề bạn gặp phải..."></textarea>
                                    </div>
                                    <div class="text-end mt-2">
                                        <button type="submit" class="btn btn-warning">
                                            Gửi báo cáo
                                        </button>
                                    </div>
                                </form>
                            @endif
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
                             <th width="50%">Hình thức thanh toán: </th>
                             <td>{{ $order->payment_method }}</td> 
                         </tr>
                         <tr> 
                             <th width="50%">Mã hóa đơn: </th>
                             <td class="text-danger">{{ $order->invoice_no }}</td> 
                         </tr> 
                        <tr>
                            <th width="50%">Tổng đơn hàng:</th>
                            <td>{{ number_format($order->amount, 0, ',', '.') }} VNĐ</td>
                        </tr>
                        <tr>
                            <th width="50%">Phí vận chuyển:</th>
                            <td>{{ number_format($order->shipping_fee, 0, ',', '.') }} VNĐ</td>
                        </tr>
                         <tr> 
                             <th width="50%">Tổng thanh toán: </th>
                             <td>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td> 
                         </tr> 
                         <tr> 
                             <th width="50%">Trạng thái: </th>
                             <td>
                                @if ($order->status == 'pending')
                                <span class="badge bg-info">Chờ xử lý</span>
                                @elseif ($order->status == 'confirm')
                                <span class="badge bg-primary">Đã xác nhận</span>
                                @elseif ($order->status == 'processing')
                                <span class="badge bg-warning">Đang xử lý</span>
                                @elseif ($order->status == 'delivered')
                                <span class="badge bg-success">Đã giao hàng</span>
                                @elseif ($order->status == 'cancel_pending')
                                <span class="badge" style="background-color: #f66; color: white;">Đăng ký huỷ</span> {{-- Đỏ nhạt --}}
                                @elseif ($order->status == 'cancelled')
                                <span class="badge bg-danger">Hủy thành công</span>
                                @endif
                            </td> 
                         </tr>
              
                     </tbody>
                 </table>
             </div> 
                         </div>
                     </div>
                 </div> <!-- end col -->
             
              
             </div> <!-- end row --> 
             
             
             <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-1 mt-4">
                <div class="col">
                    <div class="card p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Cửa hàng</th>
                                        <th>Mã sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Trọng lượng</th>
                                        <th>Hạn sử dụng</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderItem as $item)
                                    <tr>
                                        <td>
                                            <img src="{{ asset($item->product->productTemplate->image) }}" style="width:50px; height:50px" alt="Product Image">
                                        </td>
                                        <td>
                                            {{ $item->product->productTemplate->name }}
                                        </td>
                                        <td>
                                            {{ $item->client_id == NULL ? 'Chính chủ' : $item->product->client->name }}
                                        </td>
                                        <td>
                                            {{ $item->product->productTemplate->code }}
                                        </td>
                                        <td>
                                            {{ $item->qty }}
                                        </td>
                                        <td>
                                            @if ($item->product->productTemplate->stock_mode == 'quantity')
                                                {{ $item->product->productTemplate->size }} {{ $item->product->productTemplate->unit }}
                                            @elseif ($item->product->productTemplate->stock_mode == 'unit')
                                                @if ($item->productUnit->weight) {{-- product->weight ở đây là từ product_units, nên cần đảm bảo mối quan hệ đúng --}}
                                                    {{ $item->productUnit->weight }} kg/{{ $item->product->productTemplate->unit }}
                                                @else
                                                    N/A
                                                @endif
                                            @else
                                                N/A 
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Hiển thị hạn sử dụng --}}
                                            @if ($item->productUnit->expiry_date)
                                            {{ \Carbon\Carbon::parse($item->productUnit->expiry_date)->format('d/m/Y') }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            {{ number_format($item->price, 0, ',', '.') }}
                                            <br>
                                            <small class="text-danger">
                                                Tổng: {{ number_format($item->price * $item->qty, 0, ',', '.') }} VNĐ
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach 
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <h6>Tổng cộng: {{ number_format($totalPrice, 0, ',', '.') }} VNĐ</h6>
                            <h6 class="text-danger">Phí vận chuyển: {{ number_format($order->shipping_fee, 0, ',', '.') }} VNĐ</h6>
                            <h4 class="text-success">Tổng thanh toán: {{ number_format($totalAmount, 0, ',', '.') }} VNĐ</h4>
                        </div>

                        @if (in_array($order->status, ['pending', 'confirm']))
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-danger" id="showCancelForm">
                                    <i class="bi bi-x-circle"></i> Huỷ đơn
                                </button>
                            </div>

                            <form id="cancelForm" action="{{ route('user.order.cancel', $order->id) }}" method="POST" style="display: none;" class="mt-3">
                                @csrf
                                <div class="form-group">
                                    <label for="cancel_reason">Lý do huỷ đơn <span class="text-danger">*</span></label>
                                    <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="3" required placeholder="Nhập lý do huỷ đơn..."></textarea>
                                </div>
                                <div class="text-end mt-2">
                                    <button type="submit" class="btn btn-danger">
                                        Xác nhận huỷ đơn
                                    </button>
                                </div>
                            </form>
                        @endif

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
 
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reportBtn = document.getElementById('btnReport');
        const reportForm = document.getElementById('reportForm');

        const cancelBtn = document.getElementById('showCancelForm');
        const cancelForm = document.getElementById('cancelForm');

        if (cancelBtn && cancelForm) {
            cancelBtn.addEventListener('click', function () {
                cancelForm.style.display = 'block';
                cancelBtn.style.display = 'none';
                document.getElementById('cancel_reason').focus();
            });
        }

        if (reportBtn && reportForm) {
            reportBtn.addEventListener('click', function () {
                reportForm.style.display = 'block';
                reportBtn.style.display = 'none';
                document.getElementById('report_content').focus();
            });
        }
    });

</script>

 @endsection