<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistem LPM Universitas Methodist Indonesia</title>
    
    <!-- Favicon Resmi UMI -->
    <link rel="icon" href="{{ asset('images/logo-umi.jpg') }}">

    <!-- CSS Design System UMI -->
    <link rel="stylesheet" href="{{ asset('css/lpm.css') }}">

    <!-- Chart.js for Trend & Ring Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
</head>
<body>
    <!-- Mobile Backdrop -->
    <div id="sidebarBackdrop" class="modal-overlay"></div>

    <div class="app-container">
        <!-- Sidebar Membulat Putih Khas UMI -->
        <aside id="appSidebar" class="app-sidebar">
            <div class="brand-section">
                <div class="brand-left">
                    <img src="{{ asset('images/logo-umi.jpg') }}" alt="Logo Universitas Methodist Indonesia" class="brand-logo-img">
                    <div>
                        <h2 class="brand-title">SISTEM LPM</h2>
                        <span class="brand-subtitle">FIKOM UMI</span>
                    </div>
                </div>
                <!-- Tombol Tutup Sidebar untuk Tampilan Mobile -->
                <button type="button" id="closeSidebarBtn" class="sidebar-close-btn" title="Tutup Menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->isAdmin())
                    <span class="sidebar-section-title">Menu Utama Admin</span>

                    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="9" x="3" y="3" rx="1"/>
                            <rect width="7" height="5" x="14" y="3" rx="1"/>
                            <rect width="7" height="9" x="14" y="12" rx="1"/>
                            <rect width="7" height="5" x="3" y="16" rx="1"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.pengaduan.index') }}" class="nav-item {{ request()->routeIs('admin.pengaduan.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" x2="8" y1="13" y2="13"/>
                            <line x1="16" x2="8" y1="17" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                        <span>Kelola Pengaduan</span>
                    </a>

                    <a href="{{ route('admin.kategori.index') }}" class="nav-item {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        <span>Kategori Pengaduan</span>
                    </a>

                    <a href="{{ route('admin.anggota.index') }}" class="nav-item {{ request()->routeIs('admin.anggota.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Data Anggota</span>
                    </a>

                    <a href="{{ route('admin.persetujuan-akun.index') }}" class="nav-item {{ request()->routeIs('admin.persetujuan-akun.*') ? 'active' : '' }}" style="position: relative;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <polyline points="16 11 18 13 22 9"/>
                        </svg>
                        <span>Persetujuan Akun</span>
                        @php
                            $jumlahMenungguSidebar = \App\Models\User::where('status_akun', 'menunggu_verifikasi')->count();
                        @endphp
                        @if($jumlahMenungguSidebar > 0)
                            <span style="margin-left: auto; background: #EF4444; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 9999px;">{{ $jumlahMenungguSidebar }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.laporan.index') }}" class="nav-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" x2="12" y1="15" y2="3"/>
                        </svg>
                        <span>Laporan & Ekspor</span>
                    </a>
                @else
                    <span class="sidebar-section-title">Menu Pengguna</span>

                    <a href="{{ route('anggota.dashboard') }}" class="nav-item {{ request()->routeIs('anggota.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="9" x="3" y="3" rx="1"/>
                            <rect width="7" height="5" x="14" y="3" rx="1"/>
                            <rect width="7" height="9" x="14" y="12" rx="1"/>
                            <rect width="7" height="5" x="3" y="16" rx="1"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('anggota.pengaduan.create') }}" class="nav-item {{ request()->routeIs('anggota.pengaduan.create') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="12" x2="12" y2="8"/>
                            <line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                        <span>Buat Pengaduan</span>
                    </a>

                    <a href="{{ route('anggota.pengaduan.index') }}" class="nav-item {{ request()->routeIs('anggota.pengaduan.index') || request()->routeIs('anggota.pengaduan.show') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" x2="8" y1="13" y2="13"/>
                            <line x1="16" x2="8" y1="17" y2="17"/>
                        </svg>
                        <span>Pengaduan Saya</span>
                    </a>

                    <a href="{{ route('anggota.profil') }}" class="nav-item {{ request()->routeIs('anggota.profil') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span>Profil & Sandi</span>
                    </a>
                @endif
            </nav>

            <!-- Profil Singkat & Tombol Logout -->
            <div class="sidebar-user">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->nama }}</div>
                    <div class="user-role">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Civitas / Warga' }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn" title="Keluar dari sistem" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" x2="9" y1="12" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Area Konten Utama -->
        <main class="app-main">
            <!-- Topbar Desain UMI -->
            <header class="topbar">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <button id="mobileSidebarToggle" type="button" class="mobile-toggle" aria-label="Buka navigasi menu">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" x2="20" y1="12" y2="12"/>
                            <line x1="4" x2="20" y1="6" y2="6"/>
                            <line x1="4" x2="20" y1="18" y2="18"/>
                        </svg>
                        <span>Menu</span>
                    </button>
                    <div>
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                        <p class="page-desc">@yield('page-subtitle', 'Sistem Layanan Pengaduan Universitas Methodist Indonesia')</p>
                    </div>
                </div>

                <div class="topbar-actions">
                    @yield('topbar-actions')
                </div>
            </header>

            <!-- Komponen Notifikasi Melayang (Floating Toast Flash Message) -->
            <x-flash-message />

            <!-- Konten Halaman -->
            @yield('content')
        </main>
    </div>

    <!-- Script Aplikasi -->
    <script src="{{ asset('js/lpm.js') }}"></script>
    @stack('scripts')
</body>
</html>
