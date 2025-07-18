@extends('admin.layouts.plain')

@section('content')
<h1>Lupa Kata Sandi?</h1>
<p class="account-subtitle">Masukkan email Anda untuk mendapatkan tautan pengaturan ulang kata sandi</p>
<!-- Form -->
<form action="{{route('password.request')}}" method="post">
	@csrf
	<div class="form-group">
		<input class="form-control" name="email" type="text" placeholder="Email">
	</div>
	<div class="form-group mb-0">
		<button class="btn btn-primary btn-block" type="submit">Kirim</button>
	</div>
</form>
<!-- /Form -->

<div class="text-center dont-have">Ingat kata sandi Anda? <a href="{{route('login')}}">Login</a></div>
@endsection
