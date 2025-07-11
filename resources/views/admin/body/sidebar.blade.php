<div class="vertical-menu">

  <div data-simplebar class="h-100">

      <!--- Sidemenu -->
      <div id="sidebar-menu">
          <!-- Left Menu Start -->
          <ul class="metismenu list-unstyled" id="side-menu">
              <li class="menu-title" data-key="t-menu">Menu</li>

              <li>
                  <a href="{{ route('admin.dashboard') }}">
                      <i data-feather="home"></i>
                      <span data-key="t-dashboard">Bảng thống kê</span>
                  </a>
              </li>

              {{-- @if (Auth::guard('admin')->user()->can('category.menu')) --}}
                  
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="grid"></i>
                        <span data-key="t-apps">Danh mục</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        {{-- @if (Auth::guard('admin')->user()->can('category.all')) --}}
                        <li>
                            <a href="{{ route('all.category') }}">
                                <span data-key="t-calendar">Tất cả danh mục</span>
                            </a>
                        </li>
                        {{-- @endif --}}

                        {{-- @if (Auth::guard('admin')->user()->can('category.add')) --}}
                        <li>
                            <a href="{{ route('add.category') }}">
                                <span data-key="t-chat">Thêm danh mục</span>
                            </a>
                        </li>
                        {{-- @endif --}}
                    </ul>
                </li>
                
              {{-- @else --}}
                  
              {{-- @endif --}}

              
              <li>
                <a href="javascript: void(0);" class="has-arrow">
                    <i data-feather="grid"></i>
                    <span data-key="t-apps">Menu</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    {{-- @if (Auth::guard('admin')->user()->can('category.all')) --}}
                    <li>
                        <a href="{{ route('all.menu') }}">
                            <span data-key="t-calendar">Tất cả Menu</span>
                        </a>
                    </li>
                    {{-- @endif --}}

                    {{-- @if (Auth::guard('admin')->user()->can('category.add')) --}}
                    <li>
                        <a href="{{ route('add.menu') }}">
                            <span data-key="t-chat">Thêm Menu</span>
                        </a>
                    </li>
                    {{-- @endif --}}
                </ul>
              </li>

              <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="grid"></i>
                        <span data-key="t-apps">Thành phố</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('all.city') }}">
                                <span data-key="t-calendar">Tất cả thành phố</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="grid"></i>
                        <span data-key="t-apps">Quản lý sản phẩm</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('admin.all.product') }}">
                                <span data-key="t-calendar">Tất cả sản phẩm</span>
                            </a>
                        </li>
  
                        <li>
                            <a href="{{ route('admin.add.product') }}">
                                <span data-key="t-chat">Thêm sản phẩm</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="grid"></i>
                        <span data-key="t-apps">Quản lý cửa hàng</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li>
                            <a href="{{ route('pending.market') }}">
                                <span data-key="t-calendar">Cửa hàng chờ duyệt</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('approve.market') }}">
                                <span data-key="t-chat">Cửa hàng đã duyệt</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rejected.market') }}">
                                <span data-key="t-ban">Cửa hàng từ chối duyệt</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('suspended.market') }}">
                                <span data-key="t-lock">Cửa hàng đã khóa</span>
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
                            <a href="{{ route('all.orders') }}">
                                <span data-key="t-calendar">Tất cả đơn hàng</span>
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
                            <a href="{{ route('admin.all.coupon') }}">
                                <span data-key="t-calendar">Tất cả mã giảm giá</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.add.coupon') }}">
                                <span data-key="t-chat">Thêm mã giảm giá</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title mt-2" data-key="t-components">Phần tử</li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="briefcase"></i>
                        <span data-key="t-components">Quản lý báo cáo</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.all.reports') }}" data-key="t-alerts">Tất cả báo cáo</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="briefcase"></i>
                        <span data-key="t-components">Quản lý thu chi</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.wallet.report') }}" data-key="t-alerts">Báo cáo thu chi</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="gift"></i>
                        <span data-key="t-ui-elements">Quản lý đánh giá cửa hàng</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.pending.review') }}" data-key="t-lightbox">Đánh giá chờ duyệt</a></li>
                            <li><a href="{{ route('admin.approve.review') }}" data-key="t-range-slider">Đánh giá đã duyệt</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="gift"></i>
                        <span data-key="t-ui-elements">Quản lý đánh giá sản phẩm</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.pending.product.review') }}" data-key="t-lightbox">Đánh giá chờ duyệt</a></li>
                            <li><a href="{{ route('admin.approve.product.review') }}" data-key="t-range-slider">Đánh giá đã duyệt</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="gift"></i>
                        <span data-key="t-ui-elements">Quản lý báo cáo vấn đề </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                            <li><a href="{{ route('admin.order.report') }}" data-key="t-range-slider">Báo cáo đơn hàng</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i data-feather="gift"></i>
                        <span data-key="t-ui-elements">Vai trò & Quyền hạn</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('all.permission') }}" data-key="t-lightbox">Tất cả quyền hạn</a></li>
                        <li><a href="{{ route('all.roles') }}" data-key="t-range-slider">Tất cả vai trò</a></li>
                        <li><a href="{{ route('add.roles.permission') }}" data-key="t-range-slider">Vai trò trong quyền hạn</a></li>
                        <li><a href="{{ route('all.roles.permission') }}" data-key="t-range-slider">Tất cả vai trò trong quyền hạn</a></li>
                    </ul>
                </li>
                
                <li>
                <a href="javascript: void(0);" class="has-arrow">
                    <i data-feather="gift"></i>
                    <span data-key="t-ui-elements">Quản lý Admin</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="{{ route('all.admin') }}" data-key="t-lightbox">Tất cả Admin</a></li>
                    <li><a href="{{ route('add.admin') }}" data-key="t-range-slider">Thêm Admin</a></li>
                   
                    
                </ul>
            </li>

          </ul>

      </div>
      <!-- Sidebar -->
  </div>
</div>