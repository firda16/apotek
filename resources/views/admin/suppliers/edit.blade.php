@extends('admin.layouts.app')

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Edit Pemasok</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Edit Pemasok</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body custom-edit-service">

                    <!-- Edit Pemasok -->
                    <form method="post" enctype="multipart/form-data" action="{{ route('suppliers.update', $supplier) }}">
                        @csrf
                        @method('PUT')
                        <div class="service-fields mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Nama<span class="text-danger">*</span></label>
                                        <input class="form-control" type="text"
                                            value="{{ $supplier->name ?? old('name') }}" name="name">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label>Email<span class="text-danger">*</span></label>
                                    <input class="form-control" type="text"
                                        value="{{ $supplier->email ?? old('email') }}" name="email">
                                </div>
                            </div>
                        </div>

                        <div class="service-fields mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>No. Telepon<span class="text-danger">*</span></label>
                                        {{-- <input class="form-control" type="number"
                                            value="{{ $supplier->phone ?? old('phone') }}" name="phone"> --}}
                                        <input class="form-control @error('phone') is-invalid @enderror" id="phone"
                                            type="tel" name="phone" value="{{ old('phone', $supplier->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div id="phone-warning" class="text-danger d-none">
                                            Nomor telepon ini sudah terdaftar, silakan gunakan nomor lain.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label>Perusahaan<span class="text-danger">*</span></label>
                                    <input class="form-control" type="text"
                                        value="{{ $supplier->company ?? old('company') }}" name="company">
                                </div>
                            </div>
                        </div>

                        <div class="service-fields mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Alamat <span class="text-danger">*</span></label>
                                        <input type="text" name="address"
                                            value="{{ $supplier->address ?? old('address') }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit" name="form_submit"
                                value="submit">Kirim</button>
                        </div>
                    </form>
                    <!-- /Edit Pemasok -->

                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#phone').on('keyup change', function() {
                let phone = $(this).val();
                if (phone.length >= 10) {
                    $.ajax({
                        url: "{{ route('suppliers.checkPhone') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            phone: phone
                        },
                        success: function(res) {
                            if (res.exists) {
                                $('#phone-warning').removeClass('d-none');
                            } else {
                                $('#phone-warning').addClass('d-none');
                            }
                        }
                    });
                } else {
                    $('#phone-warning').addClass('d-none');
                }
            });
        });
    </script>
@endpush
