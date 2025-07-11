@extends('admin.layouts.plain')

@section('content')
<h1>Daftar</h1>
<p class="account-subtitle">Buat akun untuk mengakses dasbor</p>

<!-- Form -->
<form action="{{ route('register') }}" method="POST">
	@csrf
	<div class="form-group">
		<input class="form-control" name="name" type="text" value="{{ old('name') }}" placeholder="Nama Lengkap">
	</div>
	<div class="form-group">
		<input class="form-control" name="email" type="text" value="{{ old('email') }}" placeholder="Email">
	</div>
	<div class="form-group">
		<input class="form-control" name="password" type="password" placeholder="Kata Sandi">
	</div>
	<div class="form-group">
		<input class="form-control" name="password_confirmation" type="password" placeholder="Konfirmasi Kata Sandi">
	</div>
	<div class="form-group mb-0">
		<button class="btn btn-primary btn-block" type="submit">Daftar</button>
	</div>
</form>
<!-- /Form -->

<div class="text-center dont-have">
	Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
</div>
@endsection
