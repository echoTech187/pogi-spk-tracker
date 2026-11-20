/**
 * POGI SKP Tracker - Shared Navigation & UI Shell Utilities
 * Perkumpulan Obstetri dan Ginekologi Indonesia
 * Standard MedCred Header & Interaction Controller
 */

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
    const provSelect = document.getElementById('filterProvince');
    if (provSelect) {
      if (scope === 'jabar') provSelect.value = 'Jawa Barat';
      else if (scope === 'dki') provSelect.value = 'DKI Jakarta';
      else if (scope === 'jatim') provSelect.value = 'Jawa Timur';
      else provSelect.value = 'ALL';
      applyDoctorFilters();
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
      <span class="text-[#006948] font-semibold">Enter untuk cari penuh</span>
    </div>
    <div class="divide-y divide-slate-100">
  `;

  matches.forEach(d => {
    const initials = d.initials || d.avatar || 'DR';
    const isComplete = (d.status === 'Complete' || d.q4Credits >= 8.0);
    html += `
      <div onclick="selectGlobalSearchResult('${d.id}')" class="p-3 hover:bg-slate-50 transition cursor-pointer flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#006948] font-black text-xs flex items-center justify-center shrink-0">
          ${initials}
        </div>
        <div class="min-w-0 flex-1">
          <div class="font-bold text-slate-900 text-xs truncate">${d.name}</div>
          <div class="text-[10px] text-slate-400 font-mono mt-0.5">${d.pogiId} &bull; ${d.province}</div>
        </div>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold ${isComplete ? 'bg-emerald-50 text-[#006948] border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200'} border shrink-0">
          ${isComplete ? 'Complete' : 'Incomplete'}
        </span>
      </div>
    `;
  });

  html += `
    </div>
    <div class="px-3.5 pt-2 border-t border-slate-100 text-center">
      <a href="doctors.html?search=${encodeURIComponent(query)}" class="text-[#006948] hover:underline font-bold text-[11px]">
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
      window.location.href = 'doctors.html?search=' + encodeURIComponent(input.value.trim());
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
    window.location.href = 'doctors.html?openDoc=' + encodeURIComponent(docId);
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
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#006948] flex items-center justify-center">
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
              <rect x="0" y="0" width="30" height="30" rx="3" fill="#006948" />
              <rect x="5" y="5" width="20" height="20" rx="2" fill="#fff" />
              <rect x="10" y="10" width="10" height="10" rx="1" fill="#006948" />
              <rect x="70" y="0" width="30" height="30" rx="3" fill="#006948" />
              <rect x="75" y="5" width="20" height="20" rx="2" fill="#fff" />
              <rect x="80" y="10" width="10" height="10" rx="1" fill="#006948" />
              <rect x="0" y="70" width="30" height="30" rx="3" fill="#006948" />
              <rect x="5" y="75" width="20" height="20" rx="2" fill="#fff" />
              <rect x="10" y="80" width="10" height="10" rx="1" fill="#006948" />
              <rect x="36" y="8" width="6" height="6" rx="1" fill="#006948" />
              <rect x="48" y="8" width="6" height="6" rx="1" fill="#006948" />
              <rect x="40" y="20" width="8" height="8" rx="1" fill="#006948" />
              <rect x="55" y="20" width="6" height="6" rx="1" fill="#006948" />
              <rect x="8" y="40" width="8" height="8" rx="1" fill="#006948" />
              <rect x="22" y="42" width="6" height="6" rx="1" fill="#006948" />
              <rect x="36" y="38" width="10" height="10" rx="2" fill="#006948" />
              <rect x="52" y="36" width="8" height="8" rx="1" fill="#006948" />
              <rect x="68" y="42" width="6" height="6" rx="1" fill="#006948" />
              <rect x="82" y="38" width="8" height="8" rx="1" fill="#006948" />
              <rect x="38" y="56" width="6" height="6" rx="1" fill="#006948" />
              <rect x="50" y="54" width="8" height="8" rx="1" fill="#006948" />
              <rect x="64" y="58" width="6" height="6" rx="1" fill="#006948" />
              <rect x="80" y="56" width="8" height="8" rx="1" fill="#006948" />
              <rect x="38" y="72" width="10" height="10" rx="2" fill="#006948" />
              <rect x="54" y="74" width="6" height="6" rx="1" fill="#006948" />
              <rect x="68" y="70" width="8" height="8" rx="1" fill="#006948" />
              <rect x="84" y="76" width="6" height="6" rx="1" fill="#006948" />
              <rect x="42" y="88" width="8" height="8" rx="1" fill="#006948" />
              <rect x="58" y="86" width="6" height="6" rx="1" fill="#006948" />
              <rect x="74" y="88" width="8" height="8" rx="1" fill="#006948" />
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
          <button type="button" onclick="copyDoctorQrLink()" class="w-full py-2.5 px-4 rounded-xl bg-[#006948] hover:bg-[#005439] text-white font-extrabold shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
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
  const bg = type === 'success' ? 'bg-[#006948] text-white' : type === 'warning' ? 'bg-amber-600 text-white' : 'bg-slate-900 text-white';

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

// Logout Confirmation
function performLogout() {
  if (confirm('Apakah Anda yakin ingin keluar dari Konsol POGI SKP Tracker?')) {
    window.location.href = 'index.html';
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
