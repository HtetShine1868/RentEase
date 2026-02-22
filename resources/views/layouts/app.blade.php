@auth
<li class="nav-item">
    <a href="{{ route('rental.chats') }}" class="nav-link position-relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        Messages
        <span id="unread-badge" class="badge bg-danger position-absolute top-0 start-100 translate-middle" style="display: none;">0</span>
    </a>
</li>
@endauth

@push('scripts')
<script>
// Update unread count for messages
function updateUnreadCount() {
    fetch('{{ route("rental.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('unread-badge');
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => console.error('Error fetching unread count:', error));
}

// Update every 30 seconds
setInterval(updateUnreadCount, 30000);
// Initial update
document.addEventListener('DOMContentLoaded', function() {
    updateUnreadCount();
});
</script>
@endpush