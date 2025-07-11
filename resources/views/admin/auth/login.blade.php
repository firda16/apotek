@extends('admin.layouts.plain')

@section('content')
<h1>Masuk</h1>
<p class="account-subtitle">Akses ke dasbor sistem</p>

@if (session('login_error'))
    <x-alerts.danger :error="session('login_error')" />
@endif

<!-- Form -->
<form action="{{ route('login') }}" method="post">
    @csrf
    <div class="form-group">
        <input class="form-control" name="email" type="text" placeholder="Email">
    </div>
    <div class="form-group">
        <input class="form-control" name="password" type="password" placeholder="Kata Sandi">
    </div>
    <div class="form-group">
        <button class="btn btn-primary btn-block" type="submit">Masuk</button>
    </div>
</form>
<!-- /Form -->

<div class="text-center forgotpass">
    <a href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
</div>
<div class="text-center dont-have">
    Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
</div>
@endsection
