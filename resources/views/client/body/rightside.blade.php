<div class="right-bar">
  <div data-simplebar class="h-100">
      <div class="rightbar-title d-flex align-items-center p-3">

          <h5 class="m-0 me-2">Tùy chỉnh giao diện</h5>

          <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
              <i class="mdi mdi-close noti-icon"></i>
          </a>
      </div>

      <!-- Settings -->
      <hr class="m-0" />

      <div class="p-4">
          <h6 class="mb-3">Bố cục</h6>
          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout"
                  id="layout-vertical" value="vertical">
              <label class="form-check-label" for="layout-vertical">Dọc</label>
          </div>
          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout"
                  id="layout-horizontal" value="horizontal">
              <label class="form-check-label" for="layout-horizontal">Ngang</label>
          </div>

          <h6 class="mt-4 mb-3 pt-2">Chế độ hiển thị</h6>

          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout-mode"
                  id="layout-mode-light" value="light">
              <label class="form-check-label" for="layout-mode-light">Sáng</label>
          </div>
          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout-mode"
                  id="layout-mode-dark" value="dark">
              <label class="form-check-label" for="layout-mode-dark">Tối</label>
          </div>

          <h6 class="mt-4 mb-3 pt-2">Chiều rộng bố cục</h6>

          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout-width"
                  id="layout-width-fuild" value="fuild" onchange="document.body.setAttribute('data-layout-size', 'fluid')">
              <label class="form-check-label" for="layout-width-fuild">Đầy đủ</label>
          </div>
          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout-width"
                  id="layout-width-boxed" value="boxed" onchange="document.body.setAttribute('data-layout-size', 'boxed')">
              <label class="form-check-label" for="layout-width-boxed">Hộp</label>
          </div>

          <h6 class="mt-4 mb-3 pt-2">Vị trí bố cục</h6>

          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout-position"
                  id="layout-position-fixed" value="fixed" onchange="document.body.setAttribute('data-layout-scrollable', 'false')">
              <label class="form-check-label" for="layout-position-fixed">Cố định</label>
          </div>
          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout-position"
                  id="layout-position-scrollable" value="scrollable" onchange="document.body.setAttribute('data-layout-scrollable', 'true')">
              <label class="form-check-label" for="layout-position-scrollable">Cuộn được</label>
          </div>

          <h6 class="mt-4 mb-3 pt-2">Màu thanh trên</h6>

          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="topbar-color"
                  id="topbar-color-light" value="light" onchange="document.body.setAttribute('data-topbar', 'light')">
              <label class="form-check-label" for="topbar-color-light">Sáng</label>
          </div>
          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="topbar-color"
                  id="topbar-color-dark" value="dark" onchange="document.body.setAttribute('data-topbar', 'dark')">
              <label class="form-check-label" for="topbar-color-dark">Tối</label>
          </div>

          <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Kích thước thanh bên</h6>

          <div class="form-check sidebar-setting">
              <input class="form-check-input" type="radio" name="sidebar-size"
                  id="sidebar-size-default" value="default" onchange="document.body.setAttribute('data-sidebar-size', 'lg')">
              <label class="form-check-label" for="sidebar-size-default">Mặc định</label>
          </div>
          <div class="form-check sidebar-setting">
              <input class="form-check-input" type="radio" name="sidebar-size"
                  id="sidebar-size-compact" value="compact" onchange="document.body.setAttribute('data-sidebar-size', 'md')">
              <label class="form-check-label" for="sidebar-size-compact">Thu gọn</label>
          </div>
          <div class="form-check sidebar-setting">
              <input class="form-check-input" type="radio" name="sidebar-size"
                  id="sidebar-size-small" value="small" onchange="document.body.setAttribute('data-sidebar-size', 'sm')">
              <label class="form-check-label" for="sidebar-size-small">Nhỏ (Biểu tượng)</label>
          </div>

          <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Màu thanh bên</h6>

          <div class="form-check sidebar-setting">
              <input class="form-check-input" type="radio" name="sidebar-color"
                  id="sidebar-color-light" value="light" onchange="document.body.setAttribute('data-sidebar', 'light')">
              <label class="form-check-label" for="sidebar-color-light">Sáng</label>
          </div>
          <div class="form-check sidebar-setting">
              <input class="form-check-input" type="radio" name="sidebar-color"
                  id="sidebar-color-dark" value="dark" onchange="document.body.setAttribute('data-sidebar', 'dark')">
              <label class="form-check-label" for="sidebar-color-dark">Tối</label>
          </div>
          <div class="form-check sidebar-setting">
              <input class="form-check-input" type="radio" name="sidebar-color"
                  id="sidebar-color-brand" value="brand" onchange="document.body.setAttribute('data-sidebar', 'brand')">
              <label class="form-check-label" for="sidebar-color-brand">Thương hiệu</label>
          </div>

          <h6 class="mt-4 mb-3 pt-2">Direction</h6>

          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout-direction"
                  id="layout-direction-ltr" value="ltr">
              <label class="form-check-label" for="layout-direction-ltr">LTR</label>
          </div>
          <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="layout-direction"
                  id="layout-direction-rtl" value="rtl">
              <label class="form-check-label" for="layout-direction-rtl">RTL</label>
          </div>

      </div>

  </div> <!-- end slimscroll-menu-->
</div>