@extends('frontend.dashboard.dashboard')
@section('dashboard')

<div class="container mt-5">
    <h1 class="text-center text-success mb-4">Danh sách cửa hàng Green Food</h1>

    <div class="row">
        @foreach($markets as $market)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">{{ $market['name'] }}</h5>
                    <p class="card-text"><strong>Địa chỉ:</strong> {{ $market['address'] }}</p>
                    <p class="card-text"><strong>Điện thoại:</strong> {{ $market['phone'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
