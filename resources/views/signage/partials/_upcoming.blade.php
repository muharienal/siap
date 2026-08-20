<div id="signageUpcomingWrap">
            @if($display->show_upcoming)
                <div class="signage-upcoming">
                    <div class="upcoming-title"><i class="bi bi-clock-history"></i> Jadwal Berikutnya Hari Ini</div>
                    <div class="upcoming-list">
                        @forelse($upcomingBookings as $b)
                            <div class="upcoming-item">
                                <div class="upcoming-time">{{ \Carbon\Carbon::parse($b->start_time)->format('H:i') }}&ndash;{{ \Carbon\Carbon::parse($b->end_time)->format('H:i') }}</div>
                                <div class="upcoming-purpose">{{ $b->purpose }}</div>
                                <div class="upcoming-organizer">{{ $b->user->full_name ?? 'Unknown' }}</div>
                            </div>
                        @empty
                            <div class="upcoming-empty">Tidak ada jadwal lain hari ini.</div>
                        @endforelse
                    </div>
                </div>
            @endif
</div>
