{{--
    Komponen Notifikasi Melayang (Floating Toast Flash Message)
    Sistem LPM Universitas Methodist Indonesia (UMI)
    Warna Semantik: Hijau (Sukses), Merah (Error), Kuning (Peringatan), Biru UMI (Informasi)
--}}

@php
    $messages = [];

    if (session('success')) {
        $messages[] = [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => session('success'),
            'border_color' => '#16A34A',
            'bg_color' => '#F0FDF4',
            'text_color' => '#166534',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
        ];
    }

    if (session('error')) {
        $messages[] = [
            'type' => 'error',
            'title' => 'Terjadi Kesalahan',
            'text' => session('error'),
            'border_color' => '#DC2626',
            'bg_color' => '#FEF2F2',
            'text_color' => '#991B1B',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
        ];
    }

    if (session('warning')) {
        $messages[] = [
            'type' => 'warning',
            'title' => 'Peringatan',
            'text' => session('warning'),
            'border_color' => '#D97706',
            'bg_color' => '#FFFBEB',
            'text_color' => '#92400E',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
        ];
    }

    if (session('info')) {
        $messages[] = [
            'type' => 'info',
            'title' => 'Informasi',
            'text' => session('info'),
            'border_color' => '#1358B8',
            'bg_color' => '#E6EFFA',
            'text_color' => '#0B3F8C',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1358B8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        ];
    }

    if ($errors->any()) {
        $messages[] = [
            'type' => 'error',
            'title' => 'Validasi Gagal',
            'text' => $errors->first(),
            'border_color' => '#DC2626',
            'bg_color' => '#FEF2F2',
            'text_color' => '#991B1B',
            'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
        ];
    }
@endphp

@if(count($messages) > 0)
    <div id="flash-toast-container" class="flash-toast-container" aria-live="polite" aria-atomic="true">
        @foreach($messages as $msg)
            <div 
                class="flash-toast flash-toast-{{ $msg['type'] }}" 
                style="background-color: {{ $msg['bg_color'] }}; border-left: 4px solid {{ $msg['border_color'] }};"
                role="alert"
            >
                <div class="flash-toast-icon">
                    {!! $msg['icon'] !!}
                </div>
                <div class="flash-toast-content">
                    <div class="flash-toast-title" style="color: {{ $msg['text_color'] }};">
                        {{ $msg['title'] }}
                    </div>
                    <div class="flash-toast-text" style="color: {{ $msg['text_color'] }};">
                        {{ $msg['text'] }}
                    </div>
                </div>
                <button 
                    type="button" 
                    class="flash-toast-close" 
                    aria-label="Tutup notifikasi"
                    onclick="dismissToast(this.closest('.flash-toast'))"
                >
                    &times;
                </button>
            </div>
        @endforeach
    </div>

    <style>
        .flash-toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            max-width: 360px;
            pointer-events: none;
        }

        .flash-toast {
            pointer-events: auto;
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.06);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: opacity 0.3s ease, transform 0.3s ease, max-height 0.3s ease, margin 0.3s ease, padding 0.3s ease;
            box-sizing: border-box;
        }

        .flash-toast.toast-hiding {
            opacity: 0;
            transform: translateX(30px);
            max-height: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            overflow: hidden;
            pointer-events: none;
        }

        .flash-toast-icon {
            flex-shrink: 0;
            margin-top: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .flash-toast-content {
            flex: 1;
            min-width: 0;
        }

        .flash-toast-title {
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .flash-toast-text {
            font-size: 0.84rem;
            line-height: 1.45;
            word-break: break-word;
        }

        .flash-toast-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: #64748B;
            font-size: 1.25rem;
            line-height: 1;
            padding: 2px 4px;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s ease;
            opacity: 0.7;
        }

        .flash-toast-close:hover {
            opacity: 1;
            color: #0F172A;
            background-color: rgba(0, 0, 0, 0.05);
        }

        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @media (max-width: 640px) {
            .flash-toast-container {
                top: 16px;
                right: 16px;
                left: 16px;
                width: calc(100% - 32px);
                max-width: none;
            }
        }
    </style>

    <script>
        function dismissToast(toastElement) {
            if (!toastElement || toastElement.classList.contains('toast-hiding')) return;
            toastElement.classList.add('toast-hiding');
            setTimeout(() => {
                toastElement.remove();
                const container = document.getElementById('flash-toast-container');
                if (container && container.children.length === 0) {
                    container.remove();
                }
            }, 300);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const toasts = document.querySelectorAll('.flash-toast');
            toasts.forEach(toast => {
                // Otomatis menghilang setelah 4 detik
                setTimeout(() => {
                    dismissToast(toast);
                }, 4000);
            });
        });
    </script>
@endif
