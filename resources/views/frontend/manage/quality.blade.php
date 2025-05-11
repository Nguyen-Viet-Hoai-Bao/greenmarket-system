@extends('frontend.dashboard.dashboard')
@section('dashboard')

<div class="container my-4">
  <h1 class="mb-4 text-success">QUẢN LÝ CHẤT LƯỢNG</h1>
  <h4 class="text-primary">DANH SÁCH HỒ SƠ TỰ CÔNG BỐ SẢN PHẨM</h4>

  <div class="mt-3">
      <ol class="list-group list-group-numbered">
          @foreach ($products as $index => $product)
              <li class="list-group-item">{{ $product }}</li>
          @endforeach
      </ol>
  </div>
</div>

@endsection