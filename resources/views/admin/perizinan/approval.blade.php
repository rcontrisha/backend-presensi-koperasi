<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Izin - Dinas Koperasi & UMKM Kab. Pacitan</title>
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    @include('partials.admin-sidebar')

    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>

    <div id="app">
        @include('partials.topbar')

        <div id="main">
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Approval Pengajuan Izin</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            @include('partials.current-time')
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.rekap-izin') }}">Rekap Izin</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Approval</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Daftar Pengajuan Izin Menunggu Approval
                        </div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif

                            <!-- Tabel Daftar Pengajuan Izin -->
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Nama Pegawai</th>
                                        <th scope="col">Jenis Izin</th>
                                        <th scope="col">Tanggal Mulai</th>
                                        <th scope="col">Tanggal Selesai</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($izinList as $izin)
                                    <tr>
                                        <td>{{ $izin->user->name }}</td>
                                        <td>{{ $izin->jenis_izin }}</td>
                                        <td>{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d-m-Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d-m-Y') }}</td>
                                        <td>
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#reviewModal{{ $izin->id }}">Review</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-3">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>

        @include('partials.footer')
    </div>

    <!-- Modal untuk Detail Pengajuan Izin -->
    @foreach ($izinList as $izin)
    <div class="modal fade" id="reviewModal{{ $izin->id }}" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Detail Pengajuan Izin - {{ $izin->user->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama Pegawai:</strong> {{ $izin->user->name }}</p>
                    <p><strong>Jenis Izin:</strong> {{ $izin->jenis_izin }}</p>
                    <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d-m-Y') }}</p>
                    <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d-m-Y') }}</p>
                    <p><strong>Keterangan:</strong> {{ $izin->keterangan }}</p>

                    @if ($izin->file_pendukung)
                    <p><strong>File Pendukung:</strong> <a href="{{ asset('storage/' . $izin->file_pendukung) }}" target="_blank">Lihat File</a></p>
                    @else
                    <p><strong>File Pendukung:</strong> Tidak ada</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.perizinan.approve', $izin->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Terima</button>
                    </form>
                    <form action="{{ route('admin.perizinan.reject', $izin->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script>
        $(document).ready(function() {
            // AJAX for dynamic content (optional if required)
            $('#bulan, #tahun').change(function() {
                var bulan = $('#bulan').val();
                var tahun = $('#tahun').val();

                $.ajax({
                    url: '{{ route("admin.perizinan.approval") }}',
                    method: 'GET',
                    data: {
                        bulan: bulan,
                        tahun: tahun
                    },
                    success: function(response) {
                        $('#izin-card-container').html(response);
                    }
                });
            });
        });
    </script>

    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
</body>
</html>
