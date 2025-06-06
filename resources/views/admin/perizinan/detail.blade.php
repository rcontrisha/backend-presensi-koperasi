<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekap Izin - {{ $user->name }} - Dinas Koperasi & UMKM Kab. Pacitan</title>
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
                            <h3>Detail Izin - {{ $user->name }}</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            @include('partials.current-time')
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.rekap-izin') }}">Rekap Izin</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Daftar Izin Bulan Ini
                        </div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex">
                                    <div class="form-group mr-2">
                                        <label for="bulan" class="mr-2">Bulan</label>
                                        <select name="bulan" id="bulan" class="form-control">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ request('bulan', now()->month) == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mr-2">
                                        <label for="tahun" class="mr-2">Tahun</label>
                                        <select name="tahun" id="tahun" class="form-control">
                                            @for ($year = now()->year; $year >= now()->year - 5; $year--)
                                                <option value="{{ $year }}" {{ request('tahun', now()->year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <a href="{{ route('admin.rekap-izin.cetak-pdf', ['id' => $user->id, 'bulan' => request('bulan', now()->month), 'tahun' => request('tahun', now()->year)]) }}" target="_blank" class="btn btn-warning ml-3">
                                    Cetak PDF
                                </a>
                            </div>

                            <div class="table-responsive" id="izin-table-container">
                                @include('admin.perizinan.table', ['izins' => $izins])
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.rekap-izin') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>

        @include('partials.footer')
    </div>

    <script>
        $(document).ready(function() {
            // Trigger AJAX on change of 'bulan' or 'tahun' dropdown
            $('#bulan, #tahun').change(function() {
                var bulan = $('#bulan').val();
                var tahun = $('#tahun').val();
                
                $.ajax({
                    url: '{{ route("admin.rekap-izin.detail", $user->id) }}',
                    method: 'GET',
                    data: {
                        bulan: bulan,
                        tahun: tahun
                    },
                    success: function(response) {
                        $('#izin-table-container').html(response);
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
