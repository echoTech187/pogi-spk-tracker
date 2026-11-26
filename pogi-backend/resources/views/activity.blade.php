@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Dashboard Pemantauan SKP Nasional')
@section('content')
  <!-- Main Body -->
  <main class="flex-1 overflow-y-auto space-y-6 page-fade-in">

    <!-- MedCred Standard Heading Banner -->
    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1.5">
        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#16b3ac] text-[10px] font-black uppercase tracking-wider">
          <span>LOG OPERASIONAL &amp; AUDIT TRAIL</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
          <span>Log Aktivitas &amp; Jejak Audit Operasional</span>
          <span class="text-sm font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">(Waktu Nyata)</span>
        </h1>
        <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
          Rekaman jejak audit waktu nyata (real-time) setiap aktivitas input, pembaruan status, verifikasi kredit SKP, dan sinkronisasi SATUSEHAT terenkripsi.
        </p>
      </div>
      <div class="flex items-center gap-2.5 shrink-0">
        <button type="button" onclick="refreshActivityLog()" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5">
          <span class="material-symbols-outlined text-base" id="refreshIcon">refresh</span>
          <span>Segarkan Log</span>
        </button>
      </div>
    </div>

    <!-- 5 Metric Cards Sesuai Screenshot Asli Klien (09.12.05) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
      <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Active users</div>
        <div class="text-2xl font-black text-slate-900 mt-1">{{ $metrics['unique_users'] ?? 0 }}</div>
        <div class="text-[10px] text-emerald-700 font-bold mt-0.5">Admin &bull; Terdaftar</div>
      </div>
      <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Audit Logs</div>
        <div class="text-2xl font-black text-slate-900 mt-1">{{ $metrics['total_logs'] ?? 0 }}</div>
        <div class="text-[10px] text-slate-500 mt-0.5">Jejak Permanen</div>
      </div>
      <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas Hari Ini</div>
        <div class="text-2xl font-black text-sky-700 mt-1">{{ $metrics['today_actions'] ?? 0 }}</div>
        <div class="text-[10px] text-sky-800 font-bold mt-0.5">Real-Time Sync</div>
      </div>
      <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aksi Kredensial &amp; PIC</div>
        <div class="text-2xl font-black text-[#16b3ac] mt-1">{{ $metrics['security_actions'] ?? 0 }}</div>
        <div class="text-[10px] text-emerald-800 font-bold mt-0.5">Audit Security</div>
      </div>
    </div>

    <!-- MESIN OTOMASI PENGINGAT (CRON JOB SCHEDULER H-7 & H-2 - RULES.md BAB 3.2.A) -->
    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-900/90 to-teal-900 text-white shadow-xl space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-emerald-300 shrink-0">
            <span class="material-symbols-outlined text-2xl">schedule_send</span>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="font-black text-sm text-white">Mesin Otomasi Pengingat &amp; Cron Job Scheduler</h3>
              <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-black uppercase flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Worker Aktif
              </span>
            </div>
            <p class="text-xs text-emerald-100/70 mt-0.5">
              Eksekusi otomatis tugas terjadwal H-7 &amp; H-2 sebelum Cutoff 1 Des 2026 (00:00 WIB) sesuai regulasi RULES.md Bab 3.2.A
            </p>
          </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 flex-wrap">
          <button type="button" onclick="runCronJobSimulation('H-7')" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs">
            <span class="material-symbols-outlined text-sm text-emerald-300">play_circle</span>
            <span>Simulasi Trigger H-7</span>
          </button>
          <button type="button" onclick="runCronJobSimulation('H-2')" class="px-3.5 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-200 border border-amber-400/40 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs">
            <span class="material-symbols-outlined text-sm text-amber-300">warning</span>
            <span>Simulasi Trigger H-2</span>
          </button>
          <button type="button" onclick="refreshActivityLog()" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-sm text-slate-200" id="refreshIcon">refresh</span>
            <span>Segarkan</span>
          </button>
        </div>
      </div>

      <!-- Schedule & Status Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-white/10 text-xs">
        <div class="p-3 rounded-xl bg-white/5 border border-white/10">
          <div class="text-[10px] text-emerald-200/60 font-bold uppercase tracking-wider">Jadwal Cutoff Bulanan</div>
          <div class="text-sm font-black text-white mt-0.5">01 Des 2026 &bull; 00:00 WIB</div>
          <div class="text-[10px] text-emerald-100/60 mt-0.5">Kunci data &amp; arsip laporan bulanan</div>
        </div>
        <div class="p-3 rounded-xl bg-white/5 border border-white/10">
          <div class="text-[10px] text-emerald-200/60 font-bold uppercase tracking-wider">Next Trigger: Pengingat Rutin H-7</div>
          <div class="text-sm font-black text-emerald-300 mt-0.5">24 Nov 2026 &bull; 00:00 WIB</div>
          <div class="text-[10px] text-emerald-100/60 mt-0.5">Sasaran: {{ $metrics['incomplete_doctors'] ?? 0 }} Dokter Incomplete (Nasional)</div>
        </div>
        <div class="p-3 rounded-xl bg-white/5 border border-white/10">
          <div class="text-[10px] text-amber-200/60 font-bold uppercase tracking-wider">Next Trigger: Deadline Kritis H-2</div>
          <div class="text-sm font-black text-amber-300 mt-0.5">29 Nov 2026 &bull; 00:00 WIB</div>
          <div class="text-[10px] text-amber-100/60 mt-0.5">Eskalasi final sisa kekurangan kredit</div>
        </div>
      </div>
    </div>

    <!-- Tabel Log Aktivitas -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold text-slate-600" id="activityCategoryFilterTabs">
          <button type="button" onclick="filterActivityCategory('ALL')" id="tab-act-all" class="px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition">Semua Log</button>
          <button type="button" onclick="filterActivityCategory('Sync')" id="tab-act-sync" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-sky-500"></span> Sync SATUSEHAT
          </button>
          <button type="button" onclick="filterActivityCategory('Verification')" id="tab-act-verif" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Verifikasi Kredit
          </button>
          <button type="button" onclick="filterActivityCategory('FollowUp')" id="tab-act-fu" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Follow-Up Cabang
          </button>
          <button type="button" onclick="filterActivityCategory('Automation')" id="tab-act-auto" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-purple-500"></span> Otomasi Cron
          </button>
        </div>

        <div class="w-full sm:w-72 relative flex items-center">
          <span class="material-symbols-outlined text-slate-400 text-lg absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
          <input type="text" id="searchActivityInput" onkeyup="filterActivities()" placeholder="Cari aksi, pengguna, atau rincian..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-emerald-700 font-medium" />
        </div>
      </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3">
        <h3 class="font-extrabold text-sm text-slate-900">Riwayat Transaksi &amp; Verifikasi Kredit SKP</h3>
        <span id="activityLogCounter" class="text-xs text-slate-400 font-medium">Dicatat otomatis oleh sistem</span>
      </div>

      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
            <tr>
              <th class="py-3 px-4">Pengguna / Aktor</th>
              <th class="py-3 px-4">Aksi Dilakukan</th>
              <th class="py-3 px-4">Rincian Perubahan Data</th>
              <th class="py-3 px-4 text-center">Kategori</th>
              <th class="py-3 px-4 text-right">Waktu</th>
            </tr>
          </thead>
          <tbody id="activityTableBody" class="divide-y divide-slate-100 font-medium">
            <!-- Rendered dynamically -->
          </tbody>
        </table>
      </div>

      <!-- Mobile Touch Cards (< md) -->
      <div id="activityMobileCards" class="md:hidden divide-y divide-slate-100 p-2 sm:p-3 space-y-3">
        <!-- Rendered dynamically for mobile -->
      </div>

      <!-- Interactive MedCred Pagination Footer -->
      <div id="activityTablePagination" class="px-4 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
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
            let badgeStyle = 'bg-slate-100 text-slate-700 border-slate-200';
            let catLabel = a.category;
            if (a.category === 'Sync') { badgeStyle = 'bg-sky-50 text-sky-800 border-sky-200'; catLabel = 'Sync SATUSEHAT'; }
            else if (a.category === 'Verification') { badgeStyle = 'bg-emerald-50 text-emerald-800 border-emerald-200'; catLabel = 'Verifikasi Kredit'; }
            else if (a.category === 'FollowUp') { badgeStyle = 'bg-amber-50 text-amber-800 border-amber-200'; catLabel = 'Follow-Up Cabang'; }
            else if (a.category === 'Automation') { badgeStyle = 'bg-purple-50 text-purple-800 border-purple-200'; catLabel = 'Otomasi Cron'; }

            return `
              <tr class="hover:bg-slate-50 transition border-b border-slate-100">
                <td class="py-3 px-4 font-bold text-slate-900">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-100 text-[#16b3ac] font-bold text-[10px] flex items-center justify-center shrink-0">
                      ${(a.actor || 'S').charAt(0)}
                    </span>
                    <span>${a.actor}</span>
                  </div>
                </td>
                <td class="py-3 px-4 font-semibold text-slate-800">${a.action}</td>
                <td class="py-3 px-4 text-slate-600 max-w-md truncate" title="${a.details.replace(/"/g, '&quot;')}">${a.details}</td>
                <td class="py-3 px-4 text-center">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold ${badgeStyle} border whitespace-nowrap">
                    ${catLabel}
                  </span>
                </td>
                <td class="py-3 px-4 text-right text-slate-500 font-mono text-[11px] whitespace-nowrap">${a.timestamp}</td>
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
            let badgeStyle = 'bg-slate-100 text-slate-700 border-slate-200';
            let catLabel = a.category;
            if (a.category === 'Sync') { badgeStyle = 'bg-sky-50 text-sky-800 border-sky-200'; catLabel = 'Sync SATUSEHAT'; }
            else if (a.category === 'Verification') { badgeStyle = 'bg-emerald-50 text-emerald-800 border-emerald-200'; catLabel = 'Verifikasi Kredit'; }
            else if (a.category === 'FollowUp') { badgeStyle = 'bg-amber-50 text-amber-800 border-amber-200'; catLabel = 'Follow-Up Cabang'; }
            else if (a.category === 'Automation') { badgeStyle = 'bg-purple-50 text-purple-800 border-purple-200'; catLabel = 'Otomasi Cron'; }

            return `
              <div class="bg-white rounded-xl border border-slate-200 p-3.5 space-y-2 shadow-2xs">
                <div class="flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-100 text-[#16b3ac] font-bold text-[10px] flex items-center justify-center shrink-0">
                      ${(a.actor || 'S').charAt(0)}
                    </span>
                    <span class="font-bold text-slate-900 text-xs">${a.actor}</span>
                  </div>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeStyle} border shrink-0">
                    ${catLabel}
                  </span>
                </div>
                <div class="font-semibold text-slate-800 text-xs">${a.action}</div>
                <div class="text-slate-600 text-[11px] leading-relaxed">${a.details}</div>
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
            btn.className = 'px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition font-bold';
          } else {
            btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1 font-medium';
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
          showToast(`Simulasi Cron ${triggerType} Berhasil: ${data.count} dokter Incomplete diproses & dicatat ke jejak audit!`, 'success');
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