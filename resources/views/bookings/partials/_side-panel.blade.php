{{-- KOLOM SAMPING: cuma Timeline Peminjaman. QR Presensi & Daftar
         Hadir sudah dipindah jadi satu card gabungan di kolom utama
         (lihat partial _qr-attendance.blade.php). --}}
    <div class="booking-side-col" id="liveSideCol">
        <div class="detail-card">
            <div class="card-section">
                <div class="section-title">
                    <i class="bi bi-clock-history"></i> Timeline Peminjaman
                </div>
                <div class="timeline">
                    @foreach($timeline as $item)
                        <div class="timeline-item {{ $item['color'] }}">
                            <div class="timeline-icon {{ $item['color'] }}">
                                <i class="bi {{ $item['icon'] }}"></i>
                            </div>
                            <div class="timeline-title">{{ $item['title'] }}</div>
                            <div class="timeline-desc">{{ $item['description'] }}</div>
                            @if($item['time'])
                            <div class="timeline-time">{{ \Carbon\Carbon::parse($item['time'])->locale('id')->isoFormat('D MMM Y, HH:mm') }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>