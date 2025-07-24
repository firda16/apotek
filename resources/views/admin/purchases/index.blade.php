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
                                    <th>Tanggal Pembelian</th>
                                    <th>Pemasok</th>
                                    <th>Pembayaran</th>
                                    <th>Item</th>
                                    {{-- <th>Jumlah Produk</th>
                                    <th>Gambar</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Harga Beli</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Kedaluwarsa</th>                                   --}}
                                    <th class="action-btn">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                             @foreach ($pembelians as $pembelian)
                             @foreach ($pembelian->purchaseItems as $item)
                             <tr>
                                 <td>{{ $pembelians->firstItem() + $loop->parent->index }}</td>          
                                 <td>{{ $pembelian->created_at ? date('d M, Y', strtotime($pembelian->created_at)) : '-' }}</td>
                                 <td>{{ $pembelian->supplier->name ?? '-' }}</td>
                                 <td>{{ $pembelian->payment_method ?? '-' }}</td>
                                 <td>
                                    <ul>
                                        <li>Nama produk: {{ $item->product->nama_produk ?? '-' }}</li>
                                        <li>Jumlah: {{ $item->quantity ?? '-' }}</li>
                                        <li>Harga: Rp {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}</li>
                                        <li>Subtotal: Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</li>
                                        <li>Kategori: {{ $item->product->category->name ?? '-' }}</li>                                        
                                        <li>Expired: {{ $item->expiry_date ? date('d M, Y', strtotime($item->expiry_date)) : '-' }}</li>
                                    </ul>
                                </td>
            {{-- <td>{{ $item->product->nama_produk ?? '-' }}</td>
            <td>
                @php
                    $basePath = public_path('assets/img/purchases/');
                    $baseUrl = asset('assets/img/purchases/');
                    $imageName = Str::slug($item->product->name ?? 'gambar'); // pakai relasi produk
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
            
            
            <td>{{ $item->product->category->name ?? '-' }}</td>
            <td>Rp {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}</td>
            <td>{{ $item->quantity ?? '-' }}</td>
            <td>{{ $pembelian->expiry_date ? date('d M, Y', strtotime($pembelian->expiry_date)) : '-' }}</td>     --}}
            <td>
                <a href="{{ route('purchases.edit', $pembelian->id) }}" class="editbtn">
                    <button class="btn btn-primary"><i class="fas fa-edit"></i></button>
                </a>
                <form action="{{ route('purchases.destroy', $pembelian->id) }}" method="POST" style="display:inline;">
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
@endforeach

                            </tbody>
                        </table>
                        {{-- Pagination --}}
                    </div>
                    <div class="mt-3">
                        {{-- {{ $categories->links() }} --}}
                        {{ $pembelians->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

{{-- @push('page-js')
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
@endpush --}}
