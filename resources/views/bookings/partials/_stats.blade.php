<div class="stats-grid" id="liveStatsGrid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-clipboard-data"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">Total Booking</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label">Menunggu Persetujuan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['approved'] ?? 0 }}</div>
            <div class="stat-label">Disetujui</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>
    {{-- FIX: sebelumnya "Ditolak" dan "Dibatalkan" digabung jadi satu
         kartu/angka — padahal maknanya beda (ditolak dari awal vs sempat
         disetujui lalu dibatalkan). Sekarang kartu TERPISAH, masing-
         masing punya angkanya sendiri. Tabel di bagian bawah halaman
         tetap SATU tabel gabungan ("Peminjaman Ditolak/Dibatalkan") —
         cuma label per-barisnya yang membedakan, sesuai permintaan. --}}
    <div class="stat-card">
        <div class="stat-icon slate"><i class="bi bi-slash-circle"></i></div>
        <div class="stat-body">
            <div class="stat-value">{{ $stats['cancelled'] ?? 0 }}</div>
            <div class="stat-label">Dibatalkan</div>
        </div>
    </div>
</div>