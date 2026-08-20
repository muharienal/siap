<div id="roomGridContainer">
    @if($rooms->count() > 0)
        <div class="room-grid" id="roomGrid">
            @foreach($rooms as $room)
                <a href="{{ route('settings.rooms.show', $room->id) }}" class="room-card" style="text-decoration: none; color: inherit;">
                    <div class="room-card-image">
                        @php
                            $validPhoto = $room->photos->first(function($p) { return $p->photo_url !== null; });
                        @endphp
                        @if($validPhoto)
                            <img src="{{ $validPhoto->photo_url }}" alt="{{ $room->name }}">
                        @else
                            <div class="room-card-image-placeholder"><i class="bi bi-image"></i></div>
                        @endif
                        <span class="room-card-status {{ $room->status == 1 ? 'status-active' : 'status-inactive' }}">
                            {{ $room->status == 1 ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="room-card-body">
                        <h3 class="room-card-title">{{ $room->name }}</h3>
                        <div class="room-card-meta">
                            <span><i class="bi bi-geo-alt"></i>{{ $room->location }}</span>
                            <span><i class="bi bi-people"></i>{{ $room->capacity }} orang</span>
                        </div>
                        
                        {{-- FACILITY BADGE WITH TOOLTIP --}}
                        <div class="facility-badge-wrapper">
                            @if($room->facilities->count() > 0)
                                <span class="facility-badge has-facilities">
                                    <i class="bi bi-tools"></i> Ada Fasilitas
                                </span>
                                <div class="facility-tooltip">
                                    <ul>
                                        @foreach($room->facilities as $f)
                                            <li>{{ $f->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <span class="facility-badge no-facilities">
                                    <i class="bi bi-dash-circle"></i> Tanpa Fasilitas
                                </span>
                            @endif
                        </div>

                        <div class="room-card-footer">
                            <div class="room-card-stats">
                                <i class="bi bi-images"></i> {{ $room->photos->filter(function($p) { return $p->photo_url !== null; })->count() }} foto
                            </div>
                            <span class="btn-view-detail">Lihat Detail <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-door-open"></i>
            <h4>Belum ada ruangan</h4>
            <p>Mulai tambahkan ruangan rapat pertama Anda</p>
            <button type="button" class="btn-primary-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal" style="display: inline-flex;">
                <i class="bi bi-plus-lg"></i> Tambah Ruangan
            </button>
        </div>
    @endif
</div>
