{{-- filepath: d:\magang\apotek\resources\views\admin\customers\edit.blade.php --}}
@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ $customer->nama }}" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $customer->email }}">
            </div>
            <div class="mb-3">
                <label for="telepon" class="form-label">Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ $customer->telepon }}">
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" name="alamat" class="form-control" value="{{ $customer->alamat }}">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection