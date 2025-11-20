@extends('admin.layouts.plain')

@section('content')
<div class="container mt-5" style="max-width: 400px;">
    <h3 class="mb-4 text-center">Reset Password</h3>

    {{-- pesan error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- form reset password --}}
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ $email ?? old('email') }}" required autofocus>

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror" required>

            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Reset Password</button>
    </form>
</div>
@endsection
