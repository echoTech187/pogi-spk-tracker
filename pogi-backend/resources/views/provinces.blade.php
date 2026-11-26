@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Monitoring Kepatuhan 38 Provinsi')
@section('content')

    <!-- Clean Page Header (Modern Enterprise Standard matching Activity Log) -->
    <div class="space-y-1.5 min-w-0 pb-1">
      <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[10px] font-black uppercase tracking-wider">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>Monitoring Wilayah Nasional</span>
        <span class="text-slate-300">•</span>
        <span class="text-slate-500 font-semibold normal-case">38 Cabang POGI</span>
      </div>
      <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 flex-wrap">
        <span>Distribusi &amp; Kepatuhan Cabang POGI</span>
        <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 border border-slate-200/80 px-2.5 py-0.5 rounded-full">38 Wilayah</span>
      </h1>
      <p class="text-xs text-slate-500 max-w-3xl leading-relaxed">
        Pemetaan persebaran dokter spesialis obstetri &amp; ginekologi dan tingkat kepatuhan pemenuhan standar 160 SKP di 38 cabang POGI seluruh Indonesia.
      </p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
      <div class="flex-1 p-2.5 sm:p-3 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center gap-2.5 transition hover:border-slate-300 focus-within:border-[#16b3ac] focus-within:ring-2 focus-within:ring-[#16b3ac]/15">
        <span class="material-symbols-outlined text-slate-400 text-lg ml-1">search</span>
        <input id="searchProvinceInput" onkeyup="filterProvinces()" type="text" placeholder="Cari nama provinsi atau cabang POGI daerah..." class="w-full text-xs font-medium text-slate-800 placeholder:text-slate-400 bg-transparent focus:outline-none" />
      </div>
      <div class="flex items-center gap-2.5 shrink-0">
        <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer bg-white hover:bg-slate-50 px-3.5 py-2.5 sm:py-3 rounded-2xl border border-slate-200/90 shadow-2xs transition select-none">
          <input id="hideZeroDoctors" type="checkbox" onchange="toggleZeroDoctors()" class="w-4 h-4 rounded text-[#16b3ac] focus:ring-[#16b3ac] border-slate-300 cursor-pointer" />
          <span class="text-xs font-semibold text-slate-700">Sembunyikan 0 Dokter</span>
        </label>
      </div>
    </div>

    <!-- Tabel 38 Provinsi -->
    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
            <tr>
              <th class="py-3 px-4 w-12 text-center">#</th>
              <th class="py-3 px-4">Nama Provinsi / Cabang POGI</th>
              <th class="py-3 px-4 text-center">Jumlah Dokter</th>
              <th class="py-3 px-4 text-center">Complete</th>
              <th class="py-3 px-4 text-center">Incomplete</th>
              <th class="py-3 px-4 w-48">Progress Kepatuhan</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody id="provincesTableBody" class="divide-y divide-slate-100 font-medium">
            <!-- Rendered dynamically -->
          </tbody>
        </table>
      </div>

      <!-- Mobile Touch Cards (< md) -->
      <div id="provincesMobileCards" class="md:hidden divide-y divide-slate-100 p-2 sm:p-3 space-y-3">
        <!-- Rendered dynamically for mobile -->
      </div>

      <!-- Interactive MedCred Pagination Footer -->
      <div id="provincesTablePagination" class="px-4 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
    </div>
@endsection

