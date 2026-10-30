<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') - Sistem LPM Universitas Methodist Indonesia</title>
    
    <!-- Favicon Resmi UMI -->
    <link rel="icon" href="{{ asset('images/logo-umi.jpg') }}">

    <!-- CSS Design System UMI -->
    <link rel="stylesheet" href="{{ asset('css/lpm.css') }}">
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .auth-card {
            width: 100%;
            max-width: 460px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(19, 88, 184, 0.15);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(19, 88, 184, 0.12), 0 0 0 1px rgba(19, 88, 184, 0.08);
            padding: 40px;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #FFFFFF;
            padding: 8px 18px;
            border-radius: 9999px;
            border: 1px solid rgba(19, 88, 184, 0.18);
            box-shadow: 0 4px 14px rgba(19, 88, 184, 0.08);
            margin-bottom: 20px;
        }
        .auth-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 8px;
        }
        .auth-brand-text {
            font-size: 1.15rem;
            font-weight: 800;
            color: #1358B8;
            letter-spacing: -0.3px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-brand-badge">
                    <img src="{{ asset('images/logo-umi.jpg') }}" alt="Logo Universitas Methodist Indonesia" class="auth-logo-img">
                    <span class="auth-brand-text">Sistem LPM</span>
                </div>
                <h1 style="font-size: 1.45rem; font-weight: 800; letter-spacing: -0.5px; color: #0F172A;">@yield('header-title', 'Layanan Pengaduan')</h1>
                <p style="font-size: 0.85rem; color: #64748B; margin-top: 4px;">@yield('header-subtitle', 'Sistem Pengaduan Universitas Methodist Indonesia')</p>
            </div>

            <!-- Komponen Notifikasi Melayang (Floating Toast Flash Message) -->
            <x-flash-message />

            @yield('content')
        </div>
    </div>
    <script src="{{ asset('js/lpm.js') }}"></script>
    @stack('scripts')
</body>
</html>
