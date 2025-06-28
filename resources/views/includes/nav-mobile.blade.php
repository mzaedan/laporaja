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
    <a href="{{ route('report.myreport', ['status'=>'delivered']) }}" class="">
        <i class="fas fa-solid fa-clipboard-list"></i>
        Laporanmu
    </a>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <a href="{{ route('notifications') }}" class="" id="notification-menu">
        <i class="fas fa-bell"></i>
        <span id="notification-count" style="display:none;" class="notification-badge"></span>
        Notifikasi
    </a>
    @auth
        <a href="{{ route('profile') }}" class="">
            <i class="fas fa-user"></i>
            Profil
        </a>
    @else
    <a href="{{ route('login')}}" class="">
        <i class="fas fa-right-to-bracket"></i>
        Login
    </a>
    @endauth
</nav>