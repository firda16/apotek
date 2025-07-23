@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')

@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Pemasok</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Beranda</a></li>
		<li class="breadcrumb-item active">Pemasok</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="{{route('suppliers.create')}}" class="btn btn-primary float-right mt-2">Tambah Baru</a>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">

		<!-- Daftar Pemasok -->
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table id="supplier-table" class="datatable table table-hover table-center mb-0">
						<thead>
							<tr>
								<th>No</th>							
								<th>Nama</th>
								<th>Telepon</th>
								<th>Email</th>
								<th>Alamat</th>
								<th>Perusahaan</th>
								<th class="action-btn">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($suppliers as $supplier)
							<tr>
								<td>{{ $suppliers->firstItem() + $loop->index }}</td>								
								<td>{{$supplier->name}}</td>
								<td>{{$supplier->phone}}</td>
								<td>{{$supplier->email}}</td>
								<td>{{$supplier->address}}</td>
								<td>{{$supplier->company}}</td>
								<td>
									<div class="actions">
										<a class="btn btn-sm bg-success-light" href="{{route('suppliers.edit',$supplier)}}">
											<i class="fe fe-pencil"></i> Edit
										</a>
										<form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline;">
											@csrf
											@method('DELETE')											
											<button type="submit" class="btn btn-sm bg-danger-light deletebtn" onclick="return confirm('Yakin ingin menghapus pemasok ini?')">
												<i class="fe fe-trash"></i> Hapus
											</button>
										</form>
									</div>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="mt-3">                        
					{{ $suppliers->links('pagination::bootstrap-5') }}
				</div>
			</div>
		</div>
		<!-- /Daftar Pemasok -->

	</div>
</div>

@endsection

{{-- @push('page-js')
<script>
    $(document).ready(function() {
        var table = $('#supplier-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('suppliers.index')}}",
            columns: [
                {data: 'product', name: 'product'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'address', name: 'address'},
                {data: 'company', name: 'company'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

    });
</script>
@endpush --}}
