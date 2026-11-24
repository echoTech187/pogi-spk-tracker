@php
  $headerUser = Auth::user() ?? ($user ?? null);
  $headerName = $headerUser?->full_name ?? 'Super Admin';
  $headerEmail = $headerUser?->email ?? 'super@example.com';
  $headerRole = $headerUser?->role ?? 'SUPER_ADMIN';
  $headerRoleLabel = match($headerRole) {
      'SUPER_ADMIN' => 'Super Admin Sistem',
      'ADMIN_PUSAT' => 'POGI Pusat (Monitoring All Area)',
      'ADMIN_POGI' => 'Admin POGI (Data Entry & Kemenkes)',
      'ADMIN_CABANG' => 'Admin Cabang (Monitoring Daerah)',
      'DOKTER' => 'Dokter Spesialis Obgyn',
      default => 'Pengurus'
  };
  $headerInitials = collect(explode(' ', $headerName))->filter()->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') ?: 'SA';

  $notifPendingCount = \Illuminate\Support\Facades\DB::table('doctors')->where('status', 'INCOMPLETE')->count();
  $latestIncomplete = \Illuminate\Support\Facades\DB::table('doctors')->where('status', 'INCOMPLETE')->latest('updated_at')->first();
  $notifTime = $latestIncomplete && $latestIncomplete->updated_at 
      ? \Carbon\Carbon::parse($latestIncomplete->updated_at)->diffForHumans() 
      : 'Baru saja';
@endphp

<header class="sticky top-0 z-30 bg-white/95 backdrop-blur-xs border-b border-slate-200/90 shrink-0 w-full print:hidden">
  <div class="h-14 sm:h-16 px-4 sm:px-6 flex items-center justify-between gap-3">
    <!-- Left: Mobile menu button -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0 shrink-0">
      <button type="button" onclick="toggleMobileSidebar(true)" class="lg:hidden p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition shrink-0 cursor-pointer" aria-label="Buka Menu">
        <span class="material-symbols-outlined text-2xl">menu</span>
      </button>
    </div>

    <!-- Center: Global Search Input -->
    <div class="relative flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-2 sm:mx-4">
      <div class="relative flex items-center">
        <span class="material-symbols-outlined absolute left-3 text-slate-400 text-lg pointer-events-none">search</span>
        <input type="text" id="topGlobalSearchInput" oninput="handleGlobalTopSearch(this.value)" onkeydown="handleGlobalSearchKeydown(event)" placeholder="Cari dokter, nomor STR, SIP, atau ..." class="w-full pl-9 pr-8 py-2 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 focus:border-[#16b3ac] rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none transition shadow-2xs">
        <button type="button" id="clearTopSearchBtn" onclick="clearGlobalTopSearch()" class="hidden absolute right-2.5 text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer">
          <span class="material-symbols-outlined text-sm">close</span>
        </button>
      </div>
      <!-- Dropdown Live Search Results -->
      <div id="topSearchResultsDropdown" class="hidden absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-2 z-50 max-h-96 overflow-y-auto">
      </div>
    </div>

    <!-- Right: Notifikasi & Profil User -->
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
      <!-- Akses Kemenkes SatuSehat Action Button -->
      <a href="{{ route('kemenkes.launch') }}" target="_blank"
        onclick="if(typeof showToast === 'function') showToast('Membuka Portal Kemenkes SDMK & mencatat sesi kerja...', 'info');"
        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] border border-emerald-200/80 font-bold text-xs transition shadow-2xs"
        title="Buka SatuSehat SDMK Kemenkes (Tercatat otomatis ke riwayat kerja & log kinerja admin)">
        <span class="material-symbols-outlined text-base text-emerald-700">health_and_safety</span>
        <span class="hidden md:inline">Portal Kemenkes</span>
      </a>

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
              <span id="notifUnreadBadge" class="px-2 py-0.5 rounded-full bg-emerald-100 text-[#16b3ac] text-[10px] font-extrabold">{{ $notifPendingCount }} Perlu FU</span>
            </div>
            <button type="button" onclick="markAllNotificationsAsRead()" class="text-[11px] text-[#16b3ac] hover:underline font-bold cursor-pointer">
              Tandai Dibaca
            </button>
          </div>
          <div id="notifListContainer" class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            <div class="p-3.5 hover:bg-slate-50 transition flex items-start gap-3 cursor-pointer" onclick="window.location.href='{{ url('/doctors?filter=pending') }}'">
              <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                <span class="material-symbols-outlined text-base">pending_actions</span>
              </div>
              <div class="min-w-0 flex-1">
                <div class="font-bold text-slate-900 text-xs">Verifikasi Pending Kredit SKP</div>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ $notifPendingCount }} Dokter Obgyn butuh verifikasi dan follow-up pemenuhan kredit SKP.</div>
                <div class="text-[9px] text-slate-400 mt-1 font-mono">{{ $notifTime }}</div>
              </div>
            </div>
          </div>
          @if(in_array($headerRole, ['SUPER_ADMIN', 'ADMIN_PUSAT']))
          <div class="px-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
            <a href="{{ url('/activity') }}" class="text-slate-600 hover:text-[#16b3ac] font-bold flex items-center gap-1 transition">
              <span>Buka Log Aktivitas Lengkap</span>
              <span class="material-symbols-outlined text-xs">arrow_forward</span>
            </a>
          </div>
          @endif
        </div>
      </div>

      <!-- User Avatar Profile Circle -->
      <div class="relative">
        <button type="button" id="topUserBtn" onclick="toggleTopUserDropdown()" class="w-9 h-9 rounded-full bg-[#16b3ac] hover:bg-[#129993] text-white font-black text-xs flex items-center justify-center shadow-xs transition cursor-pointer ring-2 ring-emerald-600/20" title="Profil Pengguna">
          <span id="headerUserInitials">{{ $headerInitials }}</span>
        </button>
        <!-- User Profile Dropdown Menu -->
        <div id="topUserDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-3 z-50 text-xs">
          <div class="px-4 py-2 border-b border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#16b3ac] text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs" id="headerDropdownAvatar">
              {{ $headerInitials }}
            </div>
            <div class="min-w-0">
              <div class="font-bold text-slate-900 text-xs truncate" id="headerDropdownName">{{ $headerName }}</div>
              <div class="text-[10px] text-slate-500 truncate" id="headerDropdownRole">{{ $headerRoleLabel }}</div>
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-[#16b3ac] border border-emerald-200 text-[9px] font-bold mt-1">
                <span class="w-1 h-1 rounded-full bg-emerald-600"></span> Online
              </span>
            </div>
          </div>
          <div class="py-1">
            @if($headerRole === 'SUPER_ADMIN')
            <a href="{{ url('/manage') }}" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
              <span class="material-symbols-outlined text-base text-slate-400">admin_panel_settings</span>
              <span>Pengelolaan Master Data</span>
            </a>
            @endif
            @if(in_array($headerRole, ['SUPER_ADMIN', 'ADMIN_PUSAT']))
            <a href="{{ url('/activity') }}" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
              <span class="material-symbols-outlined text-base text-slate-400">history</span>
              <span>Jejak Audit &amp; Log Aktivitas</span>
            </a>
            @endif
            @if(in_array($headerRole, ['SUPER_ADMIN', 'ADMIN_PUSAT', 'ADMIN_CABANG']))
            <a href="{{ url('/reports') }}" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 text-slate-700 flex items-center gap-2.5 font-semibold transition">
              <span class="material-symbols-outlined text-base text-slate-400">description</span>
              <span>Laporan Bulanan SKP</span>
            </a>
            @endif
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