@section('scripts')
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


    const LIVE_SERVER_PROVINCES = @json($provinces ?? []);
    let provincePagination = { page: 1, limit: 10, total: LIVE_SERVER_PROVINCES.length || 0 };
    let currentFilteredProvinces = null;

    document.addEventListener('DOMContentLoaded', () => {
      renderProvincesTable();
    });

    function changeProvincePage(p) {
      const list = currentFilteredProvinces || (LIVE_SERVER_PROVINCES.length > 0 ? LIVE_SERVER_PROVINCES : []);
      const totalPages = Math.max(1, Math.ceil(list.length / provincePagination.limit));
      if (p < 1) p = 1;
      if (p > totalPages) p = totalPages;
      provincePagination.page = p;
      const q = (document.getElementById('searchProvinceInput')?.value || '');
      const hideZero = document.getElementById('hideZeroDoctors')?.checked || false;
      renderProvincesTable(q, hideZero, false);
    }

    function setProvincePageLimit(limit) {
      provincePagination.limit = parseInt(limit, 10) || 10;
      provincePagination.page = 1;
      const q = (document.getElementById('searchProvinceInput')?.value || '');
      const hideZero = document.getElementById('hideZeroDoctors')?.checked || false;
      renderProvincesTable(q, hideZero, false);
    }

    function renderProvincesTable(filterText = '', hideZero = false, resetPage = true) {
      if (resetPage) provincePagination.page = 1;
      const provinces = (LIVE_SERVER_PROVINCES && LIVE_SERVER_PROVINCES.length > 0)
        ? LIVE_SERVER_PROVINCES
        : [];
      const tbody = document.getElementById('provincesTableBody');
      if (!tbody) return;

      const filtered = provinces.filter(p => {
        const matchText = p.name.toLowerCase().includes(filterText.toLowerCase());
        const matchZero = hideZero ? p.doctors > 0 : true;
        return matchText && matchZero;
      });

      currentFilteredProvinces = filtered;
      provincePagination.total = filtered.length;

      const totalPages = Math.max(1, Math.ceil(provincePagination.total / provincePagination.limit));
      if (provincePagination.page > totalPages) provincePagination.page = totalPages;
      if (provincePagination.page < 1) provincePagination.page = 1;

      const start = (provincePagination.page - 1) * provincePagination.limit;
      const end = Math.min(start + provincePagination.limit, provincePagination.total);
      const pageProvinces = filtered.slice(start, end);

      if (pageProvinces.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="8" class="py-14 px-4 text-center">
              <div class="flex flex-col items-center justify-center gap-2.5 max-w-sm mx-auto">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-400 flex items-center justify-center shadow-2xs">
                  <span class="material-symbols-outlined text-2xl">location_off</span>
                </div>
                <div class="font-extrabold text-sm text-slate-800">Tidak Ada Wilayah Cabang</div>
                <p class="text-xs text-slate-500 leading-relaxed">
                  Tidak ditemukan provinsi atau cabang POGI yang cocok dengan kriteria pencarian dan filter saat ini.
                </p>
              </div>
            </td>
          </tr>
        `;
      } else {
        tbody.innerHTML = pageProvinces.map((p, idx) => {
          let statusBadge = '';
          let barColor = 'bg-[#16b3ac]';

          if (p.completionRate >= 85) {
            statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Terpenuhi (Aman)</span>';
            barColor = 'bg-[#eab308]';
          } else if (p.completionRate >= 50) {
            statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-[#16b3ac] border border-emerald-200">Hampir Memenuhi</span>';
            barColor = 'bg-[#16a34a]';
          } else {
            statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">Sangat Kurang (&lt;50%)</span>';
            barColor = 'bg-[#dc2626]';
          }

          return `
            <tr class="hover:bg-slate-50 transition border-b border-slate-100">
              <td class="py-3 px-4 text-center text-slate-400 font-mono text-[11px]">${start + idx + 1}</td>
              <td class="py-3 px-4">
                <div class="font-extrabold text-slate-900">${p.name}</div>
                <div class="text-[10px] text-slate-400">Pengurus Cabang POGI Daerah</div>
              </td>
              <td class="py-3 px-4 text-center font-bold text-slate-800">${p.doctors}</td>
              <td class="py-3 px-4 text-center font-black text-emerald-700">${p.complete}</td>
              <td class="py-3 px-4 text-center font-black ${p.incomplete > 0 ? 'text-amber-600' : 'text-slate-400'}">${p.incomplete}</td>
              <td class="py-3 px-4">
                <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                  <span class="text-slate-700">${p.completionRate.toFixed(1)}%</span>
                  <span class="text-slate-400 font-normal">${p.complete}/${p.doctors} Dr</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                  <div class="${barColor} h-2 rounded-full" style="width: ${p.completionRate}%"></div>
                </div>
              </td>
              <td class="py-3 px-4 text-center">${statusBadge}</td>
              <td class="py-3 px-4 text-center">
                <a href="/doctors?search=${encodeURIComponent(p.name)}" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] font-bold text-xs transition inline-flex items-center gap-1">
                  <span class="material-symbols-outlined text-xs">group</span>
                  <span>Lihat Dokter (${p.doctors})</span>
                </a>
              </td>
            </tr>
          `;
        }).join('');
      }

      renderMedCredPaginationControls({
        containerId: 'provincesTablePagination',
        currentPage: provincePagination.page,
        totalPages,
        totalItems: provincePagination.total,
        startIdx: provincePagination.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: 'cabang POGI daerah di seluruh Indonesia',
        onPageChangeFn: 'changeProvincePage',
        onLimitChangeFn: 'setProvincePageLimit',
        currentLimit: provincePagination.limit,
        limits: [10, 20, 38]
      });
    }

    function filterProvinces() {
      const q = document.getElementById('searchProvinceInput').value;
      const hideZero = document.getElementById('hideZeroDoctors').checked;
      renderProvincesTable(q, hideZero, true);
    }

    function toggleZeroDoctors() {
      filterProvinces();
    }
  
    function exportProvincesPdf() {
      if (typeof window.PogiPdfExporter !== 'undefined') {
        window.exportProvincesPdf();
      }
    }
    const exportProvincesCSV = exportProvincesPdf;
</script>
@endsection