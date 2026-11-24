<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f4f7f6]">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POGI SKP Tracker - Dashboard Pemantauan SKP Nasional</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Leaflet.js CDN for Interactive Vector Map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- Core Styles -->
  <link rel="stylesheet" href="/css/style.css">
  <style>
    #interactiveIndonesiaMap {
      background-color: transparent !important;
    }
    .leaflet-container {
      background: transparent !important;
      font-family: inherit;
    }
    .leaflet-tooltip {
      background: #0f172a !important;
      color: #ffffff !important;
      border: 1px solid rgba(255,255,255,0.2) !important;
      border-radius: 10px !important;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3) !important;
      padding: 6px 12px !important;
      font-size: 11px !important;
    }
    .leaflet-tooltip-top:before {
      border-top-color: #0f172a !important;
    }
  </style>
</head>
<body class="h-full bg-[#f4f7f6] text-slate-800 antialiased font-sans">
  <!-- Mobile Backdrop Overlay -->
  <div id="sidebarOverlay" onclick="toggleMobileSidebar(false)" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden pointer-events-none transition-opacity lg:hidden"></div>

  <div class="flex h-screen overflow-hidden">
    <!-- APP SIDEBAR -->
    <aside id="appSidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] lg:w-64 bg-white border-r border-slate-200/90 flex flex-col shrink-0 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-auto shadow-2xl lg:shadow-none">
      <!-- Top Brand Header -->
      <div class="px-4 py-4 border-b border-slate-100 flex items-center justify-between gap-2">
        <a href="/dashboard" class="flex items-center gap-2.5 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 p-1 flex items-center justify-center shadow-xs shrink-0">
            <img src="/assets/img/pogi_logo.jpg" alt="Logo POGI" class="w-full h-full object-contain">
          </div>
          <div class="min-w-0">
            <div class="font-black text-sm tracking-tight text-[#006948] leading-none">POGI SKP TRACKER</div>
            <div class="text-[9px] text-slate-500 font-bold leading-tight mt-1 truncate">PP POGI &bull; Standar 160 SKP</div>
          </div>
        </a>
        <button type="button" onclick="toggleMobileSidebar(false)" class="lg:hidden p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition" aria-label="Tutup Menu">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      <!-- Navigation Menu -->
      <div class="px-3 py-4 flex-1 overflow-y-auto space-y-6">
        <div>
          <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
            MONITORING NASIONAL
          </div>
          <nav class="space-y-1">
            <a href="/dashboard" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition bg-[#006948] text-white shadow-xs">
              <span class="material-symbols-outlined text-[19px]">dashboard</span>
              <span>Dashboard</span>
            </a>
            <a href="/doctors" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">stethoscope</span>
              <span>Doctors</span>
            </a>
            <a href="/provinces" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">map</span>
              <span>Provinces</span>
            </a>
            <a href="/events" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">event</span>
              <span>Events</span>
            </a>
            <a href="/activity" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[19px]">history</span>
              <span>Activity Log</span>
            </a>
            <a href="/reports" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
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
            <a href="/manage?tab=doctor" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">person</span>
              <span>Doctor</span>
            </a>
            <a href="/manage?tab=institution" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">domain</span>
              <span>Institution (RS)</span>
            </a>
            <a href="/manage?tab=event" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">calendar_month</span>
              <span>SKP Event</span>
            </a>
            <a href="/manage?tab=category" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">category</span>
              <span>Event Category</span>
            </a>
            <a href="/manage?tab=user" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
              <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
              <span>User</span>
            </a>
          </nav>
        </div>
      </div>

      <!-- Bottom Profile Card (Sesuai Screenshot Klien: Super Admin) -->
      <div class="p-3 border-t border-slate-100 bg-slate-50/70">
        <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between gap-2 shadow-2xs">
          <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
              <span class="material-symbols-outlined text-lg">admin_panel_settings</span>
            </div>
            <div class="min-w-0">
              <div class="text-xs font-bold text-slate-900 truncate">Super Admin</div>
              <div class="text-[10px] text-slate-400 font-mono truncate">super@example.com</div>
            </div>
          </div>
          <button type="button" onclick="performLogout()" title="Keluar" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition shrink-0">
            <span class="material-symbols-outlined text-lg">unfold_more</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Sticky Header -->
            <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-xs border-b border-slate-200/90 shrink-0 w-full print:hidden">
        <div class="h-14 sm:h-16 px-4 sm:px-6 flex items-center justify-between gap-3">
          <!-- Left: Mobile menu & Scope Selector -->
          <div class="flex items-center gap-2 sm:gap-3 min-w-0 shrink-0">
            <button type="button" onclick="toggleMobileSidebar(true)" class="lg:hidden p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition shrink-0" aria-label="Buka Menu">
              <span class="material-symbols-outlined text-2xl">menu</span>
            </button>

            <!-- Scope Selector (38 Provinsi / Cabang) -->
            <div class="relative">
              <button type="button" id="headerOrgScopeBtn" onclick="toggleOrgScopeDropdown()" class="flex items-center gap-2 bg-slate-50 hover:bg-slate-100 transition border border-slate-200 rounded-xl px-3 py-1.5 cursor-pointer max-w-[170px] sm:max-w-[240px]">
                <span class="material-symbols-outlined text-[#006948] text-base shrink-0">public</span>
                <span id="selectedOrgScopeLabel" class="text-xs font-bold text-slate-800 truncate">Semua Wilayah Nasional (38 Provinsi)</span>
                <span class="material-symbols-outlined text-sm text-slate-400 shrink-0">expand_more</span>
              </button>
              <div id="orgScopeDropdown" class="hidden absolute left-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50 text-xs">
                <div class="px-3.5 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cakupan Pantauan</div>
                <button type="button" onclick="selectOrgScope('all', 'Semua Wilayah Nasional (38 Provinsi)')" class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-slate-700 flex items-center justify-between font-semibold">
                  <span>Seluruh Indonesia</span>
                  <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-[#006948] text-[10px] font-bold">200 Dokter</span>
                </button>
                <button type="button" onclick="selectOrgScope('jabar', 'POGI Cabang Jawa Barat')" class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-slate-700 flex items-center justify-between font-semibold">
                  <span>POGI Cabang Jawa Barat</span>
                  <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">8 Dokter</span>
                </button>
                <button type="button" onclick="selectOrgScope('dki', 'POGI Cabang DKI Jakarta')" class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-slate-700 flex items-center justify-between font-semibold">
                  <span>POGI Cabang DKI Jakarta</span>
                  <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">22 Dokter</span>
                </button>
                <button type="button" onclick="selectOrgScope('jatim', 'POGI Cabang Jawa Timur')" class="w-full text-left px-3.5 py-2 hover:bg-emerald-50 text-slate-700 flex items-center justify-between font-semibold">
                  <span>POGI Cabang Jawa Timur</span>
                  <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold">8 Dokter</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Center: Global Search Input (Inside Red Box 1) -->
          <div class="relative flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-2 sm:mx-4">
            <div class="relative flex items-center">
              <span class="material-symbols-outlined absolute left-3 text-slate-400 text-lg pointer-events-none">search</span>
              <input type="text" id="topGlobalSearchInput" oninput="handleGlobalTopSearch(this.value)" onkeydown="handleGlobalSearchKeydown(event)" placeholder="Cari dokter, nomor STR, SIP, atau ..." class="w-full pl-9 pr-8 py-2 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 focus:border-[#006948] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none transition shadow-2xs">
              <button type="button" id="clearTopSearchBtn" onclick="clearGlobalTopSearch()" class="hidden absolute right-2.5 text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer">
                <span class="material-symbols-outlined text-sm">close</span>
              </button>
            </div>
            <!-- Dropdown Live Search Results -->
            <div id="topSearchResultsDropdown" class="hidden absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-2 z-50 max-h-96 overflow-y-auto">
              <!-- Rendered dynamically -->
            </div>
          </div>

          <!-- Right: Notifikasi & Profil User SR -->
          <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            

            <!-- Notification Bell with Red Dot Indicator -->
            <div class="relative">
              <button type="button" id="topNotifBtn" onclick="toggleTopNotifDropdown()" class="relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" aria-label="Notifikasi">
                <span class="material-symbols-outlined text-xl">notifications</span>
                <span id="notifBadgeDot" class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white animate-pulse"></span>
              </button>
              <!-- Notification Dropdown Menu -->
              <div id="topNotifDropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-3 z-50 text-xs">
                <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                  <div class="font-bold text-slate-900 flex items-center gap-1.5">
                    <span>Pemberitahuan Sistem</span>
                    <span id="notifUnreadBadge" class="px-2 py-0.5 rounded-full bg-emerald-100 text-[#006948] text-[10px] font-extrabold">3 Baru</span>
                  </div>
                  <button type="button" onclick="markAllNotificationsAsRead()" class="text-[11px] text-[#006948] hover:underline font-bold cursor-pointer">
                    Tandai Dibaca
                  </button>
                </div>
                <div id="notifListContainer" class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                  <div class="p-3.5 hover:bg-slate-50 transition flex items-start gap-3 cursor-pointer" onclick="window.location.href='doctors.html?filter=pending'">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                      <span class="material-symbols-outlined text-base">pending_actions</span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="font-bold text-slate-900 text-xs">Verifikasi Pending Kredit SKP</div>
                      <div class="text-[11px] text-slate-500 mt-0.5">64 Dokter Obgyn memiliki pending credits triwulan Q4 2026 yang belum diverifikasi.</div>
                      <div class="text-[9px] text-slate-400 mt-1 font-mono">15 menit yang lalu</div>
                    </div>
                  </div>
                  <div class="p-3.5 hover:bg-slate-50 transition flex items-start gap-3 cursor-pointer" onclick="syncSatuSehat()">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-[#006948] flex items-center justify-center shrink-0 mt-0.5">
                      <span class="material-symbols-outlined text-base">cloud_sync</span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="font-bold text-slate-900 text-xs">Sinkronisasi SATUSEHAT Berhasil</div>
                      <div class="text-[11px] text-slate-500 mt-0.5">200 data STR, SIP, dan NIK dokter spesialis Obgyn terverifikasi aktif dengan Kemenkes API.</div>
                      <div class="text-[9px] text-slate-400 mt-1 font-mono">1 jam yang lalu</div>
                    </div>
                  </div>
                  <div class="p-3.5 hover:bg-slate-50 transition flex items-start gap-3 cursor-pointer" onclick="window.location.href='provinces.html'">
                    <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                      <span class="material-symbols-outlined text-base">warning</span>
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="font-bold text-slate-900 text-xs">Peringatan Capaian Cabang Kritis</div>
                      <div class="text-[11px] text-slate-500 mt-0.5">Cabang Papua Barat Daya &amp; Maluku capaian SKP masih di bawah ambang batas minimal (&lt;40%).</div>
                      <div class="text-[9px] text-slate-400 mt-1 font-mono">3 jam yang lalu</div>
                    </div>
                  </div>
                </div>
                <div class="px-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                  <a href="/activity" class="text-slate-600 hover:text-[#006948] font-bold flex items-center gap-1 transition">
                    <span>Buka Log Aktivitas Lengkap</span>
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                  </a>
                </div>
              </div>
            </div>

            <!-- User Avatar Profile Circle [SR] -->
            <div class="relative">
              <button type="button" id="topUserBtn" onclick="toggleTopUserDropdown()" class="w-9 h-9 rounded-full bg-[#006948] hover:bg-[#005439] text-white font-black text-xs flex items-center justify-center shadow-xs transition cursor-pointer ring-2 ring-emerald-600/20" title="Profil Pengguna: Siti Rahma, S.Farm">
                SR
              </button>
              <!-- User Profile Dropdown Menu -->
              <div id="topUserDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-3 z-50 text-xs">
                <div class="px-4 py-2 border-b border-slate-100 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-[#006948] text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                    SR
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-900 text-xs truncate">Siti Rahma, S.Farm</div>
                    <div class="text-[10px] text-slate-500 truncate">Tim Data &amp; Verifikasi PP POGI</div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-[#006948] border border-emerald-200 text-[9px] font-bold mt-1">
                      <span class="w-1 h-1 rounded-full bg-emerald-600"></span> Super Admin &bull; Online
                    </span>
                  </div>
                </div>
                <div class="py-1">
                  <a href="/manage" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
                    <span class="material-symbols-outlined text-base text-slate-400">admin_panel_settings</span>
                    <span>Pengelolaan Master Data</span>
                  </a>
                  <a href="/activity" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
                    <span class="material-symbols-outlined text-base text-slate-400">history</span>
                    <span>Jejak Audit &amp; Log Aktivitas</span>
                  </a>
                  <a href="/reports" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
                    <span class="material-symbols-outlined text-base text-slate-400">description</span>
                    <span>Laporan Bulanan SKP</span>
                  </a>
                </div>
                <div class="pt-1 border-t border-slate-100 px-2">
                  <button type="button" onclick="performLogout()" class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50 rounded-xl font-bold flex items-center gap-2 transition cursor-pointer">
                    <span class="material-symbols-outlined text-base">logout</span>
                    <span>Keluar Sistem (Logout)</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Body Scrollable View -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 page-fade-in">

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
          <div class="flex items-center gap-2 shrink-0">
            <a href="/doctors" class="px-4 py-2.5 rounded-xl bg-white text-slate-900 font-extrabold text-xs hover:bg-slate-100 shadow-sm transition flex items-center gap-1.5">
              <span>Buka Direktori Dokter</span>
              <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
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
            <div id="metricTotalDoctors" class="text-2xl sm:text-3xl font-black text-slate-900">200</div>
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
            <div id="metricAvgProgress" class="text-2xl sm:text-3xl font-black text-emerald-800">43%</div>
            <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
              <div id="metricAvgProgressBar" class="bg-[#006948] h-2 rounded-full" style="width: 43%"></div>
            </div>
            <div class="text-[10px] text-slate-500 mt-1">Target Kuartal: 8.0 SKP / dokter</div>
          </div>

          <!-- Complete -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-500 mb-2">
              <span class="text-xs font-bold uppercase tracking-wider">Complete</span>
              <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">check_circle</span>
              </div>
            </div>
            <div id="metricComplete" class="text-2xl sm:text-3xl font-black text-emerald-700">42</div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
              <span class="font-bold text-emerald-800">21%</span> telah penuhi target periode
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
            <div id="metricIncomplete" class="text-2xl sm:text-3xl font-black text-amber-600">158</div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
              <span class="font-bold text-amber-800">79%</span> butuh follow-up ke RS/Cabang
            </div>
          </div>
        </div>

        <!-- 3 Subbidang Mandatory Progress Breakdown -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
              <h2 class="text-base font-extrabold text-slate-900">Standarisasi 3 Subbidang SKP POGI (Total 160 SKP / 5 Tahun)</h2>
              <p class="text-xs text-slate-500">Kewajiban pemenuhan kredit dokter Obgyn harus proporsional di seluruh 3 subbidang wajib.</p>
            </div>
            <span class="text-xs font-bold text-[#006948] bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
              Rata-rata Akumulasi: 43.8%
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
                <span class="text-[11px] font-bold text-sky-800 bg-sky-100 px-2 py-0.5 rounded">Min 80 SKP</span>
              </div>
              <div class="text-xl font-black text-slate-900">58.4% <span class="text-xs font-normal text-slate-500">(rata-rata)</span></div>
              <div class="w-full bg-slate-200 rounded-full h-2 mt-2 overflow-hidden">
                <div class="progress-bar-pelayanan h-2 rounded-full" style="width: 58.4%"></div>
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
                <span class="text-[11px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">Min 30 SKP</span>
              </div>
              <div class="text-xl font-black text-slate-900">41.2% <span class="text-xs font-normal text-slate-500">(rata-rata)</span></div>
              <div class="w-full bg-slate-200 rounded-full h-2 mt-2 overflow-hidden">
                <div class="progress-bar-pengabdian h-2 rounded-full" style="width: 41.2%"></div>
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
                <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">Min 50 SKP</span>
              </div>
              <div class="text-xl font-black text-slate-900">52.8% <span class="text-xs font-normal text-slate-500">(rata-rata)</span></div>
              <div class="w-full bg-slate-200 rounded-full h-2 mt-2 overflow-hidden">
                <div class="progress-bar-pembelajaran h-2 rounded-full" style="width: 52.8%"></div>
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

              <!-- Bali: 0 / 10 complete 0% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Bali</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">0 / 10 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">0%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-slate-300 h-1.5 rounded-full" style="width: 0%"></div>
                </div>
              </div>

              <!-- Nusa Tenggara Timur: 0 / 7 complete 0% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Nusa Tenggara Timur</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">0 / 7 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">0%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-slate-300 h-1.5 rounded-full" style="width: 0%"></div>
                </div>
              </div>

              <!-- Papua: 0 / 4 complete 0% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Papua</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">0 / 4 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">0%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-slate-300 h-1.5 rounded-full" style="width: 0%"></div>
                </div>
              </div>

              <!-- Aceh: 0 / 3 complete 0% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Aceh</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">0 / 3 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">0%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-slate-300 h-1.5 rounded-full" style="width: 0%"></div>
                </div>
              </div>

              <!-- Kepulauan Bangka Belitung: 0 / 3 complete 0% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Kepulauan Bangka Belitung</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">0 / 3 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">0%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-slate-300 h-1.5 rounded-full" style="width: 0%"></div>
                </div>
              </div>
            </div>

            <!-- Right Column: Highest -->
            <div class="space-y-4">
              <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500 pb-1">
                <span class="material-symbols-outlined text-base text-[#006948]">trending_up</span>
                <span class="font-bold text-slate-600">Highest</span>
              </div>

              <!-- Sulawesi Utara: 2 / 3 complete 67% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Sulawesi Utara</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">2 / 3 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">67%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-[#006948] h-1.5 rounded-full" style="width: 67%"></div>
                </div>
              </div>

              <!-- Kalimantan Utara: 2 / 3 complete 67% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Kalimantan Utara</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">2 / 3 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">67%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-[#006948] h-1.5 rounded-full" style="width: 67%"></div>
                </div>
              </div>

              <!-- Papua Pegunungan: 3 / 6 complete 50% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Papua Pegunungan</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">3 / 6 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">50%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-[#006948] h-1.5 rounded-full" style="width: 50%"></div>
                </div>
              </div>

              <!-- Sumatera Barat: 2 / 4 complete 50% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Sumatera Barat</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">2 / 4 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">50%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-[#006948] h-1.5 rounded-full" style="width: 50%"></div>
                </div>
              </div>

              <!-- Jambi: 3 / 7 complete 43% -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-extrabold text-slate-800">Jambi</span>
                  <div class="flex items-center gap-3">
                    <span class="text-slate-400 font-medium text-[11px] stat-count">3 / 7 complete</span>
                    <span class="font-black text-slate-900 w-10 text-right stat-pct">43%</span>
                  </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                  <div class="bg-[#006948] h-1.5 rounded-full" style="width: 43%"></div>
                </div>
              </div>
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
              <p id="mapSubTitle" class="text-xs text-slate-500 mt-0.5">200 doctors in 38 provinces.</p>
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
                <!-- Rendered dynamically -->
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
            <!-- Rendered by pogi_state -->
          </div>
        </div>

      </main>
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
            <option value="all_incomplete">Semua 158 Dokter Incomplete (Nasional)</option>
            <option value="lowest_5">5 Cabang Kepatuhan Terendah (Bengkulu, NTT, Jambi, Lampung, Papua)</option>
            <option value="jabar">Khusus Cabang Jawa Barat (9 Dokter)</option>
            <option value="jatim">Khusus Cabang Jawa Timur (5 Dokter)</option>
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

  <script src="/assets/data/indonesia-38-provinces.js"></script>
  <script src="/js/pogi_state.js"></script>
  <script src="/js/navigation.js"></script>
  <script src="/js/pdf_exporter.js"></script>
  <script>
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
        if (typeof setOrgScope === 'function') {
          setTimeout(() => setOrgScope(sKey, 'POGI Cabang ' + currentUser.branch), 100);
        }
        const orgBtn = document.getElementById('orgScopeBtn');
        if (orgBtn) orgBtn.setAttribute('disabled', 'true');
      }
    });

    function initRecentActivities() {
      const acts = PogiStore.getActivities().slice(0, 3);
      const container = document.getElementById('recentActivityList');
      if (container) {
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
      const pogiProvinces = (typeof PogiStore !== 'undefined' && PogiStore.getProvinces) 
        ? PogiStore.getProvinces() 
        : [];
      const container = document.getElementById('topProvincesList');
      if (!container) return;

      const topList = [...pogiProvinces].sort((a, b) => b.doctors - a.doctors).slice(0, 8);

      container.innerHTML = topList.map(item => {
        const rate = item.completionRate || 0;
        let dotColor = 'bg-[#dc2626]';
        let badgeColor = 'bg-red-50 text-red-700 border-red-200';
        let statusLabel = 'Sangat Kurang';

        if (rate >= 85) {
          dotColor = 'bg-[#eab308]';
          badgeColor = 'bg-amber-50 text-amber-800 border-amber-200';
          statusLabel = 'Terpenuhi';
        } else if (rate >= 50) {
          dotColor = 'bg-[#16a34a]';
          badgeColor = 'bg-emerald-50 text-emerald-800 border-emerald-200';
          statusLabel = 'Hampir Memenuhi';
        }

        return `
          <div class="flex items-center justify-between py-1.5 px-2.5 rounded-xl hover:bg-slate-50 transition cursor-pointer border border-transparent hover:border-slate-200"
               onmouseenter="highlightMapProvince('${item.name}')"
               onmouseleave="resetMapProvince('${item.name}')"
               onclick="window.location.href='doctors.html?province=${encodeURIComponent(item.name)}'">
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

      const pogiProvinces = PogiStore.getProvinces();

      function getProvinceStats(provName) {
        if (!provName) return null;
        const norm = provName.toLowerCase().replace(/^(provinsi|daerah khusus ibukota|daerah istimewa)\s+/i, '').trim();
        return pogiProvinces.find(p => {
          const pNorm = p.name.toLowerCase().replace(/^(provinsi|daerah khusus ibukota|daerah istimewa)\s+/i, '').trim();
          return pNorm === norm || p.name.toLowerCase().includes(norm) || norm.includes(p.name.toLowerCase());
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
          const provName = feature.properties.PROVINSI || feature.properties.name || '';
          const stat = getProvinceStats(provName);
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
          const stat = getProvinceStats(provName);
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
              window.location.href = 'provinces.html';
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
      PogiStore.addActivity({
        actor: 'Siti Rahma (Admin Data Pusat)',
        action: 'Kirim Notifikasi FU Massal',
        details: 'Notifikasi pengingat kelengkapan SKP terkirim ke target: ' + target,
        category: 'FollowUp'
      });
      closeFollowUpModal();
      showToast('Pesan Follow-Up Berhasil Dikirimkan ke ' + target, 'success');
    }

    function syncSatuSehat() {
      showToast('Menghubungkan ke API Plataran Sehat...', 'info');
      setTimeout(() => {
        PogiStore.addActivity({
          actor: 'System Integration',
          action: 'Sinkronisasi SATUSEHAT',
          details: 'Berhasil menyinkronkan 12 data kredit logbook dokter terbaru.',
          category: 'Sync'
        });
        showToast('Sinkronisasi Sukses: 12 Kredit SKP Baru Diperbarui!', 'success');
      }, 1200);
    }

    function selectOrgScope(scope, labelText) {
      const label = document.getElementById('selectedOrgScopeLabel');
      if (label) {
        label.innerText = labelText || (scope === 'all' ? 'Semua Wilayah Nasional (38 Provinsi)' : scope);
      }
      const dd = document.getElementById('orgScopeDropdown');
      if (dd) dd.classList.add('hidden');

      const scopeData = {
        all: { total: 200, progress: 43, complete: 42, incomplete: 158, text: '200 doctors in 38 provinces.' },
        jabar: { total: 8, progress: 25, complete: 2, incomplete: 6, text: '8 doctors in POGI Cabang Jawa Barat.' },
        dki: { total: 22, progress: 36, complete: 8, incomplete: 14, text: '22 doctors in POGI Cabang DKI Jakarta.' },
        jatim: { total: 8, progress: 25, complete: 2, incomplete: 6, text: '8 doctors in POGI Cabang Jawa Timur.' }
      };

      const cur = scopeData[scope] || scopeData.all;
      const elTot = document.getElementById('metricTotalDoctors');
      const elProg = document.getElementById('metricAvgProgress');
      const elProgBar = document.getElementById('metricAvgProgressBar');
      const elComp = document.getElementById('metricComplete');
      const elIncomp = document.getElementById('metricIncomplete');
      const elMapSub = document.getElementById('mapSubTitle');

      if (elTot) elTot.innerText = cur.total;
      if (elProg) elProg.innerText = cur.progress + '%';
      if (elProgBar) elProgBar.style.width = cur.progress + '%';
      if (elComp) elComp.innerText = cur.complete;
      if (elIncomp) elIncomp.innerText = cur.incomplete;
      if (elMapSub) elMapSub.innerText = cur.text;

      if (scope !== 'all') {
        const provNameMap = { jabar: 'Jawa Barat', dki: 'DKI Jakarta', jatim: 'Jawa Timur' };
        highlightMapProvince(provNameMap[scope] || '');
      } else {
        resetMapProvince('DKI Jakarta');
        resetMapProvince('Jawa Barat');
        resetMapProvince('Jawa Timur');
      }

      showToast('Cakupan Wilayah Diperbarui: ' + (labelText || scope), 'info');
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
</body>
</html>
