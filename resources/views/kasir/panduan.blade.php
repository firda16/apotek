@extends('kasir.layouts.app')

{{-- <x-assets.datatables /> --}}

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Panduan Kasir</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <iframe src="{{ $pdfPath }}" width="100%" height="800px" style="border:none;"></iframe>
        </div>
    </div>
@endsection
