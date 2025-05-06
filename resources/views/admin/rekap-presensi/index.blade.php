<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi Pegawai - Dinas Koperasi & UMKM Kab. Pacitan</title>
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
                            <h3>Rekap Presensi Pegawai</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            @include('partials.current-time')
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Rekap Presensi</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Daftar Rekap Presensi Pegawai
                        </div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif
                            
                            <div class="table-responsive">
                                <table class="table table-striped" id="rekapTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pegawai</th>
                                            <th>Jumlah Presensi</th>
                                            <th>Jumlah Izin</th>
                                            <th>Jumlah Absen</th> <!-- Tambahan kolom -->
                                            <th>Total Hari Kerja</th>
                                            <th>Persentase Kehadiran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rekap as $index => $data)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $data['nama'] }}</td>
                                            <td>{{ $data['jumlah_presensi'] }}</td>
                                            <td>{{ $data['jumlah_izin'] }}</td>
                                            <td>{{ $data['jumlah_absen'] }}</td> <!-- Tampilkan jumlah absen -->
                                            <td>{{ $data['total_hari_kerja'] }}</td>
                                            <td>{{ $data['presentase'] }}%</td>
                                            <td>
                                                <a href="{{ route('admin.rekap-presensi.detail', $data['id']) }}" class="btn btn-primary btn-sm">Detail</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>                                                      

                        </div>
                    </div>
                </section>
            </div>
        </div>

        @include('partials.footer')
    </div>

    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
</body>
</html>
