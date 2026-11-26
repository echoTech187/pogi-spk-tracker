@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Log Aktivitas & Jejak Audit')
@section('content')
  <!-- Main Body -->
  <main class="flex-1 overflow-y-auto space-y-6 page-fade-in pb-8">

    <!-- Modern Heading Banner -->
    <div class="p-6 sm:p-7 rounded-3xl bg-white flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
      <div class="space-y-2 relative z-10 min-w-0">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/80 text-[10px] font-black uppercase tracking-wider">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>LOG OPERASIONAL &amp; AUDIT TRAIL</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 flex-wrap">
          <span>Log Aktivitas &amp; Jejak Audit Operasional</span>
          <span class="text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200/80 px-2.5 py-0.5 rounded-full">Real-Time Sync</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-3xl leading-relaxed">
          Rekaman jejak audit waktu nyata (real-time) setiap aktivitas input, pembaruan status, verifikasi kredit SKP, dan sinkronisasi SATUSEHAT terenkripsi.
        </p>
      </div>
    </div>

    <!-- 4 Modern Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Active Users -->
      <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
        <div class="flex items-center justify-between gap-3">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Users</span>
          <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100/80 text-[#16b3ac] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
            <span class="material-symbols-outlined text-[20px]">group</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $metrics['unique_users'] ?? 0 }}</div>
          <div class="flex items-center gap-1.5 mt-1 text-xs font-semibold text-emerald-600">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Admin &bull; Terdaftar</span>
          </div>
        </div>
      </div>

      <!-- Total Audit Logs -->
      <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
        <div class="flex items-center justify-between gap-3">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Audit Logs</span>
          <div class="w-10 h-10 rounded-2xl bg-slate-100 border border-slate-200 text-slate-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $metrics['total_logs'] ?? 0 }}</div>
          <div class="flex items-center gap-1.5 mt-1 text-xs font-semibold text-slate-400">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
            <span>Jejak Permanen (Immutable)</span>
          </div>
        </div>
      </div>

      <!-- Aktivitas Hari Ini -->
      <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
        <div class="flex items-center justify-between gap-3">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas Hari Ini</span>
          <div class="w-10 h-10 rounded-2xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
            <span class="material-symbols-outlined text-[20px]">bolt</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="text-3xl font-black text-sky-700 tracking-tight">{{ $metrics['today_actions'] ?? 0 }}</div>
          <div class="flex items-center gap-1.5 mt-1 text-xs font-semibold text-sky-600">
            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
            <span>Real-Time Sync Terverifikasi</span>
          </div>
        </div>
      </div>

      <!-- Aksi Kredensial & PIC -->
      <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
        <div class="flex items-center justify-between gap-3">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aksi Kredensial &amp; PIC</span>
          <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
            <span class="material-symbols-outlined text-[20px]">shield_person</span>
          </div>
        </div>
        <div class="mt-4">
          <div class="text-3xl font-black text-indigo-700 tracking-tight">{{ $metrics['security_actions'] ?? 0 }}</div>
          <div class="flex items-center gap-1.5 mt-1 text-xs font-semibold text-indigo-600">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
            <span>Audit Security &amp; Hak Akses</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Unified Master Card: Toolbar & Log Table -->
    <div class="rounded-3xl bg-white border border-slate-200/80 shadow-2xs overflow-hidden">
      <!-- Toolbar Header -->
      <div class="p-5 sm:p-6 border-b border-slate-100 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h3 class="font-black text-base text-slate-900 leading-tight">Riwayat Transaksi &amp; Verifikasi Kredit SKP</h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar rekaman kronologis aktivitas operasional, penugasan PIC, sinkronisasi SATUSEHAT, dan eksekusi background scheduler</p>
          </div>
          <div class="w-full sm:w-72 relative flex items-center">
            <span class="material-symbols-outlined text-slate-400 text-lg absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
            <input type="text" id="searchActivityInput" onkeyup="filterActivities()" placeholder="Cari aksi, aktor, rincian..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#16b3ac]/20 focus:border-[#16b3ac] transition" />
          </div>
        </div>

        <!-- Filter Category Tabs & Counter -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1 border-t border-slate-100">
          <div class="flex flex-wrap gap-1.5 p-1 bg-slate-100/80 rounded-2xl text-xs font-bold text-slate-600" id="activityCategoryFilterTabs">
            <button type="button" onclick="filterActivityCategory('ALL')" id="tab-act-all" class="px-3.5 py-1.5 rounded-xl bg-white text-slate-900 shadow-2xs transition font-black flex items-center gap-1.5 cursor-pointer">
              Semua Log
            </button>
            <button type="button" onclick="filterActivityCategory('Sync')" id="tab-act-sync" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer">
              <span class="w-2 h-2 rounded-full bg-sky-500"></span>
              <span>Sync SATUSEHAT</span>
            </button>
            <button type="button" onclick="filterActivityCategory('Verification')" id="tab-act-verif" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span>Verifikasi Kredit</span>
            </button>
            <button type="button" onclick="filterActivityCategory('FollowUp')" id="tab-act-fu" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer">
              <span class="w-2 h-2 rounded-full bg-amber-500"></span>
              <span>Follow-Up Cabang</span>
            </button>
            <button type="button" onclick="filterActivityCategory('Automation')" id="tab-act-auto" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:text-slate-900 transition flex items-center gap-1.5 font-bold cursor-pointer">
              <span class="w-2 h-2 rounded-full bg-purple-500"></span>
              <span>Otomasi Cron</span>
            </button>
          </div>
          <div class="text-xs font-semibold text-slate-400 self-end sm:self-center">
            <span id="activityLogCounter" class="bg-slate-50 border border-slate-200/80 px-3 py-1 rounded-full text-slate-600 font-bold">Total 0 aktivitas</span>
          </div>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50/70 border-b border-slate-200/80 text-slate-400 font-black uppercase text-[10px] tracking-wider">
            <tr>
              <th class="py-3.5 px-5">Pengguna / Aktor</th>
              <th class="py-3.5 px-4">Aksi Dilakukan</th>
              <th class="py-3.5 px-4">Rincian Perubahan Data</th>
              <th class="py-3.5 px-4 text-center">Kategori</th>
              <th class="py-3.5 px-5 text-right">Waktu</th>
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


  <script src="/js/pdf_exporter.js"></script>
  <script>
    
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
        <button type="button" onclick="${onPageChangeFn}(${currentPage - 1})" ${isFirst ? 'disabled class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-300 text-xs font-bold cursor-not-allowed flex items-center gap-1"' : 'class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold flex items-center gap-1 transition shadow-2xs"'}>
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
        btnsHtml += `<button type="button" onclick="${onPageChangeFn}(1)" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">1</button>`;
        if (startP > 2) {
          btnsHtml += `<span class="px-1 text-slate-400 font-bold self-center">...</span>`;
        }
      }

      for (let p = startP; p <= endP; p++) {
        const isActive = (p === currentPage);
        btnsHtml += `
          <button type="button" onclick="${onPageChangeFn}(${p})" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center ${isActive ? 'bg-[#16b3ac] text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}">
            ${p}
          </button>
        `;
      }

      if (endP < totalPages) {
        if (endP < totalPages - 1) {
          btnsHtml += `<span class="px-1 text-slate-400 font-bold self-center">...</span>`;
        }
        btnsHtml += `<button type="button" onclick="${onPageChangeFn}(${totalPages})" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">${totalPages}</button>`;
      }

      // Next Button
      const isLast = currentPage >= totalPages;
      btnsHtml += `
        <button type="button" onclick="${onPageChangeFn}(${currentPage + 1})" ${isLast ? 'disabled class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-300 text-xs font-bold cursor-not-allowed flex items-center gap-1"' : 'class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold flex items-center gap-1 transition shadow-2xs"'}>
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


    const LIVE_SERVER_LOGS = @json($logs ?? []);
    let activeCategory = 'ALL';
    let activityPagination = { page: 1, limit: 10, total: LIVE_SERVER_LOGS.length || 0 };
    let currentFilteredActivities = null;

    document.addEventListener('DOMContentLoaded', () => {
      renderActivityTable();
    });

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
            if ((!l.details_text || details === '-') && l.new_values) {
              try {
                const parsed = typeof l.new_values === 'string' ? JSON.parse(l.new_values) : l.new_values;
                if (parsed.message) {
                  details = parsed.message;
                } else if (parsed.event && parsed.doctor_context) {
                  details = `${parsed.event} — ${parsed.doctor_context}`;
                } else if (parsed.doctor_name && parsed.total_credits !== undefined) {
                  details = `${parsed.doctor_name} — Total: ${parsed.total_credits} SKP (${parsed.new_status || 'Diproses'})`;
                } else if (parsed.doctor_name && parsed.new_pic_name) {
                  details = `${parsed.doctor_name} ditugaskan ke PIC ${parsed.new_pic_name} (${parsed.new_pic_role || 'ADMIN'})`;
                } else if (parsed.target && parsed.recipients) {
                  details = `Target: ${parsed.target} (${parsed.recipients} dokter via ${(parsed.channels || ['whatsapp']).join(', ')})`;
                } else if (parsed.credits) {
                  details = `Status: ${parsed.status || 'DONE'} • ${parsed.credits} SKP`;
                } else if (typeof parsed === 'string') {
                  details = parsed;
                } else {
                  details = Object.entries(parsed).map(([k, v]) => `${k}: ${v}`).join(' • ');
                }
              } catch(e) {
                details = String(l.new_values);
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
              timestamp: timestamp
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
      activityPagination.total = filtered.length;

      const totalPages = Math.max(1, Math.ceil(activityPagination.total / activityPagination.limit));
      if (activityPagination.page > totalPages) activityPagination.page = totalPages;
      if (activityPagination.page < 1) activityPagination.page = 1;

      const start = (activityPagination.page - 1) * activityPagination.limit;
      const end = Math.min(start + activityPagination.limit, activityPagination.total);
      const pageActivities = filtered.slice(start, end);

      const counterEl = document.getElementById('activityLogCounter');
      if (counterEl) {
        counterEl.innerText = `Total ${activityPagination.total} aktivitas audit tercatat`;
      }

      // Desktop Table
      if (tbody) {
        if (pageActivities.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="py-12 text-center text-slate-400 font-medium">Tidak ada riwayat log aktivitas yang sesuai dengan kriteria filter.</td></tr>';
        } else {
          tbody.innerHTML = pageActivities.map(a => {
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
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3.5 px-5">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-teal-50 to-emerald-100 border border-emerald-200/80 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                      ${initial}
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-slate-900 text-xs truncate max-w-[190px]">${a.actor}</div>
                      <div class="text-[10px] font-semibold text-slate-400 font-mono mt-0.5">${a.role || 'USER'}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 font-bold text-slate-800 text-xs">${a.action}</td>
                <td class="py-3.5 px-4 text-slate-600 max-w-md truncate text-xs font-medium" title="${a.details.replace(/"/g, '&quot;')}">${a.details}</td>
                <td class="py-3.5 px-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold ${badgeStyle} border whitespace-nowrap shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span>
                    <span>${catLabel}</span>
                  </span>
                </td>
                <td class="py-3.5 px-5 text-right text-slate-400 font-mono text-[11px] whitespace-nowrap">${a.timestamp}</td>
              </tr>
            `;
          }).join('');
        }
      }

      // Mobile Touch Cards (< md)
      if (mobileContainer) {
        if (pageActivities.length === 0) {
          mobileContainer.innerHTML = '<div class="py-8 text-center text-slate-400 font-medium text-xs">Tidak ada riwayat log aktivitas yang sesuai dengan kriteria filter.</div>';
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
              <div class="bg-white rounded-2xl border border-slate-200/90 p-4 space-y-2.5 shadow-2xs hover:border-[#16b3ac]/50 transition">
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
                <div class="text-right text-slate-400 font-mono text-[10px] pt-1 border-t border-slate-100">${a.timestamp}</div>
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
      const q = (document.getElementById('searchActivityInput')?.value || '');
      renderActivityTable(activeCategory, q, true);
    }

    async function runCronJobSimulation(triggerType) {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      try {
        const res = await fetch('/activity/cron-simulation', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf || ''
          },
          body: JSON.stringify({ trigger: triggerType })
        });
        const data = await res.json();
        if (data && data.success && data.log) {
          LIVE_SERVER_LOGS.unshift(data.log);
          showToast(`Trigger Cron ${triggerType} Berhasil: ${data.count} dokter Incomplete diproses & dicatat ke jejak audit!`, 'success');
        } else {
          showToast(data?.message || 'Gagal menjalankan cron trigger di server.', 'error');
        }
      } catch (err) {
        showToast('Gagal terhubung ke server untuk menjalankan cron trigger.', 'error');
      }

      // Langsung buka tab Otomasi Cron
      filterActivityCategory('Automation');
    }

    function refreshActivityLog() {
      const icon = document.getElementById('refreshIcon');
      if (icon) icon.classList.add('animate-spin');
      setTimeout(() => {
        if (icon) icon.classList.remove('animate-spin');
        renderActivityTable();
        showToast('Log aktivitas berhasil dimutakhirkan!', 'info');
      }, 500);
    }

    function exportActivityPdf() {
      if (typeof window.PogiPdfExporter !== 'undefined') {
        window.exportActivityPdf();
      }
    }
    const exportActivityCSV = exportActivityPdf;
  </script>
@endsection