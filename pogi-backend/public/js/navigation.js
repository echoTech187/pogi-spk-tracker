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
  window.PogiStore.getCurrentUser = function() {
    if (this._currentUser) return this._currentUser;
    if (typeof window !== 'undefined') {
      if (window.AUTH_USER) return (this._currentUser = window.AUTH_USER);
      if (window.CURRENT_USER) return (this._currentUser = window.CURRENT_USER);
    }
    return {
      id: 'auth-user',
      name: 'Pengguna',
      email: 'user@example.com',
      role: 'SUPER_ADMIN',
      branch: 'Pusat'
    };
  };
  window.PogiStore.getAllRoles = function() {
    return [
      { role: 'SUPER_ADMIN', roleLabel: 'Super Admin', branch: 'Pusat' },
      { role: 'ADMIN_PUSAT', roleLabel: 'Admin Pusat', branch: 'Pusat' },
      { role: 'ADMIN_POGI', roleLabel: 'Admin POGI', branch: 'Pusat' },
      { role: 'ADMIN_CABANG', roleLabel: 'Admin Cabang', branch: 'DKI Jakarta' },
      { role: 'DOKTER', roleLabel: 'User/Dokter', branch: 'DKI Jakarta' }
    ];
  };
  window.PogiStore.setCurrentUser = function(u) {
    this._currentUser = typeof u === 'string' ? { id: 'user-' + u.toLowerCase(), name: u, role: u, branch: 'Pusat' } : u;
    if (typeof window !== 'undefined') window.AUTH_USER = this._currentUser;
  };
  window.PogiStore.formatCurrentDate = function() {
    const d = new Date();
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
  };
  window.PogiStore.getDoctors = function() {
    return (window.LIVE_SERVER_DOCTORS || window.LIVE_DATABASE_DOCTORS || window.LIVE_MANAGE_DOCTORS || []);
  };
  window.PogiStore.getProvinces = function() {
    const provs = window.LIVE_SERVER_PROVINCES || window.LIVE_MANAGE_BRANCHES || [];
    return provs.map(p => typeof p === 'string' ? { name: p } : p);
  };
  window.PogiStore.getInstitutions = function() {
    return window.LIVE_MANAGE_INSTITUTIONS || window.LIVE_SERVER_INSTITUTIONS || [];
  };
  window.PogiStore.getEvents = function() {
    return window.LIVE_MANAGE_EVENTS || window.LIVE_SERVER_EVENTS || [];
  };
  window.PogiStore.getCategories = function() {
    return window.LIVE_MANAGE_CATEGORIES || [];
  };
  window.PogiStore.getUsers = function() {
    return window.LIVE_MANAGE_USERS || [];
  };
  window.PogiStore.getActivities = function() {
    return window.LIVE_SERVER_ACTIVITIES || window.LIVE_SERVER_LOGS || [];
  };
  window.PogiStore.generateUUIDv7 = function() {
    return 'id-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9);
  };
  window.PogiStore.addActivity = window.PogiStore.addActivity || function() {};
  window.PogiStore.saveDoctors = window.PogiStore.saveDoctors || function() {};
  window.PogiStore.saveInstitutions = window.PogiStore.saveInstitutions || function() {};
  window.PogiStore.saveEvents = window.PogiStore.saveEvents || function() {};
  window.PogiStore.saveCategories = window.PogiStore.saveCategories || function() {};
  window.PogiStore.saveUsers = window.PogiStore.saveUsers || function() {};
  window.PogiStore.reassignDoctor = window.PogiStore.reassignDoctor || function() {};
  window.PogiStore.addDoctorDocument = window.PogiStore.addDoctorDocument || function() {};
  window.PogiStore.updateDoctorDocumentStatus = window.PogiStore.updateDoctorDocumentStatus || function() {};
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

// Scope Selector Dropdown
function toggleOrgScopeDropdown() {
  const dd = document.getElementById('orgScopeDropdown');
  if (dd) {
    dd.classList.toggle('hidden');
  }
}

