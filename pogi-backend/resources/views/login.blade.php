<!DOCTYPE html>
<html lang="id" class="h-full bg-[#031f1e]">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POGI SKP Tracker - Masuk Konsol Pemantauan SKP Nasional</title>
  <!-- Offline-ready local compiled Tailwind CSS (0ms delay, anti-FOUC) -->
  <link rel="stylesheet" href="/css/login-fallback.css">
  <!-- Tailwind CSS CDN (Progressive Enhancement) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Core Styles -->
  <link rel="stylesheet" href="/css/style.css">
</head>
<body class="h-full bg-[#031f1e] text-slate-800 antialiased font-sans">
  <div class="flex min-h-[100dvh] w-full flex-col lg:flex-row bg-[#031f1e] overflow-x-hidden">

    <!-- Mobile Top Brand Bar (< lg) -->
    <div class="lg:hidden bg-gradient-to-r from-[#0a4c49] via-[#0e6864] to-[#052625] px-5 py-4 border-b border-emerald-800/40 flex items-center justify-between text-white">
      <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl p-1 flex items-center justify-center shadow-md shadow-emerald-500/30 shrink-0">
          <img src="logo.png" alt="Logo POGI" class="w-full h-full object-contain">
        </div>
        <div class="min-w-0">
          <h1 class="font-extrabold text-lg tracking-tight text-white leading-none">POGI SKP TRACKER</h1>
          <p class="text-[10px] text-emerald-300 font-bold tracking-wider uppercase mt-1 truncate">Standarisasi 160 SKP Dokter Obgyn</p>
        </div>
      </div>
      <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">B2B Nasional</span>
    </div>

    <!-- Form Autentikasi (Kanan di Desktop, Utama di Mobile) -->
    <div class="order-1 lg:order-2 lg:w-1/2 bg-white flex flex-col justify-center px-5 py-8 sm:p-12 lg:p-16">
      <div class="max-w-md w-full mx-auto space-y-6">
        <div>
          <div class="text-[11px] font-bold text-[#16b3ac] uppercase tracking-wider flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#16b3ac] inline-block animate-pulse"></span>
            PORTAL MONITORING PUSAT &amp; CABANG POGI
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1.5 tracking-tight">Masuk Sistem SKP POGI</h2>
          <p class="text-xs text-slate-500 mt-1 leading-relaxed">
            Sistem terpusat pemantauan kewajiban 160 SKP / 5 tahun bagi dokter spesialis obstetri &amp; ginekologi di 38 provinsi di Indonesia (3 Subbidang: Pelayanan, Pengabdian, Pembelajaran).
          </p>
        </div>

        <form id="pogiLoginForm" action="{{ route('login.post') }}" method="POST" class="space-y-4">
          @csrf
          @if ($errors->any())
            <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 space-y-1">
              @foreach ($errors->all() as $error)
                <div class="flex items-center gap-1.5 font-semibold">
                  <span class="material-symbols-outlined text-sm">error</span>
                  <span>{{ $error }}</span>
                </div>
              @endforeach
            </div>
          @endif
          @if (session('error'))
            <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 flex items-center gap-1.5 font-semibold">
              <span class="material-symbols-outlined text-sm">error</span>
              <span>{{ session('error') }}</span>
            </div>
          @endif
          @if (session('info'))
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-[#16b3ac] flex items-center gap-1.5 font-semibold">
              <span class="material-symbols-outlined text-sm">info</span>
              <span>{{ session('info') }}</span>
            </div>
          @endif

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">ID Pengurus / Email Resmi POGI</label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">person</span>
              <input id="inputEmail" name="email" type="text" required placeholder="Contoh: nama@pogi.or.id" value="{{ old('email') }}" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#16b3ac] focus:bg-white transition" />
            </div>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="text-xs font-bold text-slate-700">Kata Sandi</label>
              <a href="javascript:void(0)" onclick="alert('Silakan hubungi Sekretariat Pengurus Pusat POGI untuk bantuan reset akun.')" class="text-[11px] text-[#16b3ac] font-semibold hover:underline">Bantuan Akses?</a>
            </div>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">lock</span>
              <input id="inputPassword" name="password" type="password" required placeholder="Masukkan kata sandi akun" class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#16b3ac] focus:bg-white transition" />
              <button type="button" onclick="togglePassVisibility()" class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-base cursor-pointer">visibility</button>
            </div>
          </div>

          <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-slate-600 font-medium select-none">
              <input type="checkbox" name="remember" checked class="w-4 h-4 rounded text-[#16b3ac] focus:ring-[#16b3ac] border-slate-300" />
              <span>Ingat sesi pengurus di perangkat ini</span>
            </label>
          </div>

          <button type="submit" class="w-full py-3 px-4 bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold text-sm rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
            <span>Masuk ke Konsol SKP POGI</span>
            <span class="material-symbols-outlined text-base">arrow_forward</span>
          </button>
        </form>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
          <span>&copy; 2026 Pengurus Pusat POGI</span>
          <span class="text-emerald-700 font-bold">Terhubung</span>
        </div>
      </div>
    </div>

    <!-- Branding & Info Side (Desktop Kiri: Dark Teal Signature) -->
    <div class="order-2 lg:order-1 lg:w-1/2 bg-gradient-to-br from-[#052625] via-[#0e6864] to-[#031817] p-8 lg:p-14 text-white flex flex-col justify-between relative overflow-hidden">
      <!-- Glow & Vector Ornaments -->
      <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
      <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-teal-500/10 blur-3xl pointer-events-none"></div>

      <!-- Top Header Brand -->
      <div class="flex items-center gap-4 relative z-10">
        <div class="w-14 h-14 rounded-2xl p-1.5 flex items-center justify-center shadow-xl shadow-emerald-950/40 shrink-0">
          <img src="logo.png" alt="Logo POGI" class="w-full h-full object-contain">
        </div>
        <div>
          <h1 class="text-xl lg:text-2xl font-black tracking-tight text-white leading-none">POGI</h1>
          <p class="text-xs text-emerald-300 font-bold uppercase tracking-wider mt-1">Perkumpulan Obstetri dan Ginekologi Indonesia</p>
        </div>
      </div>

      <!-- Center Feature Highlights -->
      <div class="my-10 space-y-6 relative z-10">
        <div>
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-400/10 text-emerald-300 text-xs font-bold border border-emerald-400/20 mb-3">
            <span class="material-symbols-outlined text-sm">verified</span> Standar Kepatuhan Profesi Nasional
          </span>
          <h3 class="text-2xl lg:text-3xl font-extrabold text-white leading-tight">
            Pemenuhan Mandatori 160 SKP / 5 Tahun Dokter Obgyn
          </h3>
          <p class="text-sm text-emerald-100/80 mt-2 leading-relaxed">
            Menyelaraskan data kepatuhan kredit pendidikan berkelanjutan bagi ribuan dokter spesialis obstetri &amp; ginekologi di seluruh faskes dan cabang daerah.
          </p>
        </div>

        <!-- 3 Pilar Subbidang Cards -->
        <div class="grid grid-cols-3 gap-3 pt-2">
          <div class="p-3.5 rounded-xl bg-white/10 backdrop-blur-sm border border-emerald-400/20">
            <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-300 flex items-center justify-center mb-2">
              <span class="material-symbols-outlined text-lg">medical_services</span>
            </div>
            <div class="font-bold text-xs text-white">1. Pelayanan</div>
            <div class="text-[10px] text-emerald-200/80 mt-0.5">Praktik klinis, jaga IGD, operasi</div>
          </div>
          <div class="p-3.5 rounded-xl bg-white/10 backdrop-blur-sm border border-emerald-400/20">
            <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center mb-2">
              <span class="material-symbols-outlined text-lg">volunteer_activism</span>
            </div>
            <div class="font-bold text-xs text-white">2. Pengabdian</div>
            <div class="text-[10px] text-emerald-200/80 mt-0.5">Bakti sosial &amp; penyuluhan</div>
          </div>
          <div class="p-3.5 rounded-xl bg-white/10 backdrop-blur-sm border border-emerald-400/20">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center mb-2">
              <span class="material-symbols-outlined text-lg">school</span>
            </div>
            <div class="font-bold text-xs text-white">3. Pembelajaran</div>
            <div class="text-[10px] text-emerald-200/80 mt-0.5">PIT, simposium &amp; workshop</div>
          </div>
        </div>

        <!-- Metric Snapshot Bar -->
        <div class="p-4 rounded-2xl bg-black/20 border border-white/10 flex items-center justify-between text-xs">
          <div>
            <div class="text-[10px] text-emerald-300 uppercase font-bold">Cakupan Wilayah</div>
            <div class="text-base font-extrabold text-white mt-0.5">38 Provinsi POGI</div>
          </div>
          <div class="h-8 w-px bg-white/10"></div>
          <div>
            <div class="text-[10px] text-emerald-300 uppercase font-bold">Target Periode</div>
            <div class="text-base font-extrabold text-white mt-0.5">Q4 2026 (8 SKP)</div>
          </div>
          <div class="h-8 w-px bg-white/10"></div>
          <div>
            <div class="text-[10px] text-emerald-300 uppercase font-bold">Integrasi Data</div>
            <div class="text-base font-extrabold text-white mt-0.5">Plataran Sehat</div>
          </div>
        </div>
      </div>

      <!-- Bottom Info -->
      <div class="text-[11px] text-emerald-300/80 pt-4 border-t border-emerald-800/40 relative z-10">
        Sekretariat Pengurus Pusat Perkumpulan Obstetri dan Ginekologi Indonesia &bull; Jakarta Pusat
      </div>
    </div>
  </div>

  <script src="/js/navigation.js"></script>
  <script>
    function togglePassVisibility() {
      const p = document.getElementById('inputPassword');
      if (p) {
        p.type = p.type === 'password' ? 'text' : 'password';
      }
    }
  </script>
</body>
</html>
