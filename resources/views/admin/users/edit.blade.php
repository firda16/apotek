@extends('admin.layouts.app')

@push('page-css')

@endpush

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Ubah Pengguna</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item active">Dasbor</li>
	</ul>
</div>
@endpush

@section('content')

<div class="row">
    <div class="col-md-12 col-lg-12">

        <div class="card card-table">
            <div class="card-header">
                <h4 class="card-title">Edit Pengguna</h4>
            </div>
            <div class="card-body">
                <div class="p-5">
                    <form method="POST" enctype="multipart/form-data" action="{{route('users.update',$user->id)}}">
                        @csrf
                        @method("PUT")
                        <div class="row form-row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{$user->name}}" placeholder="Contoh: John Doe">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{$user->email}}" placeholder="contoh@email.com">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Peran</label>
                                    <div class="form-group">
                                      <select class="select2 form-select form-control" name="role">
    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
    <option value="kasir" {{ $user->role === 'kasir' ? 'selected' : '' }}>Kasir</option>
</select>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Foto</label>
                                    <input type="file" name="avatar" class="form-control">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Kata Sandi Baru (Opsional)</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Konfirmasi Kata Sandi</label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('page-js')

@endpush
