<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Pegawai - Dinas Koperasi & UMKM Kab. Pacitan</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

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
                            <h3>Daftar Akun Pegawai</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            @include('partials.current-time')
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Daftar Akun Pegawai</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            Tabel Akun Pegawai
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                                @if(session('generated_password'))
                                <br>
                                <strong>Password untuk {{ session('generated_email') }}:</strong>
                                <span class="badge bg-warning text-dark">{{ session('generated_password') }}</span>
                                <br>
                                <small>Password ini hanya tampil sekali, mohon catat atau kirim ke user.</small>
                                @endif
                            </div>
                            @endif

                            <!-- Floating Action Button (FAB) -->
                            <button type="button" class="btn btn-primary rounded-circle shadow position-fixed"
                                style="bottom: 40px; right: 40px; z-index: 1050; width: 60px; height: 60px; font-size: 2rem;"
                                data-toggle="modal" data-target="#tambahAkunModal" title="Tambah Akun Pegawai">
                                <span class="fas fa-plus"></span>
                            </button>

                            <!-- Modal Tambah Akun Pegawai -->
                            <div class="modal fade" id="tambahAkunModal" tabindex="-1" aria-labelledby="tambahAkunModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.akun-pegawai.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="tambahAkunModalLabel">Tambah Akun Pegawai</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Nama</label>
                                                    <input type="text" name="name" class="form-control" placeholder="Nama"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control" placeholder="Email"
                                                        required>
                                                </div>
                                                {{-- Hapus input password, karena password akan digenerate otomatis --}}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Tambah Akun</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped" id="akunTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Status Akun</th>
                                            <th>Status Data Pegawai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->pegawai)
                                                <span class="badge bg-primary">Lengkap</span>
                                                @else
                                                <span class="badge bg-warning text-dark">Belum ada data pegawai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.akun-pegawai.toggleStatus', $user->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                                                        {{ $user->is_active ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
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
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#akunTable').DataTable({
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
