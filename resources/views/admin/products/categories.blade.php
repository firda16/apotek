@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Kategori</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Beranda</a></li>
		<li class="breadcrumb-item active">Kategori</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="#add_categories" data-toggle="modal" class="btn btn-primary float-right mt-2">Tambah Kategori</a>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body">
				{{-- Form Pencarian --}}
				<form method="GET" action="{{ route('categories.index') }}" class="mb-3">
					<div class="input-group">
						<input type="text" name="search" class="form-control" placeholder="Cari kategori..." value="{{ request('search') }}">
						<div class="input-group-append">
							<button class="btn btn-primary" type="submit">Cari</button>
							@if(request('search'))
								<a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Reset</a>
							@endif
						</div>
					</div>
				</form>

				{{-- Tabel --}}
				<div class="table-responsive">
					<table id="category-table" class="table table-striped table-bordered table-hover table-center mb-0">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Kategori</th>
								<th>Tanggal Dibuat</th>
								<th class="text-center action-btn">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach($categories as $category)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>{{ $category->name }}</td>
									<td>{{ $category->created_at->format('d M, Y') }}</td>
									<td class="text-center">
										<a data-id="{{ $category->id }}" data-name="{{ $category->name }}" href="javascript:void(0)" class="editbtn">
											<button class="btn btn-primary"><i class="fas fa-edit"></i></button>
										</a>
										<a data-id="{{ $category->id }}" data-route="{{ route('categories.destroy',$category->id) }}" href="javascript:void(0)" id="deletebtn">
											<button class="btn btn-danger"><i class="fas fa-trash"></i></button>
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>

					{{-- Pagination --}}
					<div class="mt-3">
						{{ $categories->links() }}
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="add_categories" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Tambah Kategori</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('categories.store')}}">
					@csrf
					<div class="form-group">
						<label>Nama Kategori</label>
						<input type="text" name="name" class="form-control" required>
					</div>
					<button type="submit" class="btn btn-primary btn-block">Simpan</button>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="edit_category" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Kategori</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('categories.update') }}">
					@csrf
					<input type="hidden" name="id" id="edit_id">
					<div class="form-group">
						<label>Nama Kategori</label>
						<input type="text" class="form-control edit_name" name="name" required>
					</div>
					<button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@push('page-js')
<script>
    $(document).ready(function () {
        $('#category-table').on('click', '.editbtn', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#edit_id').val(id);
            $('.edit_name').val(name);

            $('#edit_category').modal('show');
        });
    });
</script>
@endpush
