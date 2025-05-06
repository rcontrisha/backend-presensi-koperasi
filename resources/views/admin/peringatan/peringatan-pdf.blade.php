<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px 60px;
            font-size: 14px;
        }

        hr {
            border: 1.5px solid black;
            margin: 10px 0 25px 0;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content {
            line-height: 1.6;
            white-space: pre-line;
        }

        .ttd {
            margin-top: 50px;
            text-align: right;
        }

    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td width="15%" align="center">
                <img src="{{ public_path('image/dinkop2.png') }}" width="80" height="80">
            </td>
            <td align="center">
                <div style="font-size: 16px;">PEMERINTAH KABUPATEN PACITAN</div>
                <div style="font-size: 20px; font-weight: bold;">DINAS KOPERASI & UMKM</div>
                <div style="font-size: 12px;">Jl. Jaksa Agung Suprapto No.17, Pacitan</div>
                <div style="font-size: 12px;">Telepon: (021) 551 0110</div>
            </td>
        </tr>
    </table>

    <hr />

    <div class="title">
        {{ $judul_surat }}<br>
        Nomor: {{ $nomor_surat }}
    </div>

    <div class="content">
        <p>Surat ini ditujukan kepada:<br />
            Nama: <strong>{{ $nama }}</strong><br />
            Jabatan: <strong>{{ $jabatan }}</strong>
        </p>

        <p>{{ $isi_surat }}</p>
    </div>

    <div class="ttd">
        Pacitan, {{ $tanggal }}<br /><br /><br /><br /><br />
        <strong>Najib Sepitenk</strong><br />
        Head of HR
    </div>

</body>

</html>
