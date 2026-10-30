/**
 * Sistem Informasi Layanan Pengaduan Masyarakat (LPM)
 * Universitas Methodist Indonesia (UMI)
 * Interaktivitas Antarmuka Pengguna
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Penanganan Buka/Tutup Sidebar di Tampilan Mobile
  const toggleBtn = document.getElementById('mobileSidebarToggle');
  const sidebar = document.getElementById('appSidebar');
  const overlay = document.getElementById('sidebarBackdrop');
  const closeBtn = document.getElementById('closeSidebarBtn');

  function openSidebar() {
    if (sidebar) sidebar.classList.add('open');
    if (overlay) overlay.classList.add('active');
    document.body.style.overflow = 'hidden'; // Mencegah scroll latar saat sidebar terbuka
  }

  function closeSidebar() {
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (toggleBtn) {
    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (sidebar && sidebar.classList.contains('open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', closeSidebar);
  }

  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }

  // Tutup sidebar saat tombol Escape ditekan
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeSidebar();
      // Juga tutup semua modal yang aktif
      document.querySelectorAll('.modal-overlay.active').forEach(modal => {
        modal.classList.remove('active');
      });
    }
  });

  // 2. Alert Dismissal (Penutupan Notifikasi)
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    const btn = alert.querySelector('.alert-close');
    if (btn) {
      btn.addEventListener('click', () => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => alert.remove(), 250);
      });
    }
  });

  // 3. Pratinjau Foto Bukti Pengaduan
  const fileInput = document.getElementById('fotoInput');
  const previewContainer = document.getElementById('fotoPreviewContainer');
  const previewImage = document.getElementById('fotoPreviewImage');

  if (fileInput && previewContainer && previewImage) {
    fileInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        if (file.size > 2 * 1024 * 1024) {
          alert('Ukuran file foto maksimal adalah 2 MB!');
          fileInput.value = '';
          previewContainer.style.display = 'none';
          return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
          previewImage.src = event.target.result;
          previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        previewContainer.style.display = 'none';
      }
    });
  }

  // 4. Inisialisasi otomatis tombol intip password (.password-toggle-btn)
  document.querySelectorAll('.password-toggle-btn[data-target]').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      window.togglePasswordVisibility(targetId, btn);
    });
  });
});

// Helper Modal functions
window.openModal = function(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.add('active');
};

window.closeModal = function(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.remove('active');
};

// Helper Toggle Visibilitas Password (Sensor Sandi Otomatis)
window.togglePasswordVisibility = function(targetInputId, toggleBtn) {
  const input = typeof targetInputId === 'string' ? document.getElementById(targetInputId) : targetInputId;
  if (!input) return;

  const isPassword = input.getAttribute('type') === 'password';
  input.setAttribute('type', isPassword ? 'text' : 'password');

  const eyeOpenSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
  const eyeSlashSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

  if (toggleBtn) {
    toggleBtn.innerHTML = isPassword ? eyeSlashSvg : eyeOpenSvg;
    toggleBtn.title = isPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi';
    toggleBtn.setAttribute('aria-label', toggleBtn.title);
    if (isPassword) {
      toggleBtn.classList.add('active');
      toggleBtn.style.color = '#1358B8';
    } else {
      toggleBtn.classList.remove('active');
      toggleBtn.style.color = '#94A3B8';
    }
  }
};

// Fungsi kompatibilitas lama
window.toggleVisibilityLogin = function() {
  const btn = document.querySelector('[onclick="toggleVisibilityLogin()"]') || document.getElementById('togglePasswordLogin');
  window.togglePasswordVisibility('password', btn);
};

window.toggleVisibility = function(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);
  if (!input) return;
  const btn = icon ? icon.closest('button') : null;
  window.togglePasswordVisibility(input, btn);
};
