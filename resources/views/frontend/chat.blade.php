@extends('frontend.dashboard.dashboard')
@section('dashboard')

    <h1>Form Chat</h1>

    <form method="POST" action="{{ url('/chat') }}">
        @csrf {{-- Thêm CSRF token vào form --}}

        <label for="message">Tin nhắn của bạn:</label><br>
        <input type="text" id="message" name="message" required><br><br>

        <button type="submit">Gửi</button>
    </form>

    @if(session('response'))
        <p><strong>Phản hồi:</strong> {{ session('response') }}</p>
    @endif

@endsection
    
