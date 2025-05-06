<div class="table-responsive">
    <table class="table table-striped" id="izinTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Jenis Izin</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>File Pendukung</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($izins as $index => $izin)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                    <td>{{ \Carbon\Carbon::parse($izin->tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                    <td>{{ $izin->jenis_izin }}</td>
                    <td>
                        @if($izin->status == 'diterima')
                            <span class="badge badge-success">Diterima</span>
                        @elseif($izin->status == 'menunggu')
                            <span class="badge badge-warning">Menunggu</span>
                        @elseif($izin->status == 'ditolak')
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                    <td>{{ $izin->keterangan }}</td>
                    <td>
                        @if($izin->file_pendukung)
                            <a href="{{ asset('storage/file_izin/' . $izin->file_pendukung) }}" target="_blank" class="btn btn-info btn-sm">
                                Lihat File
                            </a>
                        @else
                            Tidak ada file
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
