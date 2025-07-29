@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Data Customer</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Customers</li>
            </ul>
        </div>
        <div class="col-auto float-end ms-auto">
            <a href="#" class="btn btn-primary">
                <i class="fa fa-plus"></i> Tambah Customer
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Customer</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $index => $customer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $customer->nama }}</td>
                            <td>{{ $customer->email ?? '-' }}</td>
                            <td>{{ $customer->telepon ?? '-' }}</td>
                            <td>{{ $customer->alamat ?? '-' }}</td>
                        </tr>
                    @endforeach
                    @if($customers->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data customer.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
