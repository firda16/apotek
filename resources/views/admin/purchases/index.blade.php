@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Pembelian</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Pembelian</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="{{ route('purchases.create') }}" class="btn btn-primary float-right mt-2">Tambah Baru</a>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            {{-- Form Pencarian --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('purchases.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari nama obat, kategori, atau supplier..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Cari</button>
                                @if (request('search'))
                                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Hapus
                                        Pencarian</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Pembelian --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="purchase-table" class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Pemasok</th>
                                    <th>Harga Beli</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Kedaluwarsa</th>
                                    <th class="action-btn">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembelians as $pembelian)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @php
                                                $basePath = public_path('assets/img/purchases/');
                                                $baseUrl = asset('assets/img/purchases/');
                                                $imageName = Str::slug($pembelian->product);
                                                $extensions = ['jpg', 'jpeg', 'png'];
                                                $foundImage = null;

                                                foreach ($extensions as $ext) {
                                                    if (file_exists($basePath . $imageName . '.' . $ext)) {
                                                        $foundImage = $imageName . '.' . $ext;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            @if ($foundImage)
                                                <img src="{{ $baseUrl . '/' . $foundImage }}" width="100">
                                            @else
                                                <span style="color:red">Gambar tidak ditemukan</span>
                                            @endif
                                        </td>
                                        <td>{{ $pembelian->product }}</td>
                                        <td>{{ $pembelian->category->name ?? '-' }}</td>
                                        <td>{{ $pembelian->supplier->name ?? '-' }}</td>
                                        <td class="text-center">Rp {{ number_format($pembelian->cost_price, 0, ',', '.') }}
                                        </td>
                                        <td>{{ $pembelian->quantity }}</td>
                                        <td>{{ date_format(date_create($pembelian->expiry_date), 'd M, Y') }}</td>
                                        <td>
                                            <a href="{{ route('purchases.edit', $pembelian->id) }}" class="editbtn">
                                                <button class="btn btn-primary"><i class="fas fa-edit"></i></button>
                                            </a>
                                            <form action="{{ route('purchases.destroy', $pembelian->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('page-js')
    <script>
        $(document).ready(function() {
            $('#purchase-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('purchases.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'cost_price',
                        name: 'cost_price'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
        });
    </script>
@endpush
