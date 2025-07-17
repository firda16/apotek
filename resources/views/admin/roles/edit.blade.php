@extends('admin.layouts.app')

@push('page-css')

@endpush

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Ubah Peran</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item active">Beranda</li>
	</ul>
</div>
@endpush

@section('content')

<div class="row">
    <div class="col-md-12 col-lg-12">

        <div class="card card-table">
            <div class="card-header">
                <h4 class="card-title ">Edit Peran</h4>
            </div>
            <div class="card-body">
                <div class="p-5">
                    <form method="POST" action="{{route('roles.update',$role)}}">
                        @csrf
                        @method("PUT")
                        <div class="form-group">
                            <label>Nama Peran</label>
                            <input type="text" name="role" value="{{$role->name}}" class="form-control" placeholder="super-admin">
                        </div>
                        <div class="form-group">
                            <lable>Pilih Hak Akses</lable>
                            <select class="select2 form-select form-control" name="permission[]" multiple="multiple">
                                @foreach ($permissions as $permission)
                                    <option value="{{$permission->name}}">{{$permission->name}}</option>
                                @endforeach
                                @foreach ($permissions as $permission)
                                    <option {{$role->hasPermissionTo($permission->name) ? 'selected': ''}}>{{$permission->name}}</option>
                                @endforeach
                            </select>
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
