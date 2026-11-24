<!DOCTYPE html>
<html lang="id" class="h-full bg-[#021d15]">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 - Akses Terbatas | POGI SKP Tracker</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body class="h-full bg-[#021d15] text-slate-100 flex items-center justify-center p-4">
  <div class="max-w-md w-full bg-slate-900/90 border border-emerald-500/30 rounded-2xl p-6 sm:p-8 text-center shadow-2xl backdrop-blur-md space-y-5">
    <div class="w-16 h-16 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 mx-auto flex items-center justify-center">
      <span class="material-symbols-outlined text-3xl">lock</span>
    </div>

    <div>
      <div class="text-[10px] font-black tracking-widest text-emerald-400 uppercase">MATRIKS RBAC POGI</div>
      <h1 class="text-xl sm:text-2xl font-black text-white mt-1">{{ $message ?? 'Akses Ditolak (403 Forbidden)' }}</h1>
      <p class="text-xs text-slate-400 mt-2 leading-relaxed">
        {{ $detail ?? 'Peran akun Anda tidak memiliki izin untuk mengakses fitur ini sesuai batasan hak akses resmi.' }}
      </p>
    </div>

    <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700/60 text-left text-xs space-y-1.5">
      <div class="flex justify-between text-slate-400 text-[11px]">
        <span>Peran Anda Saat Ini:</span>
        <span class="font-bold text-amber-300 font-mono">{{ $userRole ?? (Auth::user()?->role ?? 'GUEST') }}</span>
      </div>
      @if(isset($requiredRoles))
      <div class="flex justify-between text-slate-400 text-[11px]">
        <span>Wewenang Dibutuhkan:</span>
        <span class="font-bold text-emerald-400 font-mono">{{ $requiredRoles }}</span>
      </div>
      @endif
    </div>

    <div class="pt-2 flex flex-col sm:flex-row gap-2.5">
      <a href="{{ url('/dashboard') }}" class="flex-1 py-2.5 px-4 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-bold transition text-center shadow-sm">
        Kembali ke Dashboard
      </a>
      <a href="{{ url('/login') }}" class="py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition text-center">
        Ganti Akun
      </a>
    </div>
  </div>
</body>
</html>
