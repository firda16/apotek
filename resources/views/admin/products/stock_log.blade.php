@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3>Log Stok Produk: {{ $product->name }}</h3>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal Expired</th>
                <th>Jumlah Dibeli</th>
                <th>Sudah Terjual</th>
                <th>Sisa Stok (FIFO)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($batches as $i => $batch)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $batch->expiry_date }}</td>
                    <td>{{ $batch->quantity }}</td>
                    <td>{{ $batch->sold_quantity }}</td>
                    <td>{{ $batch->quantity - $batch->sold_quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
