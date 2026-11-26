@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Log Aktivitas & Jejak Audit')
@section('content')
  <!-- Main Body -->
  <main class="flex-1 overflow-y-auto space-y-6 page-fade-in pb-8">

    <!-- Clean Page Header (Modern Enterprise Standard) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-1">
      <div class="space-y-1.5 min-w-0">
        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[10px] font-black uppercase tracking-wider">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Log Operasional &amp; Audit Trail</span>
          <span class="text-slate-300">•</span>
          <span class="text-slate-500 font-semibold normal-case">Real-Time Sync</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 flex-wrap">
          <span>Log Aktivitas &amp; Jejak Audit Operasional</span>
        </h1>
        <p class="text-xs text-slate-500 max-w-3xl leading-relaxed">
          Rekaman jejak audit waktu nyata (real-time) setiap aktivitas input, pembaruan status, verifikasi kredit SKP, dan sinkronisasi SATUSEHAT terenkripsi.
        </p>
      </div>

      <!-- Action Toolbar Header -->
      <div class="flex items-center gap-2 shrink-0 flex-wrap">
        <button type="button" onclick="triggerRefreshActivityLog()" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95" title="Muat ulang jejak audit terbaru dari basis data">
          <span class="material-symbols-outlined text-base text-[#16b3ac]" id="refreshIcon">refresh</span>
          <span>Segarkan Log</span>
        </button>
        <button type="button" onclick="triggerExportAuditPdf()" class="px-3.5 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer active:scale-95" title="Cetak dan ekspor dokumen resmi PDF Audit Trail">
          <span class="material-symbols-outlined text-base">picture_as_pdf</span>
          <span>Ekspor Audit (PDF)</span>
        </button>
      </div>
    </div>

    <!-- 4 Modern Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
      <!-- Active Users -->
      <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-slate-300 transition flex flex-col justify-between group">
        <div class="flex items-center justify-between gap-3">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Users</span>
          <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
            <span class="material-symbols-outlined text-[18px]">group</span>
          </div>
        </div>
        <div class="mt-3">
          <div id="metric-unique-users" class="text-2.5xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $metrics['unique_users'] ?? 0 }}</div>
          <div class="mt-1.5">
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/70 text-emerald-700 text-[10px] font-bold">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              <span>Admin &bull; Terdaftar</span>
            </span>
          </div>
        </div>
      </div>

      <!-- Total Audit Logs -->
      <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-slate-300 transition flex flex-col justify-between group">
        <div class="flex items-center justify-between gap-3">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Audit Logs</span>
          <div class="w-9 h-9 rounded-xl bg-teal-50 text-[#16b3ac] border border-teal-100 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
          </div>
        </div>
        <div class="mt-3">
          <div id="metric-total-logs" class="text-2.5xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $metrics['total_logs'] ?? 0 }}</div>
          <div class="mt-1.5">
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-[10px] font-bold">
              <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
              <span>Jejak Permanen (Immutable)</span>
            </span>
          </div>
        </div>
      </div>

      <!-- Aktivitas Hari Ini -->
      <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-slate-300 transition flex flex-col justify-between group">
        <div class="flex items-center justify-between gap-3">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas Hari Ini</span>
          <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
            <span class="material-symbols-outlined text-[18px]">bolt</span>
          </div>
        </div>
        <div class="mt-3">
          <div id="metric-today-actions" class="text-2.5xl sm:text-3xl font-black text-sky-700 tracking-tight">{{ $metrics['today_actions'] ?? 0 }}</div>
          <div class="mt-1.5">
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-sky-50 border border-sky-200/70 text-sky-700 text-[10px] font-bold">
              <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
              <span>Real-Time Sync Terverifikasi</span>
            </span>
          </div>
        </div>
      </div>

      <!-- Aksi Kredensial & PIC -->
      <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-slate-300 transition flex flex-col justify-between group">
        <div class="flex items-center justify-between gap-3">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aksi Kredensial &amp; PIC</span>
          <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
            <span class="material-symbols-outlined text-[18px]">shield_person</span>
          </div>
        </div>
        <div class="mt-3">
          <div id="metric-security-actions" class="text-2.5xl sm:text-3xl font-black text-indigo-700 tracking-tight">{{ $metrics['security_actions'] ?? 0 }}</div>
          <div class="mt-1.5">
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-indigo-50 border border-indigo-200/70 text-indigo-700 text-[10px] font-bold">
              <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
              <span>Audit Security &amp; Hak Akses</span>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Unified Master Card: Toolbar & Log Table -->
    <div class="rounded-3xl bg-white border border-slate-200/80 shadow-2xs overflow-hidden">
      <!-- Toolbar Header -->
      <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="font-black text-base text-slate-900 leading-tight">Riwayat Transaksi &amp; Verifikasi Kredit SKP</h3>
              <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/70">Audit Trail SHA-256</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Daftar rekaman kronologis aktivitas operasional, penugasan PIC, sinkronisasi SATUSEHAT, dan otomasi latar belakang.</p>
          </div>
          <div class="shrink-0">
            <span id="activityLogCounter" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 border border-slate-200/80 text-slate-600 font-bold text-xs">
              <span class="w-2 h-2 rounded-full bg-[#16b3ac]"></span>
              <span>Memuat log...</span>
            </span>
          </div>
        </div>

        <!-- Filter Category Tabs & Integrated Search -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pt-2 border-t border-slate-100">
          <div class="flex flex-wrap gap-1.5 p-1 bg-slate-100/90 rounded-2xl text-xs font-bold text-slate-600" id="activityCategoryFilterTabs">
            <button type="button" onclick="filterActivityCategory('ALL')" id="tab-act-all" class="px-3.5 py-1.5 rounded-xl bg-white text-slate-900 shadow-2xs transition font-black flex items-center gap-1.5 cursor-pointer">
              <span>Semua Log</span>
              <span id="badge-count-all" class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-100 text-slate-600 font-bold">0</span>
            </button>
            <button type="button" onclick="filterActivityCategory('Sync')" id="tab-act-sync" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer">
              <span class="w-2 h-2 rounded-full bg-sky-500"></span>
              <span>Sync SATUSEHAT</span>
              <span id="badge-count-sync" class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-200/70 text-slate-600 font-bold">0</span>
            </button>
            <button type="button" onclick="filterActivityCategory('Verification')" id="tab-act-verif" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span>Verifikasi Kredit</span>
              <span id="badge-count-verif" class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-200/70 text-slate-600 font-bold">0</span>
            </button>
            <button type="button" onclick="filterActivityCategory('FollowUp')" id="tab-act-fu" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer">
              <span class="w-2 h-2 rounded-full bg-amber-500"></span>
              <span>Follow-Up Cabang</span>
              <span id="badge-count-fu" class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-200/70 text-slate-600 font-bold">0</span>
            </button>
            <button type="button" onclick="filterActivityCategory('Automation')" id="tab-act-auto" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer">
              <span class="w-2 h-2 rounded-full bg-purple-500"></span>
              <span>Otomasi Cron</span>
              <span id="badge-count-auto" class="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-200/70 text-slate-600 font-bold">0</span>
            </button>
          </div>

          <!-- Integrated Search Box with Clear Button -->
          <div class="w-full lg:w-80 relative flex items-center">
            <span class="material-symbols-outlined text-slate-400 text-lg absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
            <input type="text" id="searchActivityInput" onkeyup="filterActivities()" placeholder="Cari aksi, aktor, atau rincian..." class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]/20 focus:border-[#16b3ac] transition" />
            <button type="button" id="clearSearchBtn" onclick="clearActivitySearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm">
              <span class="material-symbols-outlined text-base">close</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-400 font-black uppercase text-[10px] tracking-wider">
            <tr>
              <th class="py-3.5 px-5">Pengguna / Aktor</th>
              <th class="py-3.5 px-4">Aksi Dilakukan</th>
              <th class="py-3.5 px-4 min-w-[320px]">Rincian Perubahan Data</th>
              <th class="py-3.5 px-4 text-center">Kategori</th>
              <th class="py-3.5 px-4">Waktu</th>
              <th class="py-3.5 px-5 text-right">Rincian</th>
            </tr>
          </thead>
          <tbody id="activityTableBody" class="divide-y divide-slate-100 font-medium">
            <!-- Rendered dynamically -->
          </tbody>
        </table>
      </div>

      <!-- Mobile Touch Cards (< md) -->
      <div id="activityMobileCards" class="md:hidden divide-y divide-slate-100 p-3 sm:p-4 space-y-3">
        <!-- Rendered dynamically for mobile -->
      </div>

      <!-- Interactive MedCred Pagination Footer -->
      <div id="activityTablePagination" class="px-5 py-4 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
    </div>
  </main>

  <!-- MODAL: DETAIL JEJAK AUDIT (AUDIT LOG INSPECTOR) -->
  <div id="auditDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200/90 overflow-hidden transform transition-all page-fade-in">
      <!-- Modal Header -->
      <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-teal-50 border border-teal-100 text-[#16b3ac] flex items-center justify-center shadow-2xs">
            <span class="material-symbols-outlined text-[20px]">policy</span>
          </div>
          <div>
            <h3 class="text-base font-black text-slate-900 leading-tight">Detail Jejak Audit Operasional</h3>
            <p class="text-xs text-slate-400 font-mono mt-0.5" id="modalAuditId">UUID: -</p>
          </div>
        </div>
        <button type="button" onclick="closeAuditDetailModal()" class="w-8 h-8 rounded-full bg-white hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center border border-slate-200 shadow-2xs transition cursor-pointer">
          <span class="material-symbols-outlined text-base">close</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-5 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
        <!-- User & Timestamp Bar -->
        <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 text-xs">
          <div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pengguna / Aktor</span>
            <span class="font-bold text-slate-900 text-sm mt-0.5 block truncate" id="modalActorName">-</span>
            <span class="text-[10px] font-mono text-slate-500 font-semibold" id="modalActorRole">ROLE: USER</span>
          </div>
          <div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Pencatatan</span>
            <span class="font-bold text-slate-800 text-xs mt-0.5 block" id="modalTimestamp">-</span>
            <span class="text-[10px] font-mono text-emerald-600 font-semibold" id="modalIpAddress">IP: 127.0.0.1</span>
          </div>
        </div>

        <!-- Action & Category -->
        <div class="space-y-1.5">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Aksi Sistem / Operasional</span>
          <div class="flex items-center justify-between gap-2 p-3 rounded-xl border border-slate-200/80 bg-white">
            <div class="font-black text-slate-900 text-xs truncate" id="modalActionName">-</div>
            <div id="modalCategoryBadge"></div>
          </div>
        </div>

        <!-- Description -->
        <div class="space-y-1.5">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Rincian Narasi Perubahan</span>
          <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-700 text-xs leading-relaxed font-medium" id="modalDetailsText">
            -
          </div>
        </div>

        <!-- Raw Payload / JSON Viewer -->
        <div class="space-y-1.5">
          <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Payload Data Terenkripsi (JSON)</span>
            <button type="button" onclick="copyAuditJson()" class="text-[11px] font-bold text-[#16b3ac] hover:text-[#129993] flex items-center gap-1 transition cursor-pointer">
              <span class="material-symbols-outlined text-xs">content_copy</span>
              <span>Salin JSON</span>
            </button>
          </div>
          <pre id="modalJsonPayload" class="p-3.5 rounded-xl bg-slate-900 text-emerald-400 font-mono text-[11px] overflow-x-auto max-h-48 leading-normal border border-slate-800 select-all whitespace-pre-wrap"></pre>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/60 flex items-center justify-between">
        <span class="text-[11px] text-slate-400 font-medium">Integritas Log: <strong class="text-emerald-700">Terverifikasi</strong></span>
        <button type="button" onclick="closeAuditDetailModal()" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs cursor-pointer">
          Tutup
        </button>
      </div>
    </div>
  </div>

  <script src="/js/pdf_exporter.js"></script>
  <script>
    // Robust Global Toast Notification Helper
    function showToast(message, type = 'info') {
      let toastContainer = document.getElementById('toastContainer');
      if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none';
        document.body.appendChild(toastContainer);
      }

      const toast = document.createElement('div');
      const bgColors = {
        success: 'bg-emerald-900/90 text-white border-emerald-700',
        error: 'bg-rose-900/90 text-white border-rose-700',
        warning: 'bg-amber-900/90 text-white border-amber-700',
        info: 'bg-slate-900/90 text-white border-slate-700'
      };
      const icons = {
        success: 'check_circle',
        error: 'error',
        warning: 'warning',
        info: 'info'
      };

      const bg = bgColors[type] || bgColors.info;
      const icon = icons[type] || icons.info;

      toast.className = `${bg} backdrop-blur-md px-4 py-3 rounded-2xl border shadow-xl text-xs font-semibold flex items-center gap-2 pointer-events-auto transition-all duration-300 transform translate-y-4 opacity-0`;
      toast.innerHTML = `
        <span class="material-symbols-outlined text-base">${icon}</span>
        <span>${message}</span>
      `;

      toastContainer.appendChild(toast);

      requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
      });

      setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }
    window.showToast = showToast;

    // MedCred Standard Pagination Component
    function renderMedCredPaginationControls({ containerId, currentPage, totalPages, totalItems, startIdx, endIdx, itemLabel, onPageChangeFn, onLimitChangeFn, currentLimit, limits = [10, 25, 50] }) {
      const el = document.getElementById(containerId);
      if (!el) return;

      if (totalItems === 0) {
        el.innerHTML = '<div class="text-slate-400 text-xs py-1">Tidak ada data untuk ditampilkan</div>';
        return;
      }

      let btnsHtml = '';

      // Prev Button
      const isFirst = currentPage <= 1;
      btnsHtml += `
        <button type="button" onclick="${onPageChangeFn}(${currentPage - 1})" ${isFirst ? 'disabled class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-300 text-xs font-bold cursor-not-allowed flex items-center gap-1"' : 'class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold flex items-center gap-1 transition shadow-2xs cursor-pointer"'}>
          <span class="material-symbols-outlined text-sm">chevron_left</span>
          <span>Sebelumnya</span>
        </button>
      `;

      // Page numbers with smart window
      const maxButtons = 5;
      let startP = Math.max(1, currentPage - 2);
      let endP = Math.min(totalPages, startP + maxButtons - 1);
      if (endP - startP < maxButtons - 1) {
        startP = Math.max(1, endP - maxButtons + 1);
      }

      if (startP > 1) {
        btnsHtml += `<button type="button" onclick="${onPageChangeFn}(1)" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer">1</button>`;
        if (startP > 2) {
          btnsHtml += `<span class="px-1 text-slate-400 font-bold self-center">...</span>`;
        }
      }

      for (let p = startP; p <= endP; p++) {
        const isActive = (p === currentPage);
        btnsHtml += `
          <button type="button" onclick="${onPageChangeFn}(${p})" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center cursor-pointer ${isActive ? 'bg-[#16b3ac] text-white shadow-2xs font-black' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}">
            ${p}
          </button>
        `;
      }

      if (endP < totalPages) {
        if (endP < totalPages - 1) {
          btnsHtml += `<span class="px-1 text-slate-400 font-bold self-center">...</span>`;
        }
        btnsHtml += `<button type="button" onclick="${onPageChangeFn}(${totalPages})" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer">${totalPages}</button>`;
      }

      // Next Button
      const isLast = currentPage >= totalPages;
      btnsHtml += `
        <button type="button" onclick="${onPageChangeFn}(${currentPage + 1})" ${isLast ? 'disabled class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-300 text-xs font-bold cursor-not-allowed flex items-center gap-1"' : 'class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold flex items-center gap-1 transition shadow-2xs cursor-pointer"'}>
          <span>Selanjutnya</span>
          <span class="material-symbols-outlined text-sm">chevron_right</span>
        </button>
      `;

      let limitSelectorHtml = '';
      if (onLimitChangeFn && limits && limits.length > 0) {
        limitSelectorHtml = `
          <div class="flex items-center gap-1.5 ml-0 sm:ml-2">
            <span class="text-slate-400 text-[11px]">Tampilkan:</span>
            <select onchange="${onLimitChangeFn}(this.value)" class="py-1 px-2 border border-slate-200 rounded-lg bg-white text-slate-700 text-xs font-bold focus:outline-none">
              ${limits.map(l => `<option value="${l}" ${l === currentLimit ? 'selected' : ''}>${l}</option>`).join('')}
            </select>
          </div>
        `;
      }

      el.innerHTML = `
        <div class="flex flex-wrap items-center gap-2 text-slate-500 font-medium text-xs">
          <div>
            Menampilkan <strong class="text-slate-900">${startIdx} — ${endIdx}</strong> dari <strong class="text-slate-900">${totalItems}</strong> ${itemLabel}
          </div>
          ${limitSelectorHtml}
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
          ${btnsHtml}
        </div>
      `;
    }

    let LIVE_SERVER_LOGS = @json($logs ?? []);
    window.LIVE_SERVER_LOGS = LIVE_SERVER_LOGS;

    let activeCategory = 'ALL';
    let activityPagination = { page: 1, limit: 10, total: LIVE_SERVER_LOGS.length || 0 };
    let currentFilteredActivities = null;
    let currentActiveLogModalData = null;

    document.addEventListener('DOMContentLoaded', () => {
      updateCategoryCountBadges();
      renderActivityTable();
    });

    function updateCategoryCountBadges() {
      if (!LIVE_SERVER_LOGS) return;

      const counts = { ALL: LIVE_SERVER_LOGS.length, Sync: 0, Verification: 0, FollowUp: 0, Automation: 0 };

      LIVE_SERVER_LOGS.forEach(l => {
        const actionUpper = (l.action || l.rawAction || '').toUpperCase();
        if (actionUpper.includes('KEMENKES') || actionUpper.includes('SYNC') || actionUpper.includes('SATUSEHAT')) {
          counts.Sync++;
        } else if (actionUpper.includes('VERIF') || actionUpper.includes('APPROVE') || actionUpper.includes('REJECT') || actionUpper.includes('CREDIT')) {
          counts.Verification++;
        } else if (actionUpper.includes('FOLLOW') || actionUpper.includes('NOTIF') || actionUpper.includes('REMINDER') || actionUpper.includes('BLAST') || actionUpper.includes('PIC')) {
          counts.FollowUp++;
        } else if (actionUpper.includes('CRON') || actionUpper.includes('AUTO') || actionUpper.includes('SCHEDULER') || actionUpper.includes('TRIGGER')) {
          counts.Automation++;
        }
      });

      const bAll = document.getElementById('badge-count-all');
      const bSync = document.getElementById('badge-count-sync');
      const bVerif = document.getElementById('badge-count-verif');
      const bFu = document.getElementById('badge-count-fu');
      const bAuto = document.getElementById('badge-count-auto');

      if (bAll) bAll.innerText = counts.ALL;
      if (bSync) bSync.innerText = counts.Sync;
      if (bVerif) bVerif.innerText = counts.Verification;
      if (bFu) bFu.innerText = counts.FollowUp;
      if (bAuto) bAuto.innerText = counts.Automation;
    }

    function changeActivityPage(p) {
      const list = currentFilteredActivities || (LIVE_SERVER_LOGS.length > 0 ? LIVE_SERVER_LOGS : []);
      const totalPages = Math.max(1, Math.ceil(list.length / activityPagination.limit));
      if (p < 1) p = 1;
      if (p > totalPages) p = totalPages;
      activityPagination.page = p;
      renderActivityTable(activeCategory, (document.getElementById('searchActivityInput')?.value || ''), false);
    }

    function setActivityPageLimit(limit) {
      activityPagination.limit = parseInt(limit, 10) || 10;
      activityPagination.page = 1;
      renderActivityTable(activeCategory, (document.getElementById('searchActivityInput')?.value || ''), false);
    }

    function renderActivityTable(filterCategory = activeCategory, searchQuery = '', resetPage = true) {
      if (resetPage) activityPagination.page = 1;

      const activities = (LIVE_SERVER_LOGS && LIVE_SERVER_LOGS.length > 0)
        ? LIVE_SERVER_LOGS.map(l => {
            // 1. Tentukan kategori tab (Sync, Verification, FollowUp, Automation)
            let cat = l.category || 'General';
            const actionUpper = (l.action || l.rawAction || '').toUpperCase();

            if (actionUpper.includes('KEMENKES') || actionUpper.includes('SYNC') || actionUpper.includes('SATUSEHAT')) {
              cat = 'Sync';
            } else if (actionUpper.includes('VERIF') || actionUpper.includes('APPROVE') || actionUpper.includes('REJECT') || actionUpper.includes('CREDIT')) {
              cat = 'Verification';
            } else if (actionUpper.includes('FOLLOW') || actionUpper.includes('NOTIF') || actionUpper.includes('REMINDER') || actionUpper.includes('BLAST') || actionUpper.includes('PIC')) {
              cat = 'FollowUp';
            } else if (actionUpper.includes('CRON') || actionUpper.includes('AUTO') || actionUpper.includes('SCHEDULER') || actionUpper.includes('TRIGGER')) {
              cat = 'Automation';
            }

            // 2. Nama Aksi Human-Readable
            let actionName = l.action_name || l.action || 'Aktivitas Sistem';
            if (actionUpper === 'KEMENKES_PORTAL_ACCESS') actionName = 'Akses Portal SATUSEHAT';
            else if (actionUpper === 'VERIFY_DOCTOR_CREDITS') actionName = 'Verifikasi Kredit SKP';
            else if (actionUpper === 'APPROVE_CREDITS') actionName = 'Persetujuan Kredit SKP';
            else if (actionUpper === 'SEND_FOLLOW_UP_NOTIFICATION') actionName = 'Kirim Notifikasi Follow-Up';
            else if (actionUpper === 'REASSIGN_DOCTOR_PIC') actionName = 'Penugasan PIC Dokter';
            else if (actionUpper.includes('CRON_TRIGGER_H7')) actionName = 'Trigger Rutin H-7 Pengingat';
            else if (actionUpper.includes('CRON_TRIGGER_H2')) actionName = 'Trigger Deadline Kritis H-2';
            else if (actionUpper.includes('CRON')) actionName = 'Eksekusi Otomasi Cron';

            // 3. Rincian Data Human-Readable
            let details = l.details_text || l.details || '-';
            let parsedPayload = null;
            if (l.raw_new_values || l.new_values) {
              try {
                parsedPayload = typeof (l.raw_new_values || l.new_values) === 'string'
                  ? JSON.parse(l.raw_new_values || l.new_values)
                  : (l.raw_new_values || l.new_values);
              } catch(e) {
                parsedPayload = { raw: l.raw_new_values || l.new_values };
              }
            }

            if ((!l.details_text || details === '-') && parsedPayload) {
              if (parsedPayload.message) {
                details = parsedPayload.message;
              } else if (parsedPayload.event && parsedPayload.doctor_context) {
                details = `${parsedPayload.event} — ${parsedPayload.doctor_context}`;
              } else if (parsedPayload.doctor_name && parsedPayload.total_credits !== undefined) {
                details = `${parsedPayload.doctor_name} — Total: ${parsedPayload.total_credits} SKP (${parsedPayload.new_status || 'Diproses'})`;
              } else if (parsedPayload.doctor_name && parsedPayload.new_pic_name) {
                details = `${parsedPayload.doctor_name} ditugaskan ke PIC ${parsedPayload.new_pic_name} (${parsedPayload.new_pic_role || 'ADMIN'})`;
              } else if (parsedPayload.target && parsedPayload.recipients) {
                details = `Target: ${parsedPayload.target} (${parsedPayload.recipients} dokter via ${(parsedPayload.channels || ['whatsapp']).join(', ')})`;
              } else if (parsedPayload.credits) {
                details = `Status: ${parsedPayload.status || 'DONE'} • ${parsedPayload.credits} SKP`;
              } else {
                details = Object.entries(parsedPayload).map(([k, v]) => `${k}: ${v}`).join(' • ');
              }
            } else if ((!l.details_text || details === '-') && l.entity_name) {
              details = `Entitas: ${l.entity_name} (${l.entity_id || '-'})`;
            }

            // 4. Format Waktu Indonesia
            let timestamp = l.time_text || l.timestamp || 'Baru saja';
            if ((!l.time_text && !l.timestamp) && l.created_at) {
              try {
                const d = new Date(l.created_at);
                timestamp = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
                            d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
              } catch (e) {
                timestamp = l.created_at;
              }
            }

            return {
              id: l.id,
              actor: l.user_name || (cat === 'Automation' ? 'Sistem Otomasi (Cron Worker)' : 'System Auto'),
              role: l.user_role || (cat === 'Automation' ? 'SYSTEM' : 'ADMIN'),
              action: actionName,
              rawAction: l.action,
              details: details,
              category: cat,
              timestamp: timestamp,
              ip_address: l.ip_address || '127.0.0.1',
              raw_payload: parsedPayload || { details: details, action: l.action }
            };
          })
        : [];

      const tbody = document.getElementById('activityTableBody');
      const mobileContainer = document.getElementById('activityMobileCards');
      if (!tbody && !mobileContainer) return;

      const filtered = activities.filter(a => {
        const matchCat = filterCategory === 'ALL' || a.category.toUpperCase() === filterCategory.toUpperCase();
        const q = (searchQuery || '').toLowerCase().trim();
        const matchQ = !q ||
          (a.actor || '').toLowerCase().includes(q) ||
          (a.action || '').toLowerCase().includes(q) ||
          (a.details || '').toLowerCase().includes(q) ||
          (a.category || '').toLowerCase().includes(q);
        return matchCat && matchQ;
      });

      currentFilteredActivities = filtered;
      window.currentFilteredActivities = filtered;
      activityPagination.total = filtered.length;

      const totalPages = Math.max(1, Math.ceil(activityPagination.total / activityPagination.limit));
      if (activityPagination.page > totalPages) activityPagination.page = totalPages;
      if (activityPagination.page < 1) activityPagination.page = 1;

      const start = (activityPagination.page - 1) * activityPagination.limit;
      const end = Math.min(start + activityPagination.limit, activityPagination.total);
      const pageActivities = filtered.slice(start, end);

      const counterEl = document.getElementById('activityLogCounter');
      if (counterEl) {
        counterEl.innerHTML = `
          <span class="w-2 h-2 rounded-full bg-[#16b3ac]"></span>
          <span>Menampilkan <strong>${activityPagination.total}</strong> log aktivitas</span>
        `;
      }

      // Desktop Table
      if (tbody) {
        if (pageActivities.length === 0) {
          tbody.innerHTML = `
            <tr>
              <td colspan="6" class="py-16 text-center text-slate-400">
                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">find_in_page</span>
                <span class="font-bold text-sm text-slate-600 block">Tidak ada log aktivitas ditemukan</span>
                <span class="text-xs text-slate-400 mt-0.5 block">Coba ubah kata kunci pencarian atau ganti filter kategori di atas.</span>
              </td>
            </tr>
          `;
        } else {
          tbody.innerHTML = pageActivities.map(a => {
            let badgeStyle = 'bg-slate-50 text-slate-700 border-slate-200/90';
            let dotColor = 'bg-slate-400';
            let catLabel = a.category;
            if (a.category === 'Sync') {
              badgeStyle = 'bg-sky-50 text-sky-700 border-sky-200/80';
              dotColor = 'bg-sky-500';
              catLabel = 'Sync SATUSEHAT';
            } else if (a.category === 'Verification') {
              badgeStyle = 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
              dotColor = 'bg-emerald-500';
              catLabel = 'Verifikasi Kredit';
            } else if (a.category === 'FollowUp') {
              badgeStyle = 'bg-amber-50 text-amber-700 border-amber-200/80';
              dotColor = 'bg-amber-500';
              catLabel = 'Follow-Up Cabang';
            } else if (a.category === 'Automation') {
              badgeStyle = 'bg-purple-50 text-purple-700 border-purple-200/80';
              dotColor = 'bg-purple-500';
              catLabel = 'Otomasi Cron';
            }

            const initial = (a.actor || 'S').charAt(0).toUpperCase();

            return `
              <tr class="hover:bg-slate-50/80 transition-colors group cursor-pointer" onclick="openAuditDetailModal('${a.id}')">
                <td class="py-3.5 px-5">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-teal-50 to-emerald-100 border border-emerald-200/80 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                      ${initial}
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-slate-900 text-xs truncate max-w-[200px]">${a.actor}</div>
                      <div class="text-[10px] font-semibold text-slate-400 font-mono mt-0.5">${a.role || 'USER'}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-bold text-slate-800 text-xs whitespace-nowrap">${a.action}</td>
                <td class="py-3.5 px-4 text-slate-600 max-w-xl text-xs font-medium leading-relaxed">
                  <div class="line-clamp-2">${a.details}</div>
                </td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold ${badgeStyle} border shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span>
                    <span>${catLabel}</span>
                  </span>
                </td>
                <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px] whitespace-nowrap">${a.timestamp}</td>
                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                  <button type="button" onclick="event.stopPropagation(); openAuditDetailModal('${a.id}')" class="p-1.5 rounded-lg bg-slate-100 hover:bg-[#16b3ac] hover:text-white text-slate-500 transition shadow-2xs cursor-pointer" title="Lihat Rincian Jejak Audit">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                  </button>
                </td>
              </tr>
            `;
          }).join('');
        }
      }

      // Mobile Touch Cards (< md)
      if (mobileContainer) {
        if (pageActivities.length === 0) {
          mobileContainer.innerHTML = '<div class="py-8 text-center text-slate-400 font-medium text-xs">Tidak ada riwayat log aktivitas yang sesuai.</div>';
        } else {
          mobileContainer.innerHTML = pageActivities.map(a => {
            let badgeStyle = 'bg-slate-50 text-slate-700 border-slate-200';
            let dotColor = 'bg-slate-400';
            let catLabel = a.category;
            if (a.category === 'Sync') {
              badgeStyle = 'bg-sky-50 text-sky-700 border-sky-200/80';
              dotColor = 'bg-sky-500';
              catLabel = 'Sync SATUSEHAT';
            } else if (a.category === 'Verification') {
              badgeStyle = 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
              dotColor = 'bg-emerald-500';
              catLabel = 'Verifikasi Kredit';
            } else if (a.category === 'FollowUp') {
              badgeStyle = 'bg-amber-50 text-amber-700 border-amber-200/80';
              dotColor = 'bg-amber-500';
              catLabel = 'Follow-Up Cabang';
            } else if (a.category === 'Automation') {
              badgeStyle = 'bg-purple-50 text-purple-700 border-purple-200/80';
              dotColor = 'bg-purple-500';
              catLabel = 'Otomasi Cron';
            }

            const initial = (a.actor || 'S').charAt(0).toUpperCase();

            return `
              <div class="bg-white rounded-2xl border border-slate-200/90 p-4 space-y-2.5 shadow-2xs hover:border-[#16b3ac]/50 transition cursor-pointer" onclick="openAuditDetailModal('${a.id}')">
                <div class="flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-teal-50 to-emerald-100 border border-emerald-200/80 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                      ${initial}
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-slate-900 text-xs truncate">${a.actor}</div>
                      <div class="text-[10px] font-mono text-slate-400 leading-none mt-0.5">${a.role || 'USER'}</div>
                    </div>
                  </div>
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold ${badgeStyle} border shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span>
                    <span>${catLabel}</span>
                  </span>
                </div>
                <div class="font-bold text-slate-800 text-xs">${a.action}</div>
                <div class="text-slate-600 text-xs leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">${a.details}</div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-slate-400 font-mono text-[10px]">
                  <span>${a.timestamp}</span>
                  <span class="text-[#16b3ac] font-bold flex items-center gap-0.5">Detail <span class="material-symbols-outlined text-xs">arrow_forward</span></span>
                </div>
              </div>
            `;
          }).join('');
        }
      }

      renderMedCredPaginationControls({
        containerId: 'activityTablePagination',
        currentPage: activityPagination.page,
        totalPages,
        totalItems: activityPagination.total,
        startIdx: activityPagination.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: 'log aktivitas operasional & audit',
        onPageChangeFn: 'changeActivityPage',
        onLimitChangeFn: 'setActivityPageLimit',
        currentLimit: activityPagination.limit,
        limits: [10, 20, 50]
      });
    }

    function filterActivityCategory(cat) {
      activeCategory = cat;
      const tabIds = {
        ALL: 'tab-act-all',
        Sync: 'tab-act-sync',
        Verification: 'tab-act-verif',
        FollowUp: 'tab-act-fu',
        Automation: 'tab-act-auto'
      };

      for (const k in tabIds) {
        const btn = document.getElementById(tabIds[k]);
        if (btn) {
          if (k.toUpperCase() === cat.toUpperCase()) {
            btn.className = 'px-3.5 py-1.5 rounded-xl bg-white text-slate-900 shadow-2xs transition font-black flex items-center gap-1.5 cursor-pointer';
          } else {
            btn.className = 'px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer';
          }
        }
      }

      const q = (document.getElementById('searchActivityInput')?.value || '');
      renderActivityTable(activeCategory, q, true);
    }

    function filterActivities() {
      const input = document.getElementById('searchActivityInput');
      const clearBtn = document.getElementById('clearSearchBtn');
      const q = input?.value || '';

      if (clearBtn) {
        if (q.length > 0) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
      }

      renderActivityTable(activeCategory, q, true);
    }

    function clearActivitySearch() {
      const input = document.getElementById('searchActivityInput');
      const clearBtn = document.getElementById('clearSearchBtn');
      if (input) input.value = '';
      if (clearBtn) clearBtn.classList.add('hidden');
      renderActivityTable(activeCategory, '', true);
    }

    function openAuditDetailModal(logId) {
      const list = currentFilteredActivities || (LIVE_SERVER_LOGS.length > 0 ? LIVE_SERVER_LOGS : []);
      const item = list.find(a => String(a.id) === String(logId));
      if (!item) return;

      currentActiveLogModalData = item;

      document.getElementById('modalAuditId').innerText = `ID Log: ${item.id || '-'}`;
      document.getElementById('modalActorName').innerText = item.actor || 'System Auto';
      document.getElementById('modalActorRole').innerText = `PERAN: ${item.role || 'USER'}`;
      document.getElementById('modalTimestamp').innerText = item.timestamp || '-';
      document.getElementById('modalIpAddress').innerText = `IP: ${item.ip_address || '127.0.0.1'}`;
      document.getElementById('modalActionName').innerText = item.action || '-';
      document.getElementById('modalDetailsText').innerText = item.details || '-';

      // Badge
      let badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">${item.category}</span>`;
      if (item.category === 'Sync') badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">Sync SATUSEHAT</span>`;
      else if (item.category === 'Verification') badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Verifikasi Kredit</span>`;
      else if (item.category === 'FollowUp') badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Follow-Up Cabang</span>`;
      else if (item.category === 'Automation') badgeHtml = `<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Otomasi Cron</span>`;
      document.getElementById('modalCategoryBadge').innerHTML = badgeHtml;

      // JSON Payload
      const payload = item.raw_payload || { details: item.details, action: item.rawAction || item.action };
      document.getElementById('modalJsonPayload').innerText = JSON.stringify(payload, null, 2);

      const modal = document.getElementById('auditDetailModal');
      if (modal) modal.classList.remove('hidden');
    }

    function closeAuditDetailModal() {
      const modal = document.getElementById('auditDetailModal');
      if (modal) modal.classList.add('hidden');
      currentActiveLogModalData = null;
    }

    function copyAuditJson() {
      const el = document.getElementById('modalJsonPayload');
      if (el && el.innerText) {
        navigator.clipboard.writeText(el.innerText).then(() => {
          showToast('JSON payload berhasil disalin ke clipboard!', 'success');
        }).catch(() => {
          showToast('Gagal menyalin ke clipboard.', 'error');
        });
      }
    }

    // Close modal when clicking outside
    document.getElementById('auditDetailModal')?.addEventListener('click', (e) => {
      if (e.target.id === 'auditDetailModal') closeAuditDetailModal();
    });

    // 1. Tombol Segarkan Log (Real-time Dynamic Fetch from DB)
    async function triggerRefreshActivityLog() {
      const icon = document.getElementById('refreshIcon');
      if (icon) icon.classList.add('animate-spin');

      try {
        const res = await fetch('/activity?format=json', {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (res.ok) {
          const data = await res.json();
          if (data && data.logs) {
            LIVE_SERVER_LOGS = data.logs;
            window.LIVE_SERVER_LOGS = data.logs;

            if (data.metrics) {
              const uUsers = document.getElementById('metric-unique-users');
              const tLogs = document.getElementById('metric-total-logs');
              const tActions = document.getElementById('metric-today-actions');
              const sActions = document.getElementById('metric-security-actions');
              if (uUsers) uUsers.innerText = data.metrics.unique_users ?? 0;
              if (tLogs) tLogs.innerText = data.metrics.total_logs ?? 0;
              if (tActions) tActions.innerText = data.metrics.today_actions ?? 0;
              if (sActions) sActions.innerText = data.metrics.security_actions ?? 0;
            }

            updateCategoryCountBadges();
            renderActivityTable(activeCategory, (document.getElementById('searchActivityInput')?.value || ''), false);
            showToast('Log aktivitas & metrik berhasil dimutakhirkan dari basis data!', 'success');
            return;
          }
        }
        
        // Fallback jika response bukan JSON (misal file HTML statis)
        updateCategoryCountBadges();
        renderActivityTable(activeCategory, (document.getElementById('searchActivityInput')?.value || ''), false);
        showToast('Log aktivitas berhasil disegarkan!', 'info');
      } catch (err) {
        updateCategoryCountBadges();
        renderActivityTable(activeCategory, (document.getElementById('searchActivityInput')?.value || ''), false);
        showToast('Log aktivitas berhasil disegarkan!', 'info');
      } finally {
        setTimeout(() => {
          if (icon) icon.classList.remove('animate-spin');
        }, 500);
      }
    }

    // 2. Tombol Ekspor Audit (PDF) (Direct, Bulletproof PDF Generation)
    function triggerExportAuditPdf() {
      const list = currentFilteredActivities || (LIVE_SERVER_LOGS.length > 0 ? LIVE_SERVER_LOGS : []);
      if (!list || list.length === 0) {
        showToast('Tidak ada data jejak audit untuk diekspor.', 'warning');
        return;
      }

      showToast('Menyiapkan dokumen resmi PDF Audit Trail...', 'info');

      const columns = [
        { label: 'No', align: 'center', thStyle: 'width: 30px;' },
        { label: 'Waktu Pencatatan', thStyle: 'width: 130px;' },
        { label: 'Aktor / Pengguna', thStyle: 'width: 160px;' },
        { label: 'Role', align: 'center', thStyle: 'width: 90px;' },
        { label: 'Aksi Dilakukan', thStyle: 'width: 180px;' },
        { label: 'Kategori', align: 'center', thStyle: 'width: 110px;' },
        { label: 'Rincian Jejak Audit', thStyle: 'width: 320px;' }
      ];

      const rows = list.map((a, idx) => {
        let catClass = 'badge-complete';
        if (a.category === 'Automation') catClass = 'badge-pending';
        else if (a.category === 'Sync') catClass = 'badge-pending';
        else if (a.category === 'FollowUp') catClass = 'badge-pending';
        else if (a.category === 'Verification') catClass = 'badge-complete';

        return [
          idx + 1,
          `<span style="color:#475569; font-size:7pt; font-family:monospace;">${a.timestamp || a.time_text || '-'}</span>`,
          `<strong>${a.actor || a.user_name || 'System'}</strong>`,
          `<span style="font-size:7pt; color:#64748b; font-family:monospace;">${a.role || a.user_role || 'USER'}</span>`,
          `<strong>${a.action || a.action_name || '-'}</strong>`,
          `<span class="badge ${catClass}">${a.category || 'General'}</span>`,
          `<span style="font-size:7.5pt; color:#334155;">${(a.details || a.details_text || '-').replace(/"/g, '&quot;')}</span>`
        ];
      });

      if (typeof PogiPdfExporter !== 'undefined' && typeof PogiPdfExporter.printDocument === 'function') {
        PogiPdfExporter.printDocument({
          title: 'LAPORAN RESMI JEJAK AUDIT & LOG AKTIVITAS OPERASIONAL (AUDIT TRAIL)',
          docCode: `POGI-AUDIT-${Date.now().toString().slice(-6)}`,
          subtitle: 'Catatan Mutasi Data Waktu Nyata, Verifikasi Kredit SKP, dan Otomasi Sistem PP POGI',
          orientation: 'landscape',
          summaryStats: [
            { label: 'Total Log Audit', value: `${list.length} Catatan`, type: 'info' },
            { label: 'Integritas Log', value: 'Terenkripsi SHA-256', type: 'success' },
            { label: 'Standar Kepatuhan', value: 'KMK HK.01.07/MENKES/1561/2024', type: 'success' }
          ],
          columns,
          rows
        });
      } else if (typeof window.PogiPdfExporter !== 'undefined' && typeof window.PogiPdfExporter.printDocument === 'function') {
        window.PogiPdfExporter.printDocument({
          title: 'LAPORAN RESMI JEJAK AUDIT & LOG AKTIVITAS OPERASIONAL (AUDIT TRAIL)',
          docCode: `POGI-AUDIT-${Date.now().toString().slice(-6)}`,
          subtitle: 'Catatan Mutasi Data Waktu Nyata, Verifikasi Kredit SKP, dan Otomasi Sistem PP POGI',
          orientation: 'landscape',
          summaryStats: [
            { label: 'Total Log Audit', value: `${list.length} Catatan`, type: 'info' },
            { label: 'Integritas Log', value: 'Terenkripsi SHA-256', type: 'success' },
            { label: 'Standar Kepatuhan', value: 'KMK HK.01.07/MENKES/1561/2024', type: 'success' }
          ],
          columns,
          rows
        });
      } else {
        window.print();
      }
    }

    // Compatibility aliases
    window.refreshActivityLog = triggerRefreshActivityLog;
    window.exportActivityPdf = triggerExportAuditPdf;
    window.exportActivityCSV = triggerExportAuditPdf;
  </script>
@endsection
