<div class="section-card" id="notifListWrapper">
    @if($notifications->count() > 0)
        @foreach($notifications as $notification)
            <a href="{{ $notification->booking_id ? route('bookings.show', $notification->booking_id) : '#' }}"
               class="notif-row {{ !$notification->is_read ? 'is-unread' : '' }}"
               data-id="{{ $notification->id }}" data-unread="{{ $notification->is_read ? '0' : '1' }}">
                <div class="notif-row-icon"><i class="bi bi-bell-fill"></i></div>
                <div class="notif-row-body">
                    <div class="notif-row-message">{{ $notification->message }}</div>
                    <div class="notif-row-meta">
                        <span class="notif-row-time"><i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }} &middot; {{ $notification->created_at->format('d M Y, H:i') }}</span>
                        @if($notification->booking)
                            <span class="notif-row-room"><i class="bi bi-door-open"></i> {{ $notification->booking->room->name ?? 'N/A' }}</span>
                        @endif
                    </div>
                </div>
                @if(!$notification->is_read)
                    <span class="notif-row-dot"></span>
                @endif
            </a>
        @endforeach
    @else
        <div class="empty-state">
            <i class="bi bi-bell-slash"></i>
            <p>{{ $filter === 'unread' ? 'Tidak ada notifikasi yang belum dibaca.' : 'Belum ada notifikasi.' }}</p>
        </div>
    @endif
</div>
