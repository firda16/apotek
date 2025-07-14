@extends('admin.layouts.app')

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Pengguna</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dasbor</a></li>
		<li class="breadcrumb-item active">Daftar Pengguna</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="{{route('users.create')}}" class="btn btn-primary float-right mt-2">Tambah Pengguna</a>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover table-center mb-0">
						<thead>
							<tr>
								<th>Nama</th>
								<th>Email</th>
								<th>Peran</th>
								<th>Foto</th>
								<th>Tanggal Dibuat</th>
								<th class="text-center action-btn">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($users as $user)
							<tr>
								<td>{{ $user->name }}</td>
								<td>{{ $user->email }}</td>
								<td>{{ $user->role }}</td>
								<td>
									@if ($user->avatar)
										<img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="Avatar" width="40" height="40">
									@else
										<span class="text-muted">-</span>
									@endif
								</td>
								<td>{{ $user->created_at->format('d M Y') }}</td>
								<td class="text-center">
									<a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
									<form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
											<i class="fas fa-trash"></i>
										</button>
									</form>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					{{-- Pagination jika datanya banyak --}}
					<div class="mt-3">
						{{ $users->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
