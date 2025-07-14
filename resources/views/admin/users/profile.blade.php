@extends('admin.layouts.app')

@push('page-header')
<div class="col">
	<h3 class="page-title">Profil</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dasbor</a></li>
		<li class="breadcrumb-item active">Profil</li>
	</ul>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="profile-header">
			<div class="row align-items-center">
				<div class="col-auto profile-image">
					<a href="#">
						<img class="rounded-circle" alt="Foto Pengguna" src="{{!empty(auth()->user()->avatar) ? asset('storage/users/'.auth()->user()->avatar): asset('assets/img/avatar.png')}}">
					</a>
				</div>
				<div class="col ml-md-n2 profile-user-info">
					<h4 class="user-name mb-0">{{auth()->user()->name}}</h4>
					<h6 class="text-muted">{{auth()->user()->email}}</h6>
				</div>
			</div>
		</div>

		<div class="profile-menu">
			<ul class="nav nav-tabs nav-tabs-solid">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#per_details_tab">Tentang</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#password_tab">Kata Sandi</a>
				</li>
			</ul>
		</div>

		<div class="tab-content profile-tab-cont">

			<!-- Tab Detail Pribadi -->
			<div class="tab-pane fade show active" id="per_details_tab">
				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-body">
								<h5 class="card-title d-flex justify-content-between">
									<span>Detail Pribadi</span>
									<a class="edit-link" data-toggle="modal" href="#edit_personal_details"><i class="fa fa-edit mr-1"></i>Edit</a>
								</h5>

								<div class="row">
									<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Nama</p>
									<p class="col-sm-10">{{auth()->user()->name}}</p>
								</div>

								<div class="row">
									<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Email</p>
									<p class="col-sm-10">{{auth()->user()->email}}</p>
								</div>

								<div class="row">
									<p class="col-sm-2 text-muted text-sm-right mb-0 mb-sm-3">Peran</p>
									<p class="col-sm-10">
										@foreach (auth()->user()->getRoleNames() as $role)
											{{$role}}
										@endforeach
									</p>
								</div>

							</div>
						</div>

						<!-- Modal Edit Detail -->
						<div class="modal fade" id="edit_personal_details" aria-hidden="true" role="dialog">
							<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Ubah Detail Pribadi</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<form method="POST" enctype="multipart/form-data" action="{{route('profile.update',auth()->user())}}">
											@csrf
											<div class="row form-row">
												<div class="col-12">
													<div class="form-group">
														<label>Nama Lengkap</label>
														<input class="form-control" name="name" type="text" value="{{auth()->user()->name}}" placeholder="Nama Lengkap">
													</div>
												</div>
												<div class="col-12">
													<div class="form-group">
														<label>Email</label>
														<input class="form-control" name="email" type="text" value="{{auth()->user()->email}}" placeholder="Email">
													</div>
												</div>
												@can('edit-role')
												<div class="col-12">
													<div class="form-group">
														<label>Peran</label>
														<select class="form-control select edit_role" name="role">
															@foreach ($roles as $role)
																<option value="{{$role->name}}">{{$role->name}}</option>
															@endforeach
														</select>
													</div>
												</div>
												@endcan
												<div class="col-12">
													<div class="form-group">
														<label>Foto Pengguna</label>
														<input type="file" class="form-control" name="avatar">
													</div>
												</div>
											</div>
											<button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
										</form>
									</div>
								</div>
							</div>
						</div>
						<!-- /Modal Edit -->

					</div>
				</div>
			</div>
			<!-- /Tab Detail Pribadi -->

			<!-- Tab Kata Sandi -->
			<div id="password_tab" class="tab-pane fade">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Ubah Kata Sandi</h5>
						<div class="row">
							<div class="col-md-10 col-lg-12">
								<form method="POST" action="{{route('update-password',auth()->user())}}">
									@csrf
									@method("PUT")
									<div class="form-group">
										<label>Kata Sandi Saat Ini</label>
										<input type="password" name="current_password" class="form-control" placeholder="Masukkan kata sandi lama Anda">
									</div>
									<div class="form-group">
										<label>Kata Sandi Baru</label>
										<input type="password" name="password" class="form-control" placeholder="Masukkan kata sandi baru">
									</div>
									<div class="form-group">
										<label>Ulangi Kata Sandi</label>
										<input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
									</div>
									<button class="btn btn-primary" type="submit">Simpan Perubahan</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /Tab Kata Sandi -->

		</div>
	</div>
</div>
@endsection
