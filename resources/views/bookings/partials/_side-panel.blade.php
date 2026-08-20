    {{-- KOLOM SAMPING: QR Presensi + Timeline --}}
    <div class="booking-side-col" id="liveSideCol">
        @if($booking->status == 1 && $booking->absent_code)
        <div class="detail-card">
            <div class="card-section">
                <div class="section-title">
                    <i class="bi bi-qr-code"></i> Kode Presensi
                </div>
                <div class="qr-section">
                    <p class="text-muted small mb-2">Tunjukkan QR ini kepada peserta rapat untuk presensi</p>
                    <div class="qr-code" id="qrCodeContainer"></div>
                    <div class="qr-label">Kode: {{ $booking->absent_code }}</div>
                    <button type="button" class="btn-outline-sm mt-3" style="display:inline-flex;" data-bs-toggle="modal" data-bs-target="#printQrModal">
                        <i class="bi bi-printer"></i> Cetak QR
                    </button>
                </div>
            </div>
        </div>
        @endif

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
