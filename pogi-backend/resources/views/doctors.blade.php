@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Dashboard Pemantauan SKP Nasional')
@section('content')

  <!-- Main Body -->
  <div class="space-y-5">

    <!-- Clean Page Header (Modern Enterprise Standard matching Activity Log) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-1">
      <div class="space-y-1.5 min-w-0">
        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[10px] font-black uppercase tracking-wider">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Direktori Tenaga Medis</span>
          <span class="text-slate-300">•</span>
          <span class="text-slate-500 font-semibold normal-case">Integrasi SATUSEHAT</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 flex-wrap">
          <span>Daftar Dokter &amp; Tenaga Kesehatan</span>
          <span id="headingTotalCount" class="text-xs font-mono font-bold text-slate-500 bg-slate-100 border border-slate-200/80 px-2.5 py-0.5 rounded-full">{{ $metrics['total'] ?? count($doctors ?? []) }} Terdaftar</span>
        </h1>
        <p class="text-xs text-slate-500 max-w-3xl leading-relaxed">
          Direktori kredensialing aktif, pemantauan pengurusan perizinan instansi &amp; akses pemenuhan kredit 160 SKP (Pelayanan, Pengabdian, Pembelajaran) terintegrasi SATUSEHAT.
        </p>
      </div>
      @if(in_array(Auth::user()?->role, ['SUPER_ADMIN', 'ADMIN_POGI']))
      <div class="flex items-center gap-2 shrink-0 flex-wrap">
        <button type="button" onclick="openWaBlastModal()" id="btnWaBlastMassal" class="px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs rbac-write rbac-operational cursor-pointer active:scale-95">
          <span class="material-symbols-outlined text-base">campaign</span>
          <span>Kirim WA Blast Massal</span>
        </button>
        <button type="button" onclick="openAddDoctorModal()" id="btnAddDoctor" class="px-3.5 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs rbac-write rbac-operational cursor-pointer active:scale-95">
          <span class="material-symbols-outlined text-base">person_add</span>
          <span>Daftarkan Dokter Baru</span>
        </button>
      </div>
      @endif
    </div>

    <!-- Filter & Search Toolbar (Sesuai Screenshot Klien) -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
        <!-- Search -->
        <div>
          <label class="block font-bold text-slate-700 mb-1">Cari Dokter / No POGI / STR</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
            <input id="searchDoctorInput" onkeyup="applyDoctorFilters()" type="text" placeholder="Ketik nama atau STR..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]" />
          </div>
        </div>

        <!-- Quartal Filter (Dipisah sesuai permintaan) -->
        <div>
          <label class="block font-bold text-slate-700 mb-1">Kuartal</label>
          <select id="quarterFilter" onchange="applyDoctorFilters()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
            <option value="ALL" selected>Semua Kuartal</option>
            <option value="Q4">Q4</option>
            <option value="Q3">Q3</option>
            <option value="Q2">Q2</option>
            <option value="Q1">Q1</option>
            
          </select>
        </div>

        <!-- Tahun Filter (Dipisah sesuai permintaan) -->
        <div>
          <label class="block font-bold text-slate-700 mb-1">Tahun</label>
          <select id="yearFilter" onchange="applyDoctorFilters()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
            <option value="ALL" selected>Semua Tahun</option>
            <option value="2026">2026</option>
            <option value="2025">2025</option>
            <option value="2024">2024</option>
            
          </select>
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block font-bold text-slate-700 mb-1">Status Kepatuhan SKP</label>
          <select id="statusFilter" onchange="applyDoctorFilters()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
            <option value="ALL">Semua Status</option>
            <option value="Complete">Complete</option>
            <option value="Incomplete">Incomplete</option>
          </select>
        </div>

        <!-- Institusi Filter (All Institutions) -->
        <div>
          <label class="block font-bold text-slate-700 mb-1">Institusi / RS Jejaring</label>
          <select id="institutionFilter" onchange="applyDoctorFilters()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
            <option value="ALL">Semua RS Jejaring</option>
            @if(!empty($institutions))
              @foreach($institutions as $inst)
                <option value="{{ $inst->name }}">{{ $inst->name }}</option>
              @endforeach
            @endif
          </select>
        </div>

        <!-- Provinsi Filter (38 Cabang POGI) -->
        <div>
          <label class="block font-bold text-slate-700 mb-1">Cabang POGI / Provinsi</label>
          @if(Auth::user()?->isAdminCabang())
            @php
              $userBranch = \Illuminate\Support\Facades\DB::table('pogi_branches')->where('id', Auth::user()?->branch_id)->first();
              $userProvince = $userBranch ? \Illuminate\Support\Facades\DB::table('provinces')->where('id', $userBranch->province_id)->first() : null;
            @endphp
            <select id="provinceFilter" disabled class="w-full py-2 px-3 bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 cursor-not-allowed">
              <option value="{{ $userProvince?->name ?? 'Cabang Anda' }}">{{ $userBranch?->branch_name ?? 'Cabang Wilayah Anda' }} (Terkunci)</option>
            </select>
          @else
            <select id="provinceFilter" onchange="applyDoctorFilters()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
              <option value="ALL">Semua Provinsi (38 Cabang)</option>
              @if(!empty($provinces))
                @foreach($provinces as $p)
                  <option value="{{ $p->name }}">{{ $p->name }}</option>
                @endforeach
              @endif
            </select>
          @endif
        </div>
      </div>

      <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
        <div>Menampilkan <span id="doctorFilteredCount" class="font-bold text-slate-900">10</span> dokter dari total <span id="doctorTotalFilteredCount" class="font-bold text-slate-900">200</span> anggota</div>
        <button type="button" onclick="resetDoctorFilters()" class="text-[#16b3ac] font-bold hover:underline">Reset Filter</button>
      </div>
    </div>

    <!-- Tabel Dokter Utama (Sesuai Kolom Screenshot Asli) -->
    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
            <tr>
              <th class="py-3 px-4">Name</th>
              <th class="py-3 px-4">SKP</th>
              <th class="py-3 px-4">Pending</th>
              <th class="py-3 px-4">Pengabdian</th>
              <th class="py-3 px-4">Pelayanan</th>
              <th class="py-3 px-4">Pembelajaran</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-4">Last Activity</th>
              <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody id="doctorsTableBody" class="divide-y divide-slate-100 font-medium">
            <!-- Rendered dynamically by JavaScript -->
          </tbody>
        </table>
      </div>

      <!-- Mobile Touch Cards (< md) -->
      <div id="doctorsDirectoryMobileCards" class="md:hidden divide-y divide-slate-100 p-2 sm:p-3 space-y-3">
        <!-- Rendered dynamically for mobile -->
      </div>

      <!-- Interactive MedCred Pagination Footer -->
      <div id="doctorsDirectoryPagination" class="px-4 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
    </div>
  </div>
@endsection

