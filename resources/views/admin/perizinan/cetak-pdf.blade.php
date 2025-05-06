<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Izin - {{ $user->name }}</title>
    <style>
        <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .logo {
            width: 70px;
            height: auto;
            margin-bottom: 5px;
        }
        .line {
            border-top: 2px solid #000;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #eee;
        }
        .info {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('image/dinkop2.png') }}" alt="Logo" class="logo"><br>
        <strong>DINAS KOPERASI & UMKM KABUPATEN PACITAN</strong><br>
        <small>Jl. Jaksa Agung Suprapto No.17, Pacitan</small>
    </div>
    
    <div class="line"></div>

    <div class="info">
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p><strong>Bulan:</strong> 
            {{ \Carbon\Carbon::create()->month((int)($request->bulan ?? now()->month))->locale('id')->isoFormat('MMMM') }}
            {{ $request->tahun ?? now()->year }}
        </p>
    </div>

    <table class="table">
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
                            <span>Diterima</span>
                        @elseif($izin->status == 'menunggu')
                            <span>Menunggu</span>
                        @elseif($izin->status == 'ditolak')
                            <span>Ditolak</span>
                        @endif
                    </td>
                    <td>{{ $izin->keterangan }}</td>
                    <td>
                        @if($izin->file_pendukung)
                            <a href="{{ asset('storage/file_izin/' . $izin->file_pendukung) }}" target="_blank">Lihat File</a>
                        @else
                            Tidak ada file
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Terima kasih, Dinas Koperasi & UMKM Kab. Pacitan</p>
    </div>

</body>
</html>
