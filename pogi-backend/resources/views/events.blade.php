@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Dashboard Pemantauan SKP Nasional')
@section('content')

      <!-- Main Body -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-5 page-fade-in">

        <!-- MedCred Standard Heading Banner -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#16b3ac] text-[10px] font-black uppercase tracking-wider">
              <span>KATALOG RESMI TERAKREDITASI</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <span>Katalog Event &amp; Kegiatan SKP</span>
              <span class="text-sm font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">(10 Terakreditasi)</span>
            </h1>
            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
              Daftar kegiatan ilmiah, simposium, workshop, dan pelayanan klinis ber-SKP yang telah terverifikasi Kemenkes Plataran Sehat terintegrasi PP POGI.
            </p>
          </div>
          @if(in_array(Auth::user()?->role, ['SUPER_ADMIN', 'ADMIN_POGI']))
          <div class="flex items-center gap-2.5 shrink-0">
            <button type="button" onclick="openAddEventModal()" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-write">
              <span class="material-symbols-outlined text-base">add_circle</span>
              <span>Tambah Event SKP</span>
            </button>
          </div>
          @endif
        </div>

        <!-- Filter Subbidang Tabs -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold text-slate-600">
              <button type="button" onclick="filterEvents('ALL')" id="filter-all" class="px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition">Semua Event</button>
              <button type="button" onclick="filterEvents('Pembelajaran')" id="filter-pembelajaran" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Pembelajaran
              </button>
              <button type="button" onclick="filterEvents('Pengabdian')" id="filter-pengabdian" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-amber-600"></span> Pengabdian
              </button>
              <button type="button" onclick="filterEvents('Pelayanan')" id="filter-pelayanan" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-sky-600"></span> Pelayanan
              </button>
            </div>

            <div class="text-xs text-slate-500">
              Standar Nilai Kredit Terverifikasi Kemenkes &bull; Plataran Sehat
            </div>
          </div>
        </div>

        
        <!-- Event Cards Grid (Desktop 3 columns, Tablet 2 columns, Mobile 1 column matching Image 2) -->
        <div id="eventsGridContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          <!-- Rendered dynamically by JavaScript -->
        </div>

        <!-- MedCred Pagination Footer -->
        <div id="eventsGridPagination" class="px-4 py-3.5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
          <!-- Rendered dynamically -->
        </div>

      </main>
    </div>
  </div>

  <!-- Modal Daftar Peserta Dokter Obgyn -->
  <div id="eventParticipantsModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3 shrink-0">
        <div class="flex items-center gap-2.5">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#16b3ac] flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-xl">groups</span>
          </div>
          <div>
            <h3 id="partModalTitle" class="font-black text-slate-900 text-sm">Daftar Peserta Dokter Terverifikasi</h3>
            <p id="partModalMeta" class="text-xs text-slate-500">Terakreditasi PP POGI &bull; Terhubung ke Plataran Sehat</p>
          </div>
        </div>
        <button type="button" onclick="closeEventParticipants()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <div class="flex-1 overflow-y-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px] sticky top-0">
            <tr>
              <th class="py-2.5 px-3">Nama Dokter &amp; Gelar</th>
              <th class="py-2.5 px-3">Provinsi Cabang</th>
              <th class="py-2.5 px-3">Rumah Sakit</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3 text-center">Perolehan</th>
            </tr>
          </thead>
          <tbody id="partTableBody" class="divide-y divide-slate-100">
            <!-- Dynamic rows -->
          </tbody>
        </table>
      </div>

      <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0">
        <button type="button" onclick="exportEventParticipants()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5 transition">
          <span class="material-symbols-outlined text-sm">download</span>
          <span>Ekspor Absensi Peserta</span>
        </button>
        <button type="button" onclick="closeEventParticipants()" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold transition">
          Tutup
        </button>
      </div>
    </div>
  </div>

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


    function filterEvents(type) {
      activeFilter = type;
      const tabs = [
        { id: 'filter-all', key: 'ALL' },
        { id: 'filter-pembelajaran', key: 'Pembelajaran' },
        { id: 'filter-pengabdian', key: 'Pengabdian' },
        { id: 'filter-pelayanan', key: 'Pelayanan' }
      ];
      tabs.forEach(t => {
        const btn = document.getElementById(t.id);
        if (!btn) return;
        if (type === t.key) {
          btn.className = 'px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition font-bold';
        } else {
          btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition flex items-center gap-1 font-medium';
        }
      });
      renderEventsGrid(true);
    }

    const LIVE_SERVER_EVENTS = @json($events ?? []);
    const LIVE_SERVER_DOCTORS = @json($doctors ?? []);
    window.LIVE_SERVER_EVENTS = LIVE_SERVER_EVENTS;
    window.LIVE_SERVER_DOCTORS = LIVE_SERVER_DOCTORS;
    let activeFilter = 'ALL';
    let eventPagination = { page: 1, limit: 6, total: LIVE_SERVER_EVENTS.length || 0 };
    let currentFilteredEvents = null;

    document.addEventListener('DOMContentLoaded', () => {
      renderEventsGrid();
    });

    function changeEventPage(p) {
      const list = currentFilteredEvents || (LIVE_SERVER_EVENTS.length > 0 ? LIVE_SERVER_EVENTS : []);
      const totalPages = Math.max(1, Math.ceil(list.length / eventPagination.limit));
      if (p < 1) p = 1;
      if (p > totalPages) p = totalPages;
      eventPagination.page = p;
      renderEventsGrid(false);
    }

    function setEventPageLimit(limit) {
      eventPagination.limit = parseInt(limit, 10) || 6;
      eventPagination.page = 1;
      renderEventsGrid(false);
    }

    function renderEventsGrid(resetPage = true) {
      if (resetPage) eventPagination.page = 1;
      const events = (LIVE_SERVER_EVENTS && LIVE_SERVER_EVENTS.length > 0)
        ? LIVE_SERVER_EVENTS
        : [];
      const gridContainer = document.getElementById('eventsGridContainer');
      if (!gridContainer) return;

      const filtered = activeFilter === 'ALL' ? events : events.filter(e => e.type === activeFilter);
      currentFilteredEvents = filtered;
      eventPagination.total = filtered.length;

      const totalPages = Math.max(1, Math.ceil(eventPagination.total / eventPagination.limit));
      if (eventPagination.page > totalPages) eventPagination.page = totalPages;
      if (eventPagination.page < 1) eventPagination.page = 1;

      const start = (eventPagination.page - 1) * eventPagination.limit;
      const end = Math.min(start + eventPagination.limit, eventPagination.total);
      const pageEvents = filtered.slice(start, end);

      if (pageEvents.length === 0) {
        gridContainer.innerHTML = '<div class="col-span-full py-12 text-center text-slate-400 font-medium">Tidak ada kegiatan yang ditemukan pada subbidang ini.</div>';
      } else {
        gridContainer.innerHTML = pageEvents.map(e => {
          let badgeColor = 'bg-sky-100 text-sky-800 border-sky-200';
          if (e.type === 'Pembelajaran') badgeColor = 'bg-emerald-100 text-emerald-800 border-emerald-200';
          if (e.type === 'Pengabdian') badgeColor = 'bg-amber-100 text-amber-800 border-amber-200';
          const typeBadge = e.typeBadge || badgeColor;

          const eventTitle = e.title || e.name || 'Kegiatan Ilmiah & Pelayanan POGI';
          const eventVenue = e.venue || e.category || 'PP POGI / Daring';
          const docCount = e.doctors ? e.doctors.replace('done', 'selesai') : ((e.doctorsCompleted || 0) + ' / 100 selesai');
          const creditsVal = e.creditsDisplay || (e.credits ? e.credits.toString().replace('.', ',') : '1');

          return `
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs hover:shadow-md transition flex flex-col justify-between space-y-4">
              <div>
                <!-- Top Badges -->
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold ${typeBadge} border">
                      ${e.type}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                      ${e.id}
                    </span>
                  </div>
                  <span class="px-2.5 py-1 rounded-lg bg-[#16b3ac] text-white font-black text-xs shadow-2xs">
                    ${creditsVal} SKP
                  </span>
                </div>
                <!-- Title -->
                <h3 class="font-extrabold text-sm text-slate-900 leading-snug line-clamp-2">
                  ${eventTitle}
                </h3>
                <!-- Date & Category -->
                <div class="mt-2.5 space-y-1 text-xs text-slate-500">
                  <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-xs text-slate-400">calendar_month</span>
                    <span>${e.date || 'Periode Q4 2026'}</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-xs text-slate-400">location_on</span>
                    <span class="truncate">${eventVenue}</span>
                  </div>
                </div>
              </div>

              <!-- Footer with Peserta Tuntas & Button -->
              <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <div>
                  <div class="text-[10px] text-slate-400">Peserta Tuntas:</div>
                  <div class="font-black text-slate-900 text-xs mt-0.5">${docCount}</div>
                </div>
                <button type="button" onclick="openEventParticipants('${e.id}', '${eventTitle.replace(/'/g, "\\'")}', '${creditsVal}')" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:border-[#16b3ac] hover:bg-emerald-50 text-slate-700 hover:text-[#16b3ac] font-bold text-xs transition">
                  Lihat Peserta
                </button>
              </div>
            </div>
          `;
        }).join('');
      }

      // Render MedCred Pagination Controls
      renderMedCredPaginationControls({
        containerId: 'eventsGridPagination',
        currentPage: eventPagination.page,
        totalPages: totalPages,
        totalItems: eventPagination.total,
        startIdx: eventPagination.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: 'kegiatan ber-SKP terakreditasi PP POGI',
        onPageChangeFn: 'changeEventPage',
        onLimitChangeFn: 'setEventPageLimit',
        currentLimit: eventPagination.limit,
        limits: [6, 12, 24, 50]
      });
    }

    function openEventParticipants(eventId, title, credits) {
      const modal = document.getElementById('eventParticipantsModal');
      const titleEl = document.getElementById('partModalTitle');
      const metaEl = document.getElementById('partModalMeta');
      const tbody = document.getElementById('partTableBody');
      if (!modal || !tbody) return;

      if (titleEl) titleEl.innerText = 'Peserta: ' + title;
      if (metaEl) metaEl.innerText = 'Nilai Kredit: +' + credits + ' SKP • Terverifikasi Plataran Sehat Kemenkes';

      const doctors = (LIVE_SERVER_DOCTORS && LIVE_SERVER_DOCTORS.length > 0) ? LIVE_SERVER_DOCTORS : [];
      if (doctors.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-slate-400 italic">Belum ada peserta dokter terdaftar untuk kegiatan ini.</td></tr>`;
      } else {
        tbody.innerHTML = doctors.map(d => `
          <tr class="hover:bg-slate-50 transition">
            <td class="py-2.5 px-3">
              <div class="font-bold text-slate-900">${d.name}</div>
              <div class="text-[10px] text-slate-400 font-mono">${d.pogiId || '-'}</div>
            </td>
            <td class="py-2.5 px-3 text-slate-600">${d.province || '-'}</td>
            <td class="py-2.5 px-3 text-slate-600">${d.institution || '-'}</td>
            <td class="py-2.5 px-3 text-center">
              <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Tuntas Hadir</span>
            </td>
            <td class="py-2.5 px-3 text-center font-black text-[#16b3ac]">+${credits} SKP</td>
          </tr>
        `).join('');
      }

      modal.classList.remove('hidden');
    }

    function exportEventParticipantsPdf() {
      if (typeof window.PogiPdfExporter !== 'undefined') {
        window.exportEventParticipantsPdf();
      }
    }
    const exportEventParticipants = exportEventParticipantsPdf;

    function closeEventParticipants() {
      const modal = document.getElementById('eventParticipantsModal');
      if (modal) modal.classList.add('hidden');
    }
  
    function exportEventsPdf() {
      if (typeof window.PogiPdfExporter !== 'undefined') {
        window.exportEventsPdf();
      }
    }
    const exportEventCSV = exportEventsPdf;

    function openAddEventModal() {
      showToast('Form pendaftaran kegiatan baru akan segera dibuka...', 'info');
    }
  
</script>
@endsection