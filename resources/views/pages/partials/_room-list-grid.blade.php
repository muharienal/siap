<div id="roomListGrid">
@if($rooms->count() > 0)
    <div class="room-grid">
        @foreach($rooms as $room)
            <a href="{{ route('rooms.show', $room->id) }}" class="room-card">
                <div class="room-card-image">
                    @php
                        $validPhoto = $room->photos->first(function($p) { return $p->photo_url !== null; });
                    @endphp
                    @if($validPhoto)
                        <img src="{{ $validPhoto->photo_url }}" alt="{{ $room->name }}">
                    @else
                        <div class="room-card-image-placeholder"><i class="bi bi-image"></i></div>
                    @endif
                </div>
                <div class="room-card-body">
                    <h3 class="room-card-title">{{ $room->name }}</h3>

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
        <i class="bi bi-building"></i>
        <h4>Belum ada ruangan</h4>
        <p>Belum ada ruangan yang terdaftar.</p>
    </div>
@endif
</div>
