@props([
    'prefix' => 'doc',
    'isEdit' => false,
    'provinces' => [],
    'institutions' => [],
    'adminPogiList' => [],
])

<!-- Section 1: Doctor Profile Section -->
<div class="space-y-3.5">
  <div class="flex items-center gap-2 pb-1.5 border-b border-slate-200/60">
    <span class="w-6 h-6 rounded-lg bg-emerald-50 border border-emerald-100/60 text-[#16b3ac] flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-[14px]">stethoscope</span>
    </span>
    <h4 class="font-extrabold text-slate-800 text-xs tracking-tight">Informasi &amp; Kredensial Dokter</h4>
  </div>

  <!-- Name -->
  <div>
    <label class="block font-bold text-slate-700 mb-1.5">Nama Lengkap Dokter <span class="text-rose-500 font-extrabold">*</span></label>
    <input id="{{ $prefix }}Name" type="text" required placeholder="dr. Budi Santoso, Sp.OG" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
  </div>

  <!-- STR & Phone (2 Cols) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Nomor STR</label>
      <input id="{{ $prefix }}Str" type="text" placeholder="32.73.100.2.21.0883" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-mono font-semibold text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
    </div>
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Nomor Telepon (WhatsApp) <span class="text-rose-500 font-extrabold">*</span></label>
      <input id="{{ $prefix }}Phone" type="text" required placeholder="08123456789" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-mono font-semibold text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
      <p class="text-[11px] text-slate-400 mt-1 font-medium">Awalan 0 otomatis tersimpan sebagai +62.</p>
    </div>
  </div>

  <!-- Province & Institution (2 Cols) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Provinsi / Wilayah Cabang <span class="text-rose-500 font-extrabold">*</span></label>
      <select id="{{ $prefix }}Prov" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
        <option value="">Pilih provinsi...</option>
        @foreach($provinces ?? [] as $prov)
          @php
            $pName = is_object($prov) ? $prov->name : (is_array($prov) ? $prov['name'] : $prov);
          @endphp
          <option value="{{ $pName }}">{{ $pName }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">RS / Faskes Utama <span class="text-rose-500 font-extrabold">*</span></label>
      <select id="{{ $prefix }}InstId" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
        <option value="">Pilih institusi / RS...</option>
        @foreach($institutions ?? [] as $inst)
          @php
            $iId = is_object($inst) ? $inst->id : (is_array($inst) ? $inst['id'] : $inst);
            $iName = is_object($inst) ? $inst->name : (is_array($inst) ? $inst['name'] : $inst);
            $iProv = is_object($inst) ? ($inst->province ?? '') : (is_array($inst) ? ($inst['province'] ?? '') : '');
          @endphp
          <option value="{{ $iId }}">{{ $iName }}{{ $iProv ? " ($iProv)" : '' }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <!-- Assigned PIC Admin -->
  <div>
    <label class="block font-bold text-slate-700 mb-1.5">PIC Admin / Dokter Binaan</label>
    <select id="{{ $prefix }}AssignedTo" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
      <option value="">Belum ada Admin POGI</option>
      @foreach($adminPogiList ?? [] as $adm)
        @php
          $admName = is_object($adm) ? ($adm->full_name ?? $adm->name) : (is_array($adm) ? ($adm['full_name'] ?? $adm['name']) : $adm);
        @endphp
        <option value="{{ $admName }}">{{ $admName }} (Admin POGI)</option>
      @endforeach
    </select>
  </div>

  <!-- Email & Status Dokter (2 Cols) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Email <span class="text-rose-500 font-extrabold">*</span></label>
      <div class="relative">
        <input id="{{ $prefix }}Email" type="email" required placeholder="dokter@pogi.or.id" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
        <span class="material-symbols-outlined text-slate-400 text-[18px] absolute left-2.5 top-2.5 pointer-events-none">mail</span>
      </div>
    </div>
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Status Dokter (Master Data) <span class="text-rose-500 font-extrabold">*</span></label>
      <select id="{{ $prefix }}Status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
        <option value="ACTIVE" selected>Active (Aktif)</option>
        <option value="INACTIVE">Non-Active (Non-Aktif)</option>
      </select>
    </div>
  </div>

  <!-- Period start & Period end (2 Cols) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Periode Mulai SKP <span class="text-rose-500 font-extrabold">*</span></label>
      <div class="relative">
        <input id="{{ $prefix }}PeriodStart" type="text" required value="1 Jan 2026" placeholder="1 Jan 2026" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
        <span class="material-symbols-outlined text-slate-400 text-[18px] absolute left-2.5 top-2.5 pointer-events-none">calendar_today</span>
      </div>
    </div>
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Periode Berakhir SKP <span class="text-rose-500 font-extrabold">*</span></label>
      <div class="relative">
        <input id="{{ $prefix }}PeriodEnd" type="text" required value="31 Des 2030" placeholder="31 Des 2030" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
        <span class="material-symbols-outlined text-slate-400 text-[18px] absolute left-2.5 top-2.5 pointer-events-none">calendar_today</span>
      </div>
    </div>
  </div>
</div>

<!-- Section 2: Credentials Section (LMS Plataran Account) -->
<div class="space-y-3.5 pt-2">
  <div class="flex items-center gap-2 pb-1.5 border-b border-slate-200/60">
    <span class="w-6 h-6 rounded-lg bg-sky-50 border border-sky-100/60 text-[#0ea5e9] flex items-center justify-center shrink-0">
      <span class="material-symbols-outlined text-[14px]">vpn_key</span>
    </span>
    <h4 class="font-extrabold text-slate-800 text-xs tracking-tight">Akun Kredensial LMS Plataran Sehat</h4>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Username LMS</label>
      <input id="{{ $prefix }}PlataranUser" type="text" autocomplete="off" value="" placeholder="Masukkan username LMS..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-sky-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
    </div>
    <div>
      <label class="block font-bold text-slate-700 mb-1.5">Password LMS</label>
      <div class="relative">
        <input id="{{ $prefix }}PlataranPass" type="password" autocomplete="new-password" value="" placeholder="Masukkan password LMS..." class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-sky-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
        <button type="button" onclick="togglePasswordVisibility('{{ $prefix }}PlataranPass', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer" title="Tampilkan/Sembunyikan Password">
          <span class="material-symbols-outlined text-[18px]">visibility</span>
        </button>
      </div>
    </div>
  </div>
</div>
