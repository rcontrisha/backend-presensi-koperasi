<div class="table-responsive">
    <table class="table table-striped" id="presensiTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Alamat</th>
                <th>Foto Presensi</th>
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
                    <td id="alamat-{{ $presensi->id }}">
                        Sedang memuat...
                        <br>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $presensi->latitude }},{{ $presensi->longitude }}" target="_blank">
                            Lihat di Maps
                        </a>
                    </td>
                    <td>
                        @if($presensi->foto_presensi)
                            <img src="{{ asset('storage/foto_presensi/' . $presensi->foto_presensi) }}" alt="Foto Presensi" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            Tidak ada foto
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
