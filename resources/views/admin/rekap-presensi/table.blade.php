<div class="table-responsive">
    <table class="table table-striped" id="presensiTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Waktu Hadir</th>
                <th>Waktu Pulang</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Alamat</th>
                <th>Foto Masuk</th>
                <th>Foto Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($presensis as $index => $presensi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($presensi->waktu_presensi)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                    <td>{{ \Carbon\Carbon::parse($presensi->waktu_presensi)->format('H:i:s') }}</td>
                    <td>{{ \Carbon\Carbon::parse($presensi->waktu_pulang)->format('H:i:s') }}</td>
                    <td>{{ $presensi->latitude }}</td>
                    <td>{{ $presensi->longitude }}</td>
                    <td id="alamat-{{ $presensi->id }}">
                        Sedang memuat...
                        <br>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $presensi->latitude }},{{ $presensi->longitude }}" target="_blank">
                            Lihat di Maps
                        </a>
                    </td>
                    <td>
                        @if($presensi->foto_masuk)
                            <img src="{{ asset('storage/' . $presensi->foto_masuk) }}" alt="Foto Absen Masuk" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            Tidak ada foto
                        @endif
                    </td>
                    <td>
                        @if($presensi->foto_pulang)
                            <img src="{{ asset('storage/' . $presensi->foto_pulang) }}" alt="Foto Absen Pulang" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            Tidak ada foto
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
