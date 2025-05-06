<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Peringatan - Dinas Koperasi & UMKM Kab. Pacitan</title>

    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
                            <h3>Surat Peringatan</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            @include('partials.current-time')
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Surat Peringatan</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">Daftar Pegawai</div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped" id="pegawaiTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>E-Mail</th>
                                            <th>No. Telp</th>
                                            <th>Jabatan</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Status SP</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pegawaiList as $index => $pegawai)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $pegawai->user->name ?? '-' }}</td>
                                                <td>{{ $pegawai->user->email ?? '-' }}</td>
                                                <td>{{ $pegawai->no_telpon ?? '-' }}</td>
                                                <td>{{ $pegawai->jabatan }}</td>
                                                <td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                                <td>
                                                    @php
                                                        $jumlahSp = $pegawai->peringatan->count();
                                                    @endphp
                                                
                                                    @if ($jumlahSp === 0)
                                                        <span class="badge badge-success">Belum Pernah</span>
                                                    @elseif ($jumlahSp === 1)
                                                        <span class="badge badge-warning">SP 1</span>
                                                    @elseif ($jumlahSp === 2)
                                                        <span class="badge badge-danger">SP 2</span>
                                                    @endif
                                                </td>                                                
                                                <td>
                                                    @if ($pegawai->memenuhiSyarat)
                                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#peringatanModal{{ $pegawai->id }}">
                                                            Kirim Peringatan
                                                        </button>
                                                    @else
                                                        <span data-toggle="tooltip" title="Pegawai belum memenuhi syarat (Alpha 10 hari berturut-turut).">
                                                            <button class="btn btn-sm btn-secondary" disabled style="pointer-events: none;">
                                                                Kirim Peringatan
                                                            </button>
                                                        </span>
                                                    @endif
                                                </td>                                                                                              
                                            </tr>

                                            <!-- Modal Kirim Peringatan -->
                                            <div class="modal fade" id="peringatanModal{{ $pegawai->id }}" tabindex="-1" aria-labelledby="peringatanModalLabel{{ $pegawai->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('admin.peringatan.kirim', $pegawai->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Kirim Surat Peringatan</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="isi">Isi Surat Peringatan</label>
                                                                    <textarea name="isi_surat" class="form-control" rows="4" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Kirim</button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
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
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        $(document).ready(function() {
            $('#pegawaiTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });
        });
    </script>
</body>
</html>
