<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <title>{{ config('app.name') }} - {{ ucfirst($title ?? '') }}</title> --}}
    <title> {{ $setting?->nama ?? 'Siapotik' }}</title>
    @if (!empty($setting?->favicon))
        <link rel="icon" type="image/png" href="{{ asset($setting->favicon) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feathericon.min.css') }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/snackbar/snackbar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('page-css')

</head>

<body>

    <div class="main-wrapper">

        @include('admin.includes.header')
        @include('admin.includes.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        @stack('page-header')
                    </div>
                </div>
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <x-alerts.danger :error="$error" />
                    @endforeach
                @endif
                @yield('content')
                <x-modals.add-sale />
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap4.min.js"></script>

    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/snackbar/snackbar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <script src="{{ asset('assets/js/script.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Logika untuk tombol hapus dengan konfirmasi SweetAlert
            $('body').on('click', '#deletebtn', function() {
                var id = $(this).data('id');
                var route = $(this).data('route');
                swal.queue([{
                    title: "Apa kamu yakin?",
                    text: "Tindakan ini tidak dapat dibatalkan!",
                    type: "peringatan",
                    showCancelButton: !0,
                    confirmButtonText: '<i class="fe fe-trash mr-1"></i> Hapus!',
                    cancelButtonText: '<i class="fa fa-times mr-1"></i> Batal!',
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: !1,
                    preConfirm: function() {
                        return new Promise(function() {
                            $.ajax({
                                url: route,
                                type: "DELETE",
                                data: {
                                    "id": id
                                },
                                success: function() {
                                    swal.insertQueueStep(
                                        Swal.fire({
                                            title: "Dihapus!",
                                            text: "Resource telah dihapus.",
                                            type: "success",
                                            showConfirmButton: !
                                                1,
                                            timer: 1500,
                                        })
                                    )
                                    $('.datatable').DataTable().ajax
                                        .reload();
                                }
                            })
                        })
                    }
                }]).catch(swal.noop);
            });
        });

        // Logika untuk menampilkan notifikasi (Snackbar) dari session Laravel
        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}";
            switch (type) {
                case 'info':
                    Snackbar.show({
                        text: "{{ Session::get('message') }}",
                        actionTextColor: '#fff',
                        backgroundColor: '#2196f3'
                    });
                    break;
                case 'warning':
                    Snackbar.show({
                        text: "{{ Session::get('message') }}",
                        pos: 'top-right',
                        actionTextColor: '#fff',
                        backgroundColor: '#e2a03f'
                    });
                    break;
                case 'success':
                    Snackbar.show({
                        text: "{{ Session::get('message') }}",
                        pos: 'top-right',
                        actionTextColor: '#fff',
                        backgroundColor: '#8dbf42'
                    });
                    break;
                case 'danger':
                    Snackbar.show({
                        text: "{{ Session::get('message') }}",
                        pos: 'top-right',
                        actionTextColor: '#fff',
                        backgroundColor: '#e7515a'
                    });
                    break;
            }
        @endif
    </script>

    @stack('page-js')
</body>

</html>
