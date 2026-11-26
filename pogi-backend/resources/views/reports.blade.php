<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f4f7f6]">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POGI SKP Tracker - Laporan &amp; Analisis Realisasi SKP</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Core Styles -->
  <link rel="stylesheet" href="/css/style.css">
</head>

<body class="h-full bg-[#f4f7f6] text-slate-800 antialiased font-sans">
  <!-- Mobile Backdrop Overlay -->
  <div id="sidebarOverlay" onclick="toggleMobileSidebar(false)"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden pointer-events-none transition-opacity lg:hidden">
  </div>

  <div class="flex h-screen overflow-hidden">
    <!-- APP SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN CONSOLE WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Sticky Header -->
      @include('layouts.header')

      <!-- Main Body -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 page-fade-in print:p-0">

        <!-- HEADER LAPORAN BULANAN RESMI (ONE POGI) -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-1">
          <div class="space-y-1">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <span>LAPORAN BULANAN</span>
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed max-w-7xl">
              Pengelolaan dan Penginputan Data Satuan Kredit Profesi (SKP) Dokter Spesialis Obstetri &amp; Ginekologi mengacu pada KMK HK.01.07/MENKES/1561/2024 &amp; KMK HK.01.07/MENKES/1111/2026. Laporan resmi berkas &amp; lampiran penagihan invoice.
            </p>
          </div>


        </div>

        <!-- Status Bar Update Admin -->
        <div
          class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-3 text-xs text-slate-500">
          <div class="flex items-center gap-2">
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
              <!-- Month Navigator (< November 2026 >) -->
              <div
                class="flex items-center bg-white border border-slate-200/90 rounded-xl shadow-2xs overflow-hidden text-xs">
                <button type="button" onclick="changeMonthStep(-1)" title="Bulan Sebelumnya"
                  class="p-2 hover:bg-slate-50 text-slate-600 border-r border-slate-100 transition flex items-center justify-center cursor-pointer">
                  <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
                <button type="button" onclick="toggleMonthPickerModal(true)"
                  class="px-3.5 py-2 font-bold text-slate-800 hover:text-[#16b3ac] hover:bg-emerald-50/50 transition flex items-center gap-1.5 select-none whitespace-nowrap cursor-pointer">
                  <span class="material-symbols-outlined text-sm text-[#16b3ac]">calendar_month</span>
                  <span id="currentMonthLabel">November 2026</span>
                  <span class="material-symbols-outlined text-xs text-slate-400">expand_more</span>
                </button>
                <button type="button" onclick="changeMonthStep(1)" title="Bulan Berikutnya"
                  class="p-2 hover:bg-slate-50 text-slate-600 border-l border-slate-100 transition flex items-center justify-center cursor-pointer">
                  <span class="material-symbols-outlined text-base">chevron_right</span>
                </button>
              </div>
            </div>
          </div>
          <div class="text-xs text-slate-400 font-medium flex items-center justify-end gap-1.5">
            <!-- Tombol Arsip Laporan Bulanan (Flashback) -->
            <button type="button" onclick="openArchiveModal(true)"
              class="px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 text-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
              <span class="material-symbols-outlined text-base text-amber-600">folder_zip</span>
              <span>Arsip Laporan</span>
            </button>

            <!-- Tombol Report SKP (Dipindahkan ke sebelah Arsip Laporan) -->
            <button type="button" onclick="downloadCurrentMonthSkpReport()"
              class="px-3.5 py-2 rounded-xl bg-sky-700 hover:bg-sky-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer"
              title="Unduh PDF Resmi SKP Report (1 Halaman)">
              <span class="material-symbols-outlined text-base">assessment</span>
              <span>Report SKP</span>
            </button>
          </div>
        </div>

        <div id="pane-dashboard" class="space-y-6">

          <!-- 4 Summary KPI Cards (Murni Bulanan) -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">

            <!-- Card 1: Target -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
              <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Target Bulan Ini</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                  <span class="material-symbols-outlined text-lg">flag</span>
                </div>
              </div>
              <div id="cardTargetValue" class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">
                0,00</div>
              <div id="cardTargetSubtitle" class="text-[11px] text-slate-500 mt-1">0 dokter &times; 2,67 credits</div>
            </div>

            <!-- Card 2: Realization -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
              <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Realization</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#16b3ac] flex items-center justify-center">
                  <span class="material-symbols-outlined text-lg">trending_up</span>
                </div>
              </div>
              <div id="cardRealValue" class="text-2xl sm:text-3xl font-black text-emerald-800 font-mono tracking-tight">
                0,00</div>
              <div id="cardRealSubtitle" class="text-[11px] text-slate-500 mt-1"><span class="text-emerald-700 font-bold">0%</span> of target</div>
            </div>

            <!-- Card 3: Dokter Capai Target -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
              <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Dokter Capai Target</span>
                <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                  <span class="material-symbols-outlined text-lg">task_alt</span>
                </div>
              </div>
              <div id="cardCompleteValue" class="text-2xl sm:text-3xl font-black text-sky-900 font-mono tracking-tight">
                0 Dokter</div>
              <div id="cardCompleteSubtitle" class="text-[11px] text-slate-500 mt-1"><span class="text-sky-800 font-bold">0%</span> dari 0 dokter aktif</div>
            </div>

            <!-- Card 4: Waiting for validation -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
              <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Waiting for validation</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                  <span class="material-symbols-outlined text-lg">hourglass_top</span>
                </div>
              </div>
              <div id="cardPendingValue"
                class="text-2xl sm:text-3xl font-black text-amber-600 font-mono tracking-tight">0 SKP</div>
              <div id="cardPendingSubtitle" class="text-[11px] text-slate-500 mt-1">0 dokter pending kurasi Kemenkes</div>
            </div>

          </div>

          <!-- 4 CHARTS GRID (2x2) - Persis Gambar 2 dengan Layout Lega & Rapih -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">

            <!-- Chart 1 (Top-Left): Target vs realization -->
            <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div>
                  <h2 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Target vs realization
                  </h2>
                  <p class="text-xs text-slate-400 font-normal mt-0.5">Total credits per month, January to November
                    2026.</p>
                </div>
                <span
                  class="text-[10px] text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full font-bold border border-emerald-200">
                  Jan - Nov 2026
                </span>
              </div>
              <div class="h-72 relative">
                <canvas id="chartMonth"></canvas>
              </div>
            </div>

            <!-- Chart 2 (Top-Right): Target vs realization by SKP type -->
            <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div>
                  <h2 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Target vs realization by
                    SKP type</h2>
                  <p id="chartTypeSubtitle" class="text-xs text-slate-400 font-normal mt-0.5">Pengabdian 0% &middot; Pelayanan 0% &middot; Pembelajaran 0%</p>
                </div>
                <div class="flex items-center gap-1.5 flex-wrap">
                  <span
                    class="flex items-center gap-1 text-sky-800 font-bold text-[10px] bg-sky-50 px-2 py-0.5 rounded border border-sky-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span> Pelayanan
                  </span>
                  <span
                    class="flex items-center gap-1 text-amber-800 font-bold text-[10px] bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Pengabdian
                  </span>
                  <span
                    class="flex items-center gap-1 text-emerald-800 font-bold text-[10px] bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Pembelajaran
                  </span>
                </div>
              </div>
              <div class="h-72 relative">
                <canvas id="chartTypeHorizontal"></canvas>
              </div>
            </div>

            <!-- Chart 3 (Bottom-Left): Realization by SKP type (Donut with Center Value) -->
            <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div>
                  <h2 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Realization by SKP type
                  </h2>
                  <p class="text-xs text-slate-400 font-normal mt-0.5">Share of Done credits this month.</p>
                </div>
                <span class="text-[10px] text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full font-bold">
                  Bulan Terpilih
                </span>
              </div>
              <div class="h-72 relative flex items-center justify-center">
                <canvas id="chartDonut"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pb-4">
                  <span id="donutCenterValue" class="text-2xl sm:text-3xl font-black text-slate-900 font-mono leading-none">0,0</span>
                  <span class="text-xs text-slate-400 font-semibold mt-1">credits</span>
                </div>
              </div>
            </div>

            <!-- Chart 4 (Bottom-Right): SKP type by status -->
            <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
              <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div>
                  <h2 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">SKP type by status</h2>
                  <p class="text-xs text-slate-400 font-normal mt-0.5">Credits of every entry on events this month, by
                    entry status.</p>
                </div>
                <span
                  class="text-[10px] text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full font-bold border border-emerald-200">
                  <span class="w-1.5 h-1.5 rounded-full bg-[#16b3ac] inline-block mr-1"></span>Status Realtime
                </span>
              </div>
              <div class="h-72 relative">
                <canvas id="chartStatusHorizontal"></canvas>
              </div>
            </div>

          </div>

        </div>


      </main>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- MODAL POPUP 1: PILIHAN BULAN (PERSIS BPJS DI CHAT WHATSAPP)               -->
  <!-- ========================================================================= -->
  <div id="monthPickerModal"
    class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div
      class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden animate-scale-in">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70 shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-[#16b3ac] text-xl">calendar_month</span>
          <h3 class="font-black text-sm text-slate-900">Pilih Bulan Laporan (Flashback)</h3>
        </div>
        <button type="button" onclick="toggleMonthPickerModal(false)"
          class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 rounded-xl transition cursor-pointer">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <div class="p-6 space-y-4 flex-1 overflow-y-auto">
        <div class="flex items-center justify-between px-1">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tahun Laporan</span>
          <span class="text-xs font-black text-slate-900 bg-slate-100 px-3 py-1 rounded-xl">Tahun 2026</span>
        </div>

        <!-- 4x3 Grid Bulan (Persis Format BPJS) -->
        <div class="grid grid-cols-4 gap-2.5 text-xs font-bold">
          <button type="button" onclick="selectMonth(0)" id="mbtn-0"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Jan</button>
          <button type="button" onclick="selectMonth(1)" id="mbtn-1"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Feb</button>
          <button type="button" onclick="selectMonth(2)" id="mbtn-2"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Mar</button>
          <button type="button" onclick="selectMonth(3)" id="mbtn-3"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Apr</button>
          <button type="button" onclick="selectMonth(4)" id="mbtn-4"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Mei</button>
          <button type="button" onclick="selectMonth(5)" id="mbtn-5"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Jun</button>
          <button type="button" onclick="selectMonth(6)" id="mbtn-6"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Jul</button>
          <button type="button" onclick="selectMonth(7)" id="mbtn-7"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Agu</button>
          <button type="button" onclick="selectMonth(8)" id="mbtn-8"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Sep</button>
          <button type="button" onclick="selectMonth(9)" id="mbtn-9"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Okt</button>
          <button type="button" onclick="selectMonth(10)" id="mbtn-10"
            class="py-3 rounded-2xl bg-[#16b3ac] text-white font-extrabold shadow-sm transition cursor-pointer text-center">Nov</button>
          <button type="button" onclick="selectMonth(11)" id="mbtn-11"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center">Des</button>
        </div>

        <div class="pt-2 text-[11px] text-slate-500 border-t border-slate-100 flex items-center justify-between">
          <span>Cutoff: Setiap tgl 1</span>
          <span class="text-emerald-700 font-bold">&bull; File PDF otomatis tersedia</span>
        </div>
      </div>

      <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end shrink-0 sticky bottom-0 z-20">
        <button type="button" onclick="toggleMonthPickerModal(false)"
          class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition cursor-pointer">
          Tutup
        </button>
      </div>
    </div>
  </div>

  <!-- ========================================================================= -->
  <!-- MODAL POPUP 2: ARSIP FLASHBACK DOKUMEN (DIRAPIKAN TOTAL - MAX-W-4XL)      -->
  <!-- ========================================================================= -->
  <div id="archiveModal"
    class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div
      class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden animate-scale-in">
      <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/80 shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shadow-2xs">
            <span class="material-symbols-outlined text-xl">folder_zip</span>
          </div>
          <div>
            <h3 class="font-black text-base text-slate-900">Arsip File Laporan Bulanan (PDF Siap Unduh)</h3>
            <p class="text-xs text-slate-500">File tersimpan per bulan untuk kebutuhan flashback &amp; lampiran
              penagihan invoice.</p>
          </div>
        </div>
        <button type="button" onclick="openArchiveModal(false)"
          class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 rounded-xl transition cursor-pointer">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      <div class="p-5 sm:p-6 flex-1 overflow-y-auto">
        <div class="overflow-x-auto rounded-2xl border border-slate-200">
          <table class="w-full text-left text-xs whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
              <tr>
                <th class="py-3 px-4 w-44">Periode Bulan</th>
                <th class="py-3 px-4 w-36">Tanggal Cutoff</th>
                <th class="py-3 px-4 w-48">Terakhir Update Admin</th>
                <th class="py-3 px-4 w-36 text-center">Status File</th>
                <th class="py-3 px-4 w-72 text-right">Aksi Laporan (Pilihan Format)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($snapshots ?? [] as $s)
                <tr class="{{ $loop->first ? 'bg-emerald-50/50' : '' }}">
                  <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                    @if($loop->first)<span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>@endif
                    Bulan {{ $s->period_month }}/{{ $s->period_year }} {{ $loop->first ? '(Aktif)' : '' }}
                  </td>
                  <td class="py-3.5 px-4 text-slate-600 font-medium">1 {{ $s->period_month }} {{ $s->period_year }}</td>
                  <td class="py-3.5 px-4 text-slate-600 font-mono">{{ \Carbon\Carbon::parse($s->cutoff_timestamp ?? $s->created_at)->format('d M Y, H:i') }} WIB</td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ ($s->is_locked ?? true) ? 'bg-emerald-100 text-[#16b3ac]' : 'bg-slate-100 text-slate-700' }}">
                      {{ ($s->is_locked ?? true) ? 'Tersedia (Resmi)' : 'Draft' }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-right">
                    <div class="inline-flex items-center gap-2">
                      <button type="button" onclick="printOnePogiMonthlyReportPdf('{{ $s->period_month }}/{{ $s->period_year }}')"
                        title="Unduh PDF Resmi Laporan Bulanan ONE POGI (3 Halaman)"
                        class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-sm text-[#16b3ac]">description</span>
                        <span>Lap. Bulanan</span>
                      </button>
                      <button type="button" onclick="printOfficialSkpReportPdf('{{ $s->period_month }}/{{ $s->period_year }}')"
                        title="Unduh PDF Resmi SKP Report (1 Halaman)"
                        class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-sm text-sky-600">assessment</span>
                        <span>Report SKP</span>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-8 px-4 text-center text-slate-400">
                    <div class="flex flex-col items-center justify-center gap-1.5 py-4">
                      <span class="material-symbols-outlined text-3xl text-slate-300">folder_off</span>
                      <span class="text-xs font-semibold text-slate-500">Belum ada arsip snapshot bulanan tersimpan.</span>
                      <span class="text-[11px] text-slate-400">Arsip otomatis terbuat secara permanen pada tanggal 1 setiap bulan saat masa cutoff.</span>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div
        class="p-4 sm:p-5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 shrink-0 sticky bottom-0 z-20">
        <div class="flex items-center gap-1.5">
          <span class="material-symbols-outlined text-sm text-[#16b3ac]">verified</span>
          <span>Sistem Penyimpanan Terpadu Impruve.Id &bull; Cutoff tgl 1</span>
        </div>
        <button type="button" onclick="openArchiveModal(false)"
          class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition cursor-pointer">
          Tutup
        </button>
      </div>
    </div>
  </div>


  <!-- jsPDF & AutoTable for Client-Side Dynamic PDF Generation -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <script>
    if (!window.jspdf) {
      document.write('<script src="/js/vendor/jspdf.umd.min.js"><\/script>');
      document.write('<script src="/js/vendor/jspdf.plugin.autotable.min.js"><\/script>');
    }
  </script>

  <script>
    window.AUTH_USER = {!! json_encode([
      'id'     => Auth::id() ?? 'auth-user',
      'name'   => Auth::user()?->full_name ?? Auth::user()?->name,
      'email'  => Auth::user()?->email ?? '',
      'role'   => Auth::user()?->role ?? 'ADMIN_PUSAT',
      'branch' => Auth::user()?->branch ?? 'Pusat'
    ]) !!};
  </script>
  <script src="/js/navigation.js"></script>
  <script src="/js/pdf_exporter.js"></script>
  <script>
    let chartMonthInstance = null;
    let chartTypeInstance = null;
    let chartDonutInstance = null;
    let chartStatusInstance = null;

    const LIVE_MONTHLY_DATA = @json($monthlyData ?? []);
    const LIVE_REPORT_PDF_CONTEXT = @json($pdfContext ?? []);
    window.LIVE_MONTHLY_DATA = LIVE_MONTHLY_DATA;
    window.LIVE_REPORT_PDF_CONTEXT = LIVE_REPORT_PDF_CONTEXT;
    const monthlyData = Array.isArray(LIVE_MONTHLY_DATA) ? LIVE_MONTHLY_DATA : [];

    let currentSelectedMonthIndex = 10; // November

    document.addEventListener('DOMContentLoaded', () => {
      initReportCharts();
      renderMonthView(10);
    });

    function toggleMonthPickerModal(show) {
      const modal = document.getElementById('monthPickerModal');
      if (modal) {
        if (show === false) modal.classList.add('hidden');
        else modal.classList.toggle('hidden');
      }
    }

    function openArchiveModal(show) {
      const modal = document.getElementById('archiveModal');
      if (modal) {
        if (show === false) modal.classList.add('hidden');
        else modal.classList.toggle('hidden');
      }
    }

    function selectMonth(idx) {
      currentSelectedMonthIndex = idx;
      renderMonthView(idx);
      toggleMonthPickerModal(false);
      if (typeof showToast === 'function') {
        showToast('Memuat laporan bulanan: ' + monthlyData[idx].name, 'info');
      }
    }

    function changeMonthStep(delta) {
      let newIdx = currentSelectedMonthIndex + delta;
      if (newIdx < 0) newIdx = 0;
      if (newIdx > 11) newIdx = 11;
      selectMonth(newIdx);
    }

    function renderMonthView(idx) {
      const data = monthlyData[idx] || {};
      const tDocs = Number(data.total_doctors || 0);
      const targetVal = Number(data.target || 0);
      const realVal = Number(data.real || 0);
      const compVal = Number(data.complete || 0);
      const pendingSkpVal = Number(data.pending_skp || 0);
      const pendingDocsVal = Number(data.pending_doctors || 0);

      const realPct = targetVal > 0 ? Math.round((realVal / targetVal) * 100) : 0;
      const compPct = tDocs > 0 ? Math.round((compVal / tDocs) * 100) : 0;

      // Update Card 1 (Target)
      const elMonthLabel = document.getElementById('currentMonthLabel');
      if (elMonthLabel) elMonthLabel.innerText = data.name || '';
      
      const elTargetVal = document.getElementById('cardTargetValue');
      if (elTargetVal) elTargetVal.innerText = targetVal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const elTargetSub = document.getElementById('cardTargetSubtitle');
      if (elTargetSub) elTargetSub.innerHTML = `${tDocs} doctors &times; 2,67 credits`;

      // Update Card 2 (Realization)
      const elRealVal = document.getElementById('cardRealValue');
      if (elRealVal) elRealVal.innerText = realVal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const elRealSub = document.getElementById('cardRealSubtitle');
      if (elRealSub) {
        if (realPct >= 100) {
          elRealSub.innerHTML = `<span class="text-emerald-700 font-bold">${realPct}%</span> of target (melampaui)`;
        } else {
          elRealSub.innerHTML = `<span class="text-slate-600 font-bold">${realPct}%</span> of target`;
        }
      }

      // Update Card 3 (Complete)
      const elCompVal = document.getElementById('cardCompleteValue');
      if (elCompVal) elCompVal.innerText = `${compVal} Dokter`;
      const elCompSub = document.getElementById('cardCompleteSubtitle');
      if (elCompSub) elCompSub.innerHTML = `<span class="text-sky-800 font-bold">${compPct}%</span> dari ${tDocs} dokter aktif`;

      // Update Card 4 (Pending)
      const elPendingVal = document.getElementById('cardPendingValue');
      if (elPendingVal) elPendingVal.innerText = `${pendingSkpVal.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })} SKP`;
      const elPendingSub = document.getElementById('cardPendingSubtitle');
      if (elPendingSub) elPendingSub.innerText = `${pendingDocsVal} dokter pending kurasi Kemenkes`;

      // Update Month Picker Buttons
      for (let i = 0; i < 12; i++) {
        const btn = document.getElementById('mbtn-' + i);
        if (btn) {
          if (i === idx) {
            btn.className = 'py-3 rounded-2xl bg-[#16b3ac] text-white font-extrabold shadow-sm transition cursor-pointer text-center';
          } else {
            btn.className = 'py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#16b3ac] transition cursor-pointer text-center';
          }
        }
      }

      // Update Chart 2 (Target vs Realization by SKP type)
      const pengVal = Number(data.peng || 0);
      const pelVal = Number(data.pel || 0);
      const pembVal = Number(data.pemb || 0);
      const targetPeng = Number(data.target_peng || 0);
      const targetPel = Number(data.target_pel || 0);
      const targetPemb = Number(data.target_pemb || 0);

      const pctPeng = targetPeng > 0 ? Math.round((pengVal / targetPeng) * 100) : 0;
      const pctPel = targetPel > 0 ? Math.round((pelVal / targetPel) * 100) : 0;
      const pctPemb = targetPemb > 0 ? Math.round((pembVal / targetPemb) * 100) : 0;

      const elChartTypeSub = document.getElementById('chartTypeSubtitle');
      if (elChartTypeSub) {
        elChartTypeSub.innerHTML = `Pengabdian ${pctPeng}% &middot; Pelayanan ${pctPel}% &middot; Pembelajaran ${pctPemb}%`;
      }

      if (chartTypeInstance) {
        chartTypeInstance.data.datasets[0].data = [pengVal, pelVal, pembVal];
        chartTypeInstance.data.datasets[1].data = [targetPeng, targetPel, targetPemb];
        chartTypeInstance.update();
      }

      // Update Chart 3 (Donut)
      const elDonutCenter = document.getElementById('donutCenterValue');
      if (elDonutCenter) {
        elDonutCenter.innerText = realVal.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
      }

      if (chartDonutInstance) {
        const totalCredits = pelVal + pengVal + pembVal;
        if (totalCredits <= 0) {
          chartDonutInstance.data.datasets[0].data = [1];
          chartDonutInstance.data.datasets[0].backgroundColor = ['#e2e8f0'];
        } else {
          chartDonutInstance.data.datasets[0].data = [pelVal, pengVal, pembVal];
          chartDonutInstance.data.datasets[0].backgroundColor = ['#0284c7', '#d97706', '#16b3ac'];
        }
        chartDonutInstance.update();
      }

      // Update Chart 4 (Stacked Status by Domain)
      if (chartStatusInstance) {
        const sb = data.status_breakdown || {
          PEMBELAJARAN: { DONE: 0, NOT_SUBMITTED: 0, SUBMITTED: 0, REJECTED: 0 },
          PELAYANAN: { DONE: 0, NOT_SUBMITTED: 0, SUBMITTED: 0, REJECTED: 0 },
          PENGABDIAN: { DONE: 0, NOT_SUBMITTED: 0, SUBMITTED: 0, REJECTED: 0 }
        };
        chartStatusInstance.data.datasets[0].data = [sb.PENGABDIAN?.DONE || 0, sb.PELAYANAN?.DONE || 0, sb.PEMBELAJARAN?.DONE || 0];
        chartStatusInstance.data.datasets[1].data = [sb.PENGABDIAN?.NOT_SUBMITTED || 0, sb.PELAYANAN?.NOT_SUBMITTED || 0, sb.PEMBELAJARAN?.NOT_SUBMITTED || 0];
        chartStatusInstance.data.datasets[2].data = [sb.PENGABDIAN?.SUBMITTED || 0, sb.PELAYANAN?.SUBMITTED || 0, sb.PEMBELAJARAN?.SUBMITTED || 0];
        chartStatusInstance.data.datasets[3].data = [sb.PENGABDIAN?.REJECTED || 0, sb.PELAYANAN?.REJECTED || 0, sb.PEMBELAJARAN?.REJECTED || 0];
        chartStatusInstance.update();
      }
    }

    function initReportCharts() {
      const activeData = monthlyData[currentSelectedMonthIndex] || monthlyData[10] || {};
      const curPeng = Number(activeData.peng || 0);
      const curPel = Number(activeData.pel || 0);
      const curPemb = Number(activeData.pemb || 0);
      const curTargetPeng = Number(activeData.target_peng || 0);
      const curTargetPel = Number(activeData.target_pel || 0);
      const curTargetPemb = Number(activeData.target_pemb || 0);
      const curTotal = curPeng + curPel + curPemb;

      // 1. Chart Month
      const ctxMonth = document.getElementById('chartMonth').getContext('2d');
      chartMonthInstance = new Chart(ctxMonth, {
        type: 'bar',
        data: {
          labels: monthlyData.map(d => d.short),
          datasets: [
            {
              label: 'Realization',
              data: monthlyData.map(d => d.real),
              backgroundColor: '#16b3ac',
              hoverBackgroundColor: '#129993',
              borderRadius: 4
            },
            {
              label: 'Target',
              data: monthlyData.map(d => d.target),
              backgroundColor: '#cbd5e1',
              borderRadius: 4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, color: '#64748b', font: { size: 11 } } }
          },
          scales: {
            y: { beginAtZero: true, suggestedMax: 10, ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { dash: [4, 4] } },
            x: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { display: false } }
          }
        }
      });

      // 2. Chart Type
      const ctxType = document.getElementById('chartTypeHorizontal').getContext('2d');
      chartTypeInstance = new Chart(ctxType, {
        type: 'bar',
        data: {
          labels: ['Pengabdian', 'Pelayanan', 'Pembelajaran'],
          datasets: [
            { label: 'Realization', data: [curPeng, curPel, curPemb], backgroundColor: ['#d97706', '#0284c7', '#16b3ac'], borderRadius: 4 },
            { label: 'Target', data: [curTargetPeng, curTargetPel, curTargetPemb], backgroundColor: '#cbd5e1', borderRadius: 4 }
          ]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, color: '#64748b', font: { size: 11 } } } },
          scales: {
            x: { beginAtZero: true, suggestedMax: 5, ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { dash: [4, 4] } },
            y: { ticks: { color: '#334155', font: { size: 11, weight: '500' } }, grid: { display: false } }
          }
        }
      });

      // 3. Chart Donut
      const ctxDonut = document.getElementById('chartDonut').getContext('2d');
      chartDonutInstance = new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
          labels: ['Pelayanan', 'Pengabdian', 'Pembelajaran'],
          datasets: [{
            data: curTotal <= 0 ? [1] : [curPel, curPeng, curPemb],
            backgroundColor: curTotal <= 0 ? ['#e2e8f0'] : ['#0284c7', '#d97706', '#16b3ac'],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          cutout: '72%',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'circle', color: '#64748b', font: { size: 11 } } }
          }
        }
      });

      // 4. Chart Status
      const sb = activeData.status_breakdown || {
        PEMBELAJARAN: { DONE: 0, NOT_SUBMITTED: 0, SUBMITTED: 0, REJECTED: 0 },
        PELAYANAN: { DONE: 0, NOT_SUBMITTED: 0, SUBMITTED: 0, REJECTED: 0 },
        PENGABDIAN: { DONE: 0, NOT_SUBMITTED: 0, SUBMITTED: 0, REJECTED: 0 }
      };
      const ctxStatus = document.getElementById('chartStatusHorizontal').getContext('2d');
      chartStatusInstance = new Chart(ctxStatus, {
        type: 'bar',
        data: {
          labels: ['Pengabdian', 'Pelayanan', 'Pembelajaran'],
          datasets: [
            { label: 'Done', data: [sb.PENGABDIAN?.DONE || 0, sb.PELAYANAN?.DONE || 0, sb.PEMBELAJARAN?.DONE || 0], backgroundColor: '#16b3ac', stack: 'status' },
            { label: 'Not submitted', data: [sb.PENGABDIAN?.NOT_SUBMITTED || 0, sb.PELAYANAN?.NOT_SUBMITTED || 0, sb.PEMBELAJARAN?.NOT_SUBMITTED || 0], backgroundColor: '#94a3b8', stack: 'status' },
            { label: 'Submitted', data: [sb.PENGABDIAN?.SUBMITTED || 0, sb.PELAYANAN?.SUBMITTED || 0, sb.PEMBELAJARAN?.SUBMITTED || 0], backgroundColor: '#0ea5e9', stack: 'status' },
            { label: 'Rejected', data: [sb.PENGABDIAN?.REJECTED || 0, sb.PELAYANAN?.REJECTED || 0, sb.PEMBELAJARAN?.REJECTED || 0], backgroundColor: '#ef4444', stack: 'status' }
          ]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'rectRounded', color: '#64748b', font: { size: 10 } } } },
          scales: {
            x: { stacked: true, beginAtZero: true, suggestedMax: 5, ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { dash: [4, 4] } },
            y: { stacked: true, ticks: { color: '#334155', font: { size: 11, weight: '500' } }, grid: { display: false } }
          }
        }
      });
    }

    function downloadReportPdf() {
      if (typeof printOnePogiMonthlyReportPdf === 'function') {
        printOnePogiMonthlyReportPdf(monthlyData[currentSelectedMonthIndex].name);
      } else {
        window.print();
      }
    }

    function downloadCurrentMonthSkpReport() {
      const currentMonth = (monthlyData && monthlyData[currentSelectedMonthIndex])
        ? monthlyData[currentSelectedMonthIndex].name
        : 'November 2026';
      if (typeof printOfficialSkpReportPdf === 'function') {
        printOfficialSkpReportPdf(currentMonth);
      }
    }
  </script>
</body>

</html>