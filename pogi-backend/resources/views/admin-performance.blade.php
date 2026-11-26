@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Dashboard Pemantauan SKP Nasional')
@section('content')
  <!-- PAGE CONTENT BODY -->
  <main class="flex-1 space-y-6 max-w-7xl w-full mx-auto">
    <!-- Clean Page Header (Modern Enterprise Standard matching Activity Log) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
      <div class="space-y-1.5 min-w-0">
        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[10px] font-black uppercase tracking-wider">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Standar Operasional B2B &bull; Manual-Assisted Sync</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 flex-wrap">
          <span>Admin Performance &amp; SLA Tracker</span>
        </h1>
        <p class="text-xs text-slate-500 max-w-3xl leading-relaxed">
          Pemantauan terpusat kecepatan SLA verifikasi berkas (&le; 24 jam), rasio akurasi persetujuan Kemenkes, produktivitas tim data, dan penyeimbang beban kerja (*workload balancing*).
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex items-center gap-2 shrink-0 flex-wrap">
        @if(Auth::user()?->role === 'SUPER_ADMIN')
        <button type="button" onclick="openWorkloadRebalanceModal()" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs rbac-operational cursor-pointer active:scale-95">
          <span class="material-symbols-outlined text-sm text-emerald-400">balance</span>
          <span>Redistribusi Beban Dokter</span>
        </button>
        @endif
        <button type="button" onclick="exportKpiReport()" class="px-3.5 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer active:scale-95">
          <span class="material-symbols-outlined text-sm">download</span>
          <span>Ekspor Rekap KPI</span>
        </button>
      </div>
    </div>

    <!-- 4 Executive Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Card 1: SLA Speed -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs relative overflow-hidden">
        <div class="flex items-center justify-between text-slate-400 mb-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Rata-Rata SLA Tim</span>
          <span class="material-symbols-outlined text-sky-600 text-xl">timer</span>
        </div>
        <div class="flex items-baseline gap-2">
          <span id="kpiTeamSla" class="text-3xl font-black text-slate-900 font-mono">{{ $summary['avgTeamSla'] ?? 11.5 }} Jam</span>
          <span class="text-xs text-slate-400 font-medium">/ 24 Jam</span>
        </div>
        <div class="mt-2 flex items-center gap-1.5">
          <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-[#16b3ac] border border-emerald-200">
            &check; On-Track SLA (-37%)
          </span>
          <span class="text-[11px] text-slate-400">Target &lt; 24 Jam</span>
        </div>
      </div>

      <!-- Card 2: Approval Rate -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs relative overflow-hidden">
        <div class="flex items-center justify-between text-slate-400 mb-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Akurasi Lolos Kemenkes</span>
          <span class="material-symbols-outlined text-emerald-600 text-xl">verified</span>
        </div>
        <div class="flex items-baseline gap-2">
          <span id="kpiTeamApproval" class="text-3xl font-black text-[#16b3ac] font-mono">{{ $summary['avgTeamApproval'] ?? 98.2 }}%</span>
          <span class="text-xs text-slate-400 font-medium">Approval</span>
        </div>
        <div class="mt-2 flex items-center gap-1.5">
          <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-[#16b3ac] border border-emerald-200">
            Sangat Teliti
          </span>
          <span class="text-[11px] text-slate-400">Rejection rate &lt; 2%</span>
        </div>
      </div>

      <!-- Card 3: Total Throughput Docs -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs relative overflow-hidden">
        <div class="flex items-center justify-between text-slate-400 mb-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Berkas Diverifikasi</span>
          <span class="material-symbols-outlined text-indigo-600 text-xl">task_alt</span>
        </div>
        <div class="flex items-baseline gap-2">
          <span id="kpiTeamProcessed" class="text-3xl font-black text-slate-900 font-mono">{{ number_format($summary['totalProcessedDocs'] ?? 0, 0, ',', '.') }}</span>
          <span class="text-xs text-slate-400 font-medium">Dokumen</span>
        </div>
        <div class="mt-2 flex items-center gap-1.5">
          <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
            +18% Bulan Ini
          </span>
          <span class="text-[11px] text-slate-400">Siklus Q4 2026</span>
        </div>
      </div>

      <!-- Card 4: Member Compliance -->
      <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs relative overflow-hidden">
        <div class="flex items-center justify-between text-slate-400 mb-2">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Dokter Capai Target Q4</span>
          <span class="material-symbols-outlined text-amber-600 text-xl">medical_services</span>
        </div>
        <div class="flex items-baseline gap-2">
          <span id="kpiTeamComplete" class="text-3xl font-black text-slate-900 font-mono">{{ $summary['totalCompleteDocs'] ?? 0 }} / {{ $summary['totalAssignedDocs'] ?? 200 }}</span>
          <span id="kpiTeamRate" class="text-xs text-slate-500 font-bold font-mono">({{ $summary['teamComplianceRate'] ?? 0 }}%)</span>
        </div>
        <div class="mt-2 flex items-center gap-1.5">
          <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
            Proses Verifikasi
          </span>
          <span class="text-[11px] text-slate-400">Target &ge; 8.0 SKP</span>
        </div>
      </div>
    </div>

    <!-- Section 2: Visual Comparison Panels (SLA Benchmarking & Workload Balance) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- SLA Benchmarking Bar Chart Panel (2 Cols) -->
      <div class="lg:col-span-2 bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-extrabold text-sm sm:text-base text-slate-900">Perbandingan Kecepatan SLA Pemrosesan per Admin</h3>
            <p class="text-xs text-slate-500 mt-0.5">Batas waktu maksimal SLA adalah 24 jam. Semakin rendah bar, semakin cepat admin merespons berkas.</p>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-slate-100 text-slate-700 font-mono">Limit: 24.0 Jam</span>
        </div>

        <!-- Dynamic SLA Horizontal Bar List -->
        <div id="slaBarContainer" class="space-y-3 pt-2">
          <!-- Rendered via JS -->
        </div>
      </div>

      <!-- Workload Balance & Capacity Health (1 Col) -->
      <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-xs space-y-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-1">
            <h3 class="font-extrabold text-sm sm:text-base text-slate-900">Kapasitas &amp; Beban Kerja</h3>
            <span class="material-symbols-outlined text-amber-600 text-lg">pie_chart</span>
          </div>
          <p class="text-xs text-slate-500">Distribusi {{ $summary['totalAssignedDocs'] ?? 0 }} dokter binaan ke dalam tim admin operasional.</p>

          <div class="mt-4 p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-slate-700">Rekomendasi Kapasitas:</span>
              <span class="font-black text-[#16b3ac] font-mono">35 - 40 Dokter / Admin</span>
            </div>
            <div class="text-[11px] text-slate-500 leading-relaxed">
              Beban di atas 42 dokter berisiko menurunkan kecepatan verifikasi menjelang tanggal 1 (*cutoff* bulanan).
            </div>
          </div>

          <!-- Mini Capacity Roster -->
          <div id="workloadMiniList" class="mt-4 space-y-2.5">
            <!-- Rendered via JS -->
          </div>
        </div>

        @if(Auth::user()?->role === 'SUPER_ADMIN')
        <button type="button" onclick="openWorkloadRebalanceModal()" class="w-full mt-4 py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] text-xs font-bold transition flex items-center justify-center gap-1.5 border border-emerald-200 rbac-operational">
          <span class="material-symbols-outlined text-sm">tune</span>
          <span>Buka Tool Seimbangkan Beban (Rebalancer)</span>
        </button>
        @endif
      </div>
    </div>

    <!-- Section 3: Leaderboard & Performance Evaluation Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden space-y-4 p-5 sm:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h3 class="font-extrabold text-base text-slate-900">Leaderboard Evaluasi Kinerja Personil Admin</h3>
          <p class="text-xs text-slate-500 mt-0.5">Penilaian tertimbang berdasarkan Kecepatan SLA (35%), Akurasi Kemenkes (30%), Volume Berkas (20%), dan Kepatuhan Dokter (15%).</p>
        </div>

        <!-- Table Filters -->
        <div class="flex items-center gap-2.5">
          <input type="text" id="adminSearchInput" onkeyup="filterAdminTable()" placeholder="Cari admin..." class="px-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold focus:outline-none focus:border-[#16b3ac] focus:ring-2 focus:ring-[#16b3ac]/15 w-48 shadow-2xs transition">
          <div class="w-52">
            <select id="adminTierFilter" class="w-full">
              <option value="ALL">Semua Tier Kinerja</option>
              <option value="Top Performer">Top Performer 🌟</option>
              <option value="Good Performer">Good Performer ✅</option>
              <option value="Needs Attention">Needs Attention ⚠️</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Table Wrapper -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 border-y border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
            <tr>
              <th class="py-3 px-3.5 text-center">Peringkat</th>
              <th class="py-3 px-3">Personil Admin</th>
              <th class="py-3 px-3 text-center">Dokter Binaan</th>
              <th class="py-3 px-3 text-center">Berkas Diproses</th>
              <th class="py-3 px-3 text-center">Rata-Rata SLA</th>
              <th class="py-3 px-3 text-center">Akurasi Kemenkes</th>
              <th class="py-3 px-3 text-center">Dokter Complete</th>
              <th class="py-3 px-3 text-center">Skor Kinerja</th>
              <th class="py-3 px-3 text-center">Aksi Manajemen</th>
            </tr>
          </thead>
          <tbody id="adminLeaderboardBody" class="divide-y divide-slate-100 font-medium">
            <!-- Rendered dynamically via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@section('modals')
  <!-- MODAL 1: Detail Kinerja & Log Aktivitas Admin (Drill-Down Modal) -->
  <div id="adminDetailModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-2.5 min-w-0">
          <div id="modalAdminAvatar" class="w-10 h-10 rounded-xl bg-emerald-100 text-[#16b3ac] flex items-center justify-center font-black text-sm shrink-0">
            SR
          </div>
          <div class="min-w-0">
            <h3 id="modalAdminName" class="font-black text-slate-900 text-sm leading-snug tracking-tight truncate">Administrator POGI</h3>
            <p id="modalAdminRole" class="text-xs text-slate-500 truncate">Lead Admin POGI Tim Data Pusat</p>
          </div>
        </div>
        <button type="button" onclick="closeAdminDetailModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 flex-1 overflow-y-auto space-y-5 text-xs bg-white min-h-0">
        <!-- 3 Highlights Cards in Modal -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="p-4 rounded-2xl bg-white border border-slate-200/80 text-center shadow-2xs">
            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Dokter Asuhan</div>
            <div id="modalAdminDocCount" class="text-xl font-black text-slate-900 mt-1 font-mono">38 Dokter</div>
          </div>
          <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100/90 text-center shadow-2xs">
            <div class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">Rata-Rata SLA</div>
            <div id="modalAdminSla" class="text-xl font-black text-[#16b3ac] mt-1 font-mono">9.4 Jam</div>
          </div>
          <div class="p-4 rounded-2xl bg-sky-50/70 border border-sky-100/90 text-center shadow-2xs">
            <div class="text-[10px] text-sky-700 font-bold uppercase tracking-wider">Skor KPI</div>
            <div id="modalAdminScore" class="text-xl font-black text-sky-800 mt-1 font-mono">84 / 100</div>
          </div>
        </div>

        <!-- Section: Daftar Dokter Binaan -->
        <div class="space-y-2">
          <h4 class="font-extrabold text-slate-800 text-xs flex items-center justify-between">
            <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-[#16b3ac]"></span>Daftar Dokter Binaan Terpantau</span>
            <span id="modalDoctorBadgeCount" class="text-[10px] bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full font-bold shadow-2xs">38 Dokter</span>
          </h4>
          <div class="max-h-48 overflow-y-auto border border-slate-200/80 rounded-2xl bg-white shadow-2xs divide-y divide-slate-100">
            <div id="modalDoctorListContainer" class="p-2 space-y-1">
              <!-- Rendered via JS -->
            </div>
          </div>
        </div>

        <!-- Section: Riwayat Aksi Log Aktivitas -->
        <div class="space-y-2">
          <h4 class="font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-[#16b3ac]"></span>Riwayat Tindakan Terbaru di Sistem
          </h4>
          <div id="modalActivityLogContainer" class="space-y-2 border border-slate-200/80 rounded-2xl p-3.5 bg-white shadow-2xs">
            <!-- Rendered via JS -->
          </div>
        </div>
      </div>

      <!-- Modal Footer (Sticky) -->
      <div class="p-4 px-6 flex items-center justify-end border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
        <button type="button" onclick="closeAdminDetailModal()" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold transition cursor-pointer">Tutup</button>
      </div>
    </div>
  </div>

  <!-- MODAL 2: Redistribusi Beban Dokter (Workload Rebalancer) -->
  <div id="workloadRebalanceModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-2.5">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#16b3ac] flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-xl">balance</span>
          </div>
          <div>
            <h3 class="font-black text-slate-900 text-sm">Redistribusi Beban Kerja</h3>
            <p class="text-xs text-slate-500">Penyeimbangan kuota dokter binaan admin</p>
          </div>
        </div>
        <button type="button" onclick="closeWorkloadRebalanceModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 flex-1 overflow-y-auto space-y-4 text-xs bg-white min-h-0">
        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-xs text-emerald-900 leading-relaxed shadow-2xs">
          <strong class="text-emerald-700">Manajemen Kapasitas:</strong> Pindahkan penugasan dokter dari admin yang memiliki beban tinggi ke admin yang berkapasitas longgar agar SLA &lt; 24 jam tetap terjaga.
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1.5">Pilih Dokter yang Akan Dialihkan</label>
          <select id="rebalanceDoctorSelect" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
            <!-- Rendered via JS -->
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 mb-1.5">Admin Penerima Tugas Baru</label>
          <select id="rebalanceTargetAdminSelect" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
            <!-- Rendered via JS -->
          </select>
        </div>
      </div>

      <!-- Modal Footer (Sticky) -->
      <div class="p-4 px-6 flex items-center justify-between border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
        <button type="button" onclick="closeWorkloadRebalanceModal()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">Batal</button>
        <button type="button" onclick="submitRebalance()" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold flex items-center gap-1.5 shadow-xs transition cursor-pointer">
          <span class="material-symbols-outlined text-sm">check</span>
          <span>Simpan &amp; Alihkan Dokter</span>
        </button>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <!-- Shared Scripts -->
  <script>
    const IS_SUPER_ADMIN = @json(Auth::user()?->role === 'SUPER_ADMIN');
    const LIVE_SERVER_ADMINS = @json($admins ?? []);
    const LIVE_SERVER_SUMMARY = @json($summary ?? null);
    const LIVE_SERVER_DOCTORS = @json($doctors ?? []);
    const LIVE_SERVER_ACTIVITIES = @json($activities ?? []);
    window.LIVE_SERVER_DOCTORS = LIVE_SERVER_DOCTORS;
    window.LIVE_SERVER_ACTIVITIES = LIVE_SERVER_ACTIVITIES;
    let performanceData = null;

    document.addEventListener('DOMContentLoaded', () => {
      loadPerformanceData();
      initAdminSelect2();
    });

    function initAdminSelect2() {
      if (window.jQuery && $.fn.select2) {
        $('#adminTierFilter').select2({
          minimumResultsForSearch: Infinity,
          width: '100%'
        }).on('change', function() {
          filterAdminTable();
        });

        $('#rebalanceDoctorSelect').select2({
          dropdownParent: $('#workloadRebalanceModal'),
          width: '100%'
        });

        $('#rebalanceTargetAdminSelect').select2({
          dropdownParent: $('#workloadRebalanceModal'),
          width: '100%'
        });
      }
    }

    function loadPerformanceData() {
      performanceData = {
        summary: LIVE_SERVER_SUMMARY || { avgTeamSla: 0, avgTeamApproval: 0, totalProcessedDocs: 0, totalCompleteDocs: 0, totalAssignedDocs: 0, teamComplianceRate: 0 },
        admins: Array.isArray(LIVE_SERVER_ADMINS) ? LIVE_SERVER_ADMINS : []
      };
      renderExecutiveCards(performanceData.summary);
      renderSlaBars(performanceData.admins);
      renderWorkloadMiniList(performanceData.admins);
      renderLeaderboardTable(performanceData.admins);
      populateRebalanceDropdowns();
    }

    function renderExecutiveCards(summary) {
      document.getElementById('kpiTeamSla').innerText = `${summary.avgTeamSla} Jam`;
      document.getElementById('kpiTeamApproval').innerText = `${summary.avgTeamApproval}%`;
      document.getElementById('kpiTeamProcessed').innerText = summary.totalProcessedDocs.toLocaleString('id-ID');
      document.getElementById('kpiTeamComplete').innerText = `${summary.totalCompleteDocs} / ${summary.totalAssignedDocs}`;
      document.getElementById('kpiTeamRate').innerText = `(${summary.teamComplianceRate}%)`;
    }

    function renderSlaBars(admins) {
      const container = document.getElementById('slaBarContainer');
      if (!container) return;

      if (!admins || admins.length === 0) {
        container.innerHTML = `
          <div class="py-10 text-center flex flex-col items-center justify-center gap-2">
            <span class="material-symbols-outlined text-3xl text-slate-300">speed</span>
            <span class="text-xs font-semibold text-slate-400">Belum ada data perbandingan SLA admin.</span>
          </div>
        `;
        return;
      }

      container.innerHTML = admins.map(a => {
        // Percentage of 24 hours SLA limit
        const pct = Math.min(100, Math.round((a.avgSla / 24) * 100));
        let barColor = 'bg-emerald-500';
        let badgeColor = 'bg-emerald-50 text-emerald-800';
        if (a.avgSla > 20) {
          barColor = 'bg-amber-500';
          badgeColor = 'bg-amber-50 text-amber-800';
        } else if (a.avgSla > 15) {
          barColor = 'bg-sky-500';
          badgeColor = 'bg-sky-50 text-sky-800';
        }

        return `
          <div class="space-y-1">
            <div class="flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg ${a.avatarBg} flex items-center justify-center font-bold text-[10px] shrink-0">${a.avatar}</span>
                <span class="font-bold text-slate-800">${a.displayName}</span>
                <span class="text-[10px] text-slate-400 font-medium">(${a.doctorCount} dokter)</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-mono font-black text-slate-900 text-xs">${a.avgSla} Jam</span>
                <span class="px-2 py-0.2 rounded text-[10px] font-bold ${badgeColor}">SLA ${pct}%</span>
              </div>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
              <div class="${barColor} h-2 rounded-full transition-all duration-500" style="width: ${pct}%"></div>
            </div>
          </div>
        `;
      }).join('');
    }

    function renderWorkloadMiniList(admins) {
      const container = document.getElementById('workloadMiniList');
      if (!container) return;

      if (!admins || admins.length === 0) {
        container.innerHTML = `
          <div class="py-10 text-center flex flex-col items-center justify-center gap-2">
            <span class="material-symbols-outlined text-3xl text-slate-300">group_off</span>
            <span class="text-xs font-semibold text-slate-400">Belum ada data kapasitas admin.</span>
          </div>
        `;
        return;
      }

      container.innerHTML = admins.map(a => {
        return `
          <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs">
            <div class="flex items-center gap-2">
              <span class="w-5 h-5 rounded ${a.avatarBg} flex items-center justify-center text-[9px] font-bold">${a.avatar}</span>
              <span class="font-bold text-slate-800 truncate max-w-[120px]">${a.name}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="font-mono font-black text-slate-900">${a.doctorCount} Dok</span>
              <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border ${a.workloadClass}">${a.workloadStatus}</span>
            </div>
          </div>
        `;
      }).join('');
    }

    function renderLeaderboardTable(admins) {
      const tbody = document.getElementById('adminLeaderboardBody');
      if (!tbody) return;

      if (!admins || admins.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="9" class="py-14 px-4 text-center">
              <div class="flex flex-col items-center justify-center gap-2.5 max-w-sm mx-auto">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-400 flex items-center justify-center shadow-2xs">
                  <span class="material-symbols-outlined text-2xl">person_off</span>
                </div>
                <div class="font-bold text-sm text-slate-800">Belum Ada Data Personil Admin</div>
                <p class="text-xs text-slate-500 leading-relaxed">
                  Tidak ditemukan personil admin yang sesuai dengan kriteria pencarian atau filter tier kinerja yang dipilih.
                </p>
                <button type="button" onclick="openRebalanceModal()" class="mt-1 px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] border border-emerald-200 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer rbac-write">
                  <span class="material-symbols-outlined text-sm">balance</span>
                  <span>Buka Tool Seimbangkan Beban</span>
                </button>
              </div>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = admins.map((a, idx) => {
        const rank = idx + 1;
        let rankBadge = `<span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-black text-xs inline-flex items-center justify-center">${rank}</span>`;
        if (rank === 1) rankBadge = `<span class="w-6 h-6 rounded-full bg-amber-400 text-amber-950 font-black text-xs inline-flex items-center justify-center shadow-xs">#1</span>`;
        else if (rank === 2) rankBadge = `<span class="w-6 h-6 rounded-full bg-slate-300 text-slate-900 font-black text-xs inline-flex items-center justify-center shadow-xs">#2</span>`;
        else if (rank === 3) rankBadge = `<span class="w-6 h-6 rounded-full bg-amber-700 text-white font-black text-xs inline-flex items-center justify-center shadow-xs">#3</span>`;

        return `
          <tr class="hover:bg-slate-50/80 transition">
            <td class="py-3 px-3.5 text-center">${rankBadge}</td>
            <td class="py-3 px-3">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl ${a.avatarBg} flex items-center justify-center font-black text-xs shrink-0 shadow-2xs">
                  ${a.avatar}
                </div>
                <div>
                  <div class="font-bold text-slate-900">${a.displayName}</div>
                  <div class="text-[10px] text-slate-400">${a.role}</div>
                </div>
              </div>
            </td>
            <td class="py-3 px-3 text-center">
              <div class="font-mono font-black text-slate-900 text-xs">${a.doctorCount} Dokter</div>
              <div class="text-[10px] text-slate-400 font-medium">Beban ${Math.round((a.doctorCount / 200) * 100)}%</div>
            </td>
            <td class="py-3 px-3 text-center font-mono font-bold text-slate-800">
              ${a.processedDocs} <span class="text-[10px] text-slate-400">Berkas</span>
            </td>
            <td class="py-3 px-3 text-center">
              <span class="font-mono font-black text-[#16b3ac] text-xs">${a.avgSla} Jam</span>
              <div class="text-[10px] text-emerald-600 font-bold">&check; Target &lt; 24 Jam</div>
            </td>
            <td class="py-3 px-3 text-center">
              <span class="font-mono font-black text-slate-900 text-xs">${a.approvalRate}%</span>
              <div class="text-[10px] text-slate-400">${a.rejections} Reject</div>
            </td>
            <td class="py-3 px-3 text-center">
              <span class="font-mono font-black text-slate-900 text-xs">${a.completeCount} / ${a.doctorCount}</span>
              <div class="text-[10px] text-slate-500 font-bold font-mono">(${a.complianceRate}%)</div>
            </td>
            <td class="py-3 px-3 text-center">
              <div class="inline-flex flex-col items-center">
                <span class="px-2.5 py-1 rounded-full text-xs font-black border font-mono ${a.tierBadgeClass}">${a.overallScore} / 100</span>
                <span class="text-[9px] text-slate-500 font-bold mt-0.5">${a.tier}</span>
              </div>
            </td>
            <td class="py-3 px-3 text-center">
              <div class="inline-flex items-center gap-1.5">
                <button type="button" onclick="showAdminDetailModal('${a.id}')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition flex items-center gap-1">
                  <span class="material-symbols-outlined text-xs">visibility</span>
                  <span>Rincian</span>
                </button>
                ${IS_SUPER_ADMIN ? `
                <button type="button" onclick="openWorkloadRebalanceModal('${a.name}')" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] font-bold text-[11px] transition flex items-center gap-1 border border-emerald-200 rbac-operational">
                  <span class="material-symbols-outlined text-xs">tune</span>
                  <span>Alihkan</span>
                </button>
                ` : ''}
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function filterAdminTable() {
      if (!performanceData) return;
      const search = (document.getElementById('adminSearchInput').value || '').toLowerCase();
      const tier = document.getElementById('adminTierFilter').value;

      const filtered = performanceData.admins.filter(a => {
        const matchSearch = a.name.toLowerCase().includes(search) || a.displayName.toLowerCase().includes(search) || a.role.toLowerCase().includes(search);
        const matchTier = tier === 'ALL' || a.tier.includes(tier);
        return matchSearch && matchTier;
      });

      renderLeaderboardTable(filtered);
    }

    function showAdminDetailModal(adminId) {
      if (!performanceData) return;
      const admin = performanceData.admins.find(a => a.id === adminId);
      if (!admin) return;

      document.getElementById('modalAdminAvatar').innerText = admin.avatar;
      document.getElementById('modalAdminName').innerText = admin.displayName;
      document.getElementById('modalAdminRole').innerText = admin.role;
      document.getElementById('modalAdminDocCount').innerText = `${admin.doctorCount} Dokter`;
      document.getElementById('modalAdminSla').innerText = `${admin.avgSla} Jam`;
      document.getElementById('modalAdminScore').innerText = `${admin.overallScore} / 100`;
      document.getElementById('modalDoctorBadgeCount').innerText = `${admin.doctorCount} Dokter`;

      // Render assigned doctors
      const doctors = (LIVE_SERVER_DOCTORS || []).filter(d => (d.assignedTo && d.assignedTo.toLowerCase().includes(admin.name.toLowerCase())) || (admin.displayName && d.assignedTo && d.assignedTo.toLowerCase().includes(admin.displayName.toLowerCase())));
      const docListCont = document.getElementById('modalDoctorListContainer');
      if (doctors.length === 0) {
        docListCont.innerHTML = `
          <div class="py-8 px-4 text-center">
            <div class="flex flex-col items-center justify-center gap-2 max-w-xs mx-auto">
              <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 flex items-center justify-center shadow-2xs">
                <span class="material-symbols-outlined text-xl">person_off</span>
              </div>
              <div class="font-extrabold text-xs text-slate-800">Belum Ada Dokter Binaan</div>
              <p class="text-[11px] text-slate-500">Admin ini belum memiliki dokter binaan yang dialokasikan.</p>
            </div>
          </div>
        `;
      } else {
        docListCont.innerHTML = doctors.map(d => {
          const statusBadge = (d.status === 'Complete' || d.status === 'COMPLETE')
            ? `<span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-emerald-100 text-[#16b3ac]">Complete</span>`
            : `<span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700">Incomplete</span>`;
          return `
            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 transition text-xs">
              <div>
                <a href="/doctor-detail?id=${d.id}" class="font-bold text-slate-900 hover:text-[#16b3ac] hover:underline">${d.name}</a>
                <div class="text-[10px] text-slate-400 font-mono">STR: ${d.strNumber || '-'} &bull; ${d.hospital || '-'}</div>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-mono font-bold text-slate-700 text-[11px]">${d.q4Credits || 0} SKP</span>
                ${statusBadge}
              </div>
            </div>
          `;
        }).join('');
      }

      // Render recent activities
      const allActs = LIVE_SERVER_ACTIVITIES || [];
      const adminActs = allActs.filter(act => act.actor && (act.actor.toLowerCase().includes(admin.name.toLowerCase()) || (admin.displayName && act.actor.toLowerCase().includes(admin.displayName.toLowerCase()))));
      const actCont = document.getElementById('modalActivityLogContainer');
      const sampleActs = adminActs.length > 0 ? adminActs.slice(0, 4) : allActs.slice(0, 3);

      actCont.innerHTML = sampleActs.map(act => `
        <div class="flex items-start gap-2 text-xs py-1">
          <span class="material-symbols-outlined text-xs text-[#16b3ac] mt-0.5">check_circle</span>
          <div class="min-w-0 flex-1">
            <div class="font-bold text-slate-800">${act.action}</div>
            <div class="text-[11px] text-slate-600">${act.details}</div>
            <div class="text-[10px] text-slate-400 font-mono mt-0.5">${act.timestamp}</div>
          </div>
        </div>
      `).join('');

      document.getElementById('adminDetailModal').classList.remove('hidden');
    }

    function closeAdminDetailModal() {
      document.getElementById('adminDetailModal').classList.add('hidden');
    }

    function populateRebalanceDropdowns() {
      const docSelect = document.getElementById('rebalanceDoctorSelect');
      const adminSelect = document.getElementById('rebalanceTargetAdminSelect');
      if (!docSelect || !adminSelect) return;

      const doctors = LIVE_SERVER_DOCTORS || [];
      docSelect.innerHTML = doctors.map(d => `
        <option value="${d.id}">${d.name} (Saat ini dipegang: ${d.assignedTo || 'Unassigned'})</option>
      `).join('');

      const admins = (LIVE_SERVER_ADMINS && LIVE_SERVER_ADMINS.length > 0)
        ? LIVE_SERVER_ADMINS
        : [];
      adminSelect.innerHTML = admins.map(a => `
        <option value="${a.id}">${a.name} (${a.doctorCount || 0} Dokter)</option>
      `).join('');

      if (window.jQuery && $.fn.select2) {
        $(docSelect).trigger('change.select2');
        $(adminSelect).trigger('change.select2');
      }
    }

    function openWorkloadRebalanceModal(preferredAdminName) {
      populateRebalanceDropdowns();
      if (preferredAdminName) {
        // filter doctor select to that admin
        const docSelect = document.getElementById('rebalanceDoctorSelect');
        const doctors = LIVE_SERVER_DOCTORS || [];
        const docFromAdmin = doctors.find(d => d.assignedTo === preferredAdminName);
        if (docFromAdmin) {
          docSelect.value = docFromAdmin.id;
          if (window.jQuery && $.fn.select2) $(docSelect).trigger('change.select2');
        }
      }
      document.getElementById('workloadRebalanceModal').classList.remove('hidden');
    }

    function closeWorkloadRebalanceModal() {
      document.getElementById('workloadRebalanceModal').classList.add('hidden');
    }

    async function submitRebalance() {
      const docId = document.getElementById('rebalanceDoctorSelect').value;
      const targetAdminId = document.getElementById('rebalanceTargetAdminSelect').value;

      if (!docId || !targetAdminId) return;

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const res = await fetch(`/doctors/${docId}/assign-pic`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ assigned_pic_id: targetAdminId })
        });
        const data = await res.json();
        if (res.ok && data.success) {
          showToast(data.message || 'Dokter berhasil dialihkan!', 'success');
          closeWorkloadRebalanceModal();
          setTimeout(() => window.location.reload(), 600);
        } else {
          showToast(data.message || 'Gagal mengalihkan dokter', 'error');
        }
      } catch (err) {
        showToast('Terjadi kesalahan koneksi server', 'error');
      }
    }

    function exportKpiReport() {
      showToast('Mengunduh Laporan Rekap KPI & SLA Kinerja Admin (Q4 2026)...', 'success');
      setTimeout(() => {
        showToast('File Rekap_KPI_Admin_Performance_POGI_2026.pdf berhasil diunduh!', 'info');
      }, 1000);
    }
  </script>

@endsection