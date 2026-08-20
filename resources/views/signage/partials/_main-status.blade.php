        <main id="signageMainStatus" class="signage-status-main {{ $currentBooking ? 'occupied' : 'available' }}">
            @if($currentBooking)
                @php
                    $bStart = \Carbon\Carbon::parse($currentBooking->start_time);
                    $bEnd = \Carbon\Carbon::parse($currentBooking->end_time);
                    $totalMin = max(1, $bStart->diffInMinutes($bEnd));
                    $elapsedMin = max(0, $bStart->diffInMinutes($now));
                    $progressPct = min(100, max(0, round(($elapsedMin / $totalMin) * 100)));
                @endphp
                <div class="status-ring"><i class="bi bi-record-circle-fill"></i></div>
                <div class="status-label">AKTIF</div>
                <div class="current-meeting">
                    <div class="meeting-title">{{ $currentBooking->purpose }}</div>
                    <div class="meeting-organizer"><i class="bi bi-person-fill"></i> {{ $currentBooking->user->full_name ?? 'Unknown' }}</div>
                    <div class="meeting-time">{{ $bStart->format('H:i') }} &ndash; {{ $bEnd->format('H:i') }} WIB</div>
                    <div class="meeting-progress"><div class="meeting-progress-bar" style="width: {{ $progressPct }}%;"></div></div>
                </div>
            @else
                <div class="status-ring"><i class="bi bi-check-lg"></i></div>
                <div class="status-label">TERSEDIA</div>
                <div class="status-sub">Ruangan siap digunakan sekarang</div>
            @endif
        </main>
