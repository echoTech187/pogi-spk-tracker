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
    <aside id="appSidebar"
      class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] lg:w-64 bg-white border-r border-slate-200/90 flex flex-col shrink-0 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-auto shadow-2xl lg:shadow-none print:hidden">
      <div class="px-4 py-4 border-b border-slate-100 flex items-center justify-between gap-2">
        <a href="/dashboard" class="flex items-center gap-2.5 min-w-0">
          <div
            class="w-10 h-10 rounded-xl bg-white border border-slate-200 p-1 flex items-center justify-center shadow-xs shrink-0">
            <img src="/assets/img/pogi_logo.jpg" alt="Logo POGI" class="w-full h-full object-contain">
          </div>
          <div class="min-w-0">
            <div class="font-black text-sm tracking-tight text-[#006948] leading-none">POGI SKP TRACKER</div>
            <div class="text-[9px] text-slate-500 font-bold leading-tight mt-1 truncate">PP POGI &bull; Standar 160 SKP
            </div>
          </div>
        </a>
        <button type="button" onclick="toggleMobileSidebar(false)"
          class="lg:hidden p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      <div class="px-3 py-4 flex-1 overflow-y-auto space-y-6">
        <div>
          <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
            MONITORING NASIONAL
          </div>
          <nav class="space-y-1">
            <a href="/dashboard"
              class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">dashboard</span>
              <span>Dashboard</span>
            </a>
            <a href="/doctors"
              class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">stethoscope</span>
              <span>Doctors</span>
            </a>
            <a href="/provinces"
              class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">map</span>
              <span>Provinces</span>
            </a>
            <a href="/events"
              class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">event</span>
              <span>Events</span>
            </a>
            <a href="/activity"
              class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">history</span>
              <span>Activity Log</span>
            </a>
            <a href="/reports"
              class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition bg-[#006948] text-white shadow-xs">
              <span class="material-symbols-outlined text-[19px]">bar_chart</span>
              <span>Report</span>
            </a>
            <a href="/admin-performance" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">speed</span>
              <span>Admin Performance</span>
            </a>
          </nav>
        </div>

        <div>
          <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
            PENGATURAN DATA (MANAGE)
          </div>
          <nav class="space-y-1">
            <a href="/manage?tab=doctor"
              class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">person</span>
              <span>Doctor</span>
            </a>
            <a href="/manage?tab=institution"
              class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">domain</span>
              <span>Institution (RS)</span>
            </a>
            <a href="/manage?tab=event"
              class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">calendar_month</span>
              <span>SKP Event</span>
            </a>
            <a href="/manage?tab=category"
              class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">category</span>
              <span>Event Category</span>
            </a>
            <a href="/manage?tab=user"
              class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
              <span>User</span>
            </a>
          </nav>
        </div>
      </div>

      <div class="p-3 border-t border-slate-100 bg-slate-50/70">
        <div
          class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between gap-2 shadow-2xs">
          <div class="flex items-center gap-2.5 min-w-0">
            <div
              class="w-9 h-9 rounded-full bg-emerald-800 text-white font-extrabold text-xs flex items-center justify-center shrink-0">
              SR</div>
            <div class="min-w-0">
              <div class="text-xs font-bold text-slate-900 truncate">Siti Rahma, S.Farm</div>
              <div class="text-[9px] font-bold text-[#006948] uppercase tracking-wider truncate">TIM DATA PP POGI</div>
            </div>
          </div>
          <button type="button" onclick="performLogout()" title="Keluar"
            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition shrink-0">
            <span class="material-symbols-outlined text-lg">logout</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- MAIN CONSOLE WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Sticky Header -->
      <header
        class="sticky top-0 z-30 bg-white/95 backdrop-blur-xs border-b border-slate-200/90 shrink-0 w-full print:hidden">
        <div class="h-14 sm:h-16 px-4 sm:px-6 flex items-center justify-between gap-3">
          <!-- Left: Mobile menu & Scope Selector -->
          <div class="flex items-center gap-2 sm:gap-3 min-w-0 shrink-0">
            <button type="button" onclick="toggleMobileSidebar(true)"
              class="lg:hidden p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition shrink-0"
              aria-label="Buka Menu">
              <span class="material-symbols-outlined text-2xl">menu</span>
            </button>

            <!-- Scope Selector (38 Provinsi / Cabang) -->
            <div class="relative">
              <button type="button" id="headerOrgScopeBtn" onclick="toggleOrgScopeDropdown()"
                class="flex items-center gap-2 bg-slate-50 hover:bg-slate-100 transition border border-slate-200 rounded-xl px-3 py-1.5 cursor-pointer max-w-[170px] sm:max-w-[240px]">
                <span class="material-symbols-outlined text-[#006948] text-base shrink-0">public</span>
                <span id="selectedOrgScopeLabel" class="text-xs font-bold text-slate-800 truncate">Semua Wilayah
                  Nasional (38 Provinsi)</span>
                <span class="material-symbols-outlined text-sm text-slate-400 shrink-0">expand_more</span>
              </button>
              <div id="orgScopeDropdown"
                class="hidden absolute left-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50 text-xs">
                <div class="px-3.5 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cakupan
                  Pantauan</div>
                <button type="button" onclick="selectOrgScope('all', 'Semua Wilayah Nasional (38 Provinsi)')"
                  class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-slate-700 flex items-center justify-between font-semibold">
                  <span>Seluruh Indonesia</span>
                  <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-[#006948] text-[10px] font-bold">200
                    Dokter</span>
                </button>
                <button type="button" onclick="selectOrgScope('jabar', 'POGI Cabang Jawa Barat')"
                  class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-slate-700 flex items-center justify-between font-semibold">
                  <span>POGI Cabang Jawa Barat</span>
                  <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">8 Dokter</span>
                </button>
                <button type="button" onclick="selectOrgScope('dki', 'POGI Cabang DKI Jakarta')"
                  class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-slate-700 flex items-center justify-between font-semibold">
                  <span>POGI Cabang DKI Jakarta</span>
                  <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">22 Dokter</span>
                </button>
                <button type="button" onclick="selectOrgScope('jatim', 'POGI Cabang Jawa Timur')"
                  class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-slate-700 flex items-center justify-between font-semibold">
                  <span>POGI Cabang Jawa Timur</span>
                  <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">8 Dokter</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Center: Global Search Input (Inside Red Box 1) -->
          <div class="relative flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-2 sm:mx-4">
            <div class="relative flex items-center">
              <span
                class="material-symbols-outlined absolute left-3 text-slate-400 text-lg pointer-events-none">search</span>
              <input type="text" id="topGlobalSearchInput" oninput="handleGlobalTopSearch(this.value)"
                onkeydown="handleGlobalSearchKeydown(event)" placeholder="Cari dokter, nomor STR, SIP, atau ..."
                class="w-full pl-9 pr-8 py-2 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 focus:border-[#006948] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none transition shadow-2xs">
              <button type="button" id="clearTopSearchBtn" onclick="clearGlobalTopSearch()"
                class="hidden absolute right-2.5 text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer">
                <span class="material-symbols-outlined text-sm">close</span>
              </button>
            </div>
            <!-- Dropdown Live Search Results -->
            <div id="topSearchResultsDropdown"
              class="hidden absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-2 z-50 max-h-96 overflow-y-auto">
              <!-- Rendered dynamically -->
            </div>
          </div>

          <!-- Right: Notifikasi & Profil User SR -->
          <div class="flex items-center gap-2 sm:gap-3 shrink-0">


            <!-- Notification Bell with Red Dot Indicator -->
            <div class="relative">
              <button type="button" id="topNotifBtn" onclick="toggleTopNotifDropdown()"
                class="relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer"
                aria-label="Notifikasi">
                <span class="material-symbols-outlined text-xl">notifications</span>
                <span id="notifBadgeDot"
                  class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white animate-pulse"></span>
              </button>
              <!-- Notification Dropdown Menu -->
              <div id="topNotifDropdown"
                class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-3 z-50 text-xs">
                <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                  <div class="font-bold text-slate-900 flex items-center gap-1.5">
                    <span>Pemberitahuan Sistem</span>
                    <span id="notifUnreadBadge"
                      class="px-2 py-0.5 rounded-full bg-emerald-100 text-[#006948] text-[10px] font-extrabold">3
                      Baru</span>
                  </div>
                  <button type="button" onclick="markAllNotificationsAsRead()"
                    class="text-[11px] text-[#006948] hover:underline font-bold cursor-pointer">
                    Tandai Dibaca
                  </button>
                </div>
                <div id="notifListContainer" class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                  <div class="p-3.5 hover:bg-slate-50 transition flex items-start gap-3 cursor-pointer"
                    onclick="window.location.href='doctors.html?filter=pending'">
                    <div
                      class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                      <span class="material-symbols-outlined text-base">pending_actions</span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="font-bold text-slate-900 text-xs">Verifikasi Pending Kredit SKP</div>
                      <div class="text-[11px] text-slate-500 mt-0.5">64 Dokter Obgyn memiliki pending credits triwulan
                        Q4 2026 yang belum diverifikasi.</div>
                      <div class="text-[9px] text-slate-400 mt-1 font-mono">15 menit yang lalu</div>
                    </div>
                  </div>
                  <div class="p-3.5 hover:bg-slate-50 transition flex items-start gap-3 cursor-pointer"
                    onclick="syncSatuSehat()">
                    <div
                      class="w-7 h-7 rounded-lg bg-emerald-50 text-[#006948] flex items-center justify-center shrink-0 mt-0.5">
                      <span class="material-symbols-outlined text-base">cloud_sync</span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="font-bold text-slate-900 text-xs">Sinkronisasi SATUSEHAT Berhasil</div>
                      <div class="text-[11px] text-slate-500 mt-0.5">200 data STR, SIP, dan NIK dokter spesialis Obgyn
                        terverifikasi aktif dengan Kemenkes API.</div>
                      <div class="text-[9px] text-slate-400 mt-1 font-mono">1 jam yang lalu</div>
                    </div>
                  </div>
                  <div class="p-3.5 hover:bg-slate-50 transition flex items-start gap-3 cursor-pointer"
                    onclick="window.location.href='provinces.html'">
                    <div
                      class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                      <span class="material-symbols-outlined text-base">warning</span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="font-bold text-slate-900 text-xs">Peringatan Capaian Cabang Kritis</div>
                      <div class="text-[11px] text-slate-500 mt-0.5">Cabang Papua Barat Daya &amp; Maluku capaian SKP
                        masih di bawah ambang batas minimal (&lt;40%).</div>
                      <div class="text-[9px] text-slate-400 mt-1 font-mono">3 jam yang lalu</div>
                    </div>
                  </div>
                </div>
                <div class="px-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                  <a href="/activity"
                    class="text-slate-600 hover:text-[#006948] font-bold flex items-center gap-1 transition">
                    <span>Buka Log Aktivitas Lengkap</span>
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                  </a>
                </div>
              </div>
            </div>

            <!-- User Avatar Profile Circle [SR] -->
            <div class="relative">
              <button type="button" id="topUserBtn" onclick="toggleTopUserDropdown()"
                class="w-9 h-9 rounded-full bg-[#006948] hover:bg-[#005439] text-white font-black text-xs flex items-center justify-center shadow-xs transition cursor-pointer ring-2 ring-emerald-600/20"
                title="Profil Pengguna: Siti Rahma, S.Farm">
                SR
              </button>
              <!-- User Profile Dropdown Menu -->
              <div id="topUserDropdown"
                class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-3 z-50 text-xs">
                <div class="px-4 py-2 border-b border-slate-100 flex items-center gap-3">
                  <div
                    class="w-10 h-10 rounded-full bg-[#006948] text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                    SR
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-900 text-xs truncate">Siti Rahma, S.Farm</div>
                    <div class="text-[10px] text-slate-500 truncate">Tim Data &amp; Verifikasi PP POGI</div>
                    <span
                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-[#006948] border border-emerald-200 text-[9px] font-bold mt-1">
                      <span class="w-1 h-1 rounded-full bg-emerald-600"></span> Super Admin &bull; Online
                    </span>
                  </div>
                </div>
                <div class="py-1">
                  <a href="/manage"
                    class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
                    <span class="material-symbols-outlined text-base text-slate-400">admin_panel_settings</span>
                    <span>Pengelolaan Master Data</span>
                  </a>
                  <a href="/activity"
                    class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
                    <span class="material-symbols-outlined text-base text-slate-400">history</span>
                    <span>Jejak Audit &amp; Log Aktivitas</span>
                  </a>
                  <a href="/reports"
                    class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
                    <span class="material-symbols-outlined text-base text-slate-400">description</span>
                    <span>Laporan Bulanan SKP</span>
                  </a>
                </div>
                <div class="pt-1 border-t border-slate-100 px-2">
                  <button type="button" onclick="performLogout()"
                    class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 rounded-xl font-bold flex items-center gap-2 transition cursor-pointer">
                    <span class="material-symbols-outlined text-base">logout</span>
                    <span>Keluar Sistem (Logout)</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Body -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 page-fade-in print:p-0">

        <!-- HEADER LAPORAN BULANAN RESMI (ONE POGI) -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-1">
          <div class="space-y-1">
            <div class="flex items-center gap-2 flex-wrap">
              <span
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#112d4e] text-white tracking-wider uppercase shadow-2xs">
                IMPRUVE.ID &bull; MONTHLY REPORT ONE POGI
              </span>
              <span
                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#006948] text-[10px] font-extrabold">
                <span class="w-1.5 h-1.5 rounded-full bg-[#006948]"></span> Cutoff: Setiap Tgl 1 per Bulan
              </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <span>LAPORAN BULANAN</span>
              <span class="text-sm font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">(ONE POGI)</span>
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
                  class="px-3.5 py-2 font-bold text-slate-800 hover:text-[#006948] hover:bg-emerald-50/50 transition flex items-center gap-1.5 select-none whitespace-nowrap cursor-pointer">
                  <span class="material-symbols-outlined text-sm text-[#006948]">calendar_month</span>
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
                429,33</div>
              <div class="text-[11px] text-slate-500 mt-1">161 doctors &times; 2,67 credits</div>
            </div>

            <!-- Card 2: Realization -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
              <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Realization</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#006948] flex items-center justify-center">
                  <span class="material-symbols-outlined text-lg">trending_up</span>
                </div>
              </div>
              <div id="cardRealValue" class="text-2xl sm:text-3xl font-black text-emerald-800 font-mono tracking-tight">
                464,50</div>
              <div class="text-[11px] text-slate-500 mt-1"><span class="text-emerald-700 font-bold">108%</span> of
                target (melampaui)</div>
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
                142 Dokter</div>
              <div class="text-[11px] text-slate-500 mt-1"><span class="text-sky-800 font-bold">88%</span> dari 161
                dokter aktif</div>
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
                class="text-2xl sm:text-3xl font-black text-amber-600 font-mono tracking-tight">552 SKP</div>
              <div class="text-[11px] text-slate-500 mt-1">19 dokter pending kurasi Kemenkes</div>
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
                  <p class="text-xs text-slate-400 font-normal mt-0.5">Pengabdian 101% &middot; Pelayanan 148% &middot;
                    Pembelajaran 18%</p>
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
                  <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono leading-none">464,5</span>
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
                  <span class="w-1.5 h-1.5 rounded-full bg-[#006948] inline-block mr-1"></span>Status Realtime
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
    class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div
      class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden animate-scale-in">
      <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-[#006948] text-xl">calendar_month</span>
          <h3 class="font-black text-sm text-slate-900">Pilih Bulan Laporan (Flashback)</h3>
        </div>
        <button type="button" onclick="toggleMonthPickerModal(false)"
          class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 rounded-xl transition cursor-pointer">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between px-1">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tahun Laporan</span>
          <span class="text-xs font-black text-slate-900 bg-slate-100 px-3 py-1 rounded-xl">Tahun 2026</span>
        </div>

        <!-- 4x3 Grid Bulan (Persis Format BPJS) -->
        <div class="grid grid-cols-4 gap-2.5 text-xs font-bold">
          <button type="button" onclick="selectMonth(0)" id="mbtn-0"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Jan</button>
          <button type="button" onclick="selectMonth(1)" id="mbtn-1"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Feb</button>
          <button type="button" onclick="selectMonth(2)" id="mbtn-2"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Mar</button>
          <button type="button" onclick="selectMonth(3)" id="mbtn-3"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Apr</button>
          <button type="button" onclick="selectMonth(4)" id="mbtn-4"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Mei</button>
          <button type="button" onclick="selectMonth(5)" id="mbtn-5"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Jun</button>
          <button type="button" onclick="selectMonth(6)" id="mbtn-6"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Jul</button>
          <button type="button" onclick="selectMonth(7)" id="mbtn-7"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Agu</button>
          <button type="button" onclick="selectMonth(8)" id="mbtn-8"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Sep</button>
          <button type="button" onclick="selectMonth(9)" id="mbtn-9"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Okt</button>
          <button type="button" onclick="selectMonth(10)" id="mbtn-10"
            class="py-3 rounded-2xl bg-[#006948] text-white font-extrabold shadow-sm transition cursor-pointer text-center">Nov</button>
          <button type="button" onclick="selectMonth(11)" id="mbtn-11"
            class="py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center">Des</button>
        </div>

        <div class="pt-2 text-[11px] text-slate-500 border-t border-slate-100 flex items-center justify-between">
          <span>Cutoff: Setiap tgl 1</span>
          <span class="text-emerald-700 font-bold">&bull; File PDF otomatis tersedia</span>
        </div>
      </div>

      <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
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
    class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div
      class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-5xl overflow-hidden animate-scale-in">
      <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
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

      <div class="p-5 sm:p-6 max-h-[65vh] overflow-y-auto">
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
              <tr class="bg-emerald-50/50">
                <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> November 2026 (Aktif)
                </td>
                <td class="py-3.5 px-4 text-slate-600 font-medium">1 November 2026</td>
                <td class="py-3.5 px-4 text-slate-600 font-mono">20 Nov 2026, 09:14 WIB</td>
                <td class="py-3.5 px-4 text-center">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-[#006948]">Tersedia
                    (Resmi)</span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button type="button" onclick="printOnePogiMonthlyReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi Laporan Bulanan ONE POGI (3 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-[#006948]">description</span>
                      <span>Lap. Bulanan</span>
                    </button>
                    <button type="button" onclick="printOfficialSkpReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi SKP Report (1 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-sky-600">assessment</span>
                      <span>Report SKP</span>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="py-3.5 px-4 font-bold text-slate-800">Oktober 2026</td>
                <td class="py-3.5 px-4 text-slate-600">1 Oktober 2026</td>
                <td class="py-3.5 px-4 text-slate-600 font-mono">31 Okt 2026, 17:00 WIB</td>
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">Tersedia</span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button type="button" onclick="printOnePogiMonthlyReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi Laporan Bulanan ONE POGI (3 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-[#006948]">description</span>
                      <span>Lap. Bulanan</span>
                    </button>
                    <button type="button" onclick="printOfficialSkpReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi SKP Report (1 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-sky-600">assessment</span>
                      <span>Report SKP</span>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="py-3.5 px-4 font-bold text-slate-800">September 2026</td>
                <td class="py-3.5 px-4 text-slate-600">1 September 2026</td>
                <td class="py-3.5 px-4 text-slate-600 font-mono">30 Sep 2026, 17:00 WIB</td>
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">Tersedia</span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button type="button" onclick="printOnePogiMonthlyReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi Laporan Bulanan ONE POGI (3 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-[#006948]">description</span>
                      <span>Lap. Bulanan</span>
                    </button>
                    <button type="button" onclick="printOfficialSkpReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi SKP Report (1 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-sky-600">assessment</span>
                      <span>Report SKP</span>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="py-3.5 px-4 font-bold text-slate-800">Agustus 2026</td>
                <td class="py-3.5 px-4 text-slate-600">1 Agustus 2026</td>
                <td class="py-3.5 px-4 text-slate-600 font-mono">31 Agu 2026, 17:00 WIB</td>
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">Tersedia</span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button type="button" onclick="printOnePogiMonthlyReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi Laporan Bulanan ONE POGI (3 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-[#006948]">description</span>
                      <span>Lap. Bulanan</span>
                    </button>
                    <button type="button" onclick="printOfficialSkpReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi SKP Report (1 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-sky-600">assessment</span>
                      <span>Report SKP</span>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="py-3.5 px-4 font-bold text-slate-800">Juli 2026</td>
                <td class="py-3.5 px-4 text-slate-600">1 Juli 2026</td>
                <td class="py-3.5 px-4 text-slate-600 font-mono">31 Jul 2026, 17:00 WIB</td>
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">Tersedia</span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button type="button" onclick="printOnePogiMonthlyReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi Laporan Bulanan ONE POGI (3 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-[#006948]">description</span>
                      <span>Lap. Bulanan</span>
                    </button>
                    <button type="button" onclick="printOfficialSkpReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi SKP Report (1 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-sky-600">assessment</span>
                      <span>Report SKP</span>
                    </button>
                  </div>
                </td>
              </tr>
              <tr>
                <td class="py-3.5 px-4 font-bold text-slate-800">Juni 2026</td>
                <td class="py-3.5 px-4 text-slate-600">1 Juni 2026</td>
                <td class="py-3.5 px-4 text-slate-600 font-mono">30 Jun 2026, 17:00 WIB</td>
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">Tersedia</span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <button type="button" onclick="printOnePogiMonthlyReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi Laporan Bulanan ONE POGI (3 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-[#006948]">description</span>
                      <span>Lap. Bulanan</span>
                    </button>
                    <button type="button" onclick="printOfficialSkpReportPdf('Oktober 2026')"
                      title="Unduh PDF Resmi SKP Report (1 Halaman)"
                      class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                      <span class="material-symbols-outlined text-sm text-sky-600">assessment</span>
                      <span>Report SKP</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div
        class="p-4 sm:p-5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
        <div class="flex items-center gap-1.5">
          <span class="material-symbols-outlined text-sm text-[#006948]">verified</span>
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

  <script src="/js/pogi_state.js"></script>
  <script src="/js/navigation.js"></script>
  <script src="/js/pogi_template_data.js"></script>
  <script src="/js/pdf_exporter.js"></script>
  <script>
    let chartMonthInstance = null;
    let chartTypeInstance = null;
    let chartDonutInstance = null;
    let chartStatusInstance = null;

    const monthlyData = [
      { name: 'Januari 2026', short: 'Jan', target: 509.33, real: 126.0, complete: 38, pending: 24, pel: 110, peng: 6, pemb: 10, update: '31 Jan 2026, 17:00 WIB' },
      { name: 'Februari 2026', short: 'Feb', target: 496.00, real: 229.0, complete: 68, pending: 20, pel: 200, peng: 12, pemb: 17, update: '28 Feb 2026, 17:00 WIB' },
      { name: 'Maret 2026', short: 'Mar', target: 485.33, real: 1039.5, complete: 154, pending: 7, pel: 920, peng: 54, pemb: 65.5, update: '31 Mar 2026, 17:00 WIB' },
      { name: 'April 2026', short: 'Apr', target: 482.67, real: 283.5, complete: 88, pending: 18, pel: 250, peng: 15, pemb: 18.5, update: '30 Apr 2026, 17:00 WIB' },
      { name: 'Mei 2026', short: 'Mei', target: 477.33, real: 285.0, complete: 92, pending: 16, pel: 255, peng: 14, pemb: 16, update: '31 Mei 2026, 17:00 WIB' },
      { name: 'Juni 2026', short: 'Jun', target: 469.33, real: 821.5, complete: 148, pending: 12, pel: 740, peng: 42, pemb: 39.5, update: '30 Jun 2026, 17:00 WIB' },
      { name: 'Juli 2026', short: 'Jul', target: 469.33, real: 214.5, complete: 64, pending: 22, pel: 190, peng: 11, pemb: 13.5, update: '31 Jul 2026, 17:00 WIB' },
      { name: 'Agustus 2026', short: 'Agu', target: 461.33, real: 257.0, complete: 80, pending: 20, pel: 230, peng: 13, pemb: 14, update: '31 Agu 2026, 17:00 WIB' },
      { name: 'September 2026', short: 'Sep', target: 448.00, real: 923.5, complete: 152, pending: 9, pel: 830, peng: 48, pemb: 45.5, update: '30 Sep 2026, 17:00 WIB' },
      { name: 'Oktober 2026', short: 'Okt', target: 442.67, real: 493.5, complete: 138, pending: 14, pel: 440, peng: 26, pemb: 27.5, update: '31 Okt 2026, 17:00 WIB' },
      { name: 'November 2026', short: 'Nov', target: 429.33, real: 464.5, complete: 142, pending: 19, pel: 416, peng: 27, pemb: 21.5, update: '20 Nov 2026, 09:14 WIB' },
      { name: 'Desember 2026', short: 'Des', target: 420.00, real: 0, complete: 0, pending: 0, pel: 0, peng: 0, pemb: 0, update: 'Belum Cutoff (1 Des 2026)' }
    ];

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
      const data = monthlyData[idx];

      document.getElementById('currentMonthLabel').innerText = data.name;
      document.getElementById('cardTargetValue').innerText = data.target.toLocaleString('id-ID', { minimumFractionDigits: 2 });
      document.getElementById('cardRealValue').innerText = data.real.toLocaleString('id-ID', { minimumFractionDigits: 2 });
      document.getElementById('cardCompleteValue').innerText = data.complete + ' Dokter';
      document.getElementById('cardPendingValue').innerText = (data.pending * 29) + ' SKP';

      // Update Month Picker Buttons
      for (let i = 0; i < 12; i++) {
        const btn = document.getElementById('mbtn-' + i);
        if (btn) {
          if (i === idx) {
            btn.className = 'py-3 rounded-2xl bg-[#006948] text-white font-extrabold shadow-sm transition cursor-pointer text-center';
          } else {
            btn.className = 'py-3 rounded-2xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 hover:text-[#006948] transition cursor-pointer text-center';
          }
        }
      }

      if (chartTypeInstance) {
        chartTypeInstance.data.datasets[0].data = [data.peng, data.pel, data.pemb];
        chartTypeInstance.update();
      }
      if (chartDonutInstance) {
        chartDonutInstance.data.datasets[0].data = [data.pel, data.peng, data.pemb];
        chartDonutInstance.update();
      }
      if (chartStatusInstance) {
        chartStatusInstance.data.datasets[0].data = [data.peng, data.pel, data.pemb];
        chartStatusInstance.update();
      }
    }

    function initReportCharts() {
      // 1. Chart Month
      const ctxMonth = document.getElementById('chartMonth').getContext('2d');
      chartMonthInstance = new Chart(ctxMonth, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
          datasets: [
            {
              label: 'Realization',
              data: [126, 229, 1039.5, 283.5, 285, 821.5, 214.5, 257, 923.5, 493.5, 464.5, 0],
              backgroundColor: '#006948',
              hoverBackgroundColor: '#005439',
              borderRadius: 4
            },
            {
              label: 'Target',
              data: [509.33, 496, 485.33, 482.67, 477.33, 469.33, 469.33, 461.33, 448, 442.67, 429.33, 420],
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
            y: { beginAtZero: true, max: 1200, ticks: { stepSize: 300, color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { dash: [4, 4] } },
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
            { label: 'Realization', data: [27, 416, 21.5], backgroundColor: ['#d97706', '#0284c7', '#006948'], borderRadius: 4 },
            { label: 'Target', data: [26.83, 281.75, 120.75], backgroundColor: '#cbd5e1', borderRadius: 4 }
          ]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, color: '#64748b', font: { size: 11 } } } },
          scales: {
            x: { beginAtZero: true, max: 600, ticks: { stepSize: 150, color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { dash: [4, 4] } },
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
            data: [416, 27, 21.5],
            backgroundColor: ['#0284c7', '#d97706', '#006948'],
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
      const ctxStatus = document.getElementById('chartStatusHorizontal').getContext('2d');
      chartStatusInstance = new Chart(ctxStatus, {
        type: 'bar',
        data: {
          labels: ['Pengabdian', 'Pelayanan', 'Pembelajaran'],
          datasets: [
            { label: 'Done', data: [27, 416, 21.5], backgroundColor: '#006948', stack: 'status' },
            { label: 'Not submitted', data: [11, 204, 8.5], backgroundColor: '#94a3b8', stack: 'status' },
            { label: 'Submitted', data: [24.5, 499, 28.5], backgroundColor: '#0ea5e9', stack: 'status' },
            { label: 'Rejected', data: [4, 59, 5.5], backgroundColor: '#ef4444', stack: 'status' }
          ]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, pointStyle: 'rectRounded', color: '#64748b', font: { size: 10 } } } },
          scales: {
            x: { stacked: true, beginAtZero: true, max: 1200, ticks: { stepSize: 300, color: '#94a3b8', font: { size: 10 } }, grid: { color: '#f1f5f9' }, border: { dash: [4, 4] } },
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