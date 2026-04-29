@php
    $unreadCount = Auth::user()->unreadNotifications->count();
    $notifications = Auth::user()->notifications()->latest()->take(10)->get();
@endphp

<div class="notification-bell" id="notification-bell">
    <button type="button" class="notification-bell-btn" id="notification-bell-btn" aria-label="Notifications" title="Notifications">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M12 5.333A4 4 0 0 0 4 5.333c0 4.667-2 6-2 6h12s-2-1.333-2-6ZM9.153 14a1.333 1.333 0 0 1-2.306 0" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @if ($unreadCount > 0)
            <span class="notification-bell-badge" data-count="{{ $unreadCount }}">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>

    <div class="notification-dropdown" id="notification-dropdown">
        <div class="notification-dropdown-header">
            <span class="notification-dropdown-title">Notifications</span>
            @if ($unreadCount > 0)
                <button type="button" class="notification-mark-read" id="mark-all-read">Mark all read</button>
            @endif
        </div>

        @forelse ($notifications as $notification)
            <div class="notification-item {{ $notification->read_at ? 'notification-item-read' : 'notification-item-unread' }}">
                <div class="notification-item-message">
                    {{ $notification->data['title'] ?? 'New notification' }}
                </div>
                <div class="notification-item-time">
                    {{ $notification->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="notification-empty">No notifications yet.</div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bellBtn = document.getElementById('notification-bell-btn');
    const dropdown = document.getElementById('notification-dropdown');
    const markAllBtn = document.getElementById('mark-all-read');

    // Toggle dropdown
    if (bellBtn) {
        bellBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('is-open');
        });
    }

    // Close dropdown on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#notification-bell')) {
            dropdown.classList.remove('is-open');
        }
    });

    // Mark all as read
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            fetch('{{ route("notifications.markAllRead") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            }).then(function () {
                // Update UI
                document.querySelectorAll('.notification-item-unread').forEach(function (item) {
                    item.classList.remove('notification-item-unread');
                    item.classList.add('notification-item-read');
                });
                const badge = document.querySelector('.notification-bell-badge');
                if (badge) badge.style.display = 'none';
                markAllBtn.remove();
            });
        });
    }
});
</script>
