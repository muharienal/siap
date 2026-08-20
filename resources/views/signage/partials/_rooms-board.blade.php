        <div class="rooms-board" id="roomsBoard">
            @foreach($roomsSchedule as $item)
                @php
                    $room = $item['room'];
                    $bookings = $item['bookings'];
                    $isOccupied = $item['is_occupied'];
                    $currentId = $item['current_id'];
                @endphp
                <div class="room-panel">
                    <div class="room-panel-header {{ $isOccupied ? 'occupied' : 'available' }}">
                        <div>
                            <div class="room-panel-name">{{ $room->name }}</div>
                            <div class="room-panel-meta">
                                <i class="bi bi-people-fill"></i> {{ $room->capacity }} orang
                                <span>&middot;</span>
                                <i class="bi bi-geo-alt-fill"></i> {{ $room->location }}
                            </div>
                        </div>
                        <span class="room-panel-status {{ $isOccupied ? 'occupied' : 'available' }}">
                            @if($isOccupied)<span class="status-pulse"></span>@endif
                            {{ $isOccupied ? 'AKTIF' : 'TERSEDIA' }}
                        </span>
                    </div>

                    <div class="room-panel-schedule">
                        @forelse($bookings as $b)
                            @php
                                $bStart = \Carbon\Carbon::parse($b->start_time);
                                $bEnd = \Carbon\Carbon::parse($b->end_time);
                                $isCurrent = $b->id === $currentId;
                                $isDone = $bEnd->lt($now) && !$isCurrent;
                            @endphp
                            <div class="schedule-slot {{ $isCurrent ? 'active' : '' }} {{ $isDone ? 'done' : '' }}">
                                <div class="schedule-slot-time">
                                    {{ $bStart->format('H:i') }}
                                    <small>&ndash; {{ $bEnd->format('H:i') }}</small>
                                </div>
                                <div class="schedule-slot-info">
                                    <div class="schedule-slot-purpose">{{ $b->purpose }}</div>
                                    <div class="schedule-slot-organizer"><i class="bi bi-person-fill"></i> {{ $b->user->full_name ?? 'Unknown' }}</div>
                                </div>
                                @if($isCurrent)
                                    <span class="schedule-slot-tag active">Aktif</span>
                                @elseif($isDone)
                                    <span class="schedule-slot-tag done">Selesai</span>
                                @endif
                            </div>
                        @empty
                            <div class="schedule-empty">
                                <div class="empty-icon-ring"><i class="bi bi-calendar-check"></i></div>
                                <span>Tidak ada jadwal rapat hari ini</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
