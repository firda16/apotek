@extends('admin.layouts.app')

@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
@endpush

@push('page-header')
<div class="col-sm-12">
    <h3 class="page-title">Tambah Pembelian</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li class="breadcrumb-item active">Tambah Pembelian</li>
    </ul>
</div>
@endpush

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body custom-edit-service">

                <form method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label>Pemasok <span class="text-danger">*</span></label>
                        <select class="select2 form-select form-control" name="supplier_id" required>
                            <option value="">-- Pilih Pemasok --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>

                    <h5 class="mb-3">Produk yang Dibeli</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="purchase-table">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Tanggal Kedaluwarsa</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-items">
                                <tr>
                                    <td>
                                        <select name="products[0][product_id]" class="form-control" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="products[0][category_id]" class="form-control" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="products[0][qty]" class="form-control" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="products[0][unit_price]" class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="date" name="products[0][expiry_date]" class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="file" name="products[0][image]" class="form-control" accept="image/*">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary btn-sm" id="add-row">+ Tambah Produk</button>
                    </div>

                    <div class="submit-section mt-4">
                        <button class="btn btn-primary submit-btn" type="submit">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>

<script>
    let i = 1;

    const productsOptions = `
        <option value="">-- Pilih Produk --</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    `;

    const categoriesOptions = `
        <option value="">-- Pilih Kategori --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    `;

    document.getElementById('add-row').addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="products[${i}][product_id]" class="form-control" required>
                    ${productsOptions}
                </select>
            </td>
            <td>
                <select name="products[${i}][category_id]" class="form-control" required>
                    ${categoriesOptions}
                </select>
            </td>
            <td>
                <input type="number" name="products[${i}][qty]" class="form-control" min="1" required>
            </td>
            <td>
                <input type="number" step="0.01" name="products[${i}][unit_price]" class="form-control" required>
            </td>
            <td>
                <input type="date" name="products[${i}][expiry_date]" class="form-control" required>
            </td>
            <td>
                <input type="file" name="products[${i}][image]" class="form-control" accept="image/*">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
            </td>
        `;
        document.getElementById('purchase-items').appendChild(newRow);
        i++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endpush
