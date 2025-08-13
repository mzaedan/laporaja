<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin LaporAja</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Nav Item - Data Laporan Dropdown -->
    <li class="nav-item {{ request()->is('admin/report*') && !request()->is('admin/report-category*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReport"
            aria-expanded="true" aria-controls="collapseReport">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Laporan</span>
        </a>
        <div id="collapseReport" class="collapse" aria-labelledby="headingReport"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Menu Laporan:</h6>
                <a class="collapse-item {{ request()->is('admin/report') || request()->is('admin/report/create') ? 'active' : '' }}" 
                   href="{{ route('admin.report.index') }}">Laporan Aktif</a>
                <a class="collapse-item {{ request()->is('admin/report/completed') ? 'active' : '' }}" 
                   href="{{ route('admin.report.completed') }}">Laporan Selesai</a>
            </div>
        </div>
    </li>

    <li class="nav-item {{ request()->is('admin/report-category*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report-category.index') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Kategori</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/resident*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.resident.index') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Masyarakat</span>
        </a>
    </li>
</ul>