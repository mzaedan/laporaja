<div class="floating-button-container d-flex" onclick="window.location.href = 'take-report'">
        <button class="floating-button">
            <i class="fa-solid fa-camera"></i>
        </button>
    </div>
<nav class="nav-mobile d-flex">
    <a href="{{ route('home') }}" class="{{ request()->is('/') ? 'active' : ''  }}">
        <i class="fas fa-house"></i>
        Beranda
    </a>
    <a href="{{ route('report.myreport', ['status'=>'delivered']) }}" class="{{ request()->is('my-report*') ? 'active' : ''  }}">
        <i class="fas fa-solid fa-clipboard-list"></i>
        Laporanmu
    </a>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <a href="{{ route('notifications') }}" class="{{ request()->is('notifications') ? 'active' : ''  }}" id="notification-menu">
        <i class="fas fa-bell"></i>
        <span id="notification-count" style="{{ (isset($jumlahProgres) && $jumlahProgres > 0) ? '' : 'display:none;' }} color:red" class="notification-badge">
    {{ $jumlahProgres ?? '' }}
</span>
        Notifikasi
    </a>
    @auth
        <a href="{{ route('profile') }}" class="{{ request()->is('profile') ? 'active' : ''  }}">
            <i class="fas fa-user"></i>
            Profil
        </a>
    @else
    <a href="{{ route('login')}}" class="{{ request()->is('login') ? 'active' : ''  }}">
        <i class="fas fa-right-to-bracket"></i>
        Login
    </a>
    @endauth
</nav>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var notifMenu = document.getElementById('notification-menu');
    var notifCount = document.getElementById('notification-count');
    if (notifMenu && notifCount) {
        notifMenu.addEventListener('click', function() {
            notifCount.style.display = 'none';
        });
    }

    // Ganti URL di bawah ini dengan route yang mengembalikan jumlah notifikasi
    fetch('/api/unread-notification-count')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const count = data.count || 0;
            const badge = document.getElementById('notification-count');
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.warn('Failed to fetch notification count:', error);
            // Hide the badge on error
            const badge = document.getElementById('notification-count');
            if (badge) {
                badge.style.display = 'none';
            }
        });

    document.getElementById('notification-menu').addEventListener('click', function() {
        const badge = document.getElementById('notification-count');
        badge.style.display = 'none';
        
        // Mark notifications as read in backend
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            fetch('/api/mark-notifications-read', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).catch(error => {
                console.warn('Failed to mark notifications as read:', error);
            });
        }
    });
});
</script>