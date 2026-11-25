/**
 * POGI SKP Tracker - Shared Navigation & UI Shell Utilities
 * Perkumpulan Obstetri dan Ginekologi Indonesia
 * Standard MedCred Header & Interaction Controller
 */


// Universal Safe Store Adapter (Guarantees zero ReferenceError across all views)
if (typeof window !== 'undefined') {
  if (typeof window.PogiStore === 'undefined') {
    window.PogiStore = {};
  }

  window.PogiStore._currentUser = null;
  window.PogiStore.getCurrentUser = function () {
    // Selalu prioritaskan window.AUTH_USER yang di-inject dari Blade (server-side)
    // JANGAN cache fallback guest agar tidak mengotori state sesi berikutnya
    if (typeof window !== 'undefined') {
      if (window.AUTH_USER) {
        this._currentUser = window.AUTH_USER;
        return window.AUTH_USER;
      }
      if (window.CURRENT_USER) {
        this._currentUser = window.CURRENT_USER;
        return window.CURRENT_USER;
      }
    }
    // Hanya gunakan cache jika sudah ter-set dari AUTH_USER/CURRENT_USER sebelumnya
    if (this._currentUser && this._currentUser.id !== 'guest') return this._currentUser;
    // Fallback guest — TIDAK disimpan ke cache agar tidak memblokir AUTH_USER berikutnya
    return {
      id: 'guest',
      name: 'Super Admin',
      email: '',
      role: 'GUEST',
      branch: 'Pusat'
    };
  };
  window.PogiStore.formatCurrentDate = function () {
    const d = new Date();
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
  };
  window.PogiStore.getDoctors = function () {
    return (window.LIVE_SERVER_DOCTORS || window.LIVE_DATABASE_DOCTORS || window.LIVE_MANAGE_DOCTORS || []);
  };
  window.PogiStore.getProvinces = function () {
    const provs = window.LIVE_SERVER_PROVINCES || window.LIVE_MANAGE_BRANCHES || [];
    return provs.map(p => typeof p === 'string' ? { name: p } : p);
  };
  window.PogiStore.getInstitutions = function () {
    return window.LIVE_MANAGE_INSTITUTIONS || window.LIVE_SERVER_INSTITUTIONS || [];
  };
  window.PogiStore.getEvents = function () {
    return window.LIVE_MANAGE_EVENTS || window.LIVE_SERVER_EVENTS || [];
  };
  window.PogiStore.getCategories = function () {
    return window.LIVE_MANAGE_CATEGORIES || [];
  };
  window.PogiStore.getUsers = function () {
    return window.LIVE_MANAGE_USERS || [];
  };
  window.PogiStore.getActivities = function () {
    return window.LIVE_SERVER_ACTIVITIES || window.LIVE_SERVER_LOGS || [];
  };
}

