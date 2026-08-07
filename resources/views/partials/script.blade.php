<!-- Bootstrap 5 Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js (opsional) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function() {
    'use strict';

    // ============================================
    // SIDEBAR TOGGLE
    // ============================================
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('appSidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 991.98) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    }

    // ============================================
    // NOTIFICATIONS
    // ============================================
    var SEEN_NOTIF_KEY = 'siap_seen_notification_ids';

    function getSeenNotificationIds() {
        try {
            var raw = sessionStorage.getItem(SEEN_NOTIF_KEY);
            if (raw === null) return null; // belum pernah disimpan sama sekali di tab ini
            return new Set(JSON.parse(raw));
        } catch (e) {
            return null;
        }
    }

    function saveSeenNotificationIds(set) {
        try {
            sessionStorage.setItem(SEEN_NOTIF_KEY, JSON.stringify(Array.from(set)));
        } catch (e) { /* ignore (mis. private mode) */ }
    }

    function loadNotifications() {
        if (typeof $ === 'undefined') return;
        $.ajax({
            url: '{{ route("notifications.get") }}',
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                updateNotificationUI(response);
                handleNewNotificationToasts(response.notifications || []);
            },
            error: function() {
                $('#notificationList').html('<div class="text-center py-3 text-muted" style="font-size:var(--font-size-sm);"><i class="bi bi-exclamation-triangle"></i> Gagal memuat</div>');
            }
        });
    }

    function handleNewNotificationToasts(notifications) {
        var currentIds = notifications.map(function (n) { return n.id; });
        var seen = getSeenNotificationIds();

        if (seen === null) {
            // Pertama kali sistem ini kebuka di tab ini: catat baseline, jangan toast (biar ga spam retroaktif)
            seen = new Set(currentIds);
            saveSeenNotificationIds(seen);
            return;
        }

        var hasNew = false;
        notifications.forEach(function (notif) {
            if (!seen.has(notif.id)) {
                showToast(notif);
                seen.add(notif.id);
                hasNew = true;
            }
        });

        if (hasNew) saveSeenNotificationIds(seen);
    }

    function showToast(notif) {
        var container = document.getElementById('toastContainer');
        if (!container) return;

        var toast = document.createElement('a');
        toast.href = notif.booking_id ? '{{ url("bookings") }}/' + notif.booking_id : '{{ route("notifications.index") }}';
        toast.className = 'app-toast';
        toast.innerHTML = `
            <div class="t-icon"><i class="bi bi-bell-fill"></i></div>
            <div class="t-content">
                <div class="t-title">Notifikasi Baru${notif.room_name ? ' &middot; ' + notif.room_name : ''}</div>
                <div class="t-message">${notif.message}</div>
            </div>
            <button type="button" class="t-close" aria-label="Tutup"><i class="bi bi-x"></i></button>
        `;

        toast.querySelector('.t-close').addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            dismissToast(toast);
        });

        container.appendChild(toast);

        setTimeout(function () { dismissToast(toast); }, 7000);
    }

    function dismissToast(toast) {
        if (!toast || toast.classList.contains('hiding')) return;
        toast.classList.add('hiding');
        setTimeout(function () { toast.remove(); }, 250);
    }

    function updateNotificationUI(response) {
        const notifications = response.notifications || [];
        const unreadCount = response.unread_count || 0;
        const badge = document.getElementById('notificationBadge');
        const countEl = document.getElementById('notificationCount');
        if (unreadCount > 0) {
            badge.style.display = 'flex';
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            countEl.textContent = unreadCount > 9 ? '9+' : unreadCount;
        } else {
            badge.style.display = 'none';
            badge.textContent = '';
            countEl.textContent = '0';
        }
        const list = document.getElementById('notificationList');
        if (!list) return;
        if (notifications.length === 0) {
            list.innerHTML = '<div class="text-center py-3 text-muted" style="font-size:var(--font-size-sm);"><i class="bi bi-inbox"></i> Tidak ada notifikasi</div>';
            return;
        }
        let html = '';
        notifications.forEach(function(notif) {
            const isUnread = !notif.is_read;
            const bg = isUnread ? 'background:var(--bg-hover);' : '';
            html += `
                <a href="#" class="notif-item" data-id="${notif.id}" style="${bg}">
                    <div class="n-icon"><i class="bi bi-bell"></i></div>
                    <div class="n-content">
                        <div>${notif.message}</div>
                        <div class="n-time">${notif.created_at}</div>
                    </div>
                    ${isUnread ? '<span style="width:6px;height:6px;border-radius:50%;background:var(--brand-orange);flex-shrink:0;margin-top:8px;"></span>' : ''}
                </a>
            `;
        });
        list.innerHTML = html;
        list.querySelectorAll('.notif-item').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                markAsRead(id, this);
            });
        });
    }

    function markAsRead(id, element) {
        if (typeof $ === 'undefined') return;
        $.ajax({
            url: '{{ route("notifications.mark-read") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { notification_id: id },
            success: function() { loadNotifications(); },
            error: function() { console.error('Gagal menandai notifikasi'); }
        });
    }

    document.getElementById('markAllRead')?.addEventListener('click', function(e) {
        e.preventDefault();
        if (typeof $ === 'undefined') return;
        $.ajax({
            url: '{{ route("notifications.mark-all-read") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() { loadNotifications(); },
            error: function() { console.error('Gagal menandai semua'); }
        });
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadNotifications);
    } else {
        loadNotifications();
    }
    setInterval(loadNotifications, 15000);

})();
</script>