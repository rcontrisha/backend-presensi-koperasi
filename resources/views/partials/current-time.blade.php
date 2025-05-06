<!-- resources/views/partials/current-time.blade.php -->
<div id="current-time" class="text-right text-muted fs-5 fw-bold">
    <!-- Waktu akan ditampilkan di sini -->
</div>

<script>
    // Fungsi untuk menampilkan waktu saat ini
    function updateTime() {
        var now = new Date();
        var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        var dateString = now.toLocaleDateString('id-ID', options); // Format tanggal dan hari
        var timeString = now.toLocaleTimeString('id-ID'); // Format waktu

        document.getElementById('current-time').innerHTML = dateString + ' | ' + timeString;
    }

    // Memperbarui waktu setiap detik
    setInterval(updateTime, 1000);

    // Menampilkan waktu pertama kali ketika halaman dimuat
    updateTime();
</script>