// Mobile Sidebar Toggle
function toggleMobileSidebar(forceOpen) {
  const sidebar = document.getElementById('appSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (!sidebar || !overlay) return;

  const isOpen = !sidebar.classList.contains('-translate-x-full');
  const shouldOpen = forceOpen !== undefined ? forceOpen : !isOpen;

  if (shouldOpen) {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden', 'pointer-events-none');
    overlay.classList.add('opacity-100');
    document.body.classList.add('overflow-hidden');
  } else {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden', 'pointer-events-none');
    overlay.classList.remove('opacity-100');
    document.body.classList.remove('overflow-hidden');
  }
}

// Notification Dropdown Toggle
function toggleTopNotifDropdown() {
  const dd = document.getElementById('topNotifDropdown');
  if (dd) {
    dd.classList.toggle('hidden');
    // Close user dropdown if open
    const userDd = document.getElementById('topUserDropdown');
    if (userDd) userDd.classList.add('hidden');
  }
}

function markAllNotificationsAsRead() {
  const badgeDot = document.getElementById('notifBadgeDot');
  const unreadBadge = document.getElementById('notifUnreadBadge');
  if (badgeDot) badgeDot.classList.add('hidden');
  if (unreadBadge) {
    unreadBadge.innerText = '0 Baru';
    unreadBadge.className = 'px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold';
  }
  showToast('Semua notifikasi telah ditandai sebagai dibaca', 'info');
}

// User Profile Dropdown Toggle
function toggleTopUserDropdown() {
  const dd = document.getElementById('topUserDropdown');
  if (dd) {
    applyRbacEnforcement();
    dd.classList.toggle('hidden');
    // Close notif dropdown if open
    const notifDd = document.getElementById('topNotifDropdown');
    if (notifDd) notifDd.classList.add('hidden');
  }
}

// Global Top Search Functionality
function handleGlobalTopSearch(query) {
  const dropdown = document.getElementById('topSearchResultsDropdown');
  const clearBtn = document.getElementById('clearTopSearchBtn');
  if (!dropdown) return;

  const q = (query || '').trim().toLowerCase();
  if (clearBtn) {
    if (q.length > 0) clearBtn.classList.remove('hidden');
    else clearBtn.classList.add('hidden');
  }

  // If currently on doctors.html, also sync with doctors table search live
  const docSearchInput = document.getElementById('searchDoctorInput');
  if (docSearchInput && typeof applyDoctorFilters === 'function') {
    docSearchInput.value = query;
    applyDoctorFilters();
  }

  if (q.length < 2) {
    dropdown.innerHTML = '';
    dropdown.classList.add('hidden');
    return;
  }

  if (typeof PogiStore === 'undefined' || typeof PogiStore.getDoctors !== 'function') {
    return;
  }

  const doctors = PogiStore.getDoctors();
  const matches = doctors.filter(d => {
    return (d.name && d.name.toLowerCase().includes(q)) ||
      (d.pogiId && d.pogiId.toLowerCase().includes(q)) ||
      (d.nik && d.nik.includes(q)) ||
      (d.hospital && d.hospital.toLowerCase().includes(q)) ||
      (d.province && d.province.toLowerCase().includes(q)) ||
      (d.strNumber && d.strNumber.includes(q));
  }).slice(0, 6);

  if (matches.length === 0) {
    dropdown.innerHTML = `
      <div class="px-4 py-4 text-center text-slate-400">
        <span class="material-symbols-outlined text-2xl text-slate-300">person_search</span>
        <div class="font-bold text-xs mt-1 text-slate-600">Dokter tidak ditemukan</div>
        <div class="text-[10px] text-slate-400">Coba cari dengan nama, ID POGI, atau NIK</div>
      </div>
    `;
    dropdown.classList.remove('hidden');
    return;
  }

  let html = `
    <div class="px-3.5 py-1.5 border-b border-slate-100 flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-wider">
      <span>Hasil Pencarian (${matches.length})</span>
      <span class="text-[#16b3ac] font-semibold">Enter untuk cari penuh</span>
    </div>
    <div class="divide-y divide-slate-100">
  `;

  matches.forEach(d => {
    const initials = d.initials || d.avatar || 'DR';
    const isComplete = (d.status === 'Complete' || d.q4Credits >= 8.0);
    html += `
      <div onclick="selectGlobalSearchResult('${d.id}')" class="p-3 hover:bg-slate-50 transition cursor-pointer flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0">
          ${initials}
        </div>
        <div class="min-w-0 flex-1">
          <div class="font-bold text-slate-900 text-xs truncate">${d.name}</div>
          <div class="text-[10px] text-slate-400 font-mono mt-0.5">${d.pogiId} &bull; ${d.province}</div>
        </div>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold ${isComplete ? 'bg-emerald-50 text-[#16b3ac] border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200'} border shrink-0">
          ${isComplete ? 'Complete' : 'Incomplete'}
        </span>
      </div>
    `;
  });

  html += `
    </div>
    <div class="px-3.5 pt-2 border-t border-slate-100 text-center">
      <a href="/doctors?search=${encodeURIComponent(query)}" class="text-[#16b3ac] hover:underline font-bold text-[11px]">
        Lihat semua dokter di Direktori &rarr;
      </a>
    </div>
  `;

  dropdown.innerHTML = html;
  dropdown.classList.remove('hidden');
}

function handleGlobalSearchKeydown(e) {
  if (e.key === 'Enter') {
    const input = document.getElementById('topGlobalSearchInput');
    if (input && input.value.trim()) {
      window.location.href = '/doctors?search=' + encodeURIComponent(input.value.trim());
    }
  } else if (e.key === 'Escape') {
    clearGlobalTopSearch();
  }
}

function clearGlobalTopSearch() {
  const input = document.getElementById('topGlobalSearchInput');
  const dropdown = document.getElementById('topSearchResultsDropdown');
  const clearBtn = document.getElementById('clearTopSearchBtn');
  if (input) input.value = '';
  if (dropdown) {
    dropdown.innerHTML = '';
    dropdown.classList.add('hidden');
  }
  if (clearBtn) clearBtn.classList.add('hidden');

  if (typeof applyDoctorFilters === 'function') {
    const docSearchInput = document.getElementById('searchDoctorInput');
    if (docSearchInput) docSearchInput.value = '';
    applyDoctorFilters();
  }
}

function selectGlobalSearchResult(docId) {
  const dropdown = document.getElementById('topSearchResultsDropdown');
  if (dropdown) dropdown.classList.add('hidden');

  if (typeof openDoctorDetailModal === 'function') {
    openDoctorDetailModal(docId);
  } else {
    window.location.href = '/doctor-detail?id=' + encodeURIComponent(docId);
  }
}

// Toast Notifications
function showToast(message, type = 'success') {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none px-4 sm:px-0';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  const icon = type === 'success' ? 'check_circle' : type === 'warning' ? 'warning' : 'info';
  const bg = type === 'success' ? 'bg-[#16b3ac] text-white' : type === 'warning' ? 'bg-amber-600 text-white' : 'bg-slate-900 text-white';

  toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl ${bg} text-xs font-semibold transition-all duration-300 transform translate-y-4 opacity-0`;
  toast.innerHTML = `
    <span class="material-symbols-outlined text-base shrink-0">${icon}</span>
    <span class="flex-1">${message}</span>
    <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white p-1 cursor-pointer">
      <span class="material-symbols-outlined text-xs">close</span>
    </button>
  `;

  container.appendChild(toast);
  requestAnimationFrame(() => {
    toast.classList.remove('translate-y-4', 'opacity-0');
  });

  setTimeout(() => {
    toast.classList.add('opacity-0', 'translate-y-2');
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

// Logout Confirmation & Secure Session Invalidation
function performLogout() {
  if (confirm('Apakah Anda yakin ingin keluar dari Konsol POGI SKP Tracker?')) {
    try {
      localStorage.removeItem('pogi_current_user');
      sessionStorage.clear();
    } catch (e) { }

    window.location.href = '/logout';
  }
}

// Global Click-Outside Listener to Close Dropdowns
document.addEventListener('click', (e) => {
  // 1. Org Scope Dropdown
  const orgBtn = document.getElementById('headerOrgScopeBtn');
  const orgDropdown = document.getElementById('orgScopeDropdown');
  if (orgBtn && orgDropdown && !orgBtn.contains(e.target) && !orgDropdown.contains(e.target)) {
    orgDropdown.classList.add('hidden');
  }

  // 2. Notifications Dropdown
  const notifBtn = document.getElementById('topNotifBtn');
  const notifDropdown = document.getElementById('topNotifDropdown');
  if (notifBtn && notifDropdown && !notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
    notifDropdown.classList.add('hidden');
  }

  // 3. User Avatar Dropdown
  const userBtn = document.getElementById('topUserBtn');
  const userDropdown = document.getElementById('topUserDropdown');
  if (userBtn && userDropdown && !userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
    userDropdown.classList.add('hidden');
  }

  // 4. Global Search Results Dropdown
  const searchInput = document.getElementById('topGlobalSearchInput');
  const searchDropdown = document.getElementById('topSearchResultsDropdown');
  if (searchInput && searchDropdown && !searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
    searchDropdown.classList.add('hidden');
  }
});

// Auto-handle query parameters when page loads (e.g. search query or openDoc)
document.addEventListener('DOMContentLoaded', () => {
  try {
    const params = new URLSearchParams(window.location.search);
    const searchParam = params.get('search');
    if (searchParam) {
      const topInput = document.getElementById('topGlobalSearchInput');
      if (topInput) {
        topInput.value = searchParam;
        const clearBtn = document.getElementById('clearTopSearchBtn');
        if (clearBtn) clearBtn.classList.remove('hidden');
      }
      const docSearchInput = document.getElementById('searchDoctorInput');
      if (docSearchInput) {
        docSearchInput.value = searchParam;
        if (typeof applyDoctorFilters === 'function') {
          setTimeout(applyDoctorFilters, 100);
        }
      }
    }

    const openDocId = params.get('openDoc');
    if (openDocId && typeof openDoctorDetailModal === 'function') {
      setTimeout(() => {
        openDoctorDetailModal(openDocId);
      }, 300);
    }
  } catch (err) { }
});


function applyRbacEnforcement() {
  let user = null;
  if (typeof PogiStore !== 'undefined' && typeof PogiStore.getCurrentUser === 'function') {
    user = PogiStore.getCurrentUser();
  } else if (typeof window !== 'undefined' && window.AUTH_USER) {
    user = window.AUTH_USER;
  }
  if (!user) return;

  const rawRole = (user.role || '').toUpperCase();
  let roleKey = 'SUPER_ADMIN';
  if (rawRole === 'SUPER_ADMIN' || rawRole === 'SUPER ADMIN') {
    roleKey = 'SUPER_ADMIN';
  } else if (rawRole === 'ADMIN_PUSAT' || rawRole === 'ADMIN PUSAT') {
    roleKey = 'ADMIN_PUSAT';
  } else if (rawRole === 'ADMIN_POGI' || rawRole === 'ADMIN POGI' || rawRole === 'VERIFIKATOR' || rawRole === 'ADMIN_OPERASIONAL') {
    roleKey = 'ADMIN_POGI';
  } else if (rawRole === 'ADMIN_CABANG' || rawRole === 'ADMIN CABANG') {
    roleKey = 'ADMIN_CABANG';
  } else if (rawRole === 'DOKTER' || rawRole === 'USER/DOKTER' || rawRole === 'USER') {
    roleKey = 'DOKTER';
  }

  // 1. Sync dropdowns / switchers
  // Helper to extract clean initials from full name
  function extractUserInitials(name, roleKey, defaultAvatar) {
    if (defaultAvatar && defaultAvatar !== 'AP' && defaultAvatar.trim() !== '') return defaultAvatar.trim();
    if (name && typeof name === 'string' && name.trim()) {
      const clean = name.replace(/^(dr\.|dr\s+|drg\.|prof\.|prof\s+)/i, '')
                        .replace(/,\s*(Sp\.OG|S\.Farm|M\.Kes|MARS|PhD|M\.Ked|S\.Ked|M\.Biomed|Subsp\.[a-zA-Z]+).*$/i, '')
                        .trim();
      const parts = clean.split(/\s+/).filter(Boolean);
      if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
      } else if (parts.length === 1 && parts[0].length >= 1) {
        return parts[0][0].toUpperCase();
      }
    }
    if (defaultAvatar && defaultAvatar.trim() !== '') return defaultAvatar.trim();
    if (roleKey === 'SUPER_ADMIN') return 'SA';
    if (roleKey === 'ADMIN_PUSAT') return 'BP';
    if (roleKey === 'ADMIN_CABANG') return 'DF';
    if (roleKey === 'DOKTER') return 'CS';
    return 'SR'; // Default Admin POGI: Siti Rahma (SR)
  }

  const userInitials = extractUserInitials(user.name || user.displayName, roleKey, user.avatar);
  const userBgClass = user.avatarBg || (roleKey === 'SUPER_ADMIN' ? 'bg-purple-700 text-white' : (roleKey === 'ADMIN_PUSAT' ? 'bg-indigo-700 text-white' : (roleKey === 'ADMIN_CABANG' ? 'bg-sky-700 text-white' : (roleKey === 'DOKTER' ? 'bg-teal-700 text-white' : 'bg-[#16b3ac] text-white'))));

  // 1. Sync dropdowns / switchers
  const topSelect = document.getElementById('topHeaderRoleSelect');
  if (topSelect) topSelect.value = roleKey;
  const switcherSelect = document.getElementById('headerRoleSwitcher');
  if (switcherSelect) switcherSelect.value = roleKey;

  // 2. Update Sidebar bottom profile card
  const aside = document.querySelector('aside');
  if (aside) {
    const logoutBtn = aside.querySelector('button[onclick*="performLogout"]') ||
      aside.querySelector('button[title*="Keluar"]') ||
      aside.querySelector('button[title*="Sesi"]') ||
      aside.querySelector('button .material-symbols-outlined');
    const profileCard = document.getElementById('sidebarUserProfileCard') ||
      (logoutBtn ? logoutBtn.closest('.rounded-xl') : null);
    if (profileCard) {
      const avatarEl = document.getElementById('sidebarUserAvatar') ||
        profileCard.querySelector('#sidebarUserAvatar') ||
        profileCard.querySelector('.rounded-full') ||
        profileCard.querySelector('.w-9.h-9') ||
        profileCard.querySelector('.w-8.h-8');
      if (avatarEl) {
        avatarEl.innerText = userInitials;
        avatarEl.className = 'w-9 h-9 rounded-full ' + userBgClass + ' font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs';
      }
      const nameEl = document.getElementById('sidebarUserName') ||
        profileCard.querySelector('#sidebarUserName') ||
        profileCard.querySelector('.text-slate-900') ||
        profileCard.querySelector('.text-slate-800');
      if (nameEl) {
        nameEl.innerText = user.name || user.displayName || 'Super Admin';
      }
      const roleEl = document.getElementById('sidebarUserRole') ||
        document.getElementById('sidebarUserEmail') ||
        profileCard.querySelector('#sidebarUserRole') ||
        profileCard.querySelector('#sidebarUserEmail') ||
        profileCard.querySelector('.uppercase') ||
        profileCard.querySelector('.font-mono');
      if (roleEl) {
        roleEl.innerText = user.shortRole || user.roleLabel || user.email || roleKey;
        const roleColorClass = roleKey === 'SUPER_ADMIN' ? 'text-purple-700' : (roleKey === 'ADMIN_PUSAT' ? 'text-indigo-700' : (roleKey === 'ADMIN_CABANG' ? 'text-sky-700' : (roleKey === 'DOKTER' ? 'text-teal-700' : 'text-[#16b3ac]')));
        roleEl.className = 'text-[9px] font-bold ' + roleColorClass + ' uppercase tracking-wider truncate';
      }
    }

    // Nav Item Visibility:
    // admin-performance.html: KHUSUS Admin Pusat & Super Admin (Memantau aktivitas & kinerja verifikator)
    document.querySelectorAll('a[href*="admin-performance"]').forEach(link => {
      link.style.display = (roleKey === 'SUPER_ADMIN' || roleKey === 'ADMIN_PUSAT') ? 'flex' : 'none';
    });

    // manage.html (Pengaturan Data): Disembunyikan untuk User/Dokter
    document.querySelectorAll('a[href*="manage"]').forEach(link => {
      link.style.display = (roleKey === 'DOKTER') ? 'none' : 'flex';
    });

    // provinces.html & activity.html: Disembunyikan untuk User/Dokter
    document.querySelectorAll('a[href*="provinces"], a[href*="activity"]').forEach(link => {
      link.style.display = (roleKey === 'DOKTER') ? 'none' : 'flex';
    });

    // Heading Pengaturan Data (Manage): Sembunyikan untuk Dokter
    const allDivs = aside.querySelectorAll('div');
    allDivs.forEach(div => {
      if (div.children.length === 0 && div.innerText && div.innerText.trim().startsWith('PENGATURAN DATA')) {
        div.style.display = (roleKey === 'DOKTER') ? 'none' : 'block';
      }
    });
  }

  // 3. Update Top Header User Avatar button & profile dropdown
  const topUserBtn = document.getElementById('topUserBtn');
  if (topUserBtn) {
    const initialsSpan = topUserBtn.querySelector('#headerUserInitials') ||
                         topUserBtn.querySelector('span') ||
                         topUserBtn.querySelector('div');
    if (initialsSpan) {
      initialsSpan.innerText = userInitials;
    } else {
      topUserBtn.innerText = userInitials;
    }
    topUserBtn.className = 'w-9 h-9 rounded-full ' + userBgClass + ' font-black text-xs flex items-center justify-center shadow-xs transition cursor-pointer ring-2 ring-emerald-600/20';
    topUserBtn.title = 'Profil: ' + (user.name || 'Super Admin');
  }

  const ddAvatar = document.getElementById('headerDropdownAvatar') ||
                   document.querySelector('#topUserDropdown .rounded-full');
  if (ddAvatar) {
    ddAvatar.innerText = userInitials;
    ddAvatar.className = 'w-10 h-10 rounded-full ' + userBgClass + ' font-black text-sm flex items-center justify-center shrink-0 shadow-2xs';
  }
  const ddName = document.getElementById('headerDropdownName') ||
                 document.querySelector('#topUserDropdown .text-slate-900');
  if (ddName) {
    ddName.innerText = user.name || user.displayName || 'Super Admin';
  }
  const ddRole = document.getElementById('headerDropdownRole') ||
                 document.querySelector('#topUserDropdown .text-slate-500');
  if (ddRole) {
    ddRole.innerText = user.roleLabel || user.shortRole || roleKey;
  }

  // 4. Update Header Scope button label
  const orgBtn = document.getElementById('headerOrgScopeBtn') ||
    document.getElementById('orgScopeBtn') ||
    document.querySelector('header button[onclick*="OrgScope"]');
  if (orgBtn) {
    const orgSpan = orgBtn.querySelector('span.truncate') ||
      document.getElementById('selectedOrgScopeLabel') ||
      orgBtn.querySelector('span:not(.material-symbols-outlined)');
    if (orgSpan) {
      if (roleKey === 'ADMIN_CABANG' && user.branch && user.branch !== 'Nasional') {
        orgSpan.innerText = 'Wilayah Cabang: ' + user.branch;
      } else if (roleKey === 'DOKTER') {
        orgSpan.innerText = 'Dokter Spesialis POGI';
      } else {
        if (!orgSpan.innerText.includes('POGI Cabang')) {
          orgSpan.innerText = 'Semua Wilayah Nasional (38 Cabang)';
        }
      }
    }
  }

  // 5. Page Guarding for Restricted Pages
  const currentPath = window.location.pathname.toLowerCase();

  // Guard: admin-performance.html — Hanya untuk Super Admin & Admin Pusat
  if (currentPath.includes('admin-performance') && !(roleKey === 'SUPER_ADMIN' || roleKey === 'ADMIN_PUSAT')) {
    const mainEl = document.querySelector('main');
    if (mainEl) {
      mainEl.innerHTML = `
        <div class="p-8 max-w-xl mx-auto my-12 bg-white rounded-3xl border border-rose-200 shadow-xl text-center space-y-4">
          <div class="w-16 h-16 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">lock</span>
          </div>
          <h2 class="text-lg font-black text-slate-900">Akses Modul Terbatas (RBAC)</h2>
          <p class="text-xs text-slate-600 leading-relaxed">
            Modul <strong>Admin Performance &amp; SLA Tracker</strong> adalah instrumen evaluasi nasional untuk <strong>Admin Pusat</strong> dan <strong>Super Admin</strong> guna memantau performa serta kecepatan verifikasi tim Admin POGI.
          </p>
          <div class="pt-2">
            <a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#16b3ac] text-white text-xs font-bold hover:bg-[#129993] transition">
              <span class="material-symbols-outlined text-sm">dashboard</span>
              <span>Kembali ke Dashboard</span>
            </a>
          </div>
        </div>
      `;
    }
    return;
  }

  // Guard: manage.html untuk DOKTER
  if (currentPath.includes('manage') && roleKey === 'DOKTER') {
    const mainEl = document.querySelector('main');
    if (mainEl) {
      mainEl.innerHTML = `
        <div class="p-8 max-w-xl mx-auto my-12 bg-white rounded-3xl border border-rose-200 shadow-xl text-center space-y-4">
          <div class="w-16 h-16 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
            <span class="material-symbols-outlined text-3xl">admin_panel_settings</span>
          </div>
          <h2 class="text-lg font-black text-slate-900">Akses Pengaturan Terbatas (RBAC)</h2>
          <p class="text-xs text-slate-600 leading-relaxed">
            Modul <strong>Pengaturan Data (Manage)</strong> hanya dapat diakses oleh Administrator &amp; Admin POGI. Sebagai dokter, Anda dapat mengakses portofolio SKP Anda secara mandiri.
          </p>
          <div class="pt-2">
            <a href="/doctor-detail" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#16b3ac] text-white text-xs font-bold hover:bg-[#129993] transition">
              <span class="material-symbols-outlined text-sm">person</span>
              <span>Ke Profil SKP Saya</span>
            </a>
          </div>
        </div>
      `;
    }
    return;
  }

  // Guard: manage.html Tab User — EKSKLUSIF Super Admin (Sembunyikan untuk selain Super Admin)
  if (currentPath.includes('manage') && roleKey !== 'SUPER_ADMIN') {
    const tabUserBtn = document.getElementById('tab-btn-user');
    const sideUserBtn = document.getElementById('side-nav-user');
    if (tabUserBtn) tabUserBtn.style.display = 'none';
    if (sideUserBtn) sideUserBtn.style.display = 'none';
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'user' && typeof switchManageTab === 'function') {
      switchManageTab('doctor');
      showToast('Tab Kelola Pengguna hanya dapat diakses oleh Super Admin.', 'warning');
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    applyRbacEnforcement();
  }, 30);
});

// =========================================================================
// UNIVERSAL MODAL VIEWPORT HOISTER & Z-INDEX STANDARDIZATION
// Memastikan seluruh modal di seluruh aplikasi lepas dari stacking context
// (overflow-hidden / transform / anim), berposisi fixed inset-0 level body,
// dan memiliki z-[9999] agar backdrop overlay menutupi 100% layar (sidebar & header).
// =========================================================================
function hoistAllModalsToBody() {
  if (typeof document === 'undefined' || !document.body) return;

  const modals = document.querySelectorAll('div[id$="Modal"], div[id$="modal"], .fixed.inset-0:not(#sidebarOverlay)');
  modals.forEach(modal => {
    if (modal.id === 'sidebarOverlay') return;

    // Pindahkan ke root body jika masih terperangkap di dalam container / main
    if (modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }

    // Pastikan z-index tertinggi untuk overlay layar penuh menembus sidebar (z-50) & header (z-30)
    modal.classList.remove('z-40', 'z-50', 'z-30', 'z-20', 'z-10');
    if (!modal.classList.contains('z-[9999]')) {
      modal.classList.add('z-[9999]');
    }

    // CATATAN: Backdrop click-to-close TIDAK ditambahkan di sini.
    // Setiap halaman mengatur sendiri perilaku dismiss modal mereka
    // agar tidak bertentangan dengan logika form submit yang belum selesai.
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', hoistAllModalsToBody);
} else {
  hoistAllModalsToBody();
}
window.addEventListener('load', hoistAllModalsToBody);

