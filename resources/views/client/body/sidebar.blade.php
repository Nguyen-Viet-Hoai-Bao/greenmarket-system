@php
    $id = Auth::guard('client')->id();
    $client = App\Models\Client::find($id);
    $status = $client->status;
@endphp

<div class="vertical-menu">

  <div data-simplebar class="h-100">

      <!--- Sidemenu -->
      <div id="sidebar-menu">
          <!-- Left Menu Start -->
          <ul class="metismenu list-unstyled" id="side-menu">
              <li class="menu-title" data-key="t-menu">Danh mục</li>

              <li>
                  <a href="{{ route('client.dashboard') }}">
                      <i data-feather="home"></i>
                      <span data-key="t-dashboard">Bảng thống kê</span>
                  </a>
              </li>
              @if ($status === '1')

                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="grid"></i>
                            <span data-key="t-apps">Sản phẩm</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('all.product') }}">
                                    <span data-key="t-calendar">Tất cả sản phẩm</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('add.product') }}">
                                    <span data-key="t-chat">Thêm sản phẩm</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="grid"></i>
                            <span data-key="t-apps">Quản lý đơn hàng</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('confirm.order') }}">
                                    <span data-key="t-chat">Đơn hàng đã xác nhận</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('processing.order') }}">
                                    <span data-key="t-chat">Đơn hàng đang xử lý</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('delivered.order') }}">
                                    <span data-key="t-chat">Đơn hàng đã giao</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cancel.pending.order') }}">
                                    <span data-key="t-chat">Đơn hàng đăng ký HỦY</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cancelled.order') }}">
                                    <span data-key="t-chat">Đơn hàng đã hủy</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="grid"></i>
                            <span data-key="t-apps">Thư viện ảnh</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('all.gallery') }}">
                                    <span data-key="t-calendar">Tất cả ảnh</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('add.gallery') }}">
                                    <span data-key="t-chat">Thêm ảnh</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="grid"></i>
                            <span data-key="t-apps">Mã giảm giá</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('all.coupon') }}">
                                    <span data-key="t-calendar">Tất cả mã giảm giá</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('add.coupon') }}">
                                    <span data-key="t-chat">Thêm mã giảm giá</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="menu-title mt-2" data-key="t-components">Thành phần</li>

                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="briefcase"></i>
                            <span data-key="t-components">Quản lý báo cáo</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('client.all.reports') }}" data-key="t-alerts">Tất cả báo cáo</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);" class="has-arrow">
                            <i data-feather="grid"></i>
                            <span data-key="t-apps">Quản lý đánh giá</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li>
                                <a href="{{ route('client.all.reviews') }}">
                                    <span data-key="t-calendar">Tất cả đánh giá</span>
                                </a>
                            </li> 
                        </ul>
                    </li>

                @else 

                @endif  

          </ul>
      </div>
      <!-- Sidebar -->
  </div>
</div>