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
    function loadNotifications() {
        if (typeof $ === 'undefined') return;
        $.ajax({
            url: '{{ route("notifications.get") }}',
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) { updateNotificationUI(response); },
            error: function() {
                $('#notificationList').html('<div class="text-center py-3 text-muted" style="font-size:var(--font-size-sm);"><i class="bi bi-exclamation-triangle"></i> Gagal memuat</div>');
            }
        });
    }

    function updateNotificationUI(response) {
        const notifications = response.notifications || [];
        const unreadCount = response.unread_count || 0;
        const badge = document.getElementById('notificationBadge');
        const countEl = document.getElementById('notificationCount');
        if (unreadCount > 0) {
            badge.style.display = 'block';
            countEl.textContent = unreadCount > 9 ? '9+' : unreadCount;
        } else {
            badge.style.display = 'none';
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
    setInterval(loadNotifications, 30000);

})();
</script>