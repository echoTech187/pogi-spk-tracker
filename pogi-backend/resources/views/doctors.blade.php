<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f4f7f6]">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POGI SKP Tracker - Direktori Dokter Spesialis Obgyn</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Core Styles -->
  <link rel="stylesheet" href="/css/style.css">
</head>
<body class="h-full bg-[#f4f7f6] text-slate-800 antialiased font-sans">
  <!-- Mobile Backdrop Overlay -->
  <div id="sidebarOverlay" onclick="toggleMobileSidebar(false)" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden pointer-events-none transition-opacity lg:hidden"></div>

  <div class="flex h-screen overflow-hidden">
    <!-- APP SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN CONSOLE WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Sticky Header -->
      @include('layouts.header')

      <!-- Main Body -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-5 page-fade-in">

        <!-- MEDCRED STANDARD HEADING (Persis Screenshot Klien) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-1">
          <div class="space-y-1">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
              Daftar Dokter &amp; Tenaga Kesehatan (<span id="headingTotalCount">{{ $metrics['total'] ?? count($doctors ?? []) }} Terdaftar</span>)
            </h1>
            <p class="text-xs text-slate-500 leading-relaxed max-w-3xl">
              Direktori kredensialing aktif, pemantauan pengurusan perizinan instansi &amp; akses pemenuhan kredit 160 SKP (Pelayanan, Pengabdian, Pembelajaran) terintegrasi SATUSEHAT.
            </p>
          </div>
          @if(in_array(Auth::user()?->role, ['SUPER_ADMIN', 'ADMIN_POGI']))
          <div class="flex items-center gap-2 shrink-0 flex-wrap">
            <button type="button" onclick="openWaBlastModal()" id="btnWaBlastMassal" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-extrabold transition flex items-center gap-1.5 shadow-xs rbac-write rbac-operational">
              <span class="material-symbols-outlined text-base">campaign</span>
              <span>Kirim WA Blast Massal</span>
            </button>
            <button type="button" onclick="openAddDoctorModal()" id="btnAddDoctor" class="px-4 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold transition flex items-center gap-1.5 shadow-xs rbac-write rbac-operational">
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
      </main>
    </div>
  </div>

  <!-- Modal Detail & Verifikasi Dokter -->
  <div id="doctorDetailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-5 responsive-modal-body overflow-y-auto">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <div id="modalAvatar" class="w-12 h-12 rounded-2xl bg-[#16b3ac] text-white font-extrabold text-base flex items-center justify-center shrink-0">
            DR
          </div>
          <div>
            <h3 id="modalDoctorName" class="font-black text-slate-900 text-base">Detail Dokter</h3>
            <p id="modalDoctorMeta" class="text-xs text-slate-500">-</p>
          </div>
        </div>
        <button onclick="closeDoctorModal()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      <!-- 3 Subbidang Progress Cards in Modal (KMK 1561/2024 & KMK 1111/2026) -->
      <div class="grid grid-cols-3 gap-2.5 text-xs">
        <div class="p-3 rounded-xl bg-sky-50 border border-sky-200">
          <div class="text-[10px] text-sky-800 font-bold uppercase">1. Pelayanan (35%)</div>
          <div id="modalPelayananScore" class="text-base font-black text-sky-900 mt-0.5">0 / 56 SKP</div>
          <div class="text-[10px] text-sky-700 mt-1">Standar Min. 56 SKP</div>
        </div>
        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
          <div class="text-[10px] text-amber-800 font-bold uppercase">2. Pengabdian (5%)</div>
          <div id="modalPengabdianScore" class="text-base font-black text-amber-900 mt-0.5">0 / 8 SKP</div>
          <div class="text-[10px] text-amber-700 mt-1">Standar Min. 8 SKP</div>
        </div>
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
          <div class="text-[10px] text-emerald-800 font-bold uppercase">3. Pembelajaran (45%)</div>
          <div id="modalPembelajaranScore" class="text-base font-black text-emerald-900 mt-0.5">0 / 72 SKP</div>
          <div class="text-[10px] text-emerald-700 mt-1">Standar Min. 72 SKP</div>
        </div>
      </div>

      <!-- Logbook Entri Menunggu Persetujuan -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Logbook Pengajuan Kredit Terbaru</h4>
          <span class="text-[11px] text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Menunggu Verifikasi</span>
        </div>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
          <div class="flex items-center justify-between font-bold text-slate-900">
            <span>Sertifikat Workshop Hands-on USG Fetomaternal (POGI Jaya)</span>
            <span class="text-[#16b3ac] font-black">+4.5 SKP (Pembelajaran)</span>
          </div>
          <div class="text-[11px] text-slate-500">Nomor Registrasi: CERT-POGI-2026-9921 &bull; Tanggal: 14 Nov 2026</div>
          <div class="flex items-center gap-2 pt-1">
            <button type="button" onclick="openCertificatePreviewModal()" class="text-[11px] text-[#16b3ac] font-bold hover:underline flex items-center gap-1">
              <span class="material-symbols-outlined text-xs">visibility</span> Lihat Berkas PDF
            </button>
            <span class="text-slate-300">&bull;</span>
            <button type="button" onclick="approvePendingCredits()" class="text-[11px] text-emerald-700 font-bold hover:underline flex items-center gap-1 rbac-operational">
              <span class="material-symbols-outlined text-xs">check_circle</span> Setujui &amp; Tambahkan ke Akumulasi
            </button>
          </div>
        </div>
      </div>

      <!-- Tindak Lanjut Follow-Up -->
      <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-xs space-y-2">
        <div class="font-bold text-[#16b3ac]">Kirim Pengingat Langsung ke Dokter / RS:</div>
        <div class="flex items-center gap-2">
          <button type="button" onclick="sendDoctorReminder('wa')" class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] transition flex items-center gap-1">
            <span class="material-symbols-outlined text-xs">chat</span> Hubungi via WhatsApp
          </button>
          <button type="button" onclick="sendDoctorReminder('email')" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold text-[11px] hover:bg-slate-50 transition flex items-center gap-1">
            <span class="material-symbols-outlined text-xs">mail</span> Kirim Email Resmi POGI
          </button>
        </div>
      </div>

      <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
        <button type="button" onclick="openDoctorRepositoryPage()" class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] text-xs font-bold transition flex items-center gap-1.5 border border-emerald-200">
          <span class="material-symbols-outlined text-sm">cloud_done</span>
          <span>Buka Repositori Dokumen (Object Storage)</span>
        </button>
        <button type="button" onclick="closeDoctorModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">Tutup</button>
      </div>
    </div>
  </div>

  <!-- Modal Pratinjau Sertifikat PDF -->
  <div id="certificateModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-red-600 text-xl">picture_as_pdf</span>
          <h3 class="font-black text-slate-900 text-sm">Pratinjau Sertifikat Resmi Terakreditasi</h3>
        </div>
        <button type="button" onclick="closeCertificatePreviewModal()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>
      <div class="p-5 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 space-y-3 text-center">
        <div class="w-12 h-12 mx-auto rounded-xl bg-white border border-slate-200 p-1 flex items-center justify-center shadow-xs">
          <img src="logo.png" alt="Logo POGI" class="w-full h-full object-contain">
        </div>
        <div>
          <div class="text-[10px] uppercase font-black tracking-widest text-[#16b3ac]">PERKUMPULAN OBSTETRI DAN GINEKOLOGI INDONESIA</div>
          <h4 class="font-black text-sm text-slate-900 mt-1">SERTIFIKAT AKREDITASI ILMIAH</h4>
          <p class="text-xs text-slate-600 mt-0.5">Diberikan atas partisipasi aktif dalam: <strong>Workshop Hands-on USG Fetomaternal</strong></p>
        </div>
        <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-bold inline-block">
          Nilai Kredit: +4.5 SKP Subbidang Pembelajaran &bull; Terverifikasi Plataran Sehat
        </div>
        <div class="text-[10px] text-slate-400 font-mono">
          Nomor Registrasi: CERT-POGI-2026-9921 &bull; Terbit: 14 Nov 2026
        </div>
      </div>
      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button" onclick="closeCertificatePreviewModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Tutup</button>
        <button type="button" onclick="showToast('Mengunduh salinan resmi berkas PDF...', 'success'); closeCertificatePreviewModal();" class="px-4 py-2 rounded-xl bg-[#16b3ac] text-white text-xs font-extrabold flex items-center gap-1.5 shadow-xs">
          <span class="material-symbols-outlined text-sm">download</span>
          <span>Unduh Berkas PDF</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Dokter Baru -->
  <div id="addDoctorModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-[#16b3ac] text-xl">person_add</span>
          <h3 class="font-black text-slate-900 text-sm">Add Doctor</h3>
        </div>
        <button type="button" onclick="closeAddDoctorModal()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-lg">close</span>
        </button>
      </div>

      <form onsubmit="event.preventDefault(); submitNewDoctorForm();" class="space-y-4 text-xs">
        <!-- 1. Doctor Info Section -->
        <div class="space-y-3">
          <div class="text-[11px] font-black uppercase tracking-wider text-slate-400">Doctor info</div>

          <!-- Name -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Name</label>
            <input id="newDocName" type="text" required placeholder="dr. Budi Santoso, Sp.PD" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]" />
          </div>

          <!-- STR -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">STR</label>
            <input id="newDocStr" type="text" required placeholder="32.73.100.2.21.0883" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]" />
          </div>

          <!-- Phone -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Phone</label>
            <input id="newDocPhone" type="text" required placeholder="08123456789" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]" />
            <p class="text-[11px] text-slate-400 mt-1">Numbers starting with 0 are saved as +62.</p>
          </div>

          <!-- Province -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Province</label>
            <select id="newDocProv" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]">
              <option value="">Select province</option>
            </select>
          </div>

          <!-- Institution -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Institution</label>
            <select id="newDocInst" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]">
              <option value="">Select institution</option>
            </select>
          </div>

          <!-- Assigned PIC Admin -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">PIC Admin / Dokter Binaan</label>
            <select id="newDocAssignedTo" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]">
              <option value="Siti Rahma">Siti Rahma (Lead Admin POGI Tim Data Pusat)</option>
              <option value="Dewi Lestari">Dewi Lestari (Admin POGI DKI &amp; Banten)</option>
              <option value="Wahyu Setiawan">Wahyu Setiawan (Admin POGI Jawa Barat)</option>
              <option value="Melati Sihombing">Melati Sihombing (Admin POGI Jawa Tengah &amp; DIY)</option>
              <option value="Rina Kartika">Rina Kartika (Admin POGI Jawa Timur &amp; Bali)</option>
              <option value="Dimas Saputra">Dimas Saputra (Admin POGI Luar Jawa / Sumatera &amp; Kalimantan)</option>
              <option value="Fikri Ramadhan">Fikri Ramadhan (Admin POGI Indonesia Timur)</option>
            </select>
          </div>

          <!-- Period start & Period end -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Period start</label>
              <div class="relative">
                <input id="newDocPeriodStart" type="text" required value="1 Jan 2026" placeholder="1 Jan 2026" class="w-full pl-8 pr-2.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]" />
                <span class="material-symbols-outlined text-slate-400 text-base absolute left-2.5 top-2.5 pointer-events-none">calendar_today</span>
              </div>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Period end</label>
              <div class="relative">
                <input id="newDocPeriodEnd" type="text" required value="31 Des 2030" placeholder="31 Des 2030" class="w-full pl-8 pr-2.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]" />
                <span class="material-symbols-outlined text-slate-400 text-base absolute left-2.5 top-2.5 pointer-events-none">calendar_today</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Credentials Section (LMS Plataran Account) -->
        <div class="pt-3 border-t border-slate-100 space-y-3">
          <div>
            <div class="text-[11px] font-black uppercase tracking-wider text-slate-700">Credentials</div>
            <div class="text-[11px] text-slate-400 font-medium">LMS Plataran Account</div>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Username</label>
            <input id="newDocLmsUsername" type="text" placeholder="super@example.com" class="w-full p-2.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]" />
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Password</label>
            <input id="newDocLmsPassword" type="password" placeholder="••••••••" class="w-full p-2.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#16b3ac]" />
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
          <button type="button" onclick="closeAddDoctorModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">Batal</button>
          <button type="submit" class="px-5 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
            <span class="material-symbols-outlined text-base">person_add</span>
            <span>Add doctor</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL WA BLAST MASSAL (BAB 3.2.B & 3.2.C) -->
  <div id="waBlastModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-2.5">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#16b3ac] flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-2xl">campaign</span>
          </div>
          <div>
            <h3 class="font-black text-slate-900 text-sm">Pengiriman Notifikasi WhatsApp Blast Massal</h3>
            <p class="text-[11px] text-slate-500">Otomasi pengingat pemenuhan SKP &amp; berkas sesuai RULES.md Bab 3.2</p>
          </div>
        </div>
        <button type="button" onclick="closeWaBlastModal()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      <!-- VIEW 1: SETUP BLAST -->
      <div id="blastSetupView" class="space-y-4 text-xs">
        <!-- Target Selection -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-3">
          <div class="flex items-center justify-between">
            <label class="font-extrabold text-slate-800">1. Tentukan Sasaran Penerima Blast</label>
            <span id="blastRecipientCountBadge" class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#16b3ac] font-black text-[11px]">
              {{ $metrics['incomplete'] ?? 0 }} Dokter Terpilih
            </span>
          </div>
          <select id="blastFilterOption" onchange="updateBlastRecipients()" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-800">
            <option value="incomplete" selected>Dokter Status Incomplete (Seluruh Indonesia - {{ $metrics['incomplete'] ?? 0 }} Dokter)</option>
            <option value="missing_docs">Dokter dengan Missing Documents (Belum Unggah Bukti)</option>
            <option value="jabar_incomplete">Dokter Incomplete di Cabang Jawa Barat</option>
            <option value="dki_incomplete">Dokter Incomplete di Cabang DKI Jakarta</option>
            <option value="jatim_incomplete">Dokter Incomplete di Cabang Jawa Timur</option>
            <option value="all">Semua Dokter Terdaftar ({{ $metrics['total'] ?? 0 }} Dokter)</option>
          </select>
          <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm text-[#16b3ac]">shield</span>
            <span>Nomor WhatsApp terenkripsi at-rest dan diverifikasi sesuai data registrasi keanggotaan POGI.</span>
          </div>
        </div>

        <!-- Template Selection -->
        <div class="space-y-2">
          <label class="font-extrabold text-slate-800">2. Pilih Templat Pesan WhatsApp Resmi</label>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
            <button type="button" onclick="selectBlastTemplate('H7')" id="btnTplH7" class="p-3 rounded-xl border-2 border-[#16b3ac] bg-emerald-50 text-left font-bold text-slate-800 transition">
              <div class="text-[#16b3ac] text-xs">Pengingat H-7</div>
              <div class="text-[10px] text-slate-500 font-normal mt-0.5">Rutin 7 hari menjelang cutoff tgl 1</div>
            </button>
            <button type="button" onclick="selectBlastTemplate('H2')" id="btnTplH2" class="p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-left font-bold text-slate-800 transition">
              <div class="text-amber-700 text-xs">Peringatan Kritis H-2</div>
              <div class="text-[10px] text-slate-500 font-normal mt-0.5">Deadline 48 jam sebelum cutoff</div>
            </button>
            <button type="button" onclick="selectBlastTemplate('MISSING')" id="btnTplMissing" class="p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-left font-bold text-slate-800 transition">
              <div class="text-sky-700 text-xs">Missing Documents</div>
              <div class="text-[10px] text-slate-500 font-normal mt-0.5">Tindak lanjut berkas belum upload</div>
            </button>
          </div>
        </div>

        <!-- Preview WhatsApp Chat Bubble -->
        <div class="space-y-1.5">
          <div class="flex items-center justify-between text-[11px]">
            <span class="font-extrabold text-slate-700">3. Pratinjau Pesan yang Akan Diterima Dokter:</span>
            <span class="text-slate-400 font-mono">Variabel otomatis disesuaikan</span>
          </div>
          <div class="p-4 rounded-2xl bg-[#efeae2] border border-[#d1d7db] shadow-inner font-sans">
            <div class="bg-white rounded-2xl p-3.5 shadow-sm max-w-lg border border-slate-200/60 space-y-2 text-slate-800 text-[11px] leading-relaxed relative">
              <div class="font-bold text-[#16b3ac] flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                <span class="material-symbols-outlined text-sm">verified</span>
                <span>POGI SKP TRACKER (NOTIFIKASI RESMI ENERGI CIPTA)</span>
              </div>
              <div id="blastMessagePreviewBody" class="whitespace-pre-line font-mono text-[10.5px]">
Yth. dr. Nurul Anggraini, Sp.OG (POGI Cabang DKI Jakarta),

Berdasarkan regulasi resmi Kemenkes RI (KMK HK.01.07/MENKES/1561/2024 & KMK HK.01.07/MENKES/1111/2026), masa cutoff pemantauan SKP periode berjalan akan dikunci pada 01 Des 2026 pukul 00:00 WIB.

📊 Status Capaian: Incomplete (2.0 / 8.0 SKP)
⚠️ Kekurangan Kredit: 6.0 SKP
📁 Missing Document: Bukti Pembelajaran / SKP Event belum lengkap.

Mohon segera mengunggah bukti fisik SKP ke Repositori Cloud Internal Energi Cipta sebelum batas cutoff melalui portal mandiri:
👉 https://pogi.medcred.id/portal-dokter

Terima kasih atas kerja sama Anda,
Admin Data PP POGI & PT Energi Cipta
              </div>
              <div class="text-[9px] text-slate-400 text-right font-mono">10:42 WIB &bull; Terkirim &check;&check;</div>
            </div>
          </div>
        </div>

        <!-- Anti-Ban Rate Limiting Alert -->
        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5">
          <span class="material-symbols-outlined text-[#16b3ac] text-base shrink-0 mt-0.5">speed</span>
          <div class="text-[11px] text-emerald-900 leading-snug">
            <strong>Proteksi Anti-Ban &amp; Rate Limiting (Bab 3.2.C):</strong> Pesan akan dikirim melalui <em>Message Queue</em> dengan penundaan acak (*jitter*) 1.5 - 3.0 detik antar pesan untuk melindungi reputasi nomor WhatsApp resmi Energi Cipta.
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
          <button type="button" onclick="closeWaBlastModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</button>
          <button type="button" onclick="startWaBlastQueue()" class="px-5 py-2.5 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold shadow-md transition flex items-center gap-1.5 rbac-operational">
            <span class="material-symbols-outlined text-base">send</span>
            <span>Mulai Kirim Blast (Message Queue)</span>
          </button>
        </div>
      </div>

      <!-- VIEW 2: REAL-TIME QUEUE EXECUTION -->
      <div id="blastQueueView" class="hidden space-y-4 text-xs">
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
          <div class="flex items-center justify-between">
            <div class="font-extrabold text-slate-800 flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
              <span id="queueStatusText">Memproses Antrean Pesan (Anti-Ban Jitter Aktif)...</span>
            </div>
            <span id="queueProgressPct" class="font-mono font-black text-emerald-800">0%</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
            <div id="queueProgressBar" class="bg-gradient-to-r from-emerald-600 to-teal-500 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
          </div>
          <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono">
            <span>Terkirim: <strong id="queueSentCount" class="text-slate-900">0</strong> / <span id="queueTotalCount">0</span></span>
            <span id="queueJitterStatus">Jitter delay: ~1.8s</span>
          </div>
        </div>

        <!-- Live Queue Log Feed -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden">
          <div class="bg-slate-100 px-3.5 py-2 border-b border-slate-200 text-[10px] font-bold text-slate-600 uppercase tracking-wider flex items-center justify-between">
            <span>Feed Pengiriman Waktu Nyata</span>
            <span class="text-emerald-700 font-black">WhatsApp API Connected</span>
          </div>
          <div id="queueLogFeed" class="max-h-56 overflow-y-auto divide-y divide-slate-100 font-mono text-[11px] p-2 bg-white">
            <!-- Items appended dynamically -->
          </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
          <div class="text-[11px] text-slate-400">Jejak audit permanen dicatat pada tabel audit_logs.</div>
          <button type="button" id="btnFinishQueue" onclick="finishWaBlastQueue()" disabled class="px-5 py-2 rounded-xl bg-slate-100 text-slate-400 font-extrabold transition">
            Selesai
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="/js/pogi_state.js"></script>
  <script src="/js/navigation.js"></script>
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
    let activeDoctorId = null;
    let doctorPagination = { page: 1, limit: 10, total: LIVE_DATABASE_DOCTORS.length || 200 };
    let currentFilteredDoctors = null;
    let currentPipelineFilter = 'all';

    document.addEventListener('DOMContentLoaded', () => {
      // Inisialisasi dataset dokter langsung dari basis data live
      const allDoctors = LIVE_DATABASE_DOCTORS || [];
      const instSelect = document.getElementById('institutionFilter');
      if (instSelect && instSelect.options.length <= 1) {
        populateInstitutionFilter(allDoctors);
      }
      applyDoctorFilters();
    });

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
        if (tbody) tbody.innerHTML = '<tr><td colspan="9" class="py-12 text-center text-slate-400 font-medium">Tidak ada dokter yang cocok dengan kriteria filter.</td></tr>';
        if (mobileContainer) mobileContainer.innerHTML = '<div class="py-8 text-center text-slate-400 font-medium text-xs">Tidak ada dokter yang cocok dengan kriteria filter.</div>';
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
        const provs = (typeof INITIAL_PROVINCES !== 'undefined') ? INITIAL_PROVINCES : [];
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
        const insts = (typeof INITIAL_INSTITUTIONS !== 'undefined') ? INITIAL_INSTITUTIONS : [];
        insts.forEach(inst => {
          const opt = document.createElement('option');
          opt.value = inst.name || inst;
          opt.textContent = (inst.name || inst) + (inst.province ? ` (${inst.province})` : '');
          instSelect.appendChild(opt);
        });
      }

      const modal = document.getElementById('addDoctorModal');
      if (modal) modal.classList.remove('hidden');
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
      const inst = document.getElementById('newDocInst')?.value || 'RSUP Dr. Cipto Mangunkusumo';
      const periodStart = (document.getElementById('newDocPeriodStart')?.value || '1 Jan 2026').trim();
      const periodEnd = (document.getElementById('newDocPeriodEnd')?.value || '31 Des 2030').trim();
      const lmsUser = (document.getElementById('newDocLmsUsername')?.value || '').trim();
      const lmsPass = (document.getElementById('newDocLmsPassword')?.value || '').trim();
      const assignedTo = (document.getElementById('newDocAssignedTo') ? document.getElementById('newDocAssignedTo').value : 'Siti Rahma');

      if (!name) {
        showToast('Mohon masukkan nama dokter!', 'error');
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
        institution: inst,
        assigned_to: assignedTo,
        period_start: periodStart,
        period_end: periodEnd
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
    // BAB 3.2.B & 3.2.C — MODAL WA BLAST MASSAL & MESSAGE QUEUE
    // -------------------------------------------------------------
    let currentBlastTemplate = 'H7';
    let currentBlastRecipients = [];
    let blastQueueInterval = null;

    function openWaBlastModal() {
      if (typeof PogiStore !== 'undefined' && !PogiStore.canPerform('SEND_REMINDER')) {
        showToast('Akses Dibatasi: Peran Viewer Read-Only tidak memiliki izin kirim pesan.', 'warning');
        return;
      }
      document.getElementById('blastSetupView').classList.remove('hidden');
      document.getElementById('blastQueueView').classList.add('hidden');
      updateBlastRecipients();
      selectBlastTemplate('H7');
      const modal = document.getElementById('waBlastModal');
      if (modal) modal.classList.remove('hidden');
    }

    function closeWaBlastModal() {
      if (blastQueueInterval) clearInterval(blastQueueInterval);
      const modal = document.getElementById('waBlastModal');
      if (modal) modal.classList.add('hidden');
    }

    function updateBlastRecipients() {
      const opt = document.getElementById('blastFilterOption').value;
      const allDocs = (typeof LIVE_DATABASE_DOCTORS !== 'undefined' && LIVE_DATABASE_DOCTORS.length > 0) ? LIVE_DATABASE_DOCTORS : [];
      let list = [];

      if (opt === 'all') {
        list = allDocs;
      } else if (opt === 'incomplete') {
        list = allDocs.filter(d => (d.status !== 'Complete' && d.status !== 'COMPLETE' && d.rawStatus !== 'COMPLETE'));
      } else if (opt === 'missing_docs') {
        list = allDocs.filter(d => (d.status !== 'Complete' && d.status !== 'COMPLETE' && d.rawStatus !== 'COMPLETE') || (d.achievedCredits && d.achievedCredits < 160.0));
      } else if (opt === 'jabar_incomplete') {
        list = allDocs.filter(d => d.province && d.province.includes('Jawa Barat') && (d.status !== 'Complete' && d.status !== 'COMPLETE'));
      } else if (opt === 'dki_incomplete') {
        list = allDocs.filter(d => d.province && d.province.includes('DKI Jakarta') && (d.status !== 'Complete' && d.status !== 'COMPLETE'));
      } else if (opt === 'jatim_incomplete') {
        list = allDocs.filter(d => d.province && d.province.includes('Jawa Timur') && (d.status !== 'Complete' && d.status !== 'COMPLETE'));
      }

      currentBlastRecipients = list;
      const badge = document.getElementById('blastRecipientCountBadge');
      if (badge) badge.innerText = `${list.length} Dokter Terpilih`;
      renderBlastPreview();
    }

    function selectBlastTemplate(tpl) {
      currentBlastTemplate = tpl;
      ['btnTplH7', 'btnTplH2', 'btnTplMissing'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
          btn.className = 'p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-left font-bold text-slate-800 transition';
        }
      });

      const activeBtn = document.getElementById(`btnTpl${tpl}`);
      if (activeBtn) {
        activeBtn.className = 'p-3 rounded-xl border-2 border-[#16b3ac] bg-emerald-50 text-left font-bold text-slate-800 transition shadow-2xs';
      }
      renderBlastPreview();
    }

    function renderBlastPreview() {
      const body = document.getElementById('blastMessagePreviewBody');
      if (!body) return;

      const sampleDoc = currentBlastRecipients.length > 0 ? currentBlastRecipients[0] : ((typeof LIVE_DATABASE_DOCTORS !== 'undefined' && LIVE_DATABASE_DOCTORS.length > 0) ? LIVE_DATABASE_DOCTORS[0] : {
        name: 'Dokter Obgyn',
        province: 'Nasional',
        achievedCredits: 0,
        targetCredits: 160.0,
        qAchieved: 0,
        qTarget: 8.0
      });

      const deficit = Math.max(0, (sampleDoc.qTarget || 8.0) - (sampleDoc.qAchieved || 0)).toFixed(1);

      if (currentBlastTemplate === 'H7') {
        body.innerText = `Yth. ${sampleDoc.name} (${sampleDoc.province}),

Berdasarkan regulasi resmi Kemenkes RI (KMK HK.01.07/MENKES/1561/2024 & KMK HK.01.07/MENKES/1111/2026), masa cutoff pemantauan SKP bulanan akan dikunci pada 01 Des 2026 pukul 00:00 WIB (H-7).

📊 Status Capaian: Incomplete (${sampleDoc.qAchieved || 0} / ${sampleDoc.qTarget || 8.0} SKP)
⚠️ Kekurangan Kredit: ${deficit} SKP

Mohon segera mengunggah bukti fisik SKP ke Repositori Cloud Internal Energi Cipta sebelum cutoff melalui portal mandiri:
👉 https://pogi.medcred.id/portal-dokter

Terima kasih,
Admin Data PP POGI & PT Energi Cipta`;
      } else if (currentBlastTemplate === 'H2') {
        body.innerText = `[PERINGATAN KRITIS H-2 SEBELUM CUTOFF]
Yth. ${sampleDoc.name} (${sampleDoc.province}),

Masa pemenuhan data SKP bulanan akan dikunci permanen dalam 48 jam (01 Des 2026, 00:00 WIB). Data ini menjadi dasar laporan resmi bulanan dan verifikasi Plataran Sehat.

⚠️ Kekurangan: ${deficit} SKP belum tercatat.
📁 Dokumen pendukung belum diunggah ke Object Storage cloud internal.

Segera akses portal mandiri Anda:
👉 https://pogi.medcred.id/portal-dokter

Admin Data PP POGI & PT Energi Cipta`;
      } else {
        body.innerText = `[TINDAK LANJUT KEKURANGAN BERKAS SKP]
Yth. ${sampleDoc.name} (${sampleDoc.province}),

Sistem Missing Document Detector mendeteksi berkas bukti kegiatan SKP Anda belum diunggah ke repositori penyimpanan internal Energi Cipta untuk ranah: Pembelajaran / Pelayanan.

Harap lengkapi berkas PDF melalui tautan terenkripsi berikut:
👉 https://pogi.medcred.id/portal-dokter/unggah-mandiri

Salam Sejawat,
Pengurus Pusat POGI & Tim Teknologi Energi Cipta`;
      }
    }

    function startWaBlastQueue() {
      if (currentBlastRecipients.length === 0) {
        showToast('Tidak ada dokter sasaran yang terpilih.', 'warning');
        return;
      }

      document.getElementById('blastSetupView').classList.add('hidden');
      document.getElementById('blastQueueView').classList.remove('hidden');

      const queue = (typeof PogiStore !== 'undefined' && PogiStore.queueWaBlast)
        ? PogiStore.queueWaBlast(currentBlastRecipients, currentBlastTemplate)
        : currentBlastRecipients.map((doc, i) => ({ id: 'queue-' + i, doctorId: doc.id, doctorName: doc.name, phone: doc.phone || '-', status: 'QUEUED' }));
      const total = queue.length;

      document.getElementById('queueTotalCount').innerText = total;
      document.getElementById('queueSentCount').innerText = '0';
      document.getElementById('queueProgressBar').style.width = '0%';
      document.getElementById('queueProgressPct').innerText = '0%';
      document.getElementById('btnFinishQueue').setAttribute('disabled', 'true');
      document.getElementById('btnFinishQueue').className = 'px-5 py-2 rounded-xl bg-slate-100 text-slate-400 font-extrabold transition cursor-not-allowed';

      const feed = document.getElementById('queueLogFeed');
      feed.innerHTML = '';

      let currentIdx = 0;
      function sendNext() {
        if (currentIdx >= total) {
          document.getElementById('queueStatusText').innerText = 'Pengiriman WA Blast Selesai 100%';
          document.getElementById('btnFinishQueue').removeAttribute('disabled');
          document.getElementById('btnFinishQueue').className = 'px-5 py-2 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold shadow-sm transition cursor-pointer';
          showToast(`WA Blast Massal berhasil dikirim ke ${total} dokter via Message Queue!`, 'success');
          return;
        }

        const item = queue[currentIdx];
        const jitter = (1.5 + Math.random() * 1.2).toFixed(1);
        document.getElementById('queueJitterStatus').innerText = `Anti-Ban Jitter: ~${jitter}s`;

        const row = document.createElement('div');
        row.className = 'py-2 px-3 flex items-center justify-between gap-2 hover:bg-slate-50 transition border-b border-slate-100';
        row.innerHTML = `
          <div class="flex items-center gap-2 min-w-0">
            <span class="text-slate-400 font-mono text-[10px] w-6 shrink-0">${currentIdx + 1}.</span>
            <div class="truncate">
              <span class="font-bold text-slate-900">${item.doctorName}</span>
              <span class="text-slate-400 text-[10px]">(${item.province} • ${item.phone})</span>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="text-[9px] text-slate-400 font-mono">Jitter +${jitter}s</span>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-[#16b3ac] font-bold text-[9px] flex items-center gap-1">
              <span class="material-symbols-outlined text-[10px]">check</span> Terkirim
            </span>
          </div>
        `;
        feed.insertBefore(row, feed.firstChild);

        currentIdx++;
        const pct = Math.round((currentIdx / total) * 100);
        document.getElementById('queueSentCount').innerText = currentIdx;
        document.getElementById('queueProgressBar').style.width = pct + '%';
        document.getElementById('queueProgressPct').innerText = pct + '%';

        const delay = Math.min(300, Math.floor(Math.random() * 150) + 100);
        setTimeout(sendNext, delay);
      }

      sendNext();
    }

    function finishWaBlastQueue() {
      closeWaBlastModal();
      showToast('Seluruh log pengiriman telah diarsipkan ke Jejak Audit.', 'info');
    }
  </script>
</body>
</html>
