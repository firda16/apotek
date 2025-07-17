@extends('admin.layouts.app')

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">{{ $title }}</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
		<li class="breadcrumb-item active">Riwayat</li>
	</ul>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<h4>Riwayat Transaksi</h4>

				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead class="thead-light">
							<tr>
								<th>No</th>
								<th>Tanggal</th>
								<th>Jenis Transaksi</th>
								<th>Nama</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							@php $no = 1; @endphp

							@foreach($purchases as $item)
								<tr>
									<td>{{ $no++ }}</td>
									<td>{{ $item->created_at->format('d M Y') }}</td>
									<td><span class="badge badge-info">Pembelian</span></td>
									<td>{{ $item->supplier->name ?? '-' }}</td>
									<td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>
								</tr>
							@endforeach

							@foreach($sales as $item)
								<tr>
									<td>{{ $no++ }}</td>
									<td>{{ $item->created_at->format('d M Y') }}</td>
									<td><span class="badge badge-success">Penjualan</span></td>
									<td>{{ $item->customer_name ?? '-' }}</td>
									<td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>
								</tr>
							@endforeach

							@if($purchases->isEmpty() && $sales->isEmpty())
								<tr>
									<td colspan="5" class="text-center">Tidak ada data transaksi.</td>
								</tr>
							@endif
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection
