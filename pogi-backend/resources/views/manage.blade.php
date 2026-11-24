<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f4f7f6]">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POGI SKP Tracker - Pengelolaan Master Data (Manage)</title>
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

        <!-- MedCred Standard Heading Banner -->
        <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#006948] text-[10px] font-black uppercase tracking-wider">
              <span>PUSAT PENGELOLAAN MASTER DATA</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <span>Master Data Pangkalan POGI SKP</span>
              <span id="manageHeadingBadge" class="text-sm font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">(5 Entitas Terpadu)</span>
            </h1>
            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
              Manajemen terpadu database dokter spesialis, rumah sakit &amp; jejaring faskes, katalog kegiatan SKP, kategori kegiatan, serta hak akses pengguna sistem.
            </p>
          </div>
          
        </div>

        <!-- Top Segmented Tabs (Doctor, Institution, SKP Event, Event Category, User) -->
        <div class="p-1.5 bg-slate-200/70 rounded-2xl flex flex-wrap gap-1 text-xs font-bold text-slate-600">
          <button type="button" onclick="switchManageTab('doctor')" id="tab-btn-doctor" class="py-2 px-3.5 rounded-xl bg-white text-slate-900 shadow-xs transition flex items-center gap-1.5 font-bold">
            <span class="material-symbols-outlined text-sm">person</span>
            <span>1. Doctor</span>
          </button>
          <button type="button" onclick="switchManageTab('institution')" id="tab-btn-institution" class="py-2 px-3.5 rounded-xl text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">domain</span>
            <span>2. Institution (RS Jejaring)</span>
          </button>
          <button type="button" onclick="switchManageTab('event')" id="tab-btn-event" class="py-2 px-3.5 rounded-xl text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">calendar_month</span>
            <span>3. SKP Event Master</span>
          </button>
          <button type="button" onclick="switchManageTab('category')" id="tab-btn-category" class="py-2 px-3.5 rounded-xl text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">category</span>
            <span>4. Event Category</span>
          </button>
          <button type="button" onclick="switchManageTab('user')" id="tab-btn-user" class="py-2 px-3.5 rounded-xl text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">manage_accounts</span>
            <span>5. User &amp; Hak Akses</span>
          </button>
        </div>

        <!-- ========================================== -->
        <!-- PANE 1: DOCTOR MASTER                      -->
        <!-- ========================================== -->
        <div id="pane-doctor" class="space-y-4">
          <!-- Toolbar Filters (Persis Doctors.html) -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Cari Nama Dokter / NIK / No Anggota</label>
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-lg">search</span>
                  <input id="searchManageDoctorInput" onkeyup="filterManageDoctorTable()" type="text" placeholder="Ketik nama spesialis, NIK, STR..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-emerald-700" />
                </div>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Provinsi / Wilayah Cabang</label>
                <select id="manageDoctorProvFilter" onchange="filterManageDoctorTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option value="ALL">Semua Cabang (38 Provinsi)</option>
                <option value="Aceh">Aceh</option>
                <option value="Bali">Bali</option>
                <option value="Banten">Banten</option>
                <option value="Bengkulu">Bengkulu</option>
                <option value="DI Yogyakarta">DI Yogyakarta</option>
                <option value="DKI Jakarta">DKI Jakarta</option>
                <option value="Gorontalo">Gorontalo</option>
                <option value="Jambi">Jambi</option>
                <option value="Jawa Barat">Jawa Barat</option>
                <option value="Jawa Tengah">Jawa Tengah</option>
                <option value="Jawa Timur">Jawa Timur</option>
                <option value="Kalimantan Barat">Kalimantan Barat</option>
                <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                <option value="Kalimantan Tengah">Kalimantan Tengah</option>
                <option value="Kalimantan Timur">Kalimantan Timur</option>
                <option value="Kalimantan Utara">Kalimantan Utara</option>
                <option value="Kepulauan Bangka Belitung">Kepulauan Bangka Belitung</option>
                <option value="Kepulauan Riau">Kepulauan Riau</option>
                <option value="Lampung">Lampung</option>
                <option value="Maluku">Maluku</option>
                <option value="Maluku Utara">Maluku Utara</option>
                <option value="Nusa Tenggara Barat">Nusa Tenggara Barat</option>
                <option value="Nusa Tenggara Timur">Nusa Tenggara Timur</option>
                <option value="Papua">Papua</option>
                <option value="Papua Barat">Papua Barat</option>
                <option value="Papua Barat Daya">Papua Barat Daya</option>
                <option value="Papua Pegunungan">Papua Pegunungan</option>
                <option value="Papua Selatan">Papua Selatan</option>
                <option value="Papua Tengah">Papua Tengah</option>
                <option value="Riau">Riau</option>
                <option value="Sulawesi Barat">Sulawesi Barat</option>
                <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                <option value="Sulawesi Tengah">Sulawesi Tengah</option>
                <option value="Sulawesi Tenggara">Sulawesi Tenggara</option>
                <option value="Sulawesi Utara">Sulawesi Utara</option>
                <option value="Sumatera Barat">Sumatera Barat</option>
                <option value="Sumatera Selatan">Sumatera Selatan</option>
                <option value="Sumatera Utara">Sumatera Utara</option>
                </select>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Status Kepatuhan SKP</label>
                <select id="manageDoctorStatusFilter" onchange="filterManageDoctorTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option value="ALL">Semua Status (Complete &amp; Incomplete)</option>
                  <option value="Complete">Complete (Target Terpenuhi)</option>
                  <option value="Incomplete">Incomplete (Perlu Follow-Up)</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
              <div>Menampilkan <span id="manageDoctorCounter" class="font-bold text-slate-900">0</span> dokter dari total pangkalan data</div>
              <div class="flex items-center gap-3">
                <button type="button" onclick="resetManageDoctorFilters()" class="text-[#006948] font-bold hover:underline">Reset Filter</button>
                <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#006948] hover:bg-[#005439] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
                  <span class="material-symbols-outlined text-sm">person_add</span>
                  <span>Tambah Dokter</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Table (Persis Format doctors.html) -->
          <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
                  <tr>
                    <th class="py-3 px-4">Dokter &amp; No Anggota</th>
                    <th class="py-3 px-4">Provinsi &amp; RS Jejaring</th>
                    <th class="py-3 px-4">SKP Progress (Q4)</th>
                    <th class="py-3 px-4 text-center">Pending Credits</th>
                    <th class="py-3 px-4">
                      <div class="flex items-center gap-1 text-sky-800 font-bold">
                        <span class="w-2 h-2 rounded-full bg-sky-600"></span> Pelayanan (Min 80)
                      </div>
                    </th>
                    <th class="py-3 px-4">
                      <div class="flex items-center gap-1 text-amber-800 font-bold">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span> Pengabdian (Min 30)
                      </div>
                    </th>
                    <th class="py-3 px-4">
                      <div class="flex items-center gap-1 text-emerald-800 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Pembelajaran (Min 50)
                      </div>
                    </th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4">Aktivitas Terakhir</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody id="manageDoctorTableBody" class="divide-y divide-slate-100 font-medium">
                  <!-- Rendered dynamically -->
                </tbody>
              </table>
            </div>

            <!-- MedCred Pagination Footer -->
            <div id="manageDoctorPagination" class="px-4 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- PANE 2: INSTITUTION (RS JEJARING)          -->
        <!-- ========================================== -->
        <div id="pane-institution" class="hidden space-y-4">
          <!-- Toolbar Filters -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Cari Rumah Sakit / Klinik / Alamat</label>
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-lg">search</span>
                  <input id="searchManageInstInput" onkeyup="filterManageInstTable()" type="text" placeholder="Ketik nama rumah sakit..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-emerald-700" />
                </div>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Provinsi / Wilayah</label>
                <select id="manageInstProvFilter" onchange="filterManageInstTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option value="ALL">Semua Provinsi (38 Wilayah)</option>
                <option value="Aceh">Aceh</option>
                <option value="Bali">Bali</option>
                <option value="Banten">Banten</option>
                <option value="Bengkulu">Bengkulu</option>
                <option value="DI Yogyakarta">DI Yogyakarta</option>
                <option value="DKI Jakarta">DKI Jakarta</option>
                <option value="Gorontalo">Gorontalo</option>
                <option value="Jambi">Jambi</option>
                <option value="Jawa Barat">Jawa Barat</option>
                <option value="Jawa Tengah">Jawa Tengah</option>
                <option value="Jawa Timur">Jawa Timur</option>
                <option value="Kalimantan Barat">Kalimantan Barat</option>
                <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                <option value="Kalimantan Tengah">Kalimantan Tengah</option>
                <option value="Kalimantan Timur">Kalimantan Timur</option>
                <option value="Kalimantan Utara">Kalimantan Utara</option>
                <option value="Kepulauan Bangka Belitung">Kepulauan Bangka Belitung</option>
                <option value="Kepulauan Riau">Kepulauan Riau</option>
                <option value="Lampung">Lampung</option>
                <option value="Maluku">Maluku</option>
                <option value="Maluku Utara">Maluku Utara</option>
                <option value="Nusa Tenggara Barat">Nusa Tenggara Barat</option>
                <option value="Nusa Tenggara Timur">Nusa Tenggara Timur</option>
                <option value="Papua">Papua</option>
                <option value="Papua Barat">Papua Barat</option>
                <option value="Papua Barat Daya">Papua Barat Daya</option>
                <option value="Papua Pegunungan">Papua Pegunungan</option>
                <option value="Papua Selatan">Papua Selatan</option>
                <option value="Papua Tengah">Papua Tengah</option>
                <option value="Riau">Riau</option>
                <option value="Sulawesi Barat">Sulawesi Barat</option>
                <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                <option value="Sulawesi Tengah">Sulawesi Tengah</option>
                <option value="Sulawesi Tenggara">Sulawesi Tenggara</option>
                <option value="Sulawesi Utara">Sulawesi Utara</option>
                <option value="Sumatera Barat">Sumatera Barat</option>
                <option value="Sumatera Selatan">Sumatera Selatan</option>
                <option value="Sumatera Utara">Sumatera Utara</option>
                </select>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Tipe Faskes</label>
                <select id="manageInstTypeFilter" onchange="filterManageInstTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option value="ALL">Semua Tipe Faskes</option>
                  <option value="RSUP">RSUP (Pusat Rujukan Nasional)</option>
                  <option value="RSUD">RSUD (Daerah / Provinsi)</option>
                  <option value="Swasta">RS Swasta / Jejaring</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
              <div>Menampilkan <span id="manageInstCounter" class="font-bold text-slate-900">0</span> faskes jejaring</div>
              <div class="flex items-center gap-3">
                <button type="button" onclick="resetManageInstFilters()" class="text-[#006948] font-bold hover:underline">Reset Filter</button>
                <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#006948] hover:bg-[#005439] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
                  <span class="material-symbols-outlined text-sm">domain_add</span>
                  <span>Tambah Institusi</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
                  <tr>
                    <th class="py-3 px-4">Rumah Sakit &amp; Faskes</th>
                    <th class="py-3 px-4">Provinsi &amp; Alamat</th>
                    <th class="py-3 px-4 text-center">Dokter Obgyn</th>
                    <th class="py-3 px-4 text-center">Kapasitas Pelayanan</th>
                    <th class="py-3 px-4 text-center">Status Faskes</th>
                    <th class="py-3 px-4">Aktivitas Terakhir</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody id="manageInstTableBody" class="divide-y divide-slate-100 font-medium">
                  <!-- Rendered dynamically -->
                </tbody>
              </table>
            </div>

            <!-- Mobile Touch Cards (< md) -->
            <div id="manageInstMobileCards" class="md:hidden space-y-3 p-4">
              <!-- Rendered dynamically for mobile -->
            </div>

            <!-- MedCred Pagination Footer -->
            <div id="manageInstPagination" class="px-4 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- PANE 3: SKP EVENT MASTER                   -->
        <!-- ========================================== -->
        <div id="pane-event" class="hidden space-y-4">
          <!-- Toolbar Filters -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Cari Event / Topik Ilmiah</label>
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-lg">search</span>
                  <input id="searchManageEventInput" onkeyup="filterManageEventTable()" type="text" placeholder="Ketik nama workshop, PIT..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-emerald-700" />
                </div>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Subbidang / Ranah SKP</label>
                <select id="manageEventTypeFilter" onchange="filterManageEventTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option value="ALL">Semua Ranah (3 Subbidang)</option>
                  <option value="Pembelajaran">3. Pembelajaran (Ilmiah/Workshop)</option>
                  <option value="Pengabdian">2. Pengabdian (Baksos/Penyuluhan)</option>
                  <option value="Pelayanan">1. Pelayanan (Praktik Klinis)</option>
                </select>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Status Event</label>
                <select id="manageEventStatusFilter" onchange="filterManageEventTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option value="ALL">Semua Status</option>
                  <option value="Dibuka">Dibuka (Pendaftaran Aktif)</option>
                  <option value="Selesai">Selesai (Kredit Ditutup)</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
              <div>Menampilkan <span id="manageEventCounter" class="font-bold text-slate-900">0</span> event SKP terdaftar</div>
              <div class="flex items-center gap-3">
                <button type="button" onclick="resetManageEventFilters()" class="text-[#006948] font-bold hover:underline">Reset Filter</button>
                <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#006948] hover:bg-[#005439] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
                  <span class="material-symbols-outlined text-sm">add_circle</span>
                  <span>Tambah Event SKP</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
                  <tr>
                    <th class="py-3 px-4">Event &amp; Kegiatan Ilmiah</th>
                    <th class="py-3 px-4">Tanggal &amp; Jadwal</th>
                    <th class="py-3 px-4 text-center">Ranah SKP</th>
                    <th class="py-3 px-4 text-center">Nilai Kredit</th>
                    <th class="py-3 px-4">Peserta Tuntas</th>
                    <th class="py-3 px-4 text-center">Status Kegiatan</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody id="manageEventTableBody" class="divide-y divide-slate-100 font-medium">
                  <!-- Rendered dynamically -->
                </tbody>
              </table>
            </div>

            <!-- Mobile Touch Cards (< md) -->
            <div id="manageEventMobileCards" class="md:hidden space-y-3 p-4">
              <!-- Rendered dynamically for mobile -->
            </div>

            <!-- MedCred Pagination Footer -->
            <div id="manageEventPagination" class="px-4 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- PANE 4: EVENT CATEGORY                     -->
        <!-- ========================================== -->
        <div id="pane-category" class="hidden space-y-4">
          <!-- Toolbar Filters -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Cari Kategori Kegiatan</label>
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-lg">search</span>
                  <input id="searchManageCatInput" onkeyup="filterManageCatTable()" type="text" placeholder="Ketik nama kategori..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-emerald-700" />
                </div>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Ranah SKP Acuan</label>
                <select id="manageCatTypeFilter" onchange="filterManageCatTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option value="ALL">Semua Ranah</option>
                  <option value="Pembelajaran">Pembelajaran</option>
                  <option value="Pengabdian">Pengabdian</option>
                  <option value="Pelayanan">Pelayanan</option>
                </select>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Status Kategori</label>
                <select class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option>Aktif Digunakan</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
              <div>Menampilkan <span id="manageCatCounter" class="font-bold text-slate-900">0</span> kategori terverifikasi</div>
              <div class="flex items-center gap-3">
                <button type="button" onclick="resetManageCatFilters()" class="text-[#006948] font-bold hover:underline">Reset Filter</button>
                <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#006948] hover:bg-[#005439] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
                  <span class="material-symbols-outlined text-sm">category</span>
                  <span>Tambah Kategori</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
                  <tr>
                    <th class="py-3 px-4">Kategori Kegiatan SKP</th>
                    <th class="py-3 px-4 text-center">Ranah Terkait</th>
                    <th class="py-3 px-4 text-center">Jumlah Kegiatan</th>
                    <th class="py-3 px-4 text-center">Standar Nilai Kredit</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody id="manageCatTableBody" class="divide-y divide-slate-100 font-medium">
                  <!-- Rendered dynamically -->
                </tbody>
              </table>
            </div>

            <!-- Mobile Touch Cards (< md) -->
            <div id="manageCatMobileCards" class="md:hidden space-y-3 p-4">
              <!-- Rendered dynamically for mobile -->
            </div>

            <!-- MedCred Pagination Footer -->
            <div id="manageCatPagination" class="px-4 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- PANE 5: USER & HAK AKSES                   -->
        <!-- ========================================== -->
        <div id="pane-user" class="hidden space-y-4">
          <!-- Toolbar Filters -->
          <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Cari Pengguna / Email</label>
                <div class="relative">
                  <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-lg">search</span>
                  <input id="searchManageUserInput" onkeyup="filterManageUserTable()" type="text" placeholder="Ketik nama admin atau email..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-emerald-700" />
                </div>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Peran / Hak Akses</label>
                <select id="manageUserRoleFilter" onchange="filterManageUserTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option value="ALL">Semua Role (5 Role)</option>
                  <option value="Super Admin">Super Admin</option>
                  <option value="Admin Pusat">Admin Pusat</option>
                  <option value="Admin POGI">Admin POGI</option>
                  <option value="Admin Cabang">Admin Cabang</option>
                  <option value="User/Dokter">User/Dokter</option>
                </select>
              </div>

              <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Status Akun</label>
                <select class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
                  <option>Aktif Terverifikasi</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
              <div>Menampilkan <span id="manageUserCounter" class="font-bold text-slate-900">0</span> akun pengelola</div>
              <div class="flex items-center gap-3">
                <button type="button" onclick="resetManageUserFilters()" class="text-[#006948] font-bold hover:underline">Reset Filter</button>
                <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#006948] hover:bg-[#005439] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
                  <span class="material-symbols-outlined text-sm">manage_accounts</span>
                  <span>Tambah Pengguna</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Table -->
          <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
              <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px]">
                  <tr>
                    <th class="py-3 px-4">Pengguna &amp; Email</th>
                    <th class="py-3 px-4 text-center">Peran / Hak Akses</th>
                    <th class="py-3 px-4">Wilayah Penugasan</th>
                    <th class="py-3 px-4">Terdaftar Sejak</th>
                    <th class="py-3 px-4 text-center">Status Akun</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody id="manageUserTableBody" class="divide-y divide-slate-100 font-medium">
                  <!-- Rendered dynamically -->
                </tbody>
              </table>
            </div>

            <!-- Mobile Touch Cards (< md) -->
            <div id="manageUserMobileCards" class="md:hidden space-y-3 p-4">
              <!-- Rendered dynamically for mobile -->
            </div>

            <!-- MedCred Pagination Footer -->
            <div id="manageUserPagination" class="px-4 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"></div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Reusable Action Dropdown Menu -->
  <div id="rowActionMenu" class="hidden fixed z-50 bg-white border border-slate-200 rounded-2xl shadow-2xl py-1.5 text-xs w-40">
    <button onclick="handleRowAction('detail')" class="w-full text-left px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2 text-slate-700 font-medium">
      <span class="material-symbols-outlined text-sm">visibility</span> Rincian Lengkap
    </button>
    <button onclick="handleRowAction('edit')" class="w-full text-left px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2 text-slate-700 font-medium">
      <span class="material-symbols-outlined text-sm">edit</span> Edit Data
    </button>
    <div class="border-t border-slate-100 my-1"></div>
    <button onclick="handleRowAction('delete')" class="w-full text-left px-3.5 py-2 hover:bg-red-50 flex items-center gap-2 text-red-600 font-medium">
      <span class="material-symbols-outlined text-sm">delete</span> Hapus Data
    </button>
  </div>

  <!-- Modal Add Generic Master Item -->
  <div id="manageAddModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 id="manageAddModalTitle" class="font-black text-slate-900 text-base">Add Item</h3>
        <button onclick="closeManageAddModal()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>
      <form id="manageAddForm" onsubmit="event.preventDefault(); submitManageAdd();" class="space-y-3 text-xs">
        <div id="manageAddFormFields" class="space-y-3">
          <!-- Populated dynamically based on active tab -->
        </div>
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
          <button type="button" onclick="closeManageAddModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-xl bg-[#006948] text-white font-extrabold shadow-sm">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal View Details -->
  <div id="manageDetailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] flex flex-col">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3 shrink-0">
        <h3 id="manageDetailModalTitle" class="font-black text-slate-900 text-base">Rincian Data Master</h3>
        <button type="button" onclick="closeManageDetailModal()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>
      <div id="manageDetailContent" class="space-y-2.5 text-xs flex-1 overflow-y-auto">
        <!-- Key value pairs -->
      </div>
      <div class="flex justify-end pt-3 border-t border-slate-100 shrink-0">
        <button type="button" onclick="closeManageDetailModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">Tutup</button>
      </div>
    </div>
  </div>

  <!-- Modal Edit Item -->
  <div id="manageEditModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 id="manageEditModalTitle" class="font-black text-slate-900 text-base">Edit Data Master</h3>
        <button type="button" onclick="closeManageEditModal()" class="text-slate-400 hover:text-slate-600 p-1">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>
      <form id="manageEditForm" onsubmit="event.preventDefault(); submitManageEdit();" class="space-y-3 text-xs">
        <div id="manageEditFormFields" class="space-y-3">
          <!-- Populated dynamically -->
        </div>
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
          <button type="button" onclick="closeManageEditModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-xl bg-[#006948] text-white font-extrabold shadow-sm">Simpan Perubahan</button>
        </div>
      </form>
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
          btnsHtml += '<span class="px-1 text-slate-400 font-bold self-center">...</span>';
        }
      }

      for (let p = startP; p <= endP; p++) {
        const isActive = (p === currentPage);
        btnsHtml += `
          <button type="button" onclick="${onPageChangeFn}(${p})" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center ${isActive ? 'bg-[#006948] text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}">
            ${p}
          </button>
        `;
      }

      if (endP < totalPages) {
        if (endP < totalPages - 1) {
          btnsHtml += '<span class="px-1 text-slate-400 font-bold self-center">...</span>';
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

    let currentTab = 'doctor';

    const managePagination = {
      doctor: { page: 1, limit: 10, total: 0 },
      institution: { page: 1, limit: 10, total: 0 },
      event: { page: 1, limit: 10, total: 0 },
      category: { page: 1, limit: 10, total: 0 },
      user: { page: 1, limit: 10, total: 0 }
    };

    const currentFilteredCache = {
      doctor: null,
      institution: null,
      event: null,
      category: null,
      user: null
    };

    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const tabParam = urlParams.get('tab');
      if (tabParam && ['doctor', 'institution', 'event', 'category', 'user'].includes(tabParam)) {
        switchManageTab(tabParam);
      } else {
        switchManageTab('doctor');
      }
    });

    
    function exportActiveManageTabPdf(targetTab) {
      const tabToExport = targetTab || (typeof currentTab !== 'undefined' ? currentTab : 'doctor');
      if (tabToExport === 'doctor' && typeof window.exportDoctorsPdf === 'function') {
        window.exportDoctorsPdf();
      } else if (tabToExport === 'event' && typeof window.exportEventsPdf === 'function') {
        window.exportEventsPdf();
      } else if (typeof window.PogiPdfExporter !== 'undefined' && typeof window.PogiPdfExporter.exportManageTab === 'function') {
        window.PogiPdfExporter.exportManageTab(tabToExport);
      }
    }
    const exportActiveManageTab = exportActiveManageTabPdf;
    window.exportActiveManageTabPdf = exportActiveManageTabPdf;

    function switchManageTab(tab) {
      currentTab = tab;
      const allTabs = ['doctor', 'institution', 'event', 'category', 'user'];

      allTabs.forEach(t => {
        const pane = document.getElementById('pane-' + t);
        const topBtn = document.getElementById('tab-btn-' + t);
        const sideBtn = document.getElementById('side-nav-' + t);

        if (t === tab) {
          if (pane) pane.classList.remove('hidden');
          if (topBtn) {
            topBtn.className = 'py-2 px-3.5 rounded-xl bg-white text-slate-900 shadow-xs transition flex items-center gap-1.5 font-bold';
          }
          if (sideBtn) {
            sideBtn.className = 'nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition bg-[#006948] text-white shadow-xs';
          }
        } else {
          if (pane) pane.classList.add('hidden');
          if (topBtn) {
            topBtn.className = 'py-2 px-3.5 rounded-xl text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5';
          }
          if (sideBtn) {
            sideBtn.className = 'nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition text-slate-600 hover:text-slate-900 hover:bg-slate-100';
          }
        }
      });

      const titles = {
        doctor: { bc: 'Master Dokter Spesialis Obgyn', btn: 'Tambah Dokter' },
        institution: { bc: 'Master RS & Institusi Faskes', btn: 'Tambah RS / Faskes' },
        event: { bc: 'Master Kegiatan SKP Terakreditasi', btn: 'Tambah Kegiatan SKP' },
        category: { bc: 'Master Kategori Kegiatan SKP', btn: 'Tambah Kategori' },
        user: { bc: 'Master Pengguna & Hak Akses', btn: 'Tambah Admin / User' }
      };

      const bcEl = document.getElementById('breadcrumbCurrent');
      const addBtnTextEl = document.getElementById('topAddButtonText');
      if (bcEl) bcEl.innerText = titles[tab].bc;
      if (addBtnTextEl) addBtnTextEl.innerText = titles[tab].btn;

      if (tab === 'doctor') renderManageDoctorTable(null, true);
      if (tab === 'institution') renderManageInstTable(null, true);
      if (tab === 'event') renderManageEventTable(null, true);
      if (tab === 'category') renderManageCatTable(null, true);
      if (tab === 'user') renderManageUserTable(null, true);
    }

    function changeManagePageTab(tab, p) {
      const list = currentFilteredCache[tab] || getDefaultListForTab(tab);
      const totalPages = Math.max(1, Math.ceil(list.length / managePagination[tab].limit));
      if (p < 1) p = 1;
      if (p > totalPages) p = totalPages;
      managePagination[tab].page = p;
      triggerTabRender(tab, false);
    }

    function setManagePageLimitTab(tab, limit) {
      managePagination[tab].limit = parseInt(limit, 10) || 10;
      managePagination[tab].page = 1;
      triggerTabRender(tab, false);
    }

    function getDefaultListForTab(tab) {
      if (tab === 'doctor') return PogiStore.getDoctors();
      if (tab === 'institution') return PogiStore.getInstitutions();
      if (tab === 'event') return PogiStore.getEvents();
      if (tab === 'category') return PogiStore.getCategories();
      if (tab === 'user') return PogiStore.getUsers();
      return [];
    }

    function triggerTabRender(tab, resetPage = false) {
      if (tab === 'doctor') renderManageDoctorTable(currentFilteredCache.doctor, resetPage);
      if (tab === 'institution') renderManageInstTable(currentFilteredCache.institution, resetPage);
      if (tab === 'event') renderManageEventTable(currentFilteredCache.event, resetPage);
      if (tab === 'category') renderManageCatTable(currentFilteredCache.category, resetPage);
      if (tab === 'user') renderManageUserTable(currentFilteredCache.user, resetPage);
    }

    // ==========================================
    // TAB 1: DOCTOR (PERSIS FORMAT DOCTORS.HTML)
    // ==========================================
    function renderManageDoctorTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.doctor.page = 1;
      const allDoctors = filteredList || PogiStore.getDoctors();
      currentFilteredCache.doctor = allDoctors;
      managePagination.doctor.total = allDoctors.length;

      const totalPages = Math.max(1, Math.ceil(managePagination.doctor.total / managePagination.doctor.limit));
      if (managePagination.doctor.page > totalPages) managePagination.doctor.page = totalPages;
      if (managePagination.doctor.page < 1) managePagination.doctor.page = 1;

      const start = (managePagination.doctor.page - 1) * managePagination.doctor.limit;
      const end = Math.min(start + managePagination.doctor.limit, managePagination.doctor.total);
      const pageDoctors = allDoctors.slice(start, end);

      const tbody = document.getElementById('manageDoctorTableBody');
      const mobileContainer = document.getElementById('manageDoctorMobileCards');
      const counterEl = document.getElementById('manageDoctorCounter');
      if (counterEl) counterEl.innerText = managePagination.doctor.total;
      if (!tbody && !mobileContainer) return;

      if (pageDoctors.length === 0) {
        if (tbody) tbody.innerHTML = '<tr><td colspan="10" class="py-12 text-center text-slate-400 font-medium">Tidak ada dokter yang cocok dengan kriteria filter.</td></tr>';
        if (mobileContainer) mobileContainer.innerHTML = '<div class="py-8 text-center text-slate-400 font-medium text-xs">Tidak ada data dokter.</div>';
      } else {
        // Desktop & Tablet Rows
        if (tbody) {
          tbody.innerHTML = pageDoctors.map(d => {
            const qAchieved = d.q4Credits !== undefined ? d.q4Credits : (d.qAchieved !== undefined ? d.qAchieved : 8.0);
            const qTarget = 8.0;
            const percent = Math.min(100, Math.round((qAchieved / qTarget) * 100));
            const isComplete = (d.status === 'Complete' || qAchieved >= 8.0);
            const statusBadge = isComplete
              ? '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Complete</span>'
              : '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>Incomplete</span>';

            const initials = d.initials || d.avatar || 'DR';
            const hospital = d.hospital || d.institution || 'RS Jejaring POGI';
            const total5Year = d.total5YearAchieved || d.totalCredits || (d.pelayananCredits + d.pengabdianCredits + d.pembelajaranCredits) || 88;
            const pel = d.pelayananCredits !== undefined ? d.pelayananCredits : (d.pelayanan?.achieved || 45);
            const peng = d.pengabdianCredits !== undefined ? d.pengabdianCredits : (d.pengabdian?.achieved || 15);
            const pemb = d.pembelajaranCredits !== undefined ? d.pembelajaranCredits : (d.pembelajaran?.achieved || 25);
            const lastAct = d.lastActivity || ('Sync SATUSEHAT (' + PogiStore.formatCurrentDate() + ')');

            return `
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#006948] font-black text-xs flex items-center justify-center shrink-0">
                      ${initials}
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-slate-900 text-xs leading-snug truncate max-w-[220px]">${d.name}</div>
                      <div class="text-[10px] text-slate-400 font-mono mt-0.5">${d.pogiId} &bull; ${d.nik}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-800 text-xs">${d.province}</div>
                  <div class="text-[10px] text-slate-500 truncate max-w-[170px] mt-0.5">${hospital}</div>
                </td>
                <td class="py-3 px-4">
                  <div class="flex items-center justify-between text-[11px] mb-1 font-bold">
                    <span class="text-slate-800">${qAchieved.toFixed(1)} / ${qTarget.toFixed(1)} SKP</span>
                    <span class="${percent >= 100 ? 'text-emerald-700' : 'text-amber-600'}">${percent}%</span>
                  </div>
                  <div class="w-28 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="${percent >= 100 ? 'bg-[#006948]' : 'bg-amber-500'} h-1.5 rounded-full" style="width: ${percent}%"></div>
                  </div>
                  <div class="text-[9px] text-slate-400 mt-1">Total 5 Thn: ${total5Year}/160 SKP</div>
                </td>
                <td class="py-3 px-4 text-center">
                  ${d.pendingCredits > 0 
                    ? `<span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[11px]">+${d.pendingCredits} SKP</span>`
                    : '<span class="text-slate-400 text-[11px]">-</span>'}
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-sky-900 text-xs">${pel} <span class="text-slate-400 font-normal">/ 80</span></div>
                  <div class="w-20 bg-slate-100 rounded-full h-1 mt-1 overflow-hidden">
                    <div class="progress-bar-pelayanan h-1 rounded-full" style="width: ${Math.min(100, (pel / 80) * 100)}%"></div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-amber-900 text-xs">${peng} <span class="text-slate-400 font-normal">/ 30</span></div>
                  <div class="w-20 bg-slate-100 rounded-full h-1 mt-1 overflow-hidden">
                    <div class="progress-bar-pengabdian h-1 rounded-full" style="width: ${Math.min(100, (peng / 30) * 100)}%"></div>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-emerald-900 text-xs">${pemb} <span class="text-slate-400 font-normal">/ 50</span></div>
                  <div class="w-20 bg-slate-100 rounded-full h-1 mt-1 overflow-hidden">
                    <div class="progress-bar-pembelajaran h-1 rounded-full" style="width: ${Math.min(100, (pemb / 50) * 100)}%"></div>
                  </div>
                </td>
                <td class="py-3 px-4 text-center">${statusBadge}</td>
                <td class="py-3 px-4 text-[11px] text-slate-500">${lastAct}</td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button type="button" onclick="openManageDetailModal('doctor', '${d.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#006948] font-bold text-xs transition">
                      Rincian
                    </button>
                    <button type="button" onclick="openRowActions(event, 'doctor', '${d.id}')" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 font-black">
                      &bull;&bull;&bull;
                    </button>
                  </div>
                </td>
              </tr>
            `;
          }).join('');
        }

        // Mobile Touch Cards
        if (mobileContainer) {
          mobileContainer.innerHTML = pageDoctors.map(d => {
            const qAchieved = d.q4Credits !== undefined ? d.q4Credits : 8.0;
            const isComplete = (d.status === 'Complete' || qAchieved >= 8.0);
            const initials = d.initials || d.avatar || 'DR';
            const hospital = d.hospital || d.institution || 'RS Jejaring POGI';

            return `
              <div class="p-3.5 rounded-xl bg-white border border-slate-200/90 shadow-2xs space-y-2.5">
                <div class="flex items-start justify-between gap-2">
                  <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#006948] font-black text-xs flex items-center justify-center shrink-0">
                      ${initials}
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-slate-900 text-xs leading-snug truncate">${d.name}</div>
                      <div class="text-[10px] text-slate-400 font-mono">${d.pogiId} &bull; ${d.nik}</div>
                    </div>
                  </div>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${isComplete ? 'bg-emerald-50 text-[#006948] border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200'} border shrink-0">
                    ${isComplete ? 'Complete' : 'Incomplete'}
                  </span>
                </div>
                <div class="text-xs text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-100 space-y-0.5">
                  <div class="flex justify-between"><span class="text-slate-400 text-[10px]">Wilayah:</span> <span class="font-bold text-slate-800 text-[11px]">${d.province}</span></div>
                  <div class="flex justify-between"><span class="text-slate-400 text-[10px]">RS:</span> <span class="font-medium text-slate-700 text-[11px] truncate max-w-[200px]">${hospital}</span></div>
                </div>
                <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between gap-2">
                  <button type="button" onclick="openManageDetailModal('doctor', '${d.id}')" class="flex-1 py-1 px-2.5 rounded-lg bg-emerald-50 text-[#006948] text-xs font-bold transition flex items-center justify-center gap-1">
                    <span>Rincian</span>
                  </button>
                  <button type="button" onclick="openManageEditModal('doctor', '${d.id}')" class="py-1 px-2.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold transition rbac-operational">Edit</button>
                  <button type="button" onclick="deleteManageItem('doctor', '${d.id}')" class="py-1 px-2 rounded-lg text-red-600 hover:bg-red-50 text-xs font-bold transition rbac-operational">Hapus</button>
                </div>
              </div>
            `;
          }).join('');
        }
      }

      renderMedCredPaginationControls({
        containerId: 'manageDoctorPagination',
        currentPage: managePagination.doctor.page,
        totalPages,
        totalItems: managePagination.doctor.total,
        startIdx: managePagination.doctor.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: 'dokter Obgyn terdaftar di database nasional POGI',
        onPageChangeFn: "changeManagePageTab.bind(null, 'doctor')",
        onLimitChangeFn: "setManagePageLimitTab.bind(null, 'doctor')",
        currentLimit: managePagination.doctor.limit,
        limits: [10, 25, 50, 100]
      });
    }

    function filterManageDoctorTable() {
      const q = (document.getElementById('searchManageDoctorInput')?.value || '').toLowerCase();
      const prov = document.getElementById('manageDoctorProvFilter')?.value || 'ALL';
      const stat = document.getElementById('manageDoctorStatusFilter')?.value || 'ALL';

      const all = PogiStore.getDoctors();
      const filtered = all.filter(d => {
        const matchQ = (d.name || '').toLowerCase().includes(q) || (d.nik && d.nik.includes(q)) || (d.pogiId && d.pogiId.toLowerCase().includes(q)) || (d.strNumber && d.strNumber.includes(q));
        const matchProv = prov === 'ALL' || d.province === prov;
        const matchStat = stat === 'ALL' || d.status === stat;
        return matchQ && matchProv && matchStat;
      });

      renderManageDoctorTable(filtered, true);
    }

    function resetManageDoctorFilters() {
      document.getElementById('searchManageDoctorInput').value = '';
      document.getElementById('manageDoctorProvFilter').value = 'ALL';
      document.getElementById('manageDoctorStatusFilter').value = 'ALL';
      renderManageDoctorTable(null, true);
    }

    // ==========================================
    // TAB 2: INSTITUTION (PERSIS DENGAN FORMAT DOKTER)
    // ==========================================
    function renderManageInstTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.institution.page = 1;
      const allInst = filteredList || PogiStore.getInstitutions();
      currentFilteredCache.institution = allInst;
      managePagination.institution.total = allInst.length;

      const totalPages = Math.max(1, Math.ceil(managePagination.institution.total / managePagination.institution.limit));
      if (managePagination.institution.page > totalPages) managePagination.institution.page = totalPages;
      if (managePagination.institution.page < 1) managePagination.institution.page = 1;

      const start = (managePagination.institution.page - 1) * managePagination.institution.limit;
      const end = Math.min(start + managePagination.institution.limit, managePagination.institution.total);
      const pageInst = allInst.slice(start, end);

      const tbody = document.getElementById('manageInstTableBody');
      const mobileContainer = document.getElementById('manageInstMobileCards');
      const counterEl = document.getElementById('manageInstCounter');
      if (counterEl) counterEl.innerText = managePagination.institution.total;
      if (!tbody && !mobileContainer) return;

      if (pageInst.length === 0) {
        if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="py-12 text-center text-slate-400 font-medium">Tidak ada rumah sakit yang cocok dengan filter.</td></tr>';
        if (mobileContainer) mobileContainer.innerHTML = '<div class="py-8 text-center text-slate-400 font-medium text-xs">Tidak ada data rumah sakit.</div>';
      } else {
        // Desktop / Tablet Rows
        if (tbody) {
          tbody.innerHTML = pageInst.map(inst => `
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3 px-4">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-[#006948] font-black text-xs flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm">local_hospital</span>
                  </div>
                  <div>
                    <div class="font-bold text-slate-900 text-xs">${inst.name}</div>
                    <div class="text-[10px] text-slate-400 font-mono">${inst.id || '-'}</div>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-bold">${inst.type || 'RS Jejaring'}</span>
              </td>
              <td class="py-3 px-4 text-xs font-semibold text-slate-800">${inst.province}</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-[#006948] font-bold text-xs border border-emerald-200">${inst.doctorsCount || 0} Dokter</span>
              </td>
              <td class="py-3 px-4 text-center">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold ${inst.status === 'Aktif' || inst.status === 'Aktif Bekerjasama' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200'} border">
                  <span class="w-1.5 h-1.5 rounded-full ${inst.status === 'Aktif' || inst.status === 'Aktif Bekerjasama' ? 'bg-emerald-600' : 'bg-slate-400'}"></span>
                  ${inst.status || 'Aktif'}
                </span>
              </td>
              <td class="py-3 px-4 text-[11px] text-slate-500">${inst.lastActivity || 'Sync SATUSEHAT'}</td>
              <td class="py-3 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button type="button" onclick="openManageDetailModal('institution', '${inst.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#006948] font-bold text-xs transition">Rincian</button>
                  <button type="button" onclick="openRowActions(event, 'institution', '${inst.id}')" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 font-black">&bull;&bull;&bull;</button>
                </div>
              </td>
            </tr>
          `).join('');
        }

        // Mobile Touch Cards
        if (mobileContainer) {
          mobileContainer.innerHTML = pageInst.map(inst => `
            <div class="p-3.5 rounded-xl bg-white border border-slate-200/90 shadow-2xs space-y-2.5">
              <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                  <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#006948] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm">local_hospital</span>
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-900 text-xs leading-snug truncate">${inst.name}</div>
                    <div class="text-[10px] text-slate-400 font-mono">${inst.province} &bull; ${inst.type || 'RS'}</div>
                  </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-[#006948] font-bold text-[10px] border border-emerald-200 shrink-0">${inst.doctorsCount || 0} Dokter</span>
              </div>
              <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" onclick="openManageDetailModal('institution', '${inst.id}')" class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 text-[#006948] text-xs font-bold transition flex items-center justify-center gap-1">
                  <span>Rincian</span>
                  <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </button>
                <button type="button" onclick="openManageEditModal('institution', '${inst.id}')" class="py-1.5 px-3 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold transition rbac-operational">Edit</button>
              </div>
            </div>
          `).join('');
        }
      }

      renderMedCredPaginationControls({
        containerId: 'manageInstPagination',
        currentPage: managePagination.institution.page,
        totalPages,
        totalItems: managePagination.institution.total,
        startIdx: managePagination.institution.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: 'faskes jejaring POGI',
        onPageChangeFn: "changeManagePageTab.bind(null, 'institution')",
        onLimitChangeFn: "setManagePageLimitTab.bind(null, 'institution')",
        currentLimit: managePagination.institution.limit,
        limits: [10, 25, 50]
      });
    }

    function filterManageInstTable() {
      const q = (document.getElementById('searchManageInstInput')?.value || '').toLowerCase();
      const prov = document.getElementById('manageInstProvFilter')?.value || 'ALL';
      const all = PogiStore.getInstitutions();
      const filtered = all.filter(i => {
        const matchQ = (i.name || '').toLowerCase().includes(q) || (i.address || '').toLowerCase().includes(q);
        const matchProv = prov === 'ALL' || i.province === prov;
        return matchQ && matchProv;
      });
      renderManageInstTable(filtered, true);
    }

    function resetManageInstFilters() {
      document.getElementById('searchManageInstInput').value = '';
      document.getElementById('manageInstProvFilter').value = 'ALL';
      document.getElementById('manageInstTypeFilter').value = 'ALL';
      renderManageInstTable(null, true);
    }

    // ==========================================
    // TAB 3: SKP EVENT (PERSIS FORMAT DOKTER)
    // ==========================================
    function renderManageEventTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.event.page = 1;
      const allEvents = filteredList || PogiStore.getEvents();
      currentFilteredCache.event = allEvents;
      managePagination.event.total = allEvents.length;

      const totalPages = Math.max(1, Math.ceil(managePagination.event.total / managePagination.event.limit));
      if (managePagination.event.page > totalPages) managePagination.event.page = totalPages;
      if (managePagination.event.page < 1) managePagination.event.page = 1;

      const start = (managePagination.event.page - 1) * managePagination.event.limit;
      const end = Math.min(start + managePagination.event.limit, managePagination.event.total);
      const pageEvents = allEvents.slice(start, end);

      const tbody = document.getElementById('manageEventTableBody');
      const mobileContainer = document.getElementById('manageEventMobileCards');
      const counterEl = document.getElementById('manageEventCounter');
      if (counterEl) counterEl.innerText = managePagination.event.total;
      if (!tbody && !mobileContainer) return;

      if (pageEvents.length === 0) {
        if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="py-12 text-center text-slate-400 font-medium">Tidak ada kegiatan SKP yang cocok dengan kriteria filter.</td></tr>';
        if (mobileContainer) mobileContainer.innerHTML = '<div class="py-8 text-center text-slate-400 font-medium text-xs">Tidak ada data kegiatan.</div>';
      } else {
        // Desktop / Tablet Rows
        if (tbody) {
          tbody.innerHTML = pageEvents.map(e => {
            let badgeColor = 'bg-sky-100 text-sky-800 border-sky-200';
            if (e.type === 'Pembelajaran') badgeColor = 'bg-emerald-100 text-emerald-800 border-emerald-200';
            if (e.type === 'Pengabdian') badgeColor = 'bg-amber-100 text-amber-800 border-amber-200';
            const typeBadge = e.typeBadge || badgeColor;
            const eventTitle = e.title || e.name || 'Kegiatan SKP POGI';
            const eventCategory = e.category || e.venue || 'Pelatihan';
            const creditsVal = e.creditsDisplay || (e.credits ? e.credits.toString().replace('.', ',') : '1');
            const docCount = e.doctors ? e.doctors.replace('done', 'selesai') : ((e.doctorsCompleted || 0) + ' / 100 selesai');

            return `
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900 text-xs leading-snug truncate max-w-[240px]">${eventTitle}</div>
                  <div class="text-[10px] text-slate-400 font-mono mt-0.5">${e.date || 'Q4 2026'} &bull; ${e.id}</div>
                </td>
                <td class="py-3 px-4">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold ${typeBadge} border">
                    ${e.type}
                  </span>
                </td>
                <td class="py-3 px-4 text-xs font-semibold text-slate-700">${eventCategory}</td>
                <td class="py-3 px-4 text-center">
                  <span class="px-2.5 py-1 rounded-lg bg-[#006948] text-white font-black text-xs shadow-2xs">${creditsVal} SKP</span>
                </td>
                <td class="py-3 px-4 text-center text-xs font-semibold text-slate-800">${docCount}</td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold ${e.status === 'Dibuka' || e.status === 'Berjalan' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200'} border">
                    <span class="w-1.5 h-1.5 rounded-full ${e.status === 'Dibuka' || e.status === 'Berjalan' ? 'bg-emerald-600' : 'bg-slate-400'}"></span>
                    ${e.status || 'Dibuka'}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button type="button" onclick="openManageDetailModal('event', '${e.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#006948] font-bold text-xs transition">Rincian</button>
                    <button type="button" onclick="openRowActions(event, 'event', '${e.id}')" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 font-black">&bull;&bull;&bull;</button>
                  </div>
                </td>
              </tr>
            `;
          }).join('');
        }

        // Mobile Touch Cards
        if (mobileContainer) {
          mobileContainer.innerHTML = pageEvents.map(e => {
            const eventTitle = e.title || e.name || 'Kegiatan SKP POGI';
            const creditsVal = e.creditsDisplay || (e.credits ? e.credits.toString().replace('.', ',') : '1');
            const docCount = e.doctors ? e.doctors.replace('done', 'selesai') : ((e.doctorsCompleted || 0) + ' / 100 selesai');

            return `
              <div class="p-3.5 rounded-xl bg-white border border-slate-200/90 shadow-2xs space-y-2.5">
                <div class="flex items-start justify-between gap-2">
                  <div class="min-w-0">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-[#006948] border border-emerald-200">${e.type}</span>
                    <div class="font-bold text-slate-900 text-xs mt-1.5 leading-snug line-clamp-2">${eventTitle}</div>
                  </div>
                  <span class="px-2 py-1 rounded-lg bg-[#006948] text-white font-black text-xs shrink-0">${creditsVal} SKP</span>
                </div>
                <div class="text-[11px] text-slate-500 flex justify-between pt-1 border-t border-slate-100">
                  <span>${e.date || 'Q4 2026'}</span>
                  <span class="font-bold text-slate-800">${docCount}</span>
                </div>
                <div class="pt-1.5 flex items-center justify-between gap-2">
                  <button type="button" onclick="openManageDetailModal('event', '${e.id}')" class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 text-[#006948] text-xs font-bold transition flex items-center justify-center gap-1">
                    <span>Rincian</span>
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                  </button>
                  <button type="button" onclick="openManageEditModal('event', '${e.id}')" class="py-1.5 px-3 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold transition rbac-operational">Edit</button>
                </div>
              </div>
            `;
          }).join('');
        }
      }

      renderMedCredPaginationControls({
        containerId: 'manageEventPagination',
        currentPage: managePagination.event.page,
        totalPages,
        totalItems: managePagination.event.total,
        startIdx: managePagination.event.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: 'kegiatan ber-SKP',
        onPageChangeFn: "changeManagePageTab.bind(null, 'event')",
        onLimitChangeFn: "setManagePageLimitTab.bind(null, 'event')",
        currentLimit: managePagination.event.limit,
        limits: [10, 25, 50]
      });
    }

    function filterManageEventTable() {
      const q = (document.getElementById('searchManageEventInput')?.value || '').toLowerCase();
      const type = document.getElementById('manageEventTypeFilter')?.value || 'ALL';
      const all = PogiStore.getEvents();
      const filtered = all.filter(e => {
        const matchQ = (e.name || e.title || '').toLowerCase().includes(q) || (e.category || '').toLowerCase().includes(q);
        const matchType = type === 'ALL' || e.type === type;
        return matchQ && matchType;
      });
      renderManageEventTable(filtered, true);
    }

    function resetManageEventFilters() {
      document.getElementById('searchManageEventInput').value = '';
      document.getElementById('manageEventTypeFilter').value = 'ALL';
      document.getElementById('manageEventStatusFilter').value = 'ALL';
      renderManageEventTable(null, true);
    }

    // ==========================================
    // TAB 4: EVENT CATEGORY (PERSIS FORMAT DOKTER)
    // ==========================================
    function renderManageCatTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.category.page = 1;
      const allCats = filteredList || PogiStore.getCategories();
      currentFilteredCache.category = allCats;
      managePagination.category.total = allCats.length;

      const totalPages = Math.max(1, Math.ceil(managePagination.category.total / managePagination.category.limit));
      if (managePagination.category.page > totalPages) managePagination.category.page = totalPages;
      if (managePagination.category.page < 1) managePagination.category.page = 1;

      const start = (managePagination.category.page - 1) * managePagination.category.limit;
      const end = Math.min(start + managePagination.category.limit, managePagination.category.total);
      const pageCats = allCats.slice(start, end);

      const tbody = document.getElementById('manageCatTableBody');
      const mobileContainer = document.getElementById('manageCatMobileCards');
      const counterEl = document.getElementById('manageCatCounter');
      if (counterEl) counterEl.innerText = managePagination.category.total;
      if (!tbody && !mobileContainer) return;

      if (pageCats.length === 0) {
        if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="py-12 text-center text-slate-400 font-medium">Tidak ada kategori yang cocok dengan filter.</td></tr>';
        if (mobileContainer) mobileContainer.innerHTML = '<div class="py-8 text-center text-slate-400 font-medium text-xs">Tidak ada data kategori.</div>';
      } else {
        // Desktop / Tablet Rows
        if (tbody) {
          tbody.innerHTML = pageCats.map(cat => {
            let badgeColor = 'bg-sky-100 text-sky-800 border-sky-200';
            if (cat.type === 'Pembelajaran') badgeColor = 'bg-emerald-100 text-emerald-800 border-emerald-200';
            if (cat.type === 'Pengabdian') badgeColor = 'bg-amber-100 text-amber-800 border-amber-200';

            return `
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900 text-xs">${cat.name}</div>
                  <div class="text-[10px] text-slate-400 font-mono">${cat.id}</div>
                </td>
                <td class="py-3 px-4">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold ${badgeColor} border">${cat.type}</span>
                </td>
                <td class="py-3 px-4 text-xs font-black text-[#006948]">${cat.standardSkp || '1 - 2 SKP'}</td>
                <td class="py-3 px-4 text-xs text-slate-600 max-w-xs truncate">${cat.description || '-'}</td>
                <td class="py-3 px-4 text-center">
                  <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold">Aktif</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button type="button" onclick="openManageDetailModal('category', '${cat.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#006948] font-bold text-xs transition">Rincian</button>
                    <button type="button" onclick="openRowActions(event, 'category', '${cat.id}')" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 font-black">&bull;&bull;&bull;</button>
                  </div>
                </td>
              </tr>
            `;
          }).join('');
        }

        // Mobile Touch Cards
        if (mobileContainer) {
          mobileContainer.innerHTML = pageCats.map(cat => `
            <div class="p-3.5 rounded-xl bg-white border border-slate-200/90 shadow-2xs space-y-2">
              <div class="flex items-start justify-between gap-2">
                <div>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">${cat.type}</span>
                  <div class="font-bold text-slate-900 text-xs mt-1">${cat.name}</div>
                </div>
                <span class="font-black text-xs text-[#006948]">${cat.standardSkp || '1 - 2 SKP'}</span>
              </div>
              <div class="text-[11px] text-slate-500">${cat.description || '-'}</div>
              <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" onclick="openManageDetailModal('category', '${cat.id}')" class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 text-[#006948] text-xs font-bold transition flex items-center justify-center gap-1">
                  <span>Rincian</span>
                  <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </button>
                <button type="button" onclick="openManageEditModal('category', '${cat.id}')" class="py-1.5 px-3 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold transition rbac-operational">Edit</button>
              </div>
            </div>
          `).join('');
        }
      }

      renderMedCredPaginationControls({
        containerId: 'manageCatPagination',
        currentPage: managePagination.category.page,
        totalPages,
        totalItems: managePagination.category.total,
        startIdx: managePagination.category.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: 'kategori kegiatan SKP',
        onPageChangeFn: "changeManagePageTab.bind(null, 'category')",
        onLimitChangeFn: "setManagePageLimitTab.bind(null, 'category')",
        currentLimit: managePagination.category.limit,
        limits: [10, 25, 50]
      });
    }

    function filterManageCatTable() {
      const q = (document.getElementById('searchManageCatInput')?.value || '').toLowerCase();
      const type = document.getElementById('manageCatTypeFilter')?.value || 'ALL';
      const all = PogiStore.getCategories();
      const filtered = all.filter(c => {
        const matchQ = (c.name || '').toLowerCase().includes(q);
        const matchType = type === 'ALL' || c.type === type;
        return matchQ && matchType;
      });
      renderManageCatTable(filtered, true);
    }

    function resetManageCatFilters() {
      document.getElementById('searchManageCatInput').value = '';
      document.getElementById('manageCatTypeFilter').value = 'ALL';
      renderManageCatTable(null, true);
    }

    // ==========================================
    // TAB 5: USER MASTER (PERSIS FORMAT DOKTER)
    // ==========================================
    function renderManageUserTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.user.page = 1;
      const allUsers = filteredList || PogiStore.getUsers();
      currentFilteredCache.user = allUsers;
      managePagination.user.total = allUsers.length;

      const totalPages = Math.max(1, Math.ceil(managePagination.user.total / managePagination.user.limit));
      if (managePagination.user.page > totalPages) managePagination.user.page = totalPages;
      if (managePagination.user.page < 1) managePagination.user.page = 1;

      const start = (managePagination.user.page - 1) * managePagination.user.limit;
      const end = Math.min(start + managePagination.user.limit, managePagination.user.total);
      const pageUsers = allUsers.slice(start, end);

      const tbody = document.getElementById('manageUserTableBody');
      const mobileContainer = document.getElementById('manageUserMobileCards');
      const counterEl = document.getElementById('manageUserCounter');
      if (counterEl) counterEl.innerText = managePagination.user.total;
      if (!tbody && !mobileContainer) return;

      if (pageUsers.length === 0) {
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="py-12 text-center text-slate-400 font-medium">Tidak ada pengguna yang cocok dengan filter.</td></tr>';
        if (mobileContainer) mobileContainer.innerHTML = '<div class="py-8 text-center text-slate-400 font-medium text-xs">Tidak ada data pengguna.</div>';
      } else {
        const getRoleBadge = (role) => {
          if (role === 'Super Admin') return '<span class="px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 border border-purple-200 font-extrabold text-[10px]">Super Admin</span>';
          if (role === 'Admin Pusat') return '<span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 font-extrabold text-[10px]">Admin Pusat</span>';
          if (role === 'Admin POGI' || role === 'Verifikator') return '<span class="px-2.5 py-1 rounded-md bg-emerald-50 text-[#006948] border border-emerald-200 font-extrabold text-[10px]">Admin POGI</span>';
          if (role === 'Admin Cabang') return '<span class="px-2.5 py-1 rounded-md bg-sky-50 text-sky-700 border border-sky-200 font-extrabold text-[10px]">Admin Cabang</span>';
          return '<span class="px-2.5 py-1 rounded-md bg-teal-50 text-teal-700 border border-teal-200 font-extrabold text-[10px]">User/Dokter</span>';
        };

        // Desktop / Tablet Rows
        if (tbody) {
          tbody.innerHTML = pageUsers.map(usr => `
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3 px-4">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#006948] font-black text-xs flex items-center justify-center shrink-0">
                    ${(usr.name || 'US').substring(0, 2).toUpperCase()}
                  </div>
                  <div>
                    <div class="font-bold text-slate-900 text-xs">${usr.name}</div>
                    <div class="text-[10px] text-slate-400 font-mono">${usr.id}</div>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4 text-xs text-slate-700 font-mono">${usr.email}</td>
              <td class="py-3 px-4">
                ${getRoleBadge(usr.role)}
              </td>
              <td class="py-3 px-4 text-[11px] text-slate-500">${usr.created || PogiStore.formatCurrentDate()}</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold">Aktif</span>
              </td>
              <td class="py-3 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button type="button" onclick="openManageDetailModal('user', '${usr.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#006948] font-bold text-xs transition">Rincian</button>
                  <button type="button" onclick="openRowActions(event, 'user', '${usr.id}')" class="p-1.5 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 font-black">&bull;&bull;&bull;</button>
                </div>
              </td>
            </tr>
          `).join('');
        }

        // Mobile Touch Cards
        if (mobileContainer) {
          mobileContainer.innerHTML = pageUsers.map(usr => `
            <div class="p-3.5 rounded-xl bg-white border border-slate-200/90 shadow-2xs space-y-2">
              <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-full bg-emerald-50 text-[#006948] font-black text-xs flex items-center justify-center shrink-0">
                    ${(usr.name || 'US').substring(0, 2).toUpperCase()}
                  </div>
                  <div>
                    <div class="font-bold text-slate-900 text-xs">${usr.name}</div>
                    <div class="text-[10px] text-slate-400 font-mono">${usr.email}</div>
                  </div>
                </div>
                ${getRoleBadge(usr.role)}
              </div>
              <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" onclick="openManageDetailModal('user', '${usr.id}')" class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 text-[#006948] text-xs font-bold transition flex items-center justify-center gap-1">
                  <span>Rincian</span>
                  <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </button>
                <button type="button" onclick="openManageEditModal('user', '${usr.id}')" class="py-1.5 px-3 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold transition rbac-operational">Edit</button>
              </div>
            </div>
          `).join('');
        }
      }

      renderMedCredPaginationControls({
        containerId: 'manageUserPagination',
        currentPage: managePagination.user.page,
        totalPages,
        totalItems: managePagination.user.total,
        startIdx: managePagination.user.total === 0 ? 0 : start + 1,
        endIdx: end,
        itemLabel: 'pengguna terdaftar',
        onPageChangeFn: "changeManagePageTab.bind(null, 'user')",
        onLimitChangeFn: "setManagePageLimitTab.bind(null, 'user')",
        currentLimit: managePagination.user.limit,
        limits: [10, 25, 50]
      });
    }

    function filterManageUserTable() {
      const q = (document.getElementById('searchManageUserInput')?.value || '').toLowerCase();
      const role = document.getElementById('manageUserRoleFilter')?.value || 'ALL';
      const all = PogiStore.getUsers();
      const filtered = all.filter(u => {
        const matchQ = (u.name || '').toLowerCase().includes(q) || (u.email || '').toLowerCase().includes(q);
        const matchRole = role === 'ALL' || u.role === role;
        return matchQ && matchRole;
      });
      renderManageUserTable(filtered, true);
    }

    function resetManageUserFilters() {
      document.getElementById('searchManageUserInput').value = '';
      document.getElementById('manageUserRoleFilter').value = 'ALL';
      renderManageUserTable(null, true);
    }

    // ==========================================
    // ACTION DROPDOWN & REAL MODAL HANDLERS
    // ==========================================
    let activeActionContext = { type: null, id: null };

    function openRowActions(event, type, id) {
      event.stopPropagation();
      activeActionContext = { type, id };
      const menu = document.getElementById('rowActionMenu');
      if (!menu) return;

      menu.style.left = `${Math.min(window.innerWidth - 180, event.clientX - 120)}px`;
      menu.style.top = `${event.clientY + 10}px`;
      menu.classList.remove('hidden');
    }

    document.addEventListener('click', () => {
      const menu = document.getElementById('rowActionMenu');
      if (menu) menu.classList.add('hidden');
    });

    function handleRowAction(action) {
      const { type, id } = activeActionContext;
      if (!type || !id) return;

      if (action === 'delete') {
        deleteManageItem(type, id);
      } else if (action === 'edit') {
        openManageEditModal(type, id);
      } else if (action === 'detail') {
        openManageDetailModal(type, id);
      }
    }

    // VIEW DETAILS MODAL
    function openManageDetailModal(type, id) {
      const modal = document.getElementById('manageDetailModal');
      const title = document.getElementById('manageDetailModalTitle');
      const container = document.getElementById('manageDetailContent');
      if (!modal || !title || !container) return;

      let item = null;
      let html = '';

      if (type === 'doctor') {
        item = PogiStore.getDoctors().find(d => d.id === id);
        if (!item) return;
        title.innerText = 'Rincian Dokter: ' + item.name;
        html = `
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-800 text-white font-extrabold text-base flex items-center justify-center">
              ${item.avatar || 'DR'}
            </div>
            <div>
              <div class="font-extrabold text-sm text-slate-900">${item.name}</div>
              <div class="text-xs text-slate-500 font-mono">NIK: ${item.nik || '-'} &bull; ID: ${item.pogiId || '-'}</div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div class="p-2.5 bg-white border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Provinsi / Cabang</div>
              <div class="font-bold text-slate-800 mt-0.5">${item.province}</div>
            </div>
            <div class="p-2.5 bg-white border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Rumah Sakit</div>
              <div class="font-bold text-slate-800 mt-0.5 truncate">${item.institution}</div>
            </div>
            <div class="p-2.5 bg-white border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Target SKP Tahunan</div>
              <div class="font-black text-[#006948] mt-0.5 text-sm">${item.skpDisplay || (item.qAchieved + ' / ' + item.qTarget)}</div>
            </div>
            <div class="p-2.5 bg-white border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Status SIP / STR</div>
              <div class="font-bold text-slate-800 mt-0.5">${item.status}</div>
            </div>
            <div class="p-2.5 bg-white border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">No. WhatsApp</div>
              <div class="font-mono text-slate-700 mt-0.5">${item.phone || '-'}</div>
            </div>
            <div class="p-2.5 bg-white border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">PIC Admin POGI</div>
              <div class="font-bold text-slate-800 mt-0.5">${item.assignedTo || 'Rina Kartika'}</div>
            </div>
          </div>
          <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
            <div class="font-bold text-emerald-900 text-xs mb-1">Rincian Ranah SKP (Standar 160 SKP):</div>
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
              <div class="bg-white p-2 rounded-lg border border-emerald-100">
                <div class="text-[10px] text-slate-400 font-bold">1. Pelayanan</div>
                <div class="font-black text-slate-800 mt-0.5">${item.pelayanan?.achieved || 0} / 80</div>
              </div>
              <div class="bg-white p-2 rounded-lg border border-emerald-100">
                <div class="text-[10px] text-slate-400 font-bold">2. Pengabdian</div>
                <div class="font-black text-slate-800 mt-0.5">${item.pengabdian?.achieved || 0} / 30</div>
              </div>
              <div class="bg-white p-2 rounded-lg border border-emerald-100">
                <div class="text-[10px] text-slate-400 font-bold">3. Belajar</div>
                <div class="font-black text-slate-800 mt-0.5">${item.pembelajaran?.achieved || 0} / 50</div>
              </div>
            </div>
          </div>
        `;
      } else if (type === 'institution') {
        item = PogiStore.getInstitutions().find(i => i.id === id);
        if (!item) return;
        title.innerText = 'Rincian RS: ' + item.name;
        html = `
          <div class="space-y-3">
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Nama Rumah Sakit</div>
              <div class="font-black text-slate-900 text-sm mt-0.5">${item.name}</div>
            </div>
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Provinsi &amp; Alamat</div>
              <div class="font-semibold text-slate-800 text-xs mt-0.5">${item.address || (item.province + ' - RS Jejaring')}</div>
            </div>
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
              <div>
                <div class="text-[10px] text-emerald-800 font-bold uppercase">Dokter Obgyn Bekerja</div>
                <div class="font-black text-[#006948] text-lg mt-0.5">${item.doctors} Dokter Spesialis</div>
              </div>
              <span class="material-symbols-outlined text-3xl text-emerald-700">domain</span>
            </div>
          </div>
        `;
      } else if (type === 'event') {
        item = PogiStore.getEvents().find(e => e.id === id);
        if (!item) return;
        title.innerText = 'Rincian Kegiatan SKP: ' + (item.name || item.title);
        html = `
          <div class="space-y-3">
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Nama Kegiatan Ilmiah</div>
              <div class="font-black text-slate-900 text-sm mt-0.5">${item.name || item.title}</div>
              <div class="text-xs text-slate-500 mt-1">${item.category || 'Workshop Terakreditasi'}</div>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Ranah SKP</div>
                <div class="font-bold text-slate-800 mt-0.5">${item.type}</div>
              </div>
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Bobot Kredit</div>
                <div class="font-black text-[#006948] mt-0.5 text-sm">${item.creditsDisplay || item.credits} SKP</div>
              </div>
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Jadwal Tanggal</div>
                <div class="font-bold text-slate-800 mt-0.5">${item.date}</div>
              </div>
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Peserta Selesai</div>
                <div class="font-bold text-slate-800 mt-0.5">${item.doctors || (item.doctorsCompleted + ' done')}</div>
              </div>
            </div>
          </div>
        `;
      } else if (type === 'category') {
        item = PogiStore.getCategories().find(c => c.id === id);
        if (!item) return;
        title.innerText = 'Rincian Kategori: ' + item.name;
        html = `
          <div class="space-y-3">
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Nama Kategori</div>
              <div class="font-black text-slate-900 text-sm mt-0.5">${item.name}</div>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Ranah Terkait</div>
                <div class="font-bold text-slate-800 mt-0.5">${item.type}</div>
              </div>
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Jumlah Event Terhubung</div>
                <div class="font-black text-[#006948] mt-0.5 text-sm">${item.eventsCount} Event</div>
              </div>
            </div>
          </div>
        `;
      } else if (type === 'user') {
        item = PogiStore.getUsers().find(u => u.id === id);
        if (!item) return;
        title.innerText = 'Rincian User: ' + item.name;
        html = `
          <div class="space-y-3">
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
              <div class="text-[10px] text-slate-400 font-bold uppercase">Nama Pengguna</div>
              <div class="font-black text-slate-900 text-sm mt-0.5">${item.name}</div>
              <div class="text-xs text-slate-500 font-mono mt-0.5">${item.email}</div>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Role Akses</div>
                <div class="font-bold text-slate-800 mt-0.5">${item.role}</div>
              </div>
              <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-[10px] text-slate-400 font-bold uppercase">Terdaftar Sejak</div>
                <div class="font-bold text-slate-800 mt-0.5">${item.created}</div>
              </div>
            </div>
          </div>
        `;
      }

      container.innerHTML = html;
      modal.classList.remove('hidden');
    }

    function closeManageDetailModal() {
      const modal = document.getElementById('manageDetailModal');
      if (modal) modal.classList.add('hidden');
    }

    // EDIT ITEM MODAL
    function openManageEditModal(type, id) {
      const modal = document.getElementById('manageEditModal');
      const title = document.getElementById('manageEditModalTitle');
      const container = document.getElementById('manageEditFormFields');
      if (!modal || !title || !container) return;

      activeActionContext = { type, id };

      if (type === 'doctor') {
        const item = PogiStore.getDoctors().find(d => d.id === id);
        if (!item) return;
        title.innerText = 'Edit Dokter Obgyn: ' + item.name;
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Dokter & Gelar</label><input required type="text" id="editDocName" value="${item.name}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">NIK (16 Digit)</label><input required type="text" id="editDocNik" value="${item.nik || ''}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono" /></div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="font-bold text-slate-700 block mb-1">Provinsi / Cabang</label>
              <select id="editDocProv" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                ${PogiStore.getProvinces().map(p => `<option value="${p.name}" ${item.province === p.name ? 'selected' : ''}>${p.name}</option>`).join('')}
              </select>
            </div>
            <div><label class="font-bold text-slate-700 block mb-1">Target SKP</label><input type="text" id="editDocSkp" value="${item.skpDisplay || '0 / 32'}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" /></div>
          </div>
          <div><label class="font-bold text-slate-700 block mb-1">Rumah Sakit / Institusi</label><input type="text" id="editDocInst" value="${item.institution || ''}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">No. WhatsApp</label><input type="text" id="editDocPhone" value="${item.phone || ''}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono" /></div>
        `;
      } else if (type === 'institution') {
        const item = PogiStore.getInstitutions().find(i => i.id === id);
        if (!item) return;
        title.innerText = 'Edit Rumah Sakit: ' + item.name;
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Rumah Sakit</label><input required type="text" id="editInstName" value="${item.name}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Provinsi / Wilayah</label><input required type="text" id="editInstProv" value="${item.province}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Alamat Lengkap</label><textarea id="editInstAddr" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">${item.address || ''}</textarea></div>
        `;
      } else if (type === 'event') {
        const item = PogiStore.getEvents().find(e => e.id === id);
        if (!item) return;
        title.innerText = 'Edit Event: ' + (item.name || item.title);
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Event</label><input required type="text" id="editEvName" value="${item.name || item.title}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" /></div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="font-bold text-slate-700 block mb-1">Ranah</label>
              <select id="editEvType" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                <option value="Pembelajaran" ${item.type === 'Pembelajaran' ? 'selected' : ''}>Pembelajaran</option>
                <option value="Pengabdian" ${item.type === 'Pengabdian' ? 'selected' : ''}>Pengabdian</option>
                <option value="Pelayanan" ${item.type === 'Pelayanan' ? 'selected' : ''}>Pelayanan</option>
              </select>
            </div>
            <div><label class="font-bold text-slate-700 block mb-1">Nilai SKP</label><input type="text" id="editEvCredits" value="${item.creditsDisplay || item.credits}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" /></div>
          </div>
          <div><label class="font-bold text-slate-700 block mb-1">Kategori Event</label><input type="text" id="editEvCat" value="${item.category || ''}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Tanggal Pelaksanaan</label><input type="text" id="editEvDate" value="${item.date || ''}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
        `;
      } else if (type === 'category') {
        const item = PogiStore.getCategories().find(c => c.id === id);
        if (!item) return;
        title.innerText = 'Edit Kategori: ' + item.name;
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Kategori</label><input required type="text" id="editCatName" value="${item.name}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" /></div>
          <div>
            <label class="font-bold text-slate-700 block mb-1">Ranah SKP</label>
            <select id="editCatType" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
              <option value="Pembelajaran" ${item.type === 'Pembelajaran' ? 'selected' : ''}>Pembelajaran</option>
              <option value="Pengabdian" ${item.type === 'Pengabdian' ? 'selected' : ''}>Pengabdian</option>
              <option value="Pelayanan" ${item.type === 'Pelayanan' ? 'selected' : ''}>Pelayanan</option>
            </select>
          </div>
        `;
      } else if (type === 'user') {
        const item = PogiStore.getUsers().find(u => u.id === id);
        if (!item) return;
        title.innerText = 'Edit User: ' + item.name;
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Pengguna</label><input required type="text" id="editUsrName" value="${item.name}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Email</label><input required type="email" id="editUsrEmail" value="${item.email}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono" /></div>
          <div>
            <label class="font-bold text-slate-700 block mb-1">Role / Peran Akses</label>
            <select id="editUsrRole" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
              <option value="Super Admin" ${item.role === 'Super Admin' ? 'selected' : ''}>Super Admin</option>
              <option value="Admin Pusat" ${item.role === 'Admin Pusat' ? 'selected' : ''}>Admin Pusat</option>
              <option value="Admin POGI" ${item.role === 'Admin POGI' || item.role === 'Verifikator' ? 'selected' : ''}>Admin POGI</option>
              <option value="Admin Cabang" ${item.role === 'Admin Cabang' ? 'selected' : ''}>Admin Cabang</option>
              <option value="User/Dokter" ${item.role === 'User/Dokter' || item.role === 'Dokter' ? 'selected' : ''}>User/Dokter</option>
            </select>
          </div>
        `;
      }

      modal.classList.remove('hidden');
    }

    function closeManageEditModal() {
      const modal = document.getElementById('manageEditModal');
      if (modal) modal.classList.add('hidden');
    }

    function submitManageEdit() {
      const { type, id } = activeActionContext;
      if (!type || !id) return;

      if (type === 'doctor') {
        const docs = PogiStore.getDoctors();
        const doc = docs.find(d => d.id === id);
        if (doc) {
          doc.name = document.getElementById('editDocName').value;
          doc.nik = document.getElementById('editDocNik').value;
          doc.province = document.getElementById('editDocProv').value;
          doc.institution = document.getElementById('editDocInst').value;
          doc.phone = document.getElementById('editDocPhone').value;
          doc.skpDisplay = document.getElementById('editDocSkp').value;
          PogiStore.saveDoctors(docs);
          renderManageDoctorTable(null, false);
          showToast('Data Dokter ' + doc.name + ' berhasil diperbarui!', 'success');
        }
      } else if (type === 'institution') {
        const list = PogiStore.getInstitutions();
        const inst = list.find(i => i.id === id);
        if (inst) {
          inst.name = document.getElementById('editInstName').value;
          inst.province = document.getElementById('editInstProv').value;
          inst.address = document.getElementById('editInstAddr').value;
          PogiStore.saveInstitutions(list);
          renderManageInstTable(null, false);
          showToast('Data Institusi berhasil diperbarui!', 'success');
        }
      } else if (type === 'event') {
        const list = PogiStore.getEvents();
        const ev = list.find(e => e.id === id);
        if (ev) {
          ev.name = document.getElementById('editEvName').value;
          ev.title = ev.name;
          ev.type = document.getElementById('editEvType').value;
          ev.creditsDisplay = document.getElementById('editEvCredits').value;
          ev.credits = parseFloat(ev.creditsDisplay) || 1;
          ev.category = document.getElementById('editEvCat').value;
          ev.date = document.getElementById('editEvDate').value;
          PogiStore.saveEvents(list);
          renderManageEventTable(null, false);
          showToast('Data SKP Event berhasil diperbarui!', 'success');
        }
      } else if (type === 'category') {
        const list = PogiStore.getCategories();
        const cat = list.find(c => c.id === id);
        if (cat) {
          cat.name = document.getElementById('editCatName').value;
          cat.type = document.getElementById('editCatType').value;
          PogiStore.saveCategories(list);
          renderManageCatTable(null, false);
          showToast('Data Kategori berhasil diperbarui!', 'success');
        }
      } else if (type === 'user') {
        const list = PogiStore.getUsers();
        const usr = list.find(u => u.id === id);
        if (usr) {
          usr.name = document.getElementById('editUsrName').value;
          usr.email = document.getElementById('editUsrEmail').value;
          usr.role = document.getElementById('editUsrRole').value;
          PogiStore.saveUsers(list);
          renderManageUserTable(null, false);
          showToast('Data User berhasil diperbarui!', 'success');
        }
      }

      PogiStore.addActivity({
        actor: 'Siti Rahma (Admin Data)',
        action: 'Update Data ' + type.toUpperCase(),
        details: 'Memperbarui data master ' + type + ' ID: ' + id,
        category: 'Verification'
      });

      closeManageEditModal();
    }

    function deleteManageItem(type, id) {
      if (!confirm('Apakah Anda yakin ingin menghapus data ' + type + ' ini dari master data POGI? Tindakan ini tidak dapat dibatalkan.')) {
        return;
      }

      if (type === 'doctor') {
        let docs = PogiStore.getDoctors();
        docs = docs.filter(d => d.id !== id);
        PogiStore.saveDoctors(docs);
        renderManageDoctorTable(null, false);
      } else if (type === 'institution') {
        let list = PogiStore.getInstitutions();
        list = list.filter(i => i.id !== id);
        PogiStore.saveInstitutions(list);
        renderManageInstTable(null, false);
      } else if (type === 'event') {
        let list = PogiStore.getEvents();
        list = list.filter(e => e.id !== id);
        PogiStore.saveEvents(list);
        renderManageEventTable(null, false);
      } else if (type === 'category') {
        let list = PogiStore.getCategories();
        list = list.filter(c => c.id !== id);
        PogiStore.saveCategories(list);
        renderManageCatTable(null, false);
      } else if (type === 'user') {
        let list = PogiStore.getUsers();
        list = list.filter(u => u.id !== id);
        PogiStore.saveUsers(list);
        renderManageUserTable(null, false);
      }

      PogiStore.addActivity({
        actor: 'Siti Rahma (Admin Data)',
        action: 'Hapus Data ' + type.toUpperCase(),
        details: 'Menghapus data ' + type + ' dari database master ID: ' + id,
        category: 'Verification'
      });

      showToast('Data ' + type + ' berhasil dihapus dari master!', 'warning');
    }

    function openActiveAddModal() {
      const modal = document.getElementById('manageAddModal');
      const title = document.getElementById('manageAddModalTitle');
      const container = document.getElementById('manageAddFormFields');
      if (!modal || !title || !container) return;

      if (currentTab === 'doctor') {
        title.innerText = '+ Tambah Dokter Spesialis Obgyn';
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Lengkap & Gelar</label><input required type="text" id="addDocName" placeholder="dr. ..., Sp.OG" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">NIK (16 Digit)</label><input required type="text" id="addDocNik" placeholder="3201..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div class="grid grid-cols-2 gap-2">
            <div><label class="font-bold text-slate-700 block mb-1">Provinsi / Cabang</label><select id="addDocProv" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
              <option value="Aceh">Aceh</option>
              <option value="Bali">Bali</option>
              <option value="Banten">Banten</option>
              <option value="Bengkulu">Bengkulu</option>
              <option value="DI Yogyakarta">DI Yogyakarta</option>
              <option value="DKI Jakarta">DKI Jakarta</option>
              <option value="Gorontalo">Gorontalo</option>
              <option value="Jambi">Jambi</option>
              <option value="Jawa Barat">Jawa Barat</option>
              <option value="Jawa Tengah">Jawa Tengah</option>
              <option value="Jawa Timur">Jawa Timur</option>
              <option value="Kalimantan Barat">Kalimantan Barat</option>
              <option value="Kalimantan Selatan">Kalimantan Selatan</option>
              <option value="Kalimantan Tengah">Kalimantan Tengah</option>
              <option value="Kalimantan Timur">Kalimantan Timur</option>
              <option value="Kalimantan Utara">Kalimantan Utara</option>
              <option value="Kepulauan Bangka Belitung">Kepulauan Bangka Belitung</option>
              <option value="Kepulauan Riau">Kepulauan Riau</option>
              <option value="Lampung">Lampung</option>
              <option value="Maluku">Maluku</option>
              <option value="Maluku Utara">Maluku Utara</option>
              <option value="Nusa Tenggara Barat">Nusa Tenggara Barat</option>
              <option value="Nusa Tenggara Timur">Nusa Tenggara Timur</option>
              <option value="Papua">Papua</option>
              <option value="Papua Barat">Papua Barat</option>
              <option value="Papua Barat Daya">Papua Barat Daya</option>
              <option value="Papua Pegunungan">Papua Pegunungan</option>
              <option value="Papua Selatan">Papua Selatan</option>
              <option value="Papua Tengah">Papua Tengah</option>
              <option value="Riau">Riau</option>
              <option value="Sulawesi Barat">Sulawesi Barat</option>
              <option value="Sulawesi Selatan">Sulawesi Selatan</option>
              <option value="Sulawesi Tengah">Sulawesi Tengah</option>
              <option value="Sulawesi Tenggara">Sulawesi Tenggara</option>
              <option value="Sulawesi Utara">Sulawesi Utara</option>
              <option value="Sumatera Barat">Sumatera Barat</option>
              <option value="Sumatera Selatan">Sumatera Selatan</option>
              <option value="Sumatera Utara">Sumatera Utara</option>
              </select></div>
            <div><label class="font-bold text-slate-700 block mb-1">Target SKP</label><input type="text" id="addDocSkp" value="0 / 32" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          </div>
          <div><label class="font-bold text-slate-700 block mb-1">Rumah Sakit / Institusi</label><input type="text" id="addDocInst" placeholder="RSUP Dr. Cipto Mangunkusumo" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">No Telepon / WhatsApp</label><input type="text" id="addDocPhone" placeholder="+62812..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
        `;
      } else if (currentTab === 'institution') {
        title.innerText = '+ Tambah Rumah Sakit / Faskes Jejaring';
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Rumah Sakit / Klinik</label><input required type="text" id="addInstName" placeholder="RS ..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Provinsi</label><input required type="text" id="addInstProv" placeholder="DKI Jakarta" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Alamat Lengkap</label><textarea id="addInstAddr" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" placeholder="Jl. ..."></textarea></div>
        `;
      } else if (currentTab === 'event') {
        title.innerText = '+ Tambah Event SKP Terakreditasi';
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Event / Kegiatan</label><input required type="text" id="addEvName" placeholder="Contoh: PIT POGI 2026..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div class="grid grid-cols-2 gap-2">
            <div><label class="font-bold text-slate-700 block mb-1">Ranah</label><select id="addEvType" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><option value="Pembelajaran">Pembelajaran</option><option value="Pengabdian">Pengabdian</option><option value="Pelayanan">Pelayanan</option></select></div>
            <div><label class="font-bold text-slate-700 block mb-1">Nilai SKP</label><input required type="number" step="0.5" id="addEvCredits" value="2" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          </div>
          <div><label class="font-bold text-slate-700 block mb-1">Kategori</label><input type="text" id="addEvCat" placeholder="Simposium Ilmiah" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Tanggal</label><input type="text" id="addEvDate" placeholder="19 Des 2026" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
        `;
      } else if (currentTab === 'category') {
        title.innerText = '+ Tambah Kategori Kegiatan';
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Kategori</label><input required type="text" id="addCatName" placeholder="Contoh: Workshop Ultrasonografi..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Ranah SKP</label><select id="addCatType" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><option value="Pembelajaran">Pembelajaran</option><option value="Pengabdian">Pengabdian</option><option value="Pelayanan">Pelayanan</option></select></div>
        `;
      } else if (currentTab === 'user') {
        title.innerText = '+ Tambah Pengguna & Hak Akses';
        container.innerHTML = `
          <div><label class="font-bold text-slate-700 block mb-1">Nama Lengkap</label><input required type="text" id="addUsrName" placeholder="Nama..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Email</label><input required type="email" id="addUsrEmail" placeholder="user@pogi.or.id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl" /></div>
          <div><label class="font-bold text-slate-700 block mb-1">Role</label><select id="addUsrRole" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl"><option value="Super Admin">Super Admin</option><option value="Admin Pusat">Admin Pusat</option><option value="Admin POGI">Admin POGI</option><option value="Admin Cabang">Admin Cabang</option><option value="User/Dokter">User/Dokter</option></select></div>
        `;
      }

      modal.classList.remove('hidden');
    }

    function closeManageAddModal() {
      const modal = document.getElementById('manageAddModal');
      if (modal) modal.classList.add('hidden');
    }

    function submitManageAdd() {
      if (currentTab === 'doctor') {
        const name = document.getElementById('addDocName').value;
        const nik = document.getElementById('addDocNik').value;
        const prov = document.getElementById('addDocProv').value;
        const inst = document.getElementById('addDocInst').value;
        const phone = document.getElementById('addDocPhone').value;
        const skp = document.getElementById('addDocSkp').value;

        const docs = PogiStore.getDoctors();
        docs.unshift({
          id: 'doc-' + Date.now(),
          name,
          nik,
          pogiId: 'POGI-' + prov.substring(0, 3).toUpperCase() + '-2026',
          strNumber: '31.1.2.999.1.26',
          province: prov,
          institution: inst || 'RSUP Dr. Cipto Mangunkusumo',
          period: '1 Jan 2026 - 1 Jan 2031',
          phone: phone || '+628123456789',
          assignedTo: 'Rina Kartika',
          qTarget: 32,
          qAchieved: 0,
          skpDisplay: skp || '0 / 32',
          total5YearAchieved: 0,
          uuid: PogiStore.generateUUIDv7(),
          pelayanan: { achieved: 0, target: 56 },
          pengabdian: { achieved: 0, target: 8 },
          pembelajaran: { achieved: 0, target: 72 },
          fleksibilitas: { achieved: 0, target: 24 },
          pendingCredits: 0,
          status: 'Incomplete',
          lastActivity: 'Baru ditambahkan',
          avatar: name.charAt(0) + (name.split(' ')[1] ? name.split(' ')[1].charAt(0) : '')
        });
        PogiStore.saveDoctors(docs);
        renderManageDoctorTable(null, true);
        showToast('Dokter baru berhasil ditambahkan ke master!', 'success');
      } else if (currentTab === 'institution') {
        const name = document.getElementById('addInstName').value;
        const prov = document.getElementById('addInstProv').value;
        const addr = document.getElementById('addInstAddr').value;

        const list = PogiStore.getInstitutions();
        list.unshift({
          id: 'inst-' + Date.now(),
          name,
          doctors: 1,
          province: prov,
          address: addr || (prov + ' - RS Jejaring')
        });
        PogiStore.saveInstitutions(list);
        renderManageInstTable(null, true);
        showToast('Institusi baru berhasil ditambahkan!', 'success');
      } else if (currentTab === 'event') {
        const name = document.getElementById('addEvName').value;
        const type = document.getElementById('addEvType').value;
        const credits = document.getElementById('addEvCredits').value;
        const cat = document.getElementById('addEvCat').value;
        const date = document.getElementById('addEvDate').value;

        const list = PogiStore.getEvents();
        list.unshift({
          id: 'ev-' + Date.now(),
          name,
          title: name,
          category: cat || 'Simposium Ilmiah',
          date: date || 'Desember 2026',
          type,
          credits: parseFloat(credits) || 1,
          creditsDisplay: credits,
          doctors: '0 / 50 done',
          doctorsCompleted: 0,
          status: 'Dibuka'
        });
        PogiStore.saveEvents(list);
        renderManageEventTable(null, true);
        showToast('SKP Event baru berhasil ditambahkan!', 'success');
      } else if (currentTab === 'category') {
        const name = document.getElementById('addCatName').value;
        const type = document.getElementById('addCatType').value;

        const list = PogiStore.getCategories();
        list.unshift({
          id: 'cat-' + Date.now(),
          name,
          type,
          eventsCount: 1
        });
        PogiStore.saveCategories(list);
        renderManageCatTable(null, true);
        showToast('Kategori baru berhasil ditambahkan!', 'success');
      } else if (currentTab === 'user') {
        const name = document.getElementById('addUsrName').value;
        const email = document.getElementById('addUsrEmail').value;
        const role = document.getElementById('addUsrRole').value;

        const list = PogiStore.getUsers();
        list.unshift({
          id: 'usr-' + Date.now(),
          name,
          email,
          role,
          created: PogiStore.formatCurrentDate()
        });
        PogiStore.saveUsers(list);
        renderManageUserTable(null, true);
        showToast('Pengguna baru berhasil ditambahkan!', 'success');
      }

      PogiStore.addActivity({
        actor: 'Siti Rahma (Admin Data)',
        action: 'Tambah ' + currentTab.toUpperCase(),
        details: 'Menambahkan data baru pada tab master: ' + currentTab,
        category: 'Verification'
      });

      closeManageAddModal();
    }
  </script>
</body>
</html>