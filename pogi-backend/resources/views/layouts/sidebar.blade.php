<aside id="appSidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] lg:w-64 bg-white border-r border-slate-200/90 flex flex-col shrink-0 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-auto shadow-2xl lg:shadow-none">
  <!-- Top Brand Header -->
  <div class="px-4 py-4 border-b border-slate-100 flex items-center justify-between gap-2">
    <a href="{{ url('/dashboard') }}" class="flex items-center gap-2.5 min-w-0">
      <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 p-1 flex items-center justify-center shadow-xs shrink-0">
        <img src="{{ asset('logo.png') }}" alt="Logo POGI" class="w-full h-full object-contain">
      </div>
      <div class="min-w-0">
        <div class="font-black text-sm tracking-tight text-[#16b3ac] leading-none">POGI SKP TRACKER</div>
        <div class="text-[9px] text-slate-500 font-bold leading-tight mt-1 truncate">PP POGI &bull; Standar 160 SKP</div>
      </div>
    </a>
    <button type="button" onclick="toggleMobileSidebar(false)" class="lg:hidden p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition" aria-label="Tutup Menu">
      <span class="material-symbols-outlined text-xl">close</span>
    </button>
  </div>

  <!-- Navigation Menu -->
  <div class="px-3 py-4 flex-1 overflow-y-auto space-y-6">
    @php
      $currentRole = Auth::user()?->role ?? 'SUPER_ADMIN';
    @endphp

    <div>
      <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
        MONITORING NASIONAL
      </div>
      <nav class="space-y-1">
        <a href="{{ url('/dashboard') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition {{ Request::is('dashboard') || Request::is('/') ? 'bg-[#16b3ac] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
          <span class="material-symbols-outlined text-[19px]">dashboard</span>
          <span>Dashboard</span>
        </a>
        <a href="{{ url('/doctors') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('doctors*') ? 'bg-[#16b3ac] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
          <span class="material-symbols-outlined text-[19px]">stethoscope</span>
          <span>Doctors</span>
        </a>
        <a href="{{ url('/provinces') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('provinces*') ? 'bg-[#16b3ac] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
          <span class="material-symbols-outlined text-[19px]">map</span>
          <span>Provinces</span>
        </a>
        <a href="{{ url('/events') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('events*') ? 'bg-[#16b3ac] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
          <span class="material-symbols-outlined text-[19px]">event</span>
          <span>Events</span>
        </a>
        @if(in_array($currentRole, ['SUPER_ADMIN', 'ADMIN_PUSAT']))
        <a href="{{ url('/activity') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('activity*') ? 'bg-[#16b3ac] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
          <span class="material-symbols-outlined text-[19px]">history</span>
          <span>Activity Log</span>
        </a>
        @endif
        @if(in_array($currentRole, ['SUPER_ADMIN', 'ADMIN_PUSAT', 'ADMIN_CABANG']))
        <a href="{{ url('/reports') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('reports*') ? 'bg-[#16b3ac] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
          <span class="material-symbols-outlined text-[19px]">bar_chart</span>
          <span>Report</span>
        </a>
        @endif
        @if(in_array($currentRole, ['SUPER_ADMIN', 'ADMIN_PUSAT', 'ADMIN_POGI']))
        <a href="{{ url('/admin-performance') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ Request::is('admin-performance*') ? 'bg-[#16b3ac] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
          <span class="material-symbols-outlined text-[19px]">speed</span>
          <span>Admin Performance</span>
        </a>
        @endif
      </nav>
    </div>

    @if($currentRole === 'SUPER_ADMIN')
    <div>
      <div class="px-3 mb-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
        PENGATURAN DATA (MANAGE)
      </div>
      <nav class="space-y-1">
        <a href="{{ url('/manage?tab=doctor') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
          <span class="material-symbols-outlined text-[18px]">person</span>
          <span>Doctor</span>
        </a>
        <a href="{{ url('/manage?tab=institution') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
          <span class="material-symbols-outlined text-[18px]">domain</span>
          <span>Institution (RS)</span>
        </a>
        <a href="{{ url('/manage?tab=event') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
          <span class="material-symbols-outlined text-[18px]">calendar_month</span>
          <span>SKP Event</span>
        </a>
        <a href="{{ url('/manage?tab=category') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
          <span class="material-symbols-outlined text-[18px]">category</span>
          <span>Event Category</span>
        </a>
        @if($currentRole === 'SUPER_ADMIN')
        <a href="{{ url('/manage?tab=user') }}" class="nav-item w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100">
          <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
          <span>User &amp; Hak Akses</span>
        </a>
        @endif
      </nav>
    </div>
    @endif
  </div>

  @php
    $sidebarUser = Auth::user() ?? ($user ?? null);
    $sidebarName = $sidebarUser?->full_name ?? 'Super Admin';
    $sidebarEmail = $sidebarUser?->email ?? 'super@example.com';
    $sidebarRole = $sidebarUser?->role ?? 'SUPER_ADMIN';
  @endphp

  <!-- Bottom Profile Card -->
  <div class="p-3 border-t border-slate-100 bg-slate-50/70">
    <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between gap-2 shadow-2xs">
      <div class="flex items-center gap-2.5 min-w-0">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
          <span class="material-symbols-outlined text-lg">admin_panel_settings</span>
        </div>
        <div class="min-w-0">
          <div class="text-xs font-bold text-slate-900 truncate" id="sidebarUserName">{{ $sidebarName }}</div>
          <div class="text-[10px] text-slate-400 font-mono truncate" id="sidebarUserEmail">{{ $sidebarEmail }}</div>
        </div>
      </div>
      <button type="button" onclick="performLogout()" title="Keluar" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition shrink-0">
        <span class="material-symbols-outlined text-lg">unfold_more</span>
      </button>
    </div>
  </div>
</aside>
