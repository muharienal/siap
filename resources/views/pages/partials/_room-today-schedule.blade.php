        <div class="section-card" id="roomTodayScheduleCard">
            <div class="section-title">
                <span><i class="bi bi-calendar3"></i> Jadwal Hari Ini</span>
                <span class="schedule-date">{{ $today->translatedFormat('d M Y') }}</span>
            </div>
            <div class="schedule-list">
                @foreach($schedule as $time => $booking)
                    <div class="schedule-row">
                        <span class="time">{{ $time }}</span>
                        @if($booking)
                            <div class="slot busy">{{ $booking->purpose }}</div>
                        @else
                            <div class="slot">Kosong</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
