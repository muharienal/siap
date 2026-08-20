        <div class="section-card" id="roomInfoCard">
            <div class="section-title">
                <span><i class="bi bi-info-circle"></i> Informasi Dasar</span>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-item-icon icon-blue"><i class="bi bi-people-fill"></i></div>
                    <div class="info-item-text">
                        <span class="info-item-label">Kapasitas</span>
                        <span class="info-item-value">{{ $room->capacity }} Orang</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon icon-orange"><i class="bi bi-geo-alt-fill"></i></div>
                    <div class="info-item-text">
                        <span class="info-item-label">Lokasi</span>
                        <span class="info-item-value">{{ $room->location }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-icon {{ $room->status == 1 ? 'icon-green' : 'icon-red' }}">
                        <i class="bi {{ $room->status == 1 ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                    </div>
                    <div class="info-item-text">
                        <span class="info-item-label">Status</span>
                        @if($room->status == 1)
                            <span class="status-pill active"><span class="dot"></span> Aktif</span>
                        @else
                            <span class="status-pill inactive"><span class="dot"></span> Nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
