<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai - Dinas Koperasi & UMKM Kab. Pacitan</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- JS -->
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
                            <h3>Data Pegawai</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            @include('partials.current-time')
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Data Pegawai</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Daftar Pegawai
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped" id="pegawaiTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Alamat</th>
                                            <th>No Telpon</th>
                                            <th>Jabatan</th>
                                            <th>TTL</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Pendidikan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pegawaiList as $index => $pegawai)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $pegawai->user->name ?? '-' }}</td>
                                                <td>{{ $pegawai->alamat }}</td>
                                                <td>{{ $pegawai->no_telpon }}</td>
                                                <td>{{ $pegawai->jabatan }}</td>
                                                <td>{{ $pegawai->tempat_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d-m-Y') }}</td>
                                                <td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                                <td>{{ $pegawai->pendidikan_terakhir }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal{{ $pegawai->id }}">Edit</button>
                                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $pegawai->id }}">Hapus</button>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="editModal{{ $pegawai->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $pegawai->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Data Pegawai</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Alamat</label>
                                                                    <input type="text" name="alamat" class="form-control" value="{{ $pegawai->alamat }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>No Telpon</label>
                                                                    <input type="text" name="no_telpon" class="form-control" value="{{ $pegawai->no_telpon }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Jabatan</label>
                                                                    <input type="text" name="jabatan" class="form-control" value="{{ $pegawai->jabatan }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Tempat Lahir</label>
                                                                    <input type="text" name="tempat_lahir" class="form-control" value="{{ $pegawai->tempat_lahir }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Tanggal Lahir</label>
                                                                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ $pegawai->tanggal_lahir }}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Jenis Kelamin</label>
                                                                    <select name="jenis_kelamin" class="form-control" required>
                                                                        <option value="L" {{ $pegawai->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                                                        <option value="P" {{ $pegawai->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Pendidikan Terakhir</label>
                                                                    <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ $pegawai->pendidikan_terakhir }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Modal Delete -->
                                            <div class="modal fade" id="deleteModal{{ $pegawai->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $pegawai->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('admin.pegawai.destroy', $pegawai->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Apakah anda yakin ingin menghapus data pegawai <strong>{{ $pegawai->user->name ?? '' }}</strong>?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-danger">Hapus</button>
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

    <!-- JS -->
    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
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
