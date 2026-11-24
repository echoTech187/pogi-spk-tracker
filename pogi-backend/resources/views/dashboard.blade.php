@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Dashboard Pemantauan SKP Nasional')

@section('content')
        <!-- Welcome Banner -->
        <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-r from-[#012f22] via-[#005238] to-[#012319] text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-white/10 text-emerald-300 text-[11px] font-bold border border-white/10">
              <span class="material-symbols-outlined text-xs">verified</span> Standardisasi Mandatori 160 SKP / 5 Tahun
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight">Monitoring Pemenuhan SKP Dokter Spesialis Obgyn</h1>
            <p class="text-xs text-emerald-100/80 max-w-2xl leading-relaxed">
              Memantau ketercapaian kredit SKP di 3 Subbidang (Pelayanan, Pengabdian, Pembelajaran) untuk dokter Obgyn se-Indonesia. Lakukan tindak lanjut (FU) kepada Rumah Sakit jejaring dan Pengurus Cabang di daerah.
            </p>
          </div>
          <div class="flex flex-wrap items-center gap-2 shrink-0">
            <button type="button" onclick="launchKemenkesPortal()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-sm transition flex items-center gap-1.5 border border-emerald-400/40 cursor-pointer" title="Otomatis catat jam kerja/sesi Kemenkes ke log kinerja admin">
              <span class="material-symbols-outlined text-base">open_in_new</span>
              <span>Akses Portal Kemenkes</span>
            </button>
          </div>
        </div>

        <!-- 4 Primary Metric Cards (Sesuai Dashboard Asli Klien) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
          <!-- Total Doctors -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
              <span class="text-xs font-bold uppercase tracking-wider">Total Doctors</span>
              <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <span class="material-symbols-outlined text-lg">group</span>
              </div>
            </div>
            <div id="metricTotalDoctors" class="text-2xl sm:text-3xl font-black text-slate-900">{{ $summary->totalDoctors }}</div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
              <span class="text-emerald-700 font-bold">100% Sp.OG</span> terdaftar aktif
            </div>
          </div>

          <!-- Average Progress Q4 2026 -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
              <span class="text-xs font-bold uppercase tracking-wider">Average Progress Q4</span>
              <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#006948] flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">trending_up</span>
              </div>
            </div>
            <div id="metricAvgProgress" class="text-2xl sm:text-3xl font-black text-emerald-800">{{ $summary->avgProgressPct }}%</div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
              <div id="metricAvgProgressBar" class="bg-[#006948] h-2 rounded-full" style="width: {{ min(100, $summary->avgProgressPct) }}%"></div>
            </div>
            <div class="text-[10px] text-slate-500 mt-1">Target Kuartal: {{ number_format($summary->targetTotal / 20, 1) }} SKP / dokter</div>
          </div>

          <!-- Complete -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
              <span class="text-xs font-bold uppercase tracking-wider">Complete</span>
              <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">check_circle</span>
              </div>
            </div>
            <div id="metricComplete" class="text-2xl sm:text-3xl font-black text-emerald-700">{{ $summary->completeCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
              <span class="font-bold text-emerald-800">{{ $summary->completePercentage() }}%</span> telah penuhi target periode
            </div>
          </div>

          <!-- Incomplete (Needs Follow-Up) -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
              <span class="text-xs font-bold uppercase tracking-wider">Incomplete</span>
              <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">warning</span>
              </div>
            </div>
            <div id="metricIncomplete" class="text-2xl sm:text-3xl font-black text-amber-600">{{ $summary->incompleteCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
              <span class="font-bold text-amber-800">{{ $summary->incompletePercentage() }}%</span> butuh follow-up ke RS/Cabang
            </div>
          </div>
        </div>

        <!-- 3 Subbidang Mandatory Progress Breakdown -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
              <h2 class="text-base font-extrabold text-slate-900">Standarisasi 3 Subbidang SKP POGI (Total {{ (int) $summary->targetTotal }} SKP / 5 Tahun)</h2>
              <p class="text-xs text-slate-500">Kewajiban pemenuhan kredit dokter Obgyn harus proporsional di seluruh 3 subbidang wajib.</p>
            </div>
            <span class="text-xs font-bold text-[#006948] bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
              Rata-rata Akumulasi: {{ number_format($summary->avgAccumulationPct, 1) }}%
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Pelayanan -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-sky-600 text-lg">medical_services</span>
                  <span class="font-extrabold text-xs text-slate-900">1. Pelayanan</span>
                </div>
                <span class="text-[11px] font-bold text-sky-800 bg-sky-100 px-2 py-0.5 rounded">Min {{ (int) $summary->targetPelayanan }} SKP</span>
              </div>
              <div class="text-xl font-black text-slate-900">{{ number_format($summary->avgPelayananPct, 1) }}% <span class="text-xs font-normal text-slate-500">(rata-rata)</span></div>
              <div class="w-full bg-slate-200 rounded-full h-2 mt-2 overflow-hidden">
                <div class="progress-bar-pelayanan h-2 rounded-full" style="width: {{ min(100, $summary->avgPelayananPct) }}%"></div>
              </div>
              <div class="text-[10px] text-slate-500 mt-2">Praktik rawat inap/jalan, bedah sectio caesarea, jaga IGD maternal</div>
            </div>

            <!-- Pengabdian -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-amber-600 text-lg">volunteer_activism</span>
                  <span class="font-extrabold text-xs text-slate-900">2. Pengabdian</span>
                </div>
                <span class="text-[11px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">Min {{ (int) $summary->targetPengabdian }} SKP</span>
              </div>
              <div class="text-xl font-black text-slate-900">{{ number_format($summary->avgPengabdianPct, 1) }}% <span class="text-xs font-normal text-slate-500">(rata-rata)</span></div>
              <div class="w-full bg-slate-200 rounded-full h-2 mt-2 overflow-hidden">
                <div class="progress-bar-pengabdian h-2 rounded-full" style="width: {{ min(100, $summary->avgPengabdianPct) }}%"></div>
              </div>
              <div class="text-[10px] text-slate-500 mt-2">Bakti sosial IVA, skrining ca serviks masal, edukasi reproduksi</div>
            </div>

            <!-- Pembelajaran -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-emerald-600 text-lg">school</span>
                  <span class="font-extrabold text-xs text-slate-900">3. Pembelajaran</span>
                </div>
                <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">Min {{ (int) $summary->targetPembelajaran }} SKP</span>
              </div>
              <div class="text-xl font-black text-slate-900">{{ number_format($summary->avgPembelajaranPct, 1) }}% <span class="text-xs font-normal text-slate-500">(rata-rata)</span></div>
              <div class="w-full bg-slate-200 rounded-full h-2 mt-2 overflow-hidden">
                <div class="progress-bar-pembelajaran h-2 rounded-full" style="width: {{ min(100, $summary->avgPembelajaranPct) }}%"></div>
              </div>
              <div class="text-[10px] text-slate-500 mt-2">PIT POGI, simposium ilmiah, workshop USG terakreditasi</div>
            </div>
          </div>
        </div>

        <!-- Completion by province Card (Lowest & Highest - Persis Screenshot Klien) -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-5">
          <!-- Card Header & Toggle -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
              <h2 class="text-base font-extrabold text-slate-900">Completion by province</h2>
              <p class="text-xs text-slate-500 mt-0.5">Complete for Q4 2026</p>
            </div>
            <!-- Toggle Metric: Percentage vs Number of doctors -->
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl text-xs border border-slate-200/80 self-start sm:self-auto">
              <button id="toggleMetricPct" type="button" onclick="setCompletionMetric('percentage')" class="px-3.5 py-1.5 rounded-lg bg-white text-slate-900 font-extrabold shadow-2xs transition">
                Percentage
              </button>
              <button id="toggleMetricNum" type="button" onclick="setCompletionMetric('number')" class="px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 font-bold transition">
                Number of doctors
              </button>
            </div>
          </div>

          <!-- Two Columns: Lowest & Highest -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
            <!-- Left Column: Lowest -->
            <div class="space-y-4">
              <div class="flex items-center justify-between text-xs font-bold text-slate-500 pb-1">
                <div class="flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-base text-slate-400">trending_down</span>
                  <span class="font-bold text-slate-600">Lowest</span>
                </div>
                <button type="button" onclick="openFollowUpModal('lowest')" class="text-[11px] text-red-600 hover:text-red-700 font-bold flex items-center gap-1 hover:underline rbac-operational">
                  <span class="material-symbols-outlined text-xs">notifications_active</span>
                  <span>Follow-Up 5 Terendah</span>
                </button>
              </div>

              @foreach($summary->lowestProvinces as $prov)
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="font-extrabold text-slate-800">{{ $prov['name'] }}</span>
                    <div class="flex items-center gap-3">
                      <span class="text-slate-400 font-medium text-[11px] stat-count">{{ $prov['complete'] }} / {{ $prov['total'] }} complete</span>
                      <span class="font-black text-slate-900 w-10 text-right stat-pct">{{ $prov['pct'] }}%</span>
                    </div>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-slate-300 h-1.5 rounded-full" style="width: {{ min(100, $prov['pct']) }}%"></div>
                  </div>
                </div>
              @endforeach
            </div>

            <!-- Right Column: Highest -->
            <div class="space-y-4">
              <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500 pb-1">
                <span class="material-symbols-outlined text-base text-[#006948]">trending_up</span>
                <span class="font-bold text-slate-600">Highest</span>
              </div>

              @foreach($summary->highestProvinces as $prov)
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between text-xs">
                    <span class="font-extrabold text-slate-800">{{ $prov['name'] }}</span>
                    <div class="flex items-center gap-3">
                      <span class="text-slate-400 font-medium text-[11px] stat-count">{{ $prov['complete'] }} / {{ $prov['total'] }} complete</span>
                      <span class="font-black text-slate-900 w-10 text-right stat-pct">{{ $prov['pct'] }}%</span>
                    </div>
                  </div>
                  <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-[#006948] h-1.5 rounded-full" style="width: {{ min(100, $prov['pct']) }}%"></div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <!-- Card Footer Link -->
          <div class="flex justify-end pt-3 border-t border-slate-100">
            <a href="/provinces" class="inline-flex items-center gap-1 text-xs font-extrabold text-slate-700 hover:text-[#006948] transition">
              <span>View all provinces</span>
              <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
          </div>
        </div>

        <!-- Doctors by province Map Card (Persis Screenshot Klien) -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#006948] text-xl">map</span>
                <h2 class="text-base font-extrabold text-slate-900">Doctors by province</h2>
              </div>
              <p id="mapSubTitle" class="text-xs text-slate-500 mt-0.5">{{ $summary->totalDoctors }} doctors in 38 provinces.</p>
            </div>
            <a href="/provinces" class="text-xs font-bold text-[#006948] hover:underline flex items-center gap-1">
              <span>Monitoring 38 Cabang</span>
              <span class="material-symbols-outlined text-sm">open_in_new</span>
            </a>
          </div>

          <!-- Map & Top Provinces Layout -->
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            <!-- Left/Center Interactive Leaflet Vector Map Visualization -->
            <div class="lg:col-span-8 flex flex-col p-3 rounded-2xl bg-slate-50/60 border border-slate-100 relative">
              <div id="interactiveIndonesiaMap" class="w-full h-72 sm:h-80 md:h-96 rounded-xl bg-transparent"></div>

              <!-- Map Legend (Sesuai Permintaan Pengguna: Merah, Hijau, Kuning) -->
              <div class="pt-3 border-t border-slate-200/80 mt-3 flex flex-wrap items-center justify-between text-[11px] text-slate-600 gap-3">
                <div class="flex flex-wrap items-center gap-3">
                  <div class="flex items-center gap-1.5 font-semibold">
                    <span class="w-3.5 h-3.5 rounded-full bg-[#dc2626] inline-block shadow-2xs"></span>
                    <span class="text-slate-800 font-extrabold">Merah:</span>
                    <span>Sangat Kurang (&lt;50%)</span>
                  </div>
                  <span class="text-slate-300">|</span>
                  <div class="flex items-center gap-1.5 font-semibold">
                    <span class="w-3.5 h-3.5 rounded-full bg-[#16a34a] inline-block shadow-2xs"></span>
                    <span class="text-slate-800 font-extrabold">Hijau:</span>
                    <span>Hampir Memenuhi (50% — 84%)</span>
                  </div>
                  <span class="text-slate-300">|</span>
                  <div class="flex items-center gap-1.5 font-semibold">
                    <span class="w-3.5 h-3.5 rounded-full bg-[#eab308] inline-block shadow-2xs"></span>
                    <span class="text-slate-800 font-extrabold">Kuning:</span>
                    <span>Terpenuhi (&ge;85%)</span>
                  </div>
                  <span class="text-slate-300">|</span>
                  <div class="flex items-center gap-1.5 text-slate-500">
                    <span class="w-3 h-3 rounded-full bg-[#94a3b8] inline-block shadow-2xs"></span>
                    <span>Tidak Ada Dokter</span>
                  </div>
                </div>
                <div class="text-[10px] text-slate-400 font-semibold">
                  POGI 38 Cabang Provinsi
                </div>
              </div>
            </div>

            <!-- Right Ranked List: Top provinces -->
            <div class="lg:col-span-4 space-y-2 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6">
              <div class="text-xs font-black uppercase tracking-wider text-slate-700 pb-1">
                Top provinces
              </div>
              <div id="topProvincesList" class="space-y-1.5 text-xs font-medium">
                @forelse($summary->topProvinces as $item)
                  @php
                    $rate = $item['completionRate'] ?? 0;
                    $dotColor = 'bg-[#dc2626]';
                    $badgeColor = 'bg-red-50 text-red-700 border-red-200';
                    if ($rate >= 85) {
                      $dotColor = 'bg-[#eab308]';
                      $badgeColor = 'bg-amber-50 text-amber-800 border-amber-200';
                    } elseif ($rate >= 50) {
                      $dotColor = 'bg-[#16a34a]';
                      $badgeColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                    }
                  @endphp
                  <div class="flex items-center justify-between py-1.5 px-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer border border-transparent hover:border-slate-200"
                       onmouseenter="highlightMapProvince('{{ addslashes($item['name']) }}')"
                       onmouseleave="resetMapProvince('{{ addslashes($item['name']) }}')"
                       onclick="window.location.href='{{ url('/doctors') }}?province={{ urlencode($item['name']) }}'">
                    <div class="flex items-center gap-2 min-w-0">
                      <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }} shrink-0 shadow-2xs"></span>
                      <span class="font-bold text-slate-800 truncate text-xs">{{ $item['name'] }}</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                      <span class="text-[10px] text-slate-400 font-semibold">{{ $item['doctors'] }} Dokter</span>
                      <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold border {{ $badgeColor }}">
                        {{ number_format($rate, 0) }}%
                      </span>
                    </div>
                  </div>
                @empty
                  <div class="text-xs text-slate-400 py-2">Belum ada data cabang provinsi.</div>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Activity Log Teaser -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
              <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#006948] text-lg">update</span>
                Aktivitas Verifikasi &amp; Pengelolaan Data Terbaru
              </h3>
              <p class="text-[11px] text-slate-500">Pencatatan real-time verifikasi kredit oleh tim data pusat &amp; cabang</p>
            </div>
            <a href="/activity" class="text-xs font-bold text-[#006948] hover:underline">Semua Log Aktivitas &rarr;</a>
          </div>

          <div id="recentActivityList" class="space-y-2.5">
            @forelse($summary->recentActivities as $act)
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5 min-w-0">
                  <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#006948] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-base">task_alt</span>
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-900">{{ $act['action'] }} &bull; <span class="text-slate-500 font-normal">{{ $act['actor'] }}</span></div>
                    <div class="text-[11px] text-slate-600 truncate mt-0.5">{{ $act['details'] }}</div>
                  </div>
                </div>
                <div class="text-[10px] text-slate-400 font-medium shrink-0">{{ $act['timestamp'] }}</div>
              </div>
            @empty
              <div class="p-3 text-center text-xs text-slate-400">Belum ada aktivitas verifikasi tercatat.</div>
            @endforelse
          </div>
        </div>

  <!-- Modal Follow-Up Massal -->
  <div id="fuModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2.5">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#006948] flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">forward_to_inbox</span>
          </div>
          <div>
            <h3 class="font-black text-slate-900 text-base">Kirim Follow-Up (FU) Pemenuhan SKP</h3>
            <p class="text-xs text-slate-500">Notifikasi otomatis ke Rumah Sakit Jejaring &amp; Admin Cabang POGI</p>
          </div>
        </div>
        <button onclick="closeFollowUpModal()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      <div class="space-y-3 text-xs">
        <div>
          <label class="font-bold text-slate-700 block mb-1">Target Penerima Follow-Up:</label>
          <select id="fuTarget" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold">
            <option value="all_incomplete">Semua {{ $summary->incompleteCount }} Dokter Incomplete ({{ $summary->activeScopeLabel }})</option>
            @if(count($summary->lowestProvinces) > 0)
              <option value="lowest_5">5 Cabang Kepatuhan Terendah ({{ collect($summary->lowestProvinces)->pluck('name')->implode(', ') }})</option>
            @endif
            @foreach(collect($summary->topProvinces)->take(4) as $tp)
              <option value="{{ \Illuminate\Support\Str::slug($tp['name']) }}">Khusus Cabang {{ $tp['name'] }} ({{ $tp['doctors'] }} Dokter Terdata)</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="font-bold text-slate-700 block mb-1">Kanal Pengiriman Notifikasi:</label>
          <div class="grid grid-cols-2 gap-2">
            <label class="p-2.5 rounded-xl border border-emerald-300 bg-emerald-50/50 flex items-center gap-2 cursor-pointer">
              <input type="checkbox" checked class="rounded text-[#006948]" />
              <span class="font-bold text-slate-800">WhatsApp Gateway</span>
            </label>
            <label class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center gap-2 cursor-pointer">
              <input type="checkbox" checked class="rounded text-[#006948]" />
              <span class="font-bold text-slate-800">Email Resmi Cabang</span>
            </label>
          </div>
        </div>

        <div>
          <label class="font-bold text-slate-700 block mb-1">Pesan Pengingat Standar POGI:</label>
          <textarea rows="3" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs">Yth. Pengurus Cabang POGI & Direksi RS Jejaring. Berdasarkan pemantauan Q4 2026, mohon bantu kelengkapan pengajuan logbook Pelayanan, Pengabdian, dan Pembelajaran dokter Obgyn agar memenuhi standar 160 SKP.</textarea>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="closeFollowUpModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">Batal</button>
        <button type="button" onclick="sendFollowUpAction()" class="px-4 py-2.5 rounded-xl bg-[#006948] hover:bg-[#005439] text-white text-xs font-extrabold shadow-sm transition flex items-center gap-1.5 rbac-operational">
          <span class="material-symbols-outlined text-sm">send</span>
          <span>Kirimkan Notifikasi FU Sekarang</span>
        </button>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('assets/data/indonesia-38-provinces.js') }}"></script>
  <script src="{{ asset('js/pdf_exporter.js') }}"></script>
  <script>
    const POGI_SERVER_DATA = @json($summary->toArray());
    let leafletMap = null;
    let geoJsonLayer = null;
    const provinceLayerMap = {};

    document.addEventListener('DOMContentLoaded', () => {
      initRecentActivities();
      initTopProvincesList();
      initInteractiveIndonesiaMap();

      const currentUser = (typeof PogiStore !== 'undefined') ? PogiStore.getCurrentUser() : null;
      if (currentUser && currentUser.role === 'ADMIN_CABANG' && currentUser.branch && currentUser.branch !== 'Nasional') {
        const bLow = currentUser.branch.toLowerCase();
        let sKey = 'jabar';
        if (bLow.includes('dki') || bLow.includes('jakarta')) sKey = 'dki';
        else if (bLow.includes('timur')) sKey = 'jatim';
        const orgBtn = document.getElementById('headerOrgScopeBtn');
        if (orgBtn) orgBtn.setAttribute('disabled', 'true');
      }
    });

    function initRecentActivities() {
      const container = document.getElementById('recentActivityList');
      if (!container || container.children.length > 0) return; // Already rendered server-side

      const acts = (POGI_SERVER_DATA && POGI_SERVER_DATA.recentActivities && POGI_SERVER_DATA.recentActivities.length)
        ? POGI_SERVER_DATA.recentActivities.slice(0, 3)
        : [];
      if (acts.length > 0) {
        container.innerHTML = acts.map(a => `
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5 min-w-0">
              <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#006948] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base">task_alt</span>
              </div>
              <div class="min-w-0">
                <div class="font-bold text-slate-900">${a.action} &bull; <span class="text-slate-500 font-normal">${a.actor}</span></div>
                <div class="text-[11px] text-slate-600 truncate mt-0.5">${a.details}</div>
              </div>
            </div>
            <div class="text-[10px] text-slate-400 font-medium shrink-0">${a.timestamp}</div>
          </div>
        `).join('');
      }
    }

    function initTopProvincesList() {
      const container = document.getElementById('topProvincesList');
      if (!container || container.children.length > 0) return; // Already rendered server-side

      const topList = (POGI_SERVER_DATA && POGI_SERVER_DATA.topProvinces && POGI_SERVER_DATA.topProvinces.length)
        ? POGI_SERVER_DATA.topProvinces
        : [];

      container.innerHTML = topList.map(item => {
        const rate = item.completionRate || 0;
        let dotColor = 'bg-[#dc2626]';
        let badgeColor = 'bg-red-50 text-red-700 border-red-200';

        if (rate >= 85) {
          dotColor = 'bg-[#eab308]';
          badgeColor = 'bg-amber-50 text-amber-800 border-amber-200';
        } else if (rate >= 50) {
          dotColor = 'bg-[#16a34a]';
          badgeColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
        }

        return `
          <div class="flex items-center justify-between py-1.5 px-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer border border-transparent hover:border-slate-200"
               onmouseenter="highlightMapProvince('${item.name}')"
               onmouseleave="resetMapProvince('${item.name}')"
               onclick="window.location.href='{{ url('/doctors') }}?province=${encodeURIComponent(item.name)}'">
            <div class="flex items-center gap-2 min-w-0">
              <span class="w-2.5 h-2.5 rounded-full ${dotColor} shrink-0 shadow-2xs"></span>
              <span class="font-bold text-slate-800 truncate text-xs">${item.name}</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <span class="text-[10px] text-slate-400 font-semibold">${item.doctors} Dokter</span>
              <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold border ${badgeColor}">
                ${rate.toFixed(0)}%
              </span>
            </div>
          </div>
        `;
      }).join('');
    }

    function initInteractiveIndonesiaMap() {
      const mapContainer = document.getElementById('interactiveIndonesiaMap');
      if (!mapContainer || typeof L === 'undefined' || typeof INDONESIA_38_GEOJSON === 'undefined') return;

      leafletMap = L.map('interactiveIndonesiaMap', {
        center: [-2.5, 118.0],
        zoom: 4.8,
        minZoom: 4,
        maxZoom: 7,
        zoomControl: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        dragging: true,
        attributionControl: false
      });

      const pogiProvinces = (POGI_SERVER_DATA && POGI_SERVER_DATA.provincesData && POGI_SERVER_DATA.provincesData.length)
        ? POGI_SERVER_DATA.provincesData
        : [];

      function getProvinceStats(featureProps) {
        if (!featureProps) return null;
        const pId = String(featureProps.id || featureProps.KODE_PROV || '');
        const provName = featureProps.PROVINSI || featureProps.name || '';
        const norm = provName.toLowerCase().replace(/^(provinsi|daerah khusus ibukota|daerah istimewa)\s+/i, '').trim();
        return pogiProvinces.find(p => {
          if (pId && String(p.id) === pId) return true;
          const pNorm = (p.name || '').toLowerCase().replace(/^(provinsi|daerah khusus ibukota|daerah istimewa)\s+/i, '').trim();
          return pNorm === norm || (p.name && p.name.toLowerCase().includes(norm)) || (norm && norm.includes((p.name || '').toLowerCase()));
        });
      }

      function getProvinceColor(stat) {
        if (!stat || stat.doctors === 0) return '#94a3b8'; // Abu-abu: Tidak ada dokter
        const rate = stat.completionRate;
        if (rate >= 85) return '#eab308'; // Kuning (Terpenuhi: >= 85%) - Bab 4 RULES.md
        if (rate >= 50) return '#16a34a'; // Hijau (Hampir Memenuhi: 50% - 84%) - Bab 4 RULES.md
        return '#dc2626';                 // Merah (Sangat Kurang: < 50%) - Bab 4 RULES.md
      }

      geoJsonLayer = L.geoJSON(INDONESIA_38_GEOJSON, {
        style: function(feature) {
          const stat = getProvinceStats(feature.properties);
          const fillColor = getProvinceColor(stat);
          return {
            fillColor: fillColor,
            weight: 1,
            opacity: 1,
            color: '#ffffff',
            fillOpacity: 0.95
          };
        },
        onEachFeature: function(feature, layer) {
          const provName = feature.properties.PROVINSI || feature.properties.name || '';
          const stat = getProvinceStats(feature.properties);
          provinceLayerMap[provName.toLowerCase()] = layer;

          const docText = stat ? `${stat.doctors} Dokter Obgyn` : '0 Dokter Terdata';
          const completeText = stat ? `${stat.complete} / ${stat.doctors} Selesai (${stat.completionRate.toFixed(0)}%)` : '0%';
          const incompleteCount = stat ? stat.incomplete : 0;

          let statusBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-red-900/90 text-red-200 border border-red-500/50">● Sangat Kurang</span>';
          if (!stat || stat.doctors === 0) {
            statusBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-800 text-slate-300 border border-slate-600">0 Dokter</span>';
          } else if (stat.completionRate >= 85) {
            statusBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-900/90 text-amber-200 border border-amber-500/50">● Terpenuhi</span>';
          } else if (stat.completionRate >= 50) {
            statusBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-900/90 text-emerald-200 border border-emerald-500/50">● Hampir Memenuhi</span>';
          }

          layer.bindTooltip(`
            <div class="text-left font-sans p-1">
              <div class="flex items-center justify-between gap-3 mb-1">
                <div class="font-black text-white text-xs">${provName}</div>
                ${statusBadge}
              </div>
              <div class="text-[11px] text-slate-200 font-bold">${docText}</div>
              <div class="text-[10px] text-slate-300 mt-0.5">Target SKP Terpenuhi: <strong class="text-white">${completeText}</strong></div>
              <div class="text-[10px] text-rose-300 font-semibold mt-0.5">Belum Melengkapi SKP: ${incompleteCount} Dokter</div>
            </div>
          `, { sticky: true });

          layer.on({
            mouseover: function(e) {
              const l = e.target;
              l.setStyle({ weight: 2.5, color: '#006948', fillOpacity: 1 });
              l.bringToFront();
            },
            mouseout: function(e) {
              geoJsonLayer.resetStyle(e.target);
            },
            click: function() {
              window.location.href = '{{ url('/provinces') }}';
            }
          });
        }
      }).addTo(leafletMap);

      // Fit map bounds neatly to Indonesia bounds
      leafletMap.fitBounds(geoJsonLayer.getBounds(), { padding: [10, 10] });
    }

    function highlightMapProvince(name) {
      if (!provinceLayerMap) return;
      const key = name.toLowerCase();
      for (const k in provinceLayerMap) {
        if (k.includes(key) || key.includes(k)) {
          const layer = provinceLayerMap[k];
          layer.setStyle({ weight: 3, color: '#006948', fillOpacity: 1 });
          layer.bringToFront();
        }
      }
    }

    function resetMapProvince(name) {
      if (!geoJsonLayer || !provinceLayerMap) return;
      const key = name.toLowerCase();
      for (const k in provinceLayerMap) {
        if (k.includes(key) || key.includes(k)) {
          geoJsonLayer.resetStyle(provinceLayerMap[k]);
        }
      }
    }

    function openFollowUpModal(mode) {
      const m = document.getElementById('fuModal');
      if (m) {
        m.classList.remove('hidden');
        if (mode === 'lowest') {
          document.getElementById('fuTarget').value = 'lowest_5';
        }
      }
    }

    function closeFollowUpModal() {
      const m = document.getElementById('fuModal');
      if (m) m.classList.add('hidden');
    }

    function sendFollowUpAction() {
      const target = document.getElementById('fuTarget').selectedOptions[0].text;
      if (typeof PogiStore !== 'undefined') {
        PogiStore.addActivity({
          actor: '{{ Auth::user()?->full_name ?? "Admin" }} ({{ Auth::user()?->role ?? "Pusat" }})',
          action: 'Kirim Notifikasi FU Massal',
          details: 'Notifikasi pengingat kelengkapan SKP terkirim ke target: ' + target,
          category: 'FollowUp'
        });
      }
      closeFollowUpModal();
      showToast('Pesan Follow-Up Berhasil Dikirimkan ke ' + target, 'success');
    }

    function syncSatuSehat() {
      showToast('Menghubungkan ke API Plataran Sehat...', 'info');
      setTimeout(() => {
        if (typeof PogiStore !== 'undefined') {
          PogiStore.addActivity({
            actor: '{{ Auth::user()?->full_name ?? "System" }}',
            action: 'Sinkronisasi SATUSEHAT',
            details: 'Berhasil menyinkronkan data kredit logbook dokter terbaru.',
            category: 'Sync'
          });
        }
        showToast('Sinkronisasi Sukses: Kredit SKP Baru Diperbarui!', 'success');
      }, 1200);
    }

    function launchKemenkesPortal(doctorId, doctorName) {
      showToast('Membuka Portal Kemenkes & mencatat riwayat kerja...', 'info');

      fetch('{{ route("kemenkes.track") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          doctor_id: doctorId || null,
          doctor_name: doctorName || null,
          source: 'dashboard_hero_cta'
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          showToast('Sesi Kemenkes tercatat otomatis ke log kinerja admin!', 'success');
          window.open(data.portal_url || 'https://satusehat.kemkes.go.id', '_blank');
        } else {
          window.open('https://satusehat.kemkes.go.id', '_blank');
        }
      })
      .catch(() => {
        window.open('https://satusehat.kemkes.go.id', '_blank');
      });
    }

    function selectOrgScope(scope, labelText) {
      if (scope === 'all') {
        window.location.href = '/dashboard';
      } else {
        window.location.href = '/dashboard?scope=' + encodeURIComponent(scope);
      }
    }

    function setCompletionMetric(mode) {
      const btnPct = document.getElementById('toggleMetricPct');
      const btnNum = document.getElementById('toggleMetricNum');
      if (!btnPct || !btnNum) return;

      if (mode === 'percentage') {
        btnPct.className = 'px-3.5 py-1.5 rounded-lg bg-white text-slate-900 font-extrabold shadow-2xs transition';
        btnNum.className = 'px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 font-bold transition';
        document.querySelectorAll('.stat-pct').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.stat-count').forEach(el => {
          el.classList.remove('font-extrabold', 'text-slate-900');
          el.classList.add('font-medium', 'text-slate-400');
        });
      } else {
        btnNum.className = 'px-3.5 py-1.5 rounded-lg bg-white text-slate-900 font-extrabold shadow-2xs transition';
        btnPct.className = 'px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 font-bold transition';
        document.querySelectorAll('.stat-pct').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.stat-count').forEach(el => {
          el.classList.remove('font-medium', 'text-slate-400');
          el.classList.add('font-extrabold', 'text-slate-900');
        });
      }
    }
  </script>
@endsection
