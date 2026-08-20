<div class="signage-grid" id="signageGrid">
    @foreach($rooms as $room)
        @php
            $display = $room->display;
            $validPhoto = $room->photos->first(fn($p) => $p->photo_url !== null);
        @endphp
        <div class="signage-card">
            <div class="signage-card-image">
                @if($validPhoto)
                    <img src="{{ $validPhoto->photo_url }}" alt="{{ $room->name }}">
                @else
                    <div class="signage-card-image-placeholder"><i class="bi bi-tv"></i></div>
                @endif
                <span class="signage-status-badge {{ $display && $display->is_active ? 'on' : 'off' }}">
                    {{ $display && $display->is_active ? 'Aktif' : 'Belum Diatur' }}
                </span>
            </div>
            <div class="signage-card-body">
                <h3 class="signage-card-title">{{ $room->name }}</h3>
                <div class="signage-card-meta"><i class="bi bi-geo-alt"></i> {{ $room->location }}</div>

                @if($display)
                    <div class="signage-config-chips">
                        <span class="signage-chip">
                            <i class="bi bi-{{ $display->orientation === 'portrait' ? 'phone' : 'tv' }}"></i>
                            {{ $display->orientation === 'portrait' ? 'Portrait' : 'Landscape' }}
                        </span>
                        <span class="signage-chip"><i class="bi bi-arrow-repeat"></i> {{ $display->refresh_seconds }}s</span>
                        @if($display->show_qr)<span class="signage-chip"><i class="bi bi-qr-code"></i> QR</span>@endif
                    </div>
                @endif
            </div>
            <div class="signage-card-footer">
                <button type="button" class="btn-signage-action primary" onclick='openSignageModal(@json($room->id), @json($room->name), @json($display))'>
                    <i class="bi bi-sliders"></i> Kelola Tampilan
                </button>
                @if($display)
                    <a href="{{ route('signage.show', $display->display_token) }}" target="_blank" class="btn-signage-action" title="Buka Preview">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>
