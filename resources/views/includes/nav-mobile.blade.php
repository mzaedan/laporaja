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
        <span id="notification-count" style="display:none; color:red" class="notification-badge">2</span>
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
    // Ganti URL di bawah ini dengan route yang mengembalikan jumlah notifikasi
    fetch('/api/unread-notification-count')
        .then(response => response.json())
        .then(data => {
            const count = data.count || 0;
            const badge = document.getElementById('notification-count');
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        });

    document.getElementById('notification-menu').addEventListener('click', function() {
        const badge = document.getElementById('notification-count');
        badge.style.display = 'none';
        // (Opsional) Tandai notifikasi sebagai sudah dibaca di backend
        fetch('/api/mark-notifications-read', { method: 'POST' });
    });
});
</script>