function selectOrgScope(scope, labelText) {
  const label = document.getElementById('selectedOrgScopeLabel');
  if (label) {
    label.innerText = labelText || (scope === 'all' ? 'Semua Wilayah Nasional (38 Provinsi)' : scope);
  }
  const dd = document.getElementById('orgScopeDropdown');
  if (dd) dd.classList.add('hidden');
  showToast('Cakupan Wilayah Diperbarui: ' + (labelText || scope), 'info');

  // If on doctors.html, trigger page filter
  if (typeof applyDoctorFilters === 'function') {
    const provSelect = document.getElementById('filterProvince') || document.getElementById('provinceFilter');
    if (provSelect) {
      if (scope === 'jabar') provSelect.value = 'Jawa Barat';
      else if (scope === 'dki') provSelect.value = 'DKI Jakarta';
      else if (scope === 'jatim') provSelect.value = 'Jawa Timur';
      else provSelect.value = 'ALL';
      applyDoctorFilters();
    }
  }

  // If on provinces.html, filter search input
  if (typeof filterProvinces === 'function') {
    const searchInput = document.getElementById('searchProvinceInput');
    if (searchInput) {
      if (scope === 'jabar') searchInput.value = 'Jawa Barat';
      else if (scope === 'dki') searchInput.value = 'DKI Jakarta';
      else if (scope === 'jatim') searchInput.value = 'Jawa Timur';
      else searchInput.value = '';
      filterProvinces();
    }
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
    initRbacRoleSwitcher();
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

// Global QR Modal Controller
function openGlobalQrModal() {
  let modal = document.getElementById('globalQrModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'globalQrModal';
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity';
    modal.innerHTML = `
      <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center space-y-4 border border-slate-100 transform transition-all scale-100">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 text-left">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#16b3ac] flex items-center justify-center">
              <span class="material-symbols-outlined text-lg">qr_code_2</span>
            </div>
            <div>
              <div class="font-black text-xs text-slate-900 leading-tight">Link QR Dokter</div>
              <div class="text-[10px] text-slate-400">Portal Kredensial Mandiri</div>
            </div>
          </div>
          <button type="button" onclick="closeGlobalQrModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>

        <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex flex-col items-center justify-center space-y-2">
          <!-- Visual SVG QR Code -->
          <div class="w-44 h-44 bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-center relative">
            <svg class="w-full h-full text-slate-900" viewBox="0 0 100 100" fill="currentColor">
              <rect x="0" y="0" width="30" height="30" rx="3" fill="#16b3ac" />
              <rect x="5" y="5" width="20" height="20" rx="2" fill="#fff" />
              <rect x="10" y="10" width="10" height="10" rx="1" fill="#16b3ac" />
              <rect x="70" y="0" width="30" height="30" rx="3" fill="#16b3ac" />
              <rect x="75" y="5" width="20" height="20" rx="2" fill="#fff" />
              <rect x="80" y="10" width="10" height="10" rx="1" fill="#16b3ac" />
              <rect x="0" y="70" width="30" height="30" rx="3" fill="#16b3ac" />
              <rect x="5" y="75" width="20" height="20" rx="2" fill="#fff" />
              <rect x="10" y="80" width="10" height="10" rx="1" fill="#16b3ac" />
              <rect x="36" y="8" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="48" y="8" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="40" y="20" width="8" height="8" rx="1" fill="#16b3ac" />
              <rect x="55" y="20" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="8" y="40" width="8" height="8" rx="1" fill="#16b3ac" />
              <rect x="22" y="42" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="36" y="38" width="10" height="10" rx="2" fill="#16b3ac" />
              <rect x="52" y="36" width="8" height="8" rx="1" fill="#16b3ac" />
              <rect x="68" y="42" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="82" y="38" width="8" height="8" rx="1" fill="#16b3ac" />
              <rect x="38" y="56" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="50" y="54" width="8" height="8" rx="1" fill="#16b3ac" />
              <rect x="64" y="58" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="80" y="56" width="8" height="8" rx="1" fill="#16b3ac" />
              <rect x="38" y="72" width="10" height="10" rx="2" fill="#16b3ac" />
              <rect x="54" y="74" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="68" y="70" width="8" height="8" rx="1" fill="#16b3ac" />
              <rect x="84" y="76" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="42" y="88" width="8" height="8" rx="1" fill="#16b3ac" />
              <rect x="58" y="86" width="6" height="6" rx="1" fill="#16b3ac" />
              <rect x="74" y="88" width="8" height="8" rx="1" fill="#16b3ac" />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div class="w-8 h-8 rounded-lg bg-white p-0.5 shadow-md flex items-center justify-center">
                <img src="assets/img/pogi_logo.jpg" alt="POGI" class="w-full h-full object-contain">
              </div>
            </div>
          </div>
          <div class="text-[10px] text-slate-500 font-mono select-all">
            pogi.medcred.id/portal-dokter
          </div>
        </div>

        <div class="space-y-2 text-xs">
          <button type="button" onclick="copyDoctorQrLink()" class="w-full py-2.5 px-4 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
            <span class="material-symbols-outlined text-base">content_copy</span>
            <span>Salin Tautan Akses Dokter</span>
          </button>
          <button type="button" onclick="closeGlobalQrModal()" class="w-full py-2 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition cursor-pointer">
            Tutup
          </button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  }
  modal.classList.remove('hidden');
}

function closeGlobalQrModal() {
  const modal = document.getElementById('globalQrModal');
  if (modal) modal.classList.add('hidden');
}

function copyDoctorQrLink() {
  const url = 'https://pogi.medcred.id/portal-dokter/verifikasi-mandiri';
  if (navigator.clipboard) {
    navigator.clipboard.writeText(url).then(() => {
      showToast('Tautan QR Portal Dokter berhasil disalin ke clipboard!', 'success');
    }).catch(() => {
      showToast('Tautan: ' + url, 'info');
    });
  } else {
    showToast('Tautan: ' + url, 'info');
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
    } catch (e) {}

    const isLaravel = document.querySelector('meta[name="csrf-token"]') !== null;
    if (isLaravel) {
      window.location.href = '/logout';
    } else {
      window.location.href = '/login';
    }
  }
}

// Universal Header Actions
function syncSatuSehat() {
  showToast('Menghubungkan ke API SATUSEHAT Kemenkes RI...', 'info');
  setTimeout(() => {
    showToast('Sinkronisasi SATUSEHAT Berhasil: 200 Dokter Terverifikasi', 'success');
  }, 1200);
}

function openFollowUpModal() {
  showToast('Fitur Follow-up: Mengirimkan rekapitulasi data kepatuhan ke Sekretariat Cabang...', 'info');
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
  } catch (err) {}
});

// -------------------------------------------------------------
// -------------------------------------------------------------
// BAB 6.1 & 6.2 — RBAC ENFORCEMENT & ROLE SIMULATION (5 ROLE RESMI)
// 1. Super Admin (Full-access)
// 2. Admin Pusat (Memantau setiap aktifitas dan performance dari Admin POGI)
// 3. Admin POGI (Melakukan kesesuaian dokumen-dokumen, SKP, validasi & monitoring data dokter PJ)
// 4. Admin Cabang (Tugas verifikator untuk dokter wilayah cabang sendiri)
// 5. User/Dokter (Melihat progress SKP mereka sendiri)
// -------------------------------------------------------------
function switchUserRole(roleKey) {
  const roles = (typeof PogiStore !== 'undefined' && typeof PogiStore.getAllRoles === 'function')
    ? PogiStore.getAllRoles()
    : [
        { role: 'SUPER_ADMIN', roleLabel: 'Super Admin', branch: 'Pusat' },
        { role: 'ADMIN_PUSAT', roleLabel: 'Admin Pusat', branch: 'Pusat' },
        { role: 'ADMIN_POGI', roleLabel: 'Admin POGI', branch: 'Pusat' },
        { role: 'ADMIN_CABANG', roleLabel: 'Admin Cabang', branch: 'DKI Jakarta' },
        { role: 'DOKTER', roleLabel: 'User/Dokter', branch: 'DKI Jakarta' }
      ];
  const target = roles.find(r => r.role === roleKey) || roles[0];
  if (typeof PogiStore !== 'undefined' && typeof PogiStore.setCurrentUser === 'function') {
    PogiStore.setCurrentUser(target);
  } else if (typeof window !== 'undefined') {
    window.AUTH_USER = target;
  }
  showToast('Peran beralih ke: ' + target.roleLabel, 'info');

  const currentPath = window.location.pathname.toLowerCase();
  
  // Guard per role saat berpindah role
  if (target.role === 'DOKTER') {
    if (currentPath.includes('admin-performance') || currentPath.includes('manage') || currentPath.includes('provinces.html')) {
      window.location.href = '/doctor-detail';
      return;
    }
  } else if (target.role === 'ADMIN_POGI' || target.role === 'VERIFIKATOR' || target.role === 'ADMIN_CABANG') {
    if (currentPath.includes('admin-performance')) {
      window.location.href = '/dashboard';
      return;
    }
  }

  // Reload halaman seketika agar seluruh state tabel, tombol, dan guard langsung ter-render sesuai peran baru
  setTimeout(() => {
    window.location.reload();
  }, 100);
}

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
    const profileCard = logoutBtn ? logoutBtn.closest('.rounded-xl') : null;
    if (profileCard) {
      const avatarEl = profileCard.querySelector('.rounded-full') || profileCard.querySelector('div');
      if (avatarEl) {
        avatarEl.innerText = user.avatar || (user.name ? user.name.replace(/dr\.\s*|Sp\.OG.*|Prof\.|Dr\./gi, '').trim().split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase() : (roleKey === 'SUPER_ADMIN' ? 'SA' : (roleKey === 'ADMIN_PUSAT' ? 'PP' : (roleKey === 'ADMIN_CABANG' ? 'AC' : (roleKey === 'DOKTER' ? 'DR' : 'AP')))));
        avatarEl.className = 'w-9 h-9 rounded-full ' + (user.avatarBg || (roleKey === 'SUPER_ADMIN' ? 'bg-purple-700 text-white' : (roleKey === 'ADMIN_PUSAT' ? 'bg-indigo-700 text-white' : (roleKey === 'ADMIN_CABANG' ? 'bg-sky-700 text-white' : (roleKey === 'DOKTER' ? 'bg-teal-700 text-white' : 'bg-[#16b3ac] text-white'))))) + ' font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs';
      }
      const textContainer = profileCard.querySelector('.min-w-0');
      if (textContainer && textContainer.children.length >= 2) {
        textContainer.children[0].innerText = user.name || user.displayName || 'Pengguna';
        textContainer.children[1].innerText = user.shortRole || user.roleLabel || roleKey;
        const roleColorClass = roleKey === 'SUPER_ADMIN' ? 'text-purple-700' : (roleKey === 'ADMIN_PUSAT' ? 'text-indigo-700' : (roleKey === 'ADMIN_CABANG' ? 'text-sky-700' : (roleKey === 'DOKTER' ? 'text-teal-700' : 'text-[#16b3ac]')));
        textContainer.children[1].className = 'text-[9px] font-bold ' + roleColorClass + ' uppercase tracking-wider truncate';
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

  // 3. Update Top Header User Avatar button
  const topUserBtn = document.getElementById('topUserBtn');
  if (topUserBtn) {
    const topAv = topUserBtn.querySelector('div');
    if (topAv) {
      topAv.innerText = user.avatar || (user.name ? user.name.replace(/dr\.\s*|Sp\.OG.*|Prof\.|Dr\./gi, '').trim().split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase() : (roleKey === 'SUPER_ADMIN' ? 'SA' : (roleKey === 'ADMIN_PUSAT' ? 'PP' : (roleKey === 'ADMIN_CABANG' ? 'AC' : (roleKey === 'DOKTER' ? 'DR' : 'AP')))));
      topAv.className = 'w-7 h-7 rounded-lg ' + (user.avatarBg || (roleKey === 'SUPER_ADMIN' ? 'bg-purple-700 text-white' : (roleKey === 'ADMIN_PUSAT' ? 'bg-indigo-700 text-white' : (roleKey === 'ADMIN_CABANG' ? 'bg-sky-700 text-white' : (roleKey === 'DOKTER' ? 'bg-teal-700 text-white' : 'bg-[#16b3ac] text-white'))))) + ' font-extrabold text-[11px] flex items-center justify-center shadow-2xs';
    }
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

function initRbacRoleSwitcher() {
  applyRbacEnforcement();
}

document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    initRbacRoleSwitcher();
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

    // Tambahkan backdrop click to close jika belum ada
    if (!modal.dataset.backdropHandled) {
      modal.dataset.backdropHandled = 'true';
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          const closeBtn = modal.querySelector('button[onclick*="close"], button[onclick*="Modal"], button[data-dismiss]');
          if (closeBtn) {
            closeBtn.click();
          } else {
            modal.classList.add('hidden');
          }
        }
      });
    }
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', hoistAllModalsToBody);
} else {
  hoistAllModalsToBody();
}
window.addEventListener('load', hoistAllModalsToBody);