@section('modals')
  <!-- Modal Detail & Verifikasi Dokter -->
  <div id="doctorDetailModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-2.5 min-w-0">
          <div id="modalAvatar" class="w-10 h-10 rounded-xl bg-emerald-100 text-[#16b3ac] font-bold text-sm flex items-center justify-center shrink-0">
            DR
          </div>
          <div class="min-w-0">
            <h3 id="modalDoctorName" class="font-black text-slate-900 text-sm leading-snug tracking-tight truncate">Detail Dokter</h3>
            <p id="modalDoctorMeta" class="text-xs text-slate-500 truncate">-</p>
          </div>
        </div>
        <button type="button" onclick="closeDoctorModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 flex-1 overflow-y-auto space-y-5 text-xs bg-white min-h-0">
        <!-- 3 Subbidang Progress Cards in Modal (KMK 1561/2024 & KMK 1111/2026) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-3.5 rounded-2xl bg-sky-50/70 border border-sky-100/90 shadow-2xs">
            <div class="text-[10px] text-sky-800 font-bold uppercase tracking-wider">1. Pelayanan (35%)</div>
            <div id="modalPelayananScore" class="text-lg font-black text-sky-950 mt-1">0 / 56 SKP</div>
            <div class="text-[10px] text-sky-700/80 font-medium mt-1">Standar Min. 56 SKP</div>
          </div>
          <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100/90 shadow-2xs">
            <div class="text-[10px] text-amber-800 font-bold uppercase tracking-wider">2. Pengabdian (5%)</div>
            <div id="modalPengabdianScore" class="text-lg font-black text-amber-950 mt-1">0 / 8 SKP</div>
            <div class="text-[10px] text-amber-700/80 font-medium mt-1">Standar Min. 8 SKP</div>
          </div>
          <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100/90 shadow-2xs">
            <div class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider">3. Pembelajaran (45%)</div>
            <div id="modalPembelajaranScore" class="text-lg font-black text-emerald-950 mt-1">0 / 72 SKP</div>
            <div class="text-[10px] text-emerald-700/80 font-medium mt-1">Standar Min. 72 SKP</div>
          </div>
        </div>

        <!-- Logbook Entri Menunggu Persetujuan -->
        <div class="space-y-2.5">
          <div class="flex items-center justify-between">
            <h4 class="font-extrabold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-[#16b3ac]"></span>
              <span>Logbook Pengajuan Kredit Terbaru</span>
            </h4>
            <span class="text-[11px] text-amber-700 font-bold bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200/80 shadow-2xs">Menunggu Verifikasi</span>
          </div>
          <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs text-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-slate-900 gap-2">
              <span class="truncate">Sertifikat Workshop Hands-on USG Fetomaternal (POGI Jaya)</span>
              <span class="text-[#16b3ac] font-black shrink-0">+4.5 SKP</span>
            </div>
            <div class="text-[11px] text-slate-500 font-medium">Nomor Registrasi: CERT-POGI-2026-9921 &bull; Tanggal: 14 Nov 2026</div>
            <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
              <button type="button" onclick="openCertificatePreviewModal()" class="text-[11px] text-[#16b3ac] font-bold hover:underline flex items-center gap-1 cursor-pointer">
                <span class="material-symbols-outlined text-xs">visibility</span> Lihat Berkas PDF
              </button>
              <span class="text-slate-300">&bull;</span>
              <button type="button" onclick="approvePendingCredits()" class="text-[11px] text-emerald-700 font-bold hover:underline flex items-center gap-1 rbac-operational cursor-pointer">
                <span class="material-symbols-outlined text-xs">check_circle</span> Setujui &amp; Tambahkan ke Akumulasi
              </button>
            </div>
          </div>
        </div>

        <!-- Tindak Lanjut Follow-Up -->
        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 shadow-2xs text-xs space-y-2.5">
          <div class="font-extrabold text-[#16b3ac] text-xs flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">notifications_active</span>
            <span>Kirim Pengingat Langsung ke Dokter / RS:</span>
          </div>
          <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="sendDoctorReminder('wa')" class="px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95">
              <span class="material-symbols-outlined text-sm">chat</span> Hubungi via WhatsApp
            </button>
            <button type="button" onclick="sendDoctorReminder('email')" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs cursor-pointer active:scale-95">
              <span class="material-symbols-outlined text-sm">mail</span> Kirim Email Resmi POGI
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Footer (Sticky) -->
      <div class="p-4 px-6 flex items-center justify-between border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
        <button type="button" onclick="openDoctorRepositoryPage()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
          <span class="material-symbols-outlined text-sm">cloud_done</span>
          <span>Repositori Dokumen Cloud</span>
        </button>
        <button type="button" onclick="closeDoctorModal()" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold transition cursor-pointer">Tutup</button>
      </div>
    </div>
  </div>

  <!-- Modal Pratinjau Sertifikat PDF -->
  <div id="certificateModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-2.5">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#16b3ac] flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-xl">picture_as_pdf</span>
          </div>
          <div>
            <h3 class="font-black text-slate-900 text-sm">Pratinjau Sertifikat Resmi</h3>
            <p class="text-xs text-slate-500">Terakreditasi SKP Kemenkes RI</p>
          </div>
        </div>
        <button type="button" onclick="closeCertificatePreviewModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 flex-1 overflow-y-auto space-y-4 text-center bg-white min-h-0">
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-2xs space-y-3.5">
          <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-50 border border-slate-200/80 p-1 flex items-center justify-center shadow-2xs">
            <img src="logo.png" alt="Logo POGI" class="w-full h-full object-contain">
          </div>
          <div>
            <div class="text-[10px] uppercase font-black tracking-widest text-[#16b3ac]">PERKUMPULAN OBSTETRI DAN GINEKOLOGI INDONESIA</div>
            <h4 class="font-black text-sm text-slate-900 mt-1">SERTIFIKAT AKREDITASI ILMIAH</h4>
            <p class="text-xs text-slate-600 mt-1">Diberikan atas partisipasi aktif dalam:<br><strong class="text-slate-900">Workshop Hands-on USG Fetomaternal</strong></p>
          </div>
          <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-xs text-emerald-800 font-bold inline-block shadow-2xs">
            Nilai Kredit: +4.5 SKP Subbidang Pembelajaran &bull; Terverifikasi Plataran Sehat
          </div>
          <div class="text-[10px] text-slate-400 font-mono">
            Nomor Registrasi: CERT-POGI-2026-9921 &bull; Terbit: 14 Nov 2026
          </div>
        </div>
      </div>

      <!-- Modal Footer (Sticky) -->
      <div class="p-4 px-6 flex items-center justify-between border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
        <button type="button" onclick="closeCertificatePreviewModal()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">Tutup</button>
        <button type="button" onclick="showToast('Mengunduh salinan resmi berkas PDF...', 'success'); closeCertificatePreviewModal();" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold flex items-center gap-1.5 shadow-xs transition cursor-pointer">
          <span class="material-symbols-outlined text-sm">download</span>
          <span>Unduh Berkas PDF</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Dokter Baru -->
  <div id="addDoctorModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#16b3ac] flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-xl">person_add</span>
          </div>
          <div class="min-w-0">
            <h3 class="font-black text-slate-900 text-sm leading-snug tracking-tight truncate">Daftarkan Dokter Baru</h3>
            <p class="text-xs text-slate-500 truncate">Lengkapi profil dokter spesialis &amp; kredensial LMS Plataran</p>
          </div>
        </div>
        <button type="button" onclick="closeAddDoctorModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <form id="addDoctorForm" onsubmit="event.preventDefault(); submitNewDoctorForm();" class="flex flex-col flex-1 min-h-0 overflow-hidden">
        <!-- Modal Body (Scrollable) -->
        <div class="p-6 flex-1 overflow-y-auto space-y-5 text-xs bg-white min-h-0">
          <!-- Section 1: Informasi & Kredensial Dokter -->
          <div class="space-y-3.5">
            <div class="flex items-center gap-2 pb-1.5 border-b border-slate-200/60">
              <span class="w-6 h-6 rounded-lg bg-emerald-50 border border-emerald-100/60 text-[#16b3ac] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[14px]">stethoscope</span>
              </span>
              <h4 class="font-extrabold text-slate-800 text-xs tracking-tight">Informasi & Kredensial Dokter</h4>
            </div>

            <!-- Name -->
            <div>
              <label class="block font-bold text-slate-700 mb-1.5">Nama Lengkap Dokter <span class="text-rose-500 font-extrabold">*</span></label>
              <input id="newDocName" type="text" required placeholder="dr. Budi Santoso, Sp.OG" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
            </div>

            <!-- STR & Phone (2 Cols) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">Nomor STR</label>
                <input id="newDocStr" type="text" placeholder="32.73.100.2.21.0883" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-mono font-semibold text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">Nomor Telepon (WhatsApp) <span class="text-rose-500 font-extrabold">*</span></label>
                <input id="newDocPhone" type="text" required placeholder="08123456789" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-mono font-semibold text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
                <p class="text-[11px] text-slate-400 mt-1 font-medium">Awalan 0 otomatis tersimpan sebagai +62.</p>
              </div>
            </div>

            <!-- Province & Branch (2 Cols) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">Provinsi / Wilayah Cabang <span class="text-rose-500 font-extrabold">*</span></label>
                <select id="newDocProv" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
                  <option value="">Pilih provinsi...</option>
                </select>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">Cabang POGI <span class="text-rose-500 font-extrabold">*</span></label>
                <select id="newDocBranch" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
                  <option value="">Pilih Cabang POGI...</option>
                  @foreach($branches ?? [] as $b)
                    <option value="{{ $b->id }}">{{ $b->branch_name ?? $b->name ?? '' }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <!-- RS & PIC Admin (2 Cols) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">RS / Faskes Utama <span class="text-rose-500 font-extrabold">*</span></label>
                <select id="newDocInst" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
                  <option value="">Pilih institusi / RS...</option>
                </select>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">PIC Admin / Dokter Binaan</label>
                <select id="newDocAssignedTo" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
                  @forelse($adminPogiList ?? [] as $adm)
                    <option value="{{ $adm->full_name }}">{{ $adm->full_name }} (Admin POGI)</option>
                  @empty
                    <option value="">Belum ada Admin POGI</option>
                  @endforelse
                </select>
              </div>
            </div>

            <!-- Email & Status Dokter (2 Cols) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">Email <span class="text-rose-500 font-extrabold">*</span></label>
                <div class="relative">
                  <input id="newDocEmail" type="email" required placeholder="dokter@pogi.or.id" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
                  <span class="material-symbols-outlined text-slate-400 text-[18px] absolute left-2.5 top-2.5 pointer-events-none">mail</span>
                </div>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">Status Dokter (Master Data) <span class="text-rose-500 font-extrabold">*</span></label>
                <select id="newDocStatus" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
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
                  <input id="newDocPeriodStart" type="text" required value="1 Jan 2026" placeholder="1 Jan 2026" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
                  <span class="material-symbols-outlined text-slate-400 text-[18px] absolute left-2.5 top-2.5 pointer-events-none">calendar_today</span>
                </div>
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">Periode Berakhir SKP <span class="text-rose-500 font-extrabold">*</span></label>
                <div class="relative">
                  <input id="newDocPeriodEnd" type="text" required value="31 Des 2030" placeholder="31 Des 2030" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
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
                <input id="newDocLmsUsername" type="text" placeholder="super@example.com" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-sky-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
              </div>
              <div>
                <label class="block font-bold text-slate-700 mb-1.5">Password LMS</label>
                <input id="newDocLmsPassword" type="password" placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 shadow-2xs transition hover:border-sky-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15" />
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions (Sticky) -->
        <div class="p-4 px-6 flex items-center justify-between border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
          <button type="button" onclick="closeAddDoctorModal()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold text-xs transition shadow-xs flex items-center gap-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-sm">person_add</span>
            <span>Simpan Dokter</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal WA Blast Massal (Terhubung langsung ke wa_blast_campaigns & wa_message_queue) -->
  <div id="waBlastModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#16b3ac] flex items-center justify-center font-bold">
            <span class="material-symbols-outlined text-xl">campaign</span>
          </div>
          <div class="min-w-0">
            <h3 class="font-black text-slate-900 text-sm leading-snug tracking-tight truncate">WhatsApp Blast Massal</h3>
            <p class="text-xs text-slate-500 truncate">Otomasi pengingat pemenuhan standar 160 SKP kepada dokter</p>
          </div>
        </div>
        <button type="button" onclick="closeWaBlastModal()" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 flex-1 overflow-y-auto space-y-5 text-xs bg-white min-h-0">
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs space-y-3">
          <div class="flex items-center justify-between gap-2">
            <label class="font-extrabold text-slate-800 text-xs">1. Sasaran Penerima Blast</label>
            <span id="waBlastRecipientBadge" class="px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-[#16b3ac] font-black text-[11px] shadow-2xs">
              {{ $metrics['incomplete'] ?? 0 }} Dokter Incomplete
            </span>
          </div>
          <select id="waBlastTargetSelect" onchange="updateWaBlastRecipientCount()" class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 rounded-xl text-xs font-bold text-slate-800 shadow-2xs transition hover:border-slate-300 focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">
            <option value="all_incomplete" selected>Semua Dokter Status Incomplete (Nasional)</option>
            <option value="lowest_5">5 Cabang POGI dengan Kepatuhan Terendah</option>
          </select>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-2xs space-y-2.5">
          <label class="font-extrabold text-slate-800 text-xs block">2. Saluran Pengiriman</label>
          <div class="flex flex-wrap gap-4">
            <label class="inline-flex items-center gap-2 font-bold text-slate-700 cursor-pointer">
              <input type="checkbox" id="waBlastChWhatsapp" checked class="w-4 h-4 rounded text-[#16b3ac] border-slate-300 focus:ring-[#16b3ac]" />
              <span>WhatsApp Official</span>
            </label>
            <label class="inline-flex items-center gap-2 font-bold text-slate-700 cursor-pointer">
              <input type="checkbox" id="waBlastChEmail" class="w-4 h-4 rounded text-[#16b3ac] border-slate-300 focus:ring-[#16b3ac]" />
              <span>Email Resmi POGI</span>
            </label>
          </div>
        </div>

        <div class="space-y-2">
          <label class="font-extrabold text-slate-800 text-xs block">3. Template Pesan Pengingat</label>
          <textarea id="waBlastMessageInput" rows="4" class="w-full p-3.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 leading-relaxed shadow-2xs transition hover:border-slate-300 focus:bg-white focus:outline-none focus:border-[#16b3ac] focus:ring-3 focus:ring-[#16b3ac]/15">Yth. [Nama Dokter] (Cabang [Cabang] - [Provinsi]), kami mengingatkan agar segera melengkapi bukti berkas SKP (tautan Google Drive) pada portal POGI SKP Tracker sebelum jadwal cutoff bulanan.</textarea>
        </div>
      </div>

      <!-- Modal Footer (Sticky) -->
      <div class="p-4 px-6 flex items-center justify-between border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
        <button type="button" onclick="closeWaBlastModal()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">Batal</button>
        <button type="button" id="btnSubmitWaBlast" onclick="submitWaBlast()" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold text-xs transition shadow-xs flex items-center gap-1.5 cursor-pointer">
          <span class="material-symbols-outlined text-sm">send</span>
          <span>Kirim ke Antrean WA Blast</span>
        </button>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="/js/pdf_exporter.js"></script>
  <script>
    
    // MedCred Standard Pagination Component
    function renderMedCredPaginationControls({ containerId, currentPage, totalPages, totalItems, startIdx, endIdx, itemLabel, onPageChangeFn, onLimitChangeFn, currentLimit, limits = [10, 25, 50] }) {
      const el = document.getElementById(containerId);
      if (!el) return;

      if (totalItems === 0) {
        el.innerHTML = '<div class="text-slate-400 text-xs py-1">Tidak ada data untuk ditampilkan</div>';
        return;
      }

      let btnsHtml = '';

      // Prev Button
      const isFirst = currentPage <= 1;
      btnsHtml += `
        <button type="button" onclick="${onPageChangeFn}(${currentPage - 1})" ${isFirst ? 'disabled class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-300 text-xs font-bold cursor-not-allowed flex items-center gap-1"' : 'class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold flex items-center gap-1 transition shadow-2xs"'}>
          <span class="material-symbols-outlined text-sm">chevron_left</span>
          <span>Sebelumnya</span>
        </button>
      `;

      // Page numbers with smart window
      const maxButtons = 5;
      let startP = Math.max(1, currentPage - 2);
      let endP = Math.min(totalPages, startP + maxButtons - 1);
      if (endP - startP < maxButtons - 1) {
        startP = Math.max(1, endP - maxButtons + 1);
      }

      if (startP > 1) {
        btnsHtml += `<button type="button" onclick="${onPageChangeFn}(1)" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">1</button>`;
        if (startP > 2) {
          btnsHtml += `<span class="px-1 text-slate-400 font-bold self-center">...</span>`;
        }
      }

      for (let p = startP; p <= endP; p++) {
        const isActive = (p === currentPage);
        btnsHtml += `
          <button type="button" onclick="${onPageChangeFn}(${p})" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center ${isActive ? 'bg-[#16b3ac] text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}">
            ${p}
          </button>
        `;
      }

      if (endP < totalPages) {
        if (endP < totalPages - 1) {
          btnsHtml += `<span class="px-1 text-slate-400 font-bold self-center">...</span>`;
        }
        btnsHtml += `<button type="button" onclick="${onPageChangeFn}(${totalPages})" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">${totalPages}</button>`;
      }

      // Next Button
      const isLast = currentPage >= totalPages;
      btnsHtml += `
        <button type="button" onclick="${onPageChangeFn}(${currentPage + 1})" ${isLast ? 'disabled class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-300 text-xs font-bold cursor-not-allowed flex items-center gap-1"' : 'class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold flex items-center gap-1 transition shadow-2xs"'}>
          <span>Selanjutnya</span>
          <span class="material-symbols-outlined text-sm">chevron_right</span>
        </button>
      `;

      let limitSelectorHtml = '';
      if (onLimitChangeFn && limits && limits.length > 0) {
        limitSelectorHtml = `
          <div class="flex items-center gap-1.5 ml-0 sm:ml-2">
            <span class="text-slate-400 text-[11px]">Tampilkan:</span>
            <select onchange="${onLimitChangeFn}(this.value)" class="py-1 px-2 border border-slate-200 rounded-lg bg-white text-slate-700 text-xs font-bold focus:outline-none">
              ${limits.map(l => `<option value="${l}" ${l === currentLimit ? 'selected' : ''}>${l}</option>`).join('')}
            </select>
          </div>
        `;
      }

      el.innerHTML = `
        <div class="flex flex-wrap items-center gap-2 text-slate-500 font-medium text-xs">
          <div>
            Menampilkan <strong class="text-slate-900">${startIdx} — ${endIdx}</strong> dari <strong class="text-slate-900">${totalItems}</strong> ${itemLabel}
          </div>
          ${limitSelectorHtml}
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
          ${btnsHtml}
        </div>
      `;
    }


    const LIVE_DATABASE_DOCTORS = @json($doctors ?? []);
    const LIVE_SERVER_PROVINCES = @json($provinces ?? []);
    const LIVE_SERVER_INSTITUTIONS = @json($institutions ?? []);
    window.LIVE_DATABASE_DOCTORS = LIVE_DATABASE_DOCTORS;
    window.LIVE_SERVER_DOCTORS = LIVE_DATABASE_DOCTORS;
    window.LIVE_SERVER_PROVINCES = LIVE_SERVER_PROVINCES;
    window.LIVE_SERVER_INSTITUTIONS = LIVE_SERVER_INSTITUTIONS;
    let activeDoctorId = null;
    let doctorPagination = { page: 1, limit: 10, total: LIVE_DATABASE_DOCTORS.length || 0 };
    let currentFilteredDoctors = null;
    let currentPipelineFilter = 'all';

    document.addEventListener('DOMContentLoaded', () => {
      // Inisialisasi dataset dokter langsung dari basis data live
      const allDoctors = LIVE_DATABASE_DOCTORS || [];
      const instSelect = document.getElementById('institutionFilter');
      if (instSelect && instSelect.options.length <= 1) {
        populateInstitutionFilter(allDoctors);
      }
      initDoctorSelect2();
      applyDoctorFilters();
    });

    function initDoctorSelect2() {
      if (window.jQuery && $.fn.select2) {
        $('#quarterFilter, #yearFilter, #statusFilter, #institutionFilter, #provinceFilter').select2({
          width: '100%'
        }).on('change', function() {
          applyDoctorFilters();
        });

        $('#newDocProv, #newDocInst, #newDocAssignedTo, #newDocBranch, #newDocStatus').select2({
          dropdownParent: $('#addDoctorModal'),
          width: '100%'
        });

        $('#waBlastTargetSelect').select2({
          dropdownParent: $('#waBlastModal'),
          width: '100%'
        }).on('change', function() {
          updateWaBlastRecipientCount();
        });
      }
    }

    function populateInstitutionFilter(doctors) {
      const select = document.getElementById('institutionFilter');
      if (!select) return;
      const currentVal = select.value || 'ALL';
      const instSet = new Set();
      (doctors || LIVE_DATABASE_DOCTORS || []).forEach(d => {
        if (d.hospital) instSet.add(d.hospital.trim());
        if (d.institution) instSet.add(d.institution.trim());
      });
      const sorted = Array.from(instSet).filter(Boolean).sort((a, b) => a.localeCompare(b));
      let html = '<option value="ALL">Semua RS Jejaring</option>';
      sorted.forEach(name => {
        html += `<option value="${name}">${name}</option>`;
      });
      select.innerHTML = html;
      if (currentVal && Array.from(select.options).some(o => o.value === currentVal)) {
        select.value = currentVal;
      }
      if (window.jQuery && $.fn.select2) {
        $(select).trigger('change.select2');
      }
    }

    function setDoctorStatusPipeline(status) {
      currentPipelineFilter = status;
      ['all', 'complete', 'incomplete', 'pending'].forEach(tab => {
        const btn = document.getElementById('pipe-tab-' + tab);
        if (btn) {
          if (tab === status) {
            btn.className = 'px-3 py-1 rounded-lg bg-white text-slate-900 shadow-2xs transition font-bold';
          } else {
            btn.className = 'px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition font-bold';
          }
        }
      });
      applyDoctorFilters();
    }

    function changeDoctorPage(p) {
      const list = currentFilteredDoctors || LIVE_DATABASE_DOCTORS || [];
      const totalPages = Math.max(1, Math.ceil(list.length / doctorPagination.limit));
      if (p < 1) p = 1;
      if (p > totalPages) p = totalPages;
      doctorPagination.page = p;
      renderDoctorTable(currentFilteredDoctors, false);
      const el = document.getElementById('doctorsDirectoryTableWrapper') || document.getElementById('doctorsDirectoryTableBody');
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function setDoctorPageLimit(limit) {
      doctorPagination.limit = parseInt(limit, 10) || 10;
      doctorPagination.page = 1;
      renderDoctorTable(currentFilteredDoctors, false);
    }

    function formatNumberID(num) {
      if (num === null || num === undefined || isNaN(num)) return '0';
      const parsed = parseFloat(num);
      if (Number.isInteger(parsed)) return parsed.toString();
      return parsed.toString().replace('.', ',');
    }

    function renderDomainCell(achieved, target) {
      const ach = +(achieved || 0);
      const tgt = +(target || 0);
      const isDeficit = tgt > 0 && ach < tgt;
      const achStr = formatNumberID(ach);
      const tgtStr = formatNumberID(tgt);
      const achColor = isDeficit ? 'text-red-500 font-bold' : 'text-slate-800 font-semibold';
      return `<span class="${achColor}">${achStr}</span> <span class="text-slate-400 font-normal">/</span> ${tgtStr}`;
    }

    function calculateDoctorStats(d) {
      const isComp = (d.status === 'Complete' || d.status === 'COMPLETE' || d.rawStatus === 'COMPLETE');
      const achieved = Number(d.achievedCredits !== undefined ? d.achievedCredits : (d.q4Credits || 0));
      const target = Number(d.targetCredits !== undefined ? d.targetCredits : 160.0);
      const pending = Number(d.pendingCredits || 0);
      const pel = Number(d.pelayananCredits || 0);
      const tgtPel = Number(d.targetPelayananCredits || 56.0);
      const peng = Number(d.pengabdianCredits || 0);
      const tgtPeng = Number(d.targetPengabdianCredits || 8.0);
      const pemb = Number(d.pembelajaranCredits || 0);
      const tgtPemb = Number(d.targetPembelajaranCredits || 72.0);

      let redWidth = 0;
      let yellowWidth = 0;
      if (!isComp) {
        const tgt = Math.max(0.1, target);
        redWidth = Math.min(100, Math.round((achieved / tgt) * 100));
        yellowWidth = Math.min(100 - redWidth, Math.round((pending / tgt) * 100));
      }

      return {
        achieved,
        target,
        redPercent: redWidth,
        yellowPercent: yellowWidth,
        isGreen: isComp,
        isComplete: isComp,
        pendingCredits: pending,
        pengabdian: { achieved: peng, target: tgtPeng },
        pelayanan: { achieved: pel, target: tgtPel },
        pembelajaran: { achieved: pemb, target: tgtPemb },
        lastActivity: d.lastActivity || 'Baru saja'
      };
    }

    function renderDoctorTable(customList, resetPage = true) {
      if (resetPage) doctorPagination.page = 1;
      const allDoctors = customList || LIVE_DATABASE_DOCTORS || [];
      currentFilteredDoctors = allDoctors;
      doctorPagination.total = allDoctors.length;

      const totalPages = Math.max(1, Math.ceil(doctorPagination.total / doctorPagination.limit));
      if (doctorPagination.page > totalPages) doctorPagination.page = totalPages;
      if (doctorPagination.page < 1) doctorPagination.page = 1;

      const start = (doctorPagination.page - 1) * doctorPagination.limit;
      const end = Math.min(start + doctorPagination.limit, doctorPagination.total);
      const pageDoctors = allDoctors.slice(start, end);

      const tbody = document.getElementById('doctorsDirectoryTableBody') || document.getElementById('doctorsTableBody');
      const mobileContainer = document.getElementById('doctorsDirectoryMobileCards');
      
      const filterSummaryRange = document.getElementById('filterSummaryRange');
      const filterSummaryTotal = document.getElementById('filterSummaryTotal');
      const headingTotalCount = document.getElementById('headingTotalCount');
      const doctorFilteredCount = document.getElementById('doctorFilteredCount');
      const doctorTotalFilteredCount = document.getElementById('doctorTotalFilteredCount');
      if (filterSummaryRange) filterSummaryRange.innerText = (doctorPagination.total === 0 ? '0' : `${start + 1} — ${end}`);
      if (filterSummaryTotal) filterSummaryTotal.innerText = doctorPagination.total;
      if (headingTotalCount) headingTotalCount.innerText = `${doctorPagination.total} Terdaftar`;
      if (doctorFilteredCount) doctorFilteredCount.innerText = pageDoctors.length;
      if (doctorTotalFilteredCount) doctorTotalFilteredCount.innerText = doctorPagination.total;

      const qVal = document.getElementById('quarterFilter') ? document.getElementById('quarterFilter').value : 'ALL';
      const yVal = document.getElementById('yearFilter') ? document.getElementById('yearFilter').value : 'ALL';
      let activePeriod = 'Q4 2026';
      if (qVal === 'ALL' && yVal === 'ALL') {
        activePeriod = 'Q4 2026';
      } else if (qVal === 'ALL' && yVal !== 'ALL') {
        activePeriod = yVal;
      } else if (qVal !== 'ALL' && yVal === 'ALL') {
        activePeriod = `${qVal} 2026`;
      } else {
        activePeriod = `${qVal} ${yVal}`;
      }

      if (pageDoctors.length === 0) {
        if (tbody) {
          tbody.innerHTML = `
            <tr>
              <td colspan="9" class="py-14 px-4 text-center">
                <div class="flex flex-col items-center justify-center gap-2.5 max-w-sm mx-auto">
                  <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-400 flex items-center justify-center shadow-2xs">
                    <span class="material-symbols-outlined text-2xl">person_off</span>
                  </div>
                  <div class="font-bold text-sm text-slate-800">Tidak Ada Data Dokter</div>
                  <p class="text-xs text-slate-500 leading-relaxed">
                    Tidak ditemukan dokter yang sesuai dengan kriteria filter atau pencarian saat ini.
                  </p>
                  <button type="button" onclick="openAddDoctorModal()" class="mt-1 px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] border border-emerald-200 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer rbac-write">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    <span>Tambah Dokter Baru</span>
                  </button>
                </div>
              </td>
            </tr>
          `;
        }
        if (mobileContainer) {
          mobileContainer.innerHTML = `
            <div class="py-12 px-4 text-center">
              <div class="flex flex-col items-center justify-center gap-2.5 max-w-xs mx-auto">
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 flex items-center justify-center shadow-2xs">
                  <span class="material-symbols-outlined text-xl">person_off</span>
                </div>
                <div class="font-bold text-xs text-slate-800">Tidak Ada Data Dokter</div>
                <p class="text-[11px] text-slate-500">Tidak ada dokter yang cocok dengan filter saat ini.</p>
                <button type="button" onclick="openAddDoctorModal()" class="mt-1 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] border border-emerald-200 font-bold text-xs transition inline-flex items-center gap-1 shadow-2xs cursor-pointer rbac-write">
                  <span class="material-symbols-outlined text-xs">person_add</span>
                  <span>Tambah Dokter Baru</span>
                </button>
              </div>
            </div>
          `;
        }
      } else {
        // Desktop / Tablet Rows (9 Kolom sesuai referensi gambar)
        if (tbody) {
          tbody.innerHTML = pageDoctors.map(d => {
            const stats = calculateDoctorStats(d);
            const redWidth = stats.redPercent;
            const yellowWidth = stats.yellowPercent;
            const pendingVal = stats.pendingCredits;
            const lastAct = stats.lastActivity;

            return `
              <tr class="hover:bg-slate-50/90 transition cursor-pointer" onclick="openDoctorDetail('${d.id}')">
                <!-- 1. Name -->
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900 text-xs">${d.name}</div>
                  <div class="text-[10px] text-slate-400 font-mono mt-0.5 flex items-center gap-1.5">
                    <span>${d.nik || '-'}</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-indigo-600 font-semibold font-sans" title="PIC Admin Binaan">PIC: ${d.assignedTo || 'Belum Ditentukan'}</span>
                  </div>
                </td>
                <!-- 2. SKP -->
                <td class="py-3 px-4 w-44">
                  <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden flex mb-1.5">
                    ${stats.isComplete
                      ? `<div class="bg-emerald-600 h-1.5 rounded-full w-full"></div>`
                      : (redWidth > 0 || yellowWidth > 0)
                        ? `<div class="bg-red-500 h-1.5 rounded-l-full" style="width: ${redWidth}%"></div>
                           <div class="bg-amber-400 h-1.5 ${redWidth === 0 ? 'rounded-l-full' : ''} rounded-r-full" style="width: ${yellowWidth}%"></div>`
                        : `<div class="h-1.5 w-full"></div>`
                    }
                  </div>
                  <div class="text-xs font-semibold text-slate-800">
                    ${formatNumberID(stats.achieved)} <span class="text-slate-400 font-normal">/</span> ${formatNumberID(stats.target)}
                  </div>
                </td>
                <!-- 3. Pending -->
                <td class="py-3 px-4">
                  ${pendingVal > 0
                    ? `<span class="text-amber-500 font-bold">${formatNumberID(pendingVal)}</span>`
                    : `<span class="text-slate-400">—</span>`
                  }
                </td>
                <!-- 4. Pengabdian -->
                <td class="py-3 px-4">
                  ${renderDomainCell(stats.pengabdian.achieved, stats.pengabdian.target)}
                </td>
                <!-- 5. Pelayanan -->
                <td class="py-3 px-4">
                  ${renderDomainCell(stats.pelayanan.achieved, stats.pelayanan.target)}
                </td>
                <!-- 6. Pembelajaran -->
                <td class="py-3 px-4">
                  ${renderDomainCell(stats.pembelajaran.achieved, stats.pembelajaran.target)}
                </td>
                <!-- 7. Status -->
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold ${stats.isComplete ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                    ${stats.isComplete ? 'Complete' : 'Incomplete'}
                  </span>
                </td>
                <!-- 8. Last Activity -->
                <td class="py-3 px-4 text-slate-600 text-xs">
                  ${lastAct}
                </td>
                <!-- 9. Aksi -->
                <td class="py-3 px-4 text-center">
                  <button type="button" onclick="event.stopPropagation(); openDoctorDetail('${d.id}')" title="Detail Dokter" class="text-slate-400 hover:text-slate-700 font-extrabold text-base tracking-widest px-2 py-1 rounded hover:bg-slate-100 transition">
                    &hellip;
                  </button>
                </td>
              </tr>
            `;
          }).join('');
        }

        // Mobile Touch Cards (< md)
        if (mobileContainer) {
          mobileContainer.innerHTML = pageDoctors.map(d => {
            const stats = calculateDoctorStats(d);
            const pendingVal = stats.pendingCredits;
            const lastAct = stats.lastActivity;

            return `
              <div onclick="openDoctorDetail('${d.id}')" class="p-3.5 rounded-xl bg-white border border-slate-200/90 shadow-2xs space-y-3 cursor-pointer hover:border-emerald-300 transition">
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <div class="font-bold text-slate-900 text-xs leading-snug">${d.name}</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">${d.nik || '-'}</div>
                  </div>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ${stats.isComplete ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'} shrink-0">
                    ${stats.isComplete ? 'Complete' : 'Incomplete'}
                  </span>
                </div>

                <!-- Domain Credits Grid -->
                <div class="grid grid-cols-3 gap-2 p-2 rounded-lg bg-slate-50 border border-slate-100 text-[11px]">
                  <div>
                    <span class="block text-[9px] text-slate-400 uppercase font-bold">Pengabdian</span>
                    ${renderDomainCell(stats.pengabdian.achieved, stats.pengabdian.target)}
                  </div>
                  <div>
                    <span class="block text-[9px] text-slate-400 uppercase font-bold">Pelayanan</span>
                    ${renderDomainCell(stats.pelayanan.achieved, stats.pelayanan.target)}
                  </div>
                  <div>
                    <span class="block text-[9px] text-slate-400 uppercase font-bold">Pembelajaran</span>
                    ${renderDomainCell(stats.pembelajaran.achieved, stats.pembelajaran.target)}
                  </div>
                </div>

                <!-- SKP & Pending -->
                <div class="flex items-center justify-between text-xs pt-1">
                  <div>
                    <span class="text-[10px] text-slate-400">SKP:</span>
                    <span class="font-bold text-slate-800 ml-1">${formatNumberID(stats.achieved)} / ${formatNumberID(stats.target)}</span>
                  </div>
                  <div>
                    <span class="text-[10px] text-slate-400">Pending:</span>
                    <span class="font-bold text-amber-500 ml-1">${pendingVal > 0 ? formatNumberID(pendingVal) : '—'}</span>
                  </div>
                </div>

                <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-100 flex items-center justify-between">
                  <span>Last Activity: ${lastAct}</span>
                  <span class="text-emerald-700 font-bold flex items-center gap-0.5">Detail &rarr;</span>
                </div>
              </div>
            `;
          }).join('');
        }
      }

      // Render Pagination
      renderMedCredPaginationControls({
        containerId: 'doctorsDirectoryPagination',
        currentPage: doctorPagination.page,
        totalPages: totalPages,
        totalItems: doctorPagination.total,
        startIdx: doctorPagination.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: `dokter (${doctorPagination.total} terdata nasional)`,
        onPageChangeFn: 'changeDoctorPage',
        onLimitChangeFn: 'setDoctorPageLimit',
        currentLimit: doctorPagination.limit,
        limits: [10, 25, 50, 200]
      });
    }

    function applyDoctorFilters() {
      const q = (document.getElementById('searchDoctorInput') ? document.getElementById('searchDoctorInput').value : '').toLowerCase().trim();
      const prov = document.getElementById('provinceFilter') ? document.getElementById('provinceFilter').value : 'ALL';
      const inst = document.getElementById('institutionFilter') ? document.getElementById('institutionFilter').value : 'ALL';
      const status = document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : 'ALL';
      const qVal = document.getElementById('quarterFilter') ? document.getElementById('quarterFilter').value : 'ALL';
      const yVal = document.getElementById('yearFilter') ? document.getElementById('yearFilter').value : 'ALL';
      let activePeriod = 'Q4 2026';
      if (qVal === 'ALL' && yVal === 'ALL') {
        activePeriod = 'Q4 2026';
      } else if (qVal === 'ALL' && yVal !== 'ALL') {
        activePeriod = yVal;
      } else if (qVal !== 'ALL' && yVal === 'ALL') {
        activePeriod = `${qVal} 2026`;
      } else {
        activePeriod = `${qVal} ${yVal}`;
      }

      let list = [...(LIVE_DATABASE_DOCTORS || [])];

      const currentUser = (typeof PogiStore !== 'undefined') ? PogiStore.getCurrentUser() : null;
      if (currentUser && currentUser.role === 'ADMIN_CABANG' && currentUser.branch && currentUser.branch !== 'Nasional') {
        const provFilter = document.getElementById('provinceFilter');
        if (provFilter) {
          provFilter.value = currentUser.branch;
          provFilter.setAttribute('disabled', 'true');
        }
        const headCount = document.getElementById('headingTotalCount');
        if (headCount) {
          headCount.innerText = `${list.length} Terdaftar (Cabang ${currentUser.branch})`;
        }
      } else if (currentUser && (currentUser.role === 'DOKTER' || currentUser.role === 'USER/DOKTER' || currentUser.role === 'USER')) {
        const provFilter = document.getElementById('provinceFilter');
        if (provFilter) provFilter.setAttribute('disabled', 'true');
        const headCount = document.getElementById('headingTotalCount');
        if (headCount) {
          headCount.innerText = `1 Dokter (Portofolio Mandiri Saya)`;
        }
      } else if (currentUser && (currentUser.role === 'ADMIN_PUSAT' || currentUser.role === 'ADMIN PUSAT')) {
        const headCount = document.getElementById('headingTotalCount');
        if (headCount) {
          headCount.innerText = `${list.length} Terdaftar (Mode Audit Nasional)`;
        }
      }

      // Pipeline status filter
      if (currentPipelineFilter === 'complete') {
        list = list.filter(d => (d.status === 'Complete' || d.status === 'COMPLETE' || d.rawStatus === 'COMPLETE'));
      } else if (currentPipelineFilter === 'incomplete') {
        list = list.filter(d => (d.status !== 'Complete' && d.status !== 'COMPLETE' && d.rawStatus !== 'COMPLETE'));
      } else if (currentPipelineFilter === 'pending') {
        list = list.filter(d => Number(d.pendingCredits || 0) > 0);
      }

      // Toolbar filters
      if (q) {
        list = list.filter(d =>
          d.name.toLowerCase().includes(q) ||
          (d.pogiId && d.pogiId.toLowerCase().includes(q)) ||
          (d.nik && d.nik.includes(q)) ||
          (d.hospital && d.hospital.toLowerCase().includes(q))
        );
      }

      if (prov && prov !== 'ALL') {
        list = list.filter(d => d.province && (d.province.toLowerCase() === prov.toLowerCase() || d.province.toLowerCase().includes(prov.toLowerCase())));
      }

      if (inst && inst !== 'ALL') {
        list = list.filter(d => (d.hospital && d.hospital === inst) || (d.institution && d.institution === inst));
      }

      if (status && status !== 'ALL') {
        list = list.filter(d => {
          const isComp = (d.status === 'Complete' || d.status === 'COMPLETE' || d.rawStatus === 'COMPLETE');
          return status === 'Complete' ? isComp : !isComp;
        });
      }

      renderDoctorTable(list, true);
    }

    function resetDoctorFilters() {
      if (document.getElementById('searchDoctorInput')) document.getElementById('searchDoctorInput').value = '';
      if (document.getElementById('quarterFilter')) document.getElementById('quarterFilter').value = 'ALL';
      if (document.getElementById('yearFilter')) document.getElementById('yearFilter').value = 'ALL';
      if (document.getElementById('institutionFilter')) document.getElementById('institutionFilter').value = 'ALL';
      if (document.getElementById('provinceFilter')) document.getElementById('provinceFilter').value = 'ALL';
      if (document.getElementById('statusFilter')) document.getElementById('statusFilter').value = 'ALL';
      currentPipelineFilter = 'all';
      setDoctorStatusPipeline('all');
    }

    function exportDoctorsPdf() {
      if (typeof window.PogiPdfExporter !== 'undefined') {
        window.exportDoctorsPdf(currentFilteredDoctors);
      }
    }
    const exportDoctorsCsv = exportDoctorsPdf;

    function openDoctorDetail(docId) {
      window.location.href = '/doctor-detail?id=' + encodeURIComponent(docId);
    }

    function openDoctorDetailModal(docId) {
      activeDoctorId = docId;
      const d = (typeof LIVE_DATABASE_DOCTORS !== 'undefined' ? LIVE_DATABASE_DOCTORS.find(x => x.id === docId) : null);
      if (!d) return;

      const initials = d.initials || d.avatar || 'DR';
      const pel = Number(d.pelayananCredits || 0);
      const peng = Number(d.pengabdianCredits || 0);
      const pemb = Number(d.pembelajaranCredits || 0);
      const strNum = d.strNumber || d.nik || '-';

      if (document.getElementById('modalAvatar')) document.getElementById('modalAvatar').innerText = initials;
      if (document.getElementById('modalDoctorName')) document.getElementById('modalDoctorName').innerText = d.name;
      if (document.getElementById('modalDoctorMeta')) document.getElementById('modalDoctorMeta').innerText = `${d.pogiId} • STR: ${strNum} • ${d.province}`;
      if (document.getElementById('modalPelayananScore')) document.getElementById('modalPelayananScore').innerText = `${pel} / 56 SKP`;
      if (document.getElementById('modalPengabdianScore')) document.getElementById('modalPengabdianScore').innerText = `${peng} / 8 SKP`;
      if (document.getElementById('modalPembelajaranScore')) document.getElementById('modalPembelajaranScore').innerText = `${pemb} / 72 SKP`;

      const modal = document.getElementById('doctorDetailModal');
      if (modal) modal.classList.remove('hidden');
    }

    function closeDoctorModal() {
      document.getElementById('doctorDetailModal').classList.add('hidden');
    }

    function openDoctorRepositoryPage() {
      if (activeDoctorId) {
        window.location.href = '/doctor-detail?id=' + encodeURIComponent(activeDoctorId) + '&tab=repository';
      }
    }

    function openCertificatePreviewModal() {
      const modal = document.getElementById('certificateModal');
      if (modal) modal.classList.remove('hidden');
    }

    function closeCertificatePreviewModal() {
      const modal = document.getElementById('certificateModal');
      if (modal) modal.classList.add('hidden');
    }

    function approvePendingCredits() {
      if (!activeDoctorId) return;
      fetch(`/doctors/${activeDoctorId}/verify-credits`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          credit_amount: 4.5,
          domain: 'PEMBELAJARAN',
          description: 'Verifikasi Kredit SKP Pembelajaran'
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const doc = (LIVE_DATABASE_DOCTORS || []).find(d => d.id === activeDoctorId);
          if (doc) {
            doc.pendingCredits = 0;
            doc.pembelajaranCredits = (doc.pembelajaranCredits || 0) + 4.5;
            doc.achievedCredits = (doc.achievedCredits || 0) + 4.5;
            if (doc.achievedCredits >= (doc.targetCredits || 160)) {
              doc.status = 'Complete';
              doc.rawStatus = 'COMPLETE';
            }
            doc.lastActivity = 'Baru saja diverifikasi';
            renderDoctorTable(currentFilteredDoctors, false);
          }
          showToast('Kredit SKP Pembelajaran (+4.5 SKP) Berhasil Diverifikasi & Disimpan ke Database!', 'success');
        } else {
          showToast(data.message || 'Gagal memverifikasi kredit', 'error');
        }
        closeDoctorModal();
      })
      .catch(err => {
        showToast('Gagal menghubungi server verifikasi: ' + err.message, 'error');
        closeDoctorModal();
      });
    }

    function sendDoctorReminder(channel) {
      const type = channel === 'wa' ? 'WhatsApp' : 'Email';
      showToast('Pengingat kelengkapan 160 SKP terkirim ke dokter via ' + type, 'success');
    }

    const exportDoctorData = exportDoctorsPdf;

    function openAddDoctorModal() {
      // 1. Populasi Dropdown Provinsi (38 Provinsi Nasional)
      const provSelect = document.getElementById('newDocProv');
      if (provSelect && provSelect.options.length <= 1) {
        const provs = (typeof LIVE_SERVER_PROVINCES !== 'undefined' && LIVE_SERVER_PROVINCES.length > 0) ? LIVE_SERVER_PROVINCES : [];
        provs.forEach(p => {
          const opt = document.createElement('option');
          opt.value = p.name || p;
          opt.textContent = p.name || p;
          provSelect.appendChild(opt);
        });
      }

      // 2. Populasi Dropdown Institusi / RS Jejaring
      const instSelect = document.getElementById('newDocInst');
      if (instSelect && instSelect.options.length <= 1) {
        const insts = (typeof LIVE_SERVER_INSTITUTIONS !== 'undefined' && LIVE_SERVER_INSTITUTIONS.length > 0) ? LIVE_SERVER_INSTITUTIONS : [];
        insts.forEach(inst => {
          const opt = document.createElement('option');
          opt.value = inst.name || inst;
          opt.textContent = (inst.name || inst) + (inst.province ? ` (${inst.province})` : '');
          instSelect.appendChild(opt);
        });
      }

      const modal = document.getElementById('addDoctorModal');
      if (modal) {
        modal.classList.remove('hidden');
        if (window.jQuery && $.fn.select2) {
          $('#newDocProv, #newDocInst, #newDocAssignedTo, #newDocBranch, #newDocStatus').trigger('change.select2');
        }
      }
    }

    function closeAddDoctorModal() {
      const modal = document.getElementById('addDoctorModal');
      if (modal) modal.classList.add('hidden');
    }

    function submitNewDoctorForm() {
      const name = (document.getElementById('newDocName')?.value || '').trim();
      const str = (document.getElementById('newDocStr')?.value || '').trim();
      let phone = (document.getElementById('newDocPhone')?.value || '').trim();
      const prov = document.getElementById('newDocProv')?.value || 'DKI Jakarta';
      const branchId = document.getElementById('newDocBranch')?.value || '';
      const inst = document.getElementById('newDocInst')?.value || '-';
      const email = (document.getElementById('newDocEmail')?.value || '').trim();
      const status = document.getElementById('newDocStatus')?.value || 'ACTIVE';
      const periodStart = (document.getElementById('newDocPeriodStart')?.value || '1 Jan 2026').trim();
      const periodEnd = (document.getElementById('newDocPeriodEnd')?.value || '31 Des 2030').trim();
      const lmsUser = (document.getElementById('newDocLmsUsername')?.value || '').trim();
      const lmsPass = (document.getElementById('newDocLmsPassword')?.value || '').trim();
      const assignedTo = (document.getElementById('newDocAssignedTo') ? document.getElementById('newDocAssignedTo').value : 'Anna');

      if (!name) {
        showToast('Mohon masukkan nama dokter!', 'error');
        return;
      }
      if (!email) {
        showToast('Mohon masukkan email dokter!', 'error');
        return;
      }
      if (!branchId) {
        showToast('Mohon pilih Cabang POGI!', 'error');
        return;
      }

      // Format Telepon: Numbers starting with 0 are saved as +62
      if (phone.startsWith('0')) {
        phone = '+62' + phone.substring(1);
      } else if (phone && !phone.startsWith('+')) {
        phone = '+62' + phone;
      } else if (!phone) {
        phone = '+628123456789';
      }

      const initials = name.replace(/dr\.\s*|Sp\.OG|Sp\.PD|Prof\.|Dr\./gi, '').trim().split(' ').filter(c => /[A-Za-z]/.test(c)).slice(0, 2).map(w => w[0]).join('').toUpperCase() || 'DR';
      const yearMatch = periodEnd.match(/\d{4}/);
      const endYear = yearMatch ? yearMatch[0] : '2030';
      const periodLabel = `${periodStart} - ${periodEnd}`;

      const payload = {
        name: name,
        str: str,
        phone: phone,
        province: prov,
        branch_id: branchId,
        institution: inst,
        assigned_to: assignedTo,
        email: email,
        status: status,
        period_start: periodStart,
        period_end: periodEnd,
        plataran_sehat_username: lmsUser,
        plataran_sehat_password: lmsPass
      };

      fetch('/doctors', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.doctor) {
          const created = data.doctor;
          const newDocItem = {
            id: created.id,
            pogiId: created.pogiId,
            name: created.name,
            initials: initials,
            nik: created.nik || created.strNumber || str,
            strNumber: created.strNumber || str,
            sipNumber: created.sipNumber || '-',
            province: created.province || prov,
            hospital: created.hospital || inst,
            institution: created.hospital || inst,
            phone: phone,
            assignedTo: created.assignedTo || assignedTo,
            status: 'Incomplete',
            rawStatus: 'INCOMPLETE',
            validUntilLabel: 'Siklus 5 Thn: Habis ' + endYear,
            avatar: initials,
            lastActivity: 'Baru didaftarkan (Live Database)',
            periodLabel: periodLabel,
            achievedCredits: 0,
            targetCredits: 160,
            pendingCredits: 0,
            q4Credits: 0,
            pelayananCredits: 0,
            pengabdianCredits: 0,
            pembelajaranCredits: 0,
            targetPelayananCredits: 56,
            targetPengabdianCredits: 8,
            targetPembelajaranCredits: 72
          };

          if (typeof LIVE_DATABASE_DOCTORS !== 'undefined') {
            LIVE_DATABASE_DOCTORS.unshift(newDocItem);
          }

          closeAddDoctorModal();
          applyDoctorFilters();
          showToast('Dokter baru ' + name + ' berhasil didaftarkan ke sistem & database!', 'success');
        } else {
          showToast(data.message || 'Gagal mendaftarkan dokter baru', 'error');
        }
      })
      .catch(err => {
        showToast('Gagal menghubungi server database: ' + (err.message || 'Terjadi kesalahan'), 'error');
      });
    }

    // -------------------------------------------------------------
    // WA BLAST MASSAL (Terhubung ke /dashboard/send-follow-up)
    // -------------------------------------------------------------
    function openWaBlastModal() {
      const sel = document.getElementById('waBlastTargetSelect');
      if (sel && sel.options.length <= 2 && typeof LIVE_SERVER_PROVINCES !== 'undefined') {
        LIVE_SERVER_PROVINCES.forEach(p => {
          const pName = p.name || p;
          const opt = document.createElement('option');
          opt.value = pName;
          opt.textContent = 'Dokter Incomplete - ' + pName;
          sel.appendChild(opt);
        });
      }
      updateWaBlastRecipientCount();
      const modal = document.getElementById('waBlastModal');
      if (modal) modal.classList.remove('hidden');
    }

    function closeWaBlastModal() {
      const modal = document.getElementById('waBlastModal');
      if (modal) modal.classList.add('hidden');
    }

    function updateWaBlastRecipientCount() {
      const sel = document.getElementById('waBlastTargetSelect');
      const badge = document.getElementById('waBlastRecipientBadge');
      if (!sel || !badge) return;
      const val = sel.value;
      const allDocs = Array.isArray(LIVE_DATABASE_DOCTORS) ? LIVE_DATABASE_DOCTORS : [];
      const incompleteDocs = allDocs.filter(d => (d.status || '').toUpperCase() !== 'COMPLETE');
      if (val === 'all_incomplete') {
        badge.innerText = incompleteDocs.length + ' Dokter Incomplete';
      } else if (val === 'lowest_5') {
        badge.innerText = '5 Cabang Terendah';
      } else {
        const count = incompleteDocs.filter(d => (d.province || '').toLowerCase().includes(val.toLowerCase())).length;
        badge.innerText = count + ' Dokter Incomplete';
      }
    }

    async function submitWaBlast() {
      const target = document.getElementById('waBlastTargetSelect')?.value || 'all_incomplete';
      const message = document.getElementById('waBlastMessageInput')?.value?.trim() || '';
      if (!message) {
        showToast('Template pesan tidak boleh kosong.', 'warning');
        return;
      }
      const channels = [];
      if (document.getElementById('waBlastChWhatsapp')?.checked) channels.push('whatsapp');
      if (document.getElementById('waBlastChEmail')?.checked) channels.push('email');
      if (channels.length === 0) channels.push('whatsapp');

      const btn = document.getElementById('btnSubmitWaBlast');
      if (btn) { btn.disabled = true; btn.classList.add('opacity-60'); }

      try {
        const res = await fetch('/dashboard/send-follow-up', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ target, channels, message })
        });
        const data = await res.json();
        if (res.ok && data.success) {
          closeWaBlastModal();
          showToast(data.message || 'Kampanye WA Blast berhasil dimasukkan ke antrean!', 'success');
        } else {
          showToast(data.message || 'Gagal mengirim kampanye WA Blast.', 'error');
        }
      } catch (err) {
        showToast('Gagal terhubung ke server untuk mengirim WA Blast.', 'error');
      } finally {
        if (btn) { btn.disabled = false; btn.classList.remove('opacity-60'); }
      }
    }
  </script>
@endsection
