@extends('admin.layouts.plain') {{-- sesuaikan dengan layout admin kamu --}}

@section('content')
<div class="container mt-5" style="max-width: 400px;">
    <h3 class="mb-4 text-center">Lupa Password</h3>

    {{-- pesan sukses --}}
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- form lupa password --}}
    <form method="POST" action="{{ url('forgot-password') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" required autofocus>

            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
    </form>
</div>
@endsection
