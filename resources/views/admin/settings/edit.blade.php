@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Edit Identitas Website</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-3">
            <label for="nama">Nama Website</label>
            <input type="text" name="nama" id="nama" class="form-control" 
                   value="{{ old('nama', $setting->nama ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="alamat">Alamat</label>
            <input type="text" name="alamat" id="alamat" class="form-control" 
                   value="{{ old('alamat', $setting->alamat ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label for="telepon">Telepon</label>
            <input type="text" name="telepon" id="telepon" class="form-control" 
                   value="{{ old('telepon', $setting->telepon ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" 
                   value="{{ old('email', $setting->email ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label for="image">Logo / Gambar</label>
            @if(!empty($setting->image))
                <div class="mb-2">
                    <img src="{{ asset($setting->image) }}" alt="Logo" width="120">
                </div>
            @endif
            <input type="file" name="image" id="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
