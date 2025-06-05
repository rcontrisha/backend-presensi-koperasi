<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi - {{ $user->name }}</title>
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
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

<div class="header">
    <img src="{{ asset('image/dinkop2.png') }}" alt="Logo" class="logo"><br>
    <strong>DINAS KOPERASI & UMKM KABUPATEN PACITAN</strong><br>
    <small>Jl. Jaksa Agung Suprapto No.17, Pacitan</small>
</div>

<div class="line"></div>

<div class="info">
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Bulan:</strong> 
        {{ \Carbon\Carbon::create()->month((int)($bulan))->locale('id')->isoFormat('MMMM') }}
        {{ $tahun }}
    </p>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Alamat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($presensis as $index => $presensi)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($presensi->waktu_presensi)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                <td>{{ \Carbon\Carbon::parse($presensi->waktu_presensi)->format('H:i:s') }}</td>
                <td>{{ $presensi->latitude }}</td>
                <td>{{ $presensi->longitude }}</td>
                <td>{{ $presensi->alamat }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="no-print" style="text-align:center; margin-top: 20px;">
    <a href="{{ route('admin.rekap-presensi') }}" class="btn btn-secondary">Kembali</a>
</div>

</body>
</html>
