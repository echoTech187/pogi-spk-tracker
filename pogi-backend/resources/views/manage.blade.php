@extends('layouts.app')

@section('title', 'POGI SKP Tracker - Dashboard Pemantauan SKP Nasional')
@section('content')

  <!-- Main Body -->
  <div class="space-y-5">

    <!-- MedCred Standard Heading Banner -->
    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1.5">
        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#16b3ac] text-[10px] font-black uppercase tracking-wider">
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
            <div class="relative flex items-center">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
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
            <button type="button" onclick="resetManageDoctorFilters()" class="text-[#16b3ac] font-bold hover:underline">Reset Filter</button>
            <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
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
            <div class="relative flex items-center">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
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
              @php
                $instTypeOptions = $institutionTypes ?? ['RSUD', 'RSUP', 'RS Swasta', 'RS Jejaring', 'Klinik Utama', 'Klinik Pratama', 'Puskesmas'];
              @endphp
              @foreach($instTypeOptions as $t)
                <option value="{{ $t }}">{{ $t }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
          <div>Menampilkan <span id="manageInstCounter" class="font-bold text-slate-900">0</span> faskes jejaring</div>
          <div class="flex items-center gap-3">
            <button type="button" onclick="resetManageInstFilters()" class="text-[#16b3ac] font-bold hover:underline">Reset Filter</button>
            <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
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
                <th class="py-3 px-4">Tipe &amp; Kelas</th>
                <th class="py-3 px-4">Provinsi &amp; Alamat</th>
                <th class="py-3 px-4 text-center">Dokter Obgyn</th>
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
            <div class="relative flex items-center">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
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
            <button type="button" onclick="resetManageEventFilters()" class="text-[#16b3ac] font-bold hover:underline">Reset Filter</button>
            <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
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
            <div class="relative flex items-center">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
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
            <select id="manageCatStatusFilter" onchange="filterManageCatTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
              <option value="ALL">Semua Status</option>
              <option value="ACTIVE">ACTIVE</option>
              <option value="INACTIVE">INACTIVE</option>
            </select>
          </div>
        </div>

        <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
          <div>Menampilkan <span id="manageCatCounter" class="font-bold text-slate-900">0</span> kategori terverifikasi</div>
          <div class="flex items-center gap-3">
            <button type="button" onclick="resetManageCatFilters()" class="text-[#16b3ac] font-bold hover:underline">Reset Filter</button>
            <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
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
                <th class="py-3 px-4 text-center">Standar Nilai Kredit</th>
                <th class="py-3 px-4">Deskripsi / Keterangan</th>
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
            <div class="relative flex items-center">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
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
            <select id="manageUserStatusFilter" onchange="filterManageUserTable()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
              <option value="ALL">Semua Status</option>
              <option value="ACTIVE">ACTIVE</option>
              <option value="INACTIVE">INACTIVE</option>
              <option value="BLOCK">BLOCK</option>
            </select>
          </div>
        </div>

        <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 text-slate-500">
          <div>Menampilkan <span id="manageUserCounter" class="font-bold text-slate-900">0</span> akun pengelola</div>
          <div class="flex items-center gap-3">
            <button type="button" onclick="resetManageUserFilters()" class="text-[#16b3ac] font-bold hover:underline">Reset Filter</button>
            <button type="button" onclick="openActiveAddModal()" class="px-3.5 py-1.5 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white text-xs font-extrabold shadow-xs transition flex items-center gap-1.5 rbac-operational">
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
</div>
@endsection

@section('modals')
  <style>
    @keyframes pogiModalPop {
      from { opacity: 0; transform: scale(0.97) translateY(6px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .pogi-modal-card {
      animation: pogiModalPop 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    }
  </style>
  <!-- Modal Add Generic Master Item -->
  <div id="manageAddModal" class="fixed inset-0 z-[9999] bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-3 sm:p-4 transition-opacity">
    <div class="pogi-modal-card bg-white rounded-3xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3.5 min-w-0">
          <div id="manageAddModalIconWrap" class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100 text-[#16b3ac] flex items-center justify-center shrink-0">
            <span id="manageAddModalIcon" class="material-symbols-outlined text-[20px]">add_circle</span>
          </div>
          <div class="min-w-0">
            <h3 id="manageAddModalTitle" class="font-black text-slate-900 text-base leading-tight truncate">Tambah Data Master</h3>
            <p id="manageAddModalSubtitle" class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">Lengkapi informasi berikut untuk menyimpan ke basis data POGI</p>
          </div>
        </div>
        <button type="button" onclick="closeManageAddModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </div>
      <form id="manageAddForm" onsubmit="event.preventDefault(); submitManageAdd();" class="flex flex-col flex-1 min-h-0 overflow-hidden">
        <div id="manageAddFormFields" class="p-5 sm:p-6 space-y-4 text-xs flex-1 overflow-y-auto bg-slate-50/60">
          <!-- Populated dynamically based on active tab -->
        </div>
        <div class="px-6 py-4 flex items-center justify-between gap-3 border-t border-slate-100 bg-white shrink-0 sticky bottom-0 z-20">
          <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">Kolom bertanda <span class="text-rose-500 font-bold">*</span> wajib diisi</span>
          <div class="flex items-center gap-2.5 ml-auto">
            <button type="button" onclick="closeManageAddModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">Batal</button>
            <button id="manageAddSubmitBtn" type="submit" class="px-5 py-2.5 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold text-xs shadow-sm shadow-teal-500/20 transition flex items-center gap-1.5 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
              <span class="material-symbols-outlined text-[16px]">check_circle</span>
              <span>Simpan Data</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal View Details -->
  <div id="manageDetailModal" class="fixed inset-0 z-[9999] bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-3 sm:p-4 transition-opacity">
    <div class="pogi-modal-card bg-white rounded-3xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3.5 min-w-0">
          <div class="w-10 h-10 rounded-2xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center shrink-0">
            <span id="manageDetailModalIcon" class="material-symbols-outlined text-[20px]">visibility</span>
          </div>
          <div class="min-w-0">
            <h3 id="manageDetailModalTitle" class="font-black text-slate-900 text-base leading-tight truncate">Rincian Data Master</h3>
            <p id="manageDetailModalSubtitle" class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">Ringkasan informasi terverifikasi pada basis data POGI</p>
          </div>
        </div>
        <button type="button" onclick="closeManageDetailModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </div>
      <!-- Modal Body (Scrollable) -->
      <div id="manageDetailContent" class="p-5 sm:p-6 space-y-4 text-xs flex-1 overflow-y-auto bg-slate-50/60">
        <!-- Key value pairs -->
      </div>
      <!-- Modal Footer (Sticky) -->
      <div class="px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100 bg-white shrink-0 sticky bottom-0 z-20">
        <div class="flex items-center gap-2.5 ml-auto">
          <button id="manageDetailEditBtn" type="button" class="px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/80 font-extrabold text-xs transition flex items-center gap-1.5 cursor-pointer rbac-operational">
            <span class="material-symbols-outlined text-[16px]">edit_square</span>
            <span>Edit Data</span>
          </button>
          <button type="button" onclick="closeManageDetailModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Item -->
  <div id="manageEditModal" class="fixed inset-0 z-[9999] bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-3 sm:p-4 transition-opacity">
    <div class="pogi-modal-card bg-white rounded-3xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200/80 overflow-hidden relative z-10">
      <!-- Modal Header (Sticky) -->
      <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3.5 min-w-0">
          <div id="manageEditModalIconWrap" class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0">
            <span id="manageEditModalIcon" class="material-symbols-outlined text-[20px]">edit_square</span>
          </div>
          <div class="min-w-0">
            <h3 id="manageEditModalTitle" class="font-black text-slate-900 text-base leading-tight truncate">Edit Data Master</h3>
            <p id="manageEditModalSubtitle" class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">Perbarui detail entitas di bawah ini lalu simpan perubahan</p>
          </div>
        </div>
        <button type="button" onclick="closeManageEditModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </div>
      <form id="manageEditForm" onsubmit="event.preventDefault(); submitManageEdit();" class="flex flex-col flex-1 min-h-0 overflow-hidden">
        <div id="manageEditFormFields" class="p-5 sm:p-6 space-y-4 text-xs flex-1 overflow-y-auto bg-slate-50/60">
          <!-- Populated dynamically -->
        </div>
        <div class="px-6 py-4 flex items-center justify-between gap-3 border-t border-slate-100 bg-white shrink-0 sticky bottom-0 z-20">
          <span class="text-[11px] text-slate-400 font-medium hidden sm:inline"></span>
          <div class="flex items-center gap-2.5 ml-auto">
            <button type="button" onclick="closeManageEditModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">Batal</button>
            <button id="manageEditSubmitBtn" type="submit" class="px-5 py-2.5 rounded-xl bg-[#16b3ac] hover:bg-[#129993] text-white font-extrabold text-xs shadow-sm shadow-teal-500/20 transition flex items-center gap-1.5 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
              <span class="material-symbols-outlined text-[16px]">save</span>
              <span>Simpan Perubahan</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="/js/pdf_exporter.js"></script>
  <script>
    window.LIVE_MANAGE_DOCTORS = @json($doctors ?? []);
    window.LIVE_MANAGE_INSTITUTIONS = @json($institutions ?? []);
    window.LIVE_MANAGE_EVENTS = @json($events ?? []);
    window.LIVE_MANAGE_CATEGORIES = @json($categories ?? []);
    window.LIVE_MANAGE_USERS = @json($users ?? []);
    window.LIVE_MANAGE_BRANCHES = @json($branches ?? []);
    window.LIVE_MANAGE_PROVINCES = @json($provinces ?? []);
    window.LIVE_MANAGE_INSTITUTION_TYPES = @json($institutionTypes ?? []);

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
          <button type="button" onclick="${onPageChangeFn}(${p})" class="w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center ${isActive ? 'bg-[#16b3ac] text-white shadow-2xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}">
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

    // ==========================================
    // SELECT2 INITIALIZATION HELPERS
    // ==========================================
    function initManageFilterSelect2() {
      if (!window.jQuery || !$.fn.select2) return;
      const filterSelectors = [
        '#manageDoctorProvFilter',
        '#manageDoctorStatusFilter',
        '#manageInstProvFilter',
        '#manageInstTypeFilter',
        '#manageEventTypeFilter',
        '#manageEventStatusFilter',
        '#manageCatTypeFilter',
        '#manageCatStatusFilter',
        '#manageUserRoleFilter',
        '#manageUserStatusFilter'
      ];
      filterSelectors.forEach(sel => {
        const $el = $(sel);
        if ($el.length && !$el.hasClass('select2-hidden-accessible')) {
          $el.select2({
            width: '100%'
          });
        }
      });
    }

    function initModalSelect2(modalEl) {
      if (!window.jQuery || !$.fn.select2) return;
      const $modal = $(modalEl);
      $modal.find('select').each(function() {
        const $sel = $(this);
        if ($sel.hasClass('no-select2')) return;
        if ($sel.hasClass('select2-hidden-accessible')) {
          $sel.select2('destroy');
        }
        $sel.select2({
          dropdownParent: $modal,
          width: '100%'
        });
      });

      // Special event binding for Role select to trigger toggleBranchField reliably
      $modal.find('#editUsrRole').off('change.roleSelect2').on('change.roleSelect2', function() {
        toggleBranchField('edit', this.value);
      });
      $modal.find('#addUsrRole').off('change.roleSelect2').on('change.roleSelect2', function() {
        toggleBranchField('add', this.value);
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const tabParam = urlParams.get('tab');
      if (tabParam && ['doctor', 'institution', 'event', 'category', 'user'].includes(tabParam)) {
        switchManageTab(tabParam);
      } else {
        switchManageTab('doctor');
      }
      setTimeout(initManageFilterSelect2, 50);
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
            sideBtn.className = 'nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition bg-[#16b3ac] text-white shadow-xs';
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

      setTimeout(initManageFilterSelect2, 20);
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
      if (tab === 'doctor') return window.LIVE_MANAGE_DOCTORS || [];
      if (tab === 'institution') return window.LIVE_MANAGE_INSTITUTIONS || [];
      if (tab === 'event') return window.LIVE_MANAGE_EVENTS || [];
      if (tab === 'category') return window.LIVE_MANAGE_CATEGORIES || [];
      if (tab === 'user') return window.LIVE_MANAGE_USERS || [];
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
      const allDoctors = filteredList || getDefaultListForTab('doctor');
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
            const pel = d.pelayananCredits !== undefined ? d.pelayananCredits : (d.pelayanan?.achieved || 0);
            const peng = d.pengabdianCredits !== undefined ? d.pengabdianCredits : (d.pengabdian?.achieved || 0);
            const pemb = d.pembelajaranCredits !== undefined ? d.pembelajaranCredits : (d.pembelajaran?.achieved || 0);
            const total5Year = d.total5YearAchieved ?? d.totalCredits ?? (pel + peng + pemb);
            const lastAct = d.lastActivity || '-';

            return `
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-4">
                  <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0">
                      ${initials}
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-slate-900 text-xs leading-snug truncate max-w-[220px]">${d.name}</div>
                      <div class="text-[10px] text-slate-400 font-mono mt-0.5">${d.pogiId || '-'}</div>
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
                    <div class="${percent >= 100 ? 'bg-[#16b3ac]' : 'bg-amber-500'} h-1.5 rounded-full" style="width: ${percent}%"></div>
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
                    <button type="button" onclick="openManageDetailModal('doctor', '${d.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] font-bold text-xs transition cursor-pointer">
                      Rincian
                    </button>
                    <button type="button" onclick="openManageEditModal('doctor', '${d.id}')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer rbac-operational">
                      Edit
                    </button>
                    <button type="button" onclick="deleteManageItem('doctor', '${d.id}')" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition cursor-pointer rbac-operational">
                      Hapus
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
                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0">
                      ${initials}
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-slate-900 text-xs leading-snug truncate">${d.name}</div>
                      <div class="text-[10px] text-slate-400 font-mono">${d.pogiId || '-'}</div>
                    </div>
                  </div>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ${isComplete ? 'bg-emerald-50 text-[#16b3ac] border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200'} border shrink-0">
                    ${isComplete ? 'Complete' : 'Incomplete'}
                  </span>
                </div>
                <div class="text-xs text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-100 space-y-0.5">
                  <div class="flex justify-between"><span class="text-slate-400 text-[10px]">Wilayah:</span> <span class="font-bold text-slate-800 text-[11px]">${d.province}</span></div>
                  <div class="flex justify-between"><span class="text-slate-400 text-[10px]">RS:</span> <span class="font-medium text-slate-700 text-[11px] truncate max-w-[200px]">${hospital}</span></div>
                </div>
                <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between gap-2">
                  <button type="button" onclick="openManageDetailModal('doctor', '${d.id}')" class="flex-1 py-1 px-2.5 rounded-lg bg-emerald-50 text-[#16b3ac] text-xs font-bold transition flex items-center justify-center gap-1">
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

      const all = getDefaultListForTab('doctor');
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
      if (window.jQuery) {
        $('#manageDoctorProvFilter').val('ALL').trigger('change');
        $('#manageDoctorStatusFilter').val('ALL').trigger('change');
      } else {
        document.getElementById('manageDoctorProvFilter').value = 'ALL';
        document.getElementById('manageDoctorStatusFilter').value = 'ALL';
      }
      renderManageDoctorTable(null, true);
    }

    // ==========================================
    // TAB 2: INSTITUTION (PERSIS DENGAN FORMAT DOKTER)
    // ==========================================
    function renderManageInstTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.institution.page = 1;
      const allInst = filteredList || getDefaultListForTab('institution');
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
                  <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm">local_hospital</span>
                  </div>
                  <div>
                    <div class="font-bold text-slate-900 text-xs">${inst.name}</div>
                    ${inst.code ? `<div class="text-[10px] text-slate-400 font-mono">Kode RS: ${inst.code}</div>` : ''}
                  </div>
                </div>
              </td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-bold">${inst.type || 'RS Jejaring'}</span>
                ${inst.hospital_class ? `<span class="ml-1 text-[10px] font-bold text-slate-500">Kelas ${inst.hospital_class}</span>` : ''}
              </td>
              <td class="py-3 px-4">
                <div class="text-xs font-semibold text-slate-800">${inst.province}</div>
                <div class="text-[10px] text-slate-500 truncate max-w-[180px]">${inst.city || inst.address || '-'}</div>
              </td>
              <td class="py-3 px-4 text-center">
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-[#16b3ac] font-bold text-xs border border-emerald-200">${inst.doctorsCount || 0} Dokter</span>
              </td>
              <td class="py-3 px-4 text-center">
                ${(() => {
                  const isAct = (inst.is_active === true || inst.status === 'Aktif' || inst.status === 'ACTIVE' || inst.status === 'Aktif Bekerjasama');
                  return `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold ${isAct ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200'} border">
                    <span class="w-1.5 h-1.5 rounded-full ${isAct ? 'bg-emerald-600' : 'bg-slate-400'}"></span>
                    ${isAct ? 'Aktif' : 'Nonaktif'}
                  </span>`;
                })()}
              </td>
              <td class="py-3 px-4 text-[11px] text-slate-500 whitespace-nowrap">${inst.lastActivity || '-'}</td>
              <td class="py-3 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button type="button" onclick="openManageDetailModal('institution', '${inst.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] font-bold text-xs transition cursor-pointer">Rincian</button>
                  <button type="button" onclick="openManageEditModal('institution', '${inst.id}')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer rbac-operational">Edit</button>
                  <button type="button" onclick="deleteManageItem('institution', '${inst.id}')" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition cursor-pointer rbac-operational">Hapus</button>
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
                  <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#16b3ac] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-sm">local_hospital</span>
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-900 text-xs leading-snug truncate">${inst.name}</div>
                    <div class="text-[10px] text-slate-400 font-mono">${inst.province} &bull; ${inst.type || 'RS'}</div>
                  </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-[#16b3ac] font-bold text-[10px] border border-emerald-200 shrink-0">${inst.doctorsCount || 0} Dokter</span>
              </div>
              <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" onclick="openManageDetailModal('institution', '${inst.id}')" class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 text-[#16b3ac] text-xs font-bold transition flex items-center justify-center gap-1">
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
      const type = document.getElementById('manageInstTypeFilter')?.value || 'ALL';
      const all = getDefaultListForTab('institution');
      const filtered = all.filter(i => {
        const matchQ = (i.name || '').toLowerCase().includes(q) || (i.address || '').toLowerCase().includes(q) || (i.code || '').toLowerCase().includes(q);
        const matchProv = prov === 'ALL' || i.province === prov;
        const iType = (i.institution_type || i.type || '').trim().toLowerCase();
        const matchType = type === 'ALL' || iType === type.trim().toLowerCase();
        return matchQ && matchProv && matchType;
      });
      renderManageInstTable(filtered, true);
    }

    function resetManageInstFilters() {
      document.getElementById('searchManageInstInput').value = '';
      if (window.jQuery) {
        $('#manageInstProvFilter').val('ALL').trigger('change');
        $('#manageInstTypeFilter').val('ALL').trigger('change');
      } else {
        document.getElementById('manageInstProvFilter').value = 'ALL';
        document.getElementById('manageInstTypeFilter').value = 'ALL';
      }
      renderManageInstTable(null, true);
    }

    // ==========================================
    // TAB 3: SKP EVENT (PERSIS FORMAT DOKTER)
    // ==========================================
    function renderManageEventTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.event.page = 1;
      const allEvents = filteredList || getDefaultListForTab('event');
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
                  <div class="text-[10px] text-slate-500 font-medium mt-0.5">${eventCategory}</div>
                </td>
                <td class="py-3 px-4">
                  <div class="text-xs font-bold text-slate-800">${e.date || 'Q4 2026'}</div>
                  <div class="text-[10px] text-slate-400 font-medium">Jadwal Pelaksanaan</div>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold ${typeBadge} border">
                    ${e.type || 'Pembelajaran'}
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="px-2.5 py-1 rounded-lg bg-[#16b3ac] text-white font-black text-xs shadow-2xs">${creditsVal} SKP</span>
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
                    <button type="button" onclick="openManageDetailModal('event', '${e.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] font-bold text-xs transition cursor-pointer">Rincian</button>
                    <button type="button" onclick="openManageEditModal('event', '${e.id}')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer rbac-operational">Edit</button>
                    <button type="button" onclick="deleteManageItem('event', '${e.id}')" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition cursor-pointer rbac-operational">Hapus</button>
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
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-[#16b3ac] border border-emerald-200">${e.type}</span>
                    <div class="font-bold text-slate-900 text-xs mt-1.5 leading-snug line-clamp-2">${eventTitle}</div>
                  </div>
                  <span class="px-2 py-1 rounded-lg bg-[#16b3ac] text-white font-black text-xs shrink-0">${creditsVal} SKP</span>
                </div>
                <div class="text-[11px] text-slate-500 flex justify-between pt-1 border-t border-slate-100">
                  <span>${e.date || 'Q4 2026'}</span>
                  <span class="font-bold text-slate-800">${docCount}</span>
                </div>
                <div class="pt-1.5 flex items-center justify-between gap-2">
                  <button type="button" onclick="openManageDetailModal('event', '${e.id}')" class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 text-[#16b3ac] text-xs font-bold transition flex items-center justify-center gap-1">
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
      const all = getDefaultListForTab('event');
      const filtered = all.filter(e => {
        const matchQ = (e.name || e.title || '').toLowerCase().includes(q) || (e.category || '').toLowerCase().includes(q);
        const matchType = type === 'ALL' || e.type === type;
        return matchQ && matchType;
      });
      renderManageEventTable(filtered, true);
    }

    function resetManageEventFilters() {
      document.getElementById('searchManageEventInput').value = '';
      if (window.jQuery) {
        $('#manageEventTypeFilter').val('ALL').trigger('change');
        $('#manageEventStatusFilter').val('ALL').trigger('change');
      } else {
        document.getElementById('manageEventTypeFilter').value = 'ALL';
        document.getElementById('manageEventStatusFilter').value = 'ALL';
      }
      renderManageEventTable(null, true);
    }

    // ==========================================
    // TAB 4: EVENT CATEGORY (PERSIS FORMAT DOKTER)
    // ==========================================
    function renderManageCatTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.category.page = 1;
      const allCats = filteredList || getDefaultListForTab('category');
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
                </td>
                <td class="py-3 px-4">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold ${badgeColor} border">${cat.type}</span>
                </td>
                <td class="py-3 px-4 text-xs font-black text-[#16b3ac]">${cat.standardSkp || '1 - 2 SKP'}</td>
                <td class="py-3 px-4 text-xs text-slate-600 max-w-xs truncate">${cat.description || '-'}</td>
                <td class="py-3 px-4 text-center">
                  ${(cat.status === 'INACTIVE' || cat.is_active === false)
                    ? '<span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold">INACTIVE</span>'
                    : '<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold">ACTIVE</span>'
                  }
                </td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <button type="button" onclick="openManageDetailModal('category', '${cat.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] font-bold text-xs transition cursor-pointer">Rincian</button>
                    <button type="button" onclick="openManageEditModal('category', '${cat.id}')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer rbac-operational">Edit</button>
                    <button type="button" onclick="deleteManageItem('category', '${cat.id}')" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition cursor-pointer rbac-operational">Hapus</button>
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
                <span class="font-black text-xs text-[#16b3ac]">${cat.standardSkp || '1 - 2 SKP'}</span>
              </div>
              <div class="text-[11px] text-slate-500">${cat.description || '-'}</div>
              <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" onclick="openManageDetailModal('category', '${cat.id}')" class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 text-[#16b3ac] text-xs font-bold transition flex items-center justify-center gap-1">
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
      const status = document.getElementById('manageCatStatusFilter')?.value || 'ALL';
      const all = getDefaultListForTab('category');
      const filtered = all.filter(c => {
        const matchQ = (c.name || '').toLowerCase().includes(q);
        const matchType = type === 'ALL' || c.type === type || (c.domain && c.domain.toUpperCase() === type.toUpperCase());
        const cStatus = (c.status || (c.is_active === false ? 'INACTIVE' : 'ACTIVE')).toUpperCase();
        const matchStatus = status === 'ALL' || cStatus === status;
        return matchQ && matchType && matchStatus;
      });
      renderManageCatTable(filtered, true);
    }

    function resetManageCatFilters() {
      document.getElementById('searchManageCatInput').value = '';
      if (window.jQuery) {
        $('#manageCatTypeFilter').val('ALL').trigger('change');
        $('#manageCatStatusFilter').val('ALL').trigger('change');
      } else {
        document.getElementById('manageCatTypeFilter').value = 'ALL';
        const stEl = document.getElementById('manageCatStatusFilter');
        if (stEl) stEl.value = 'ALL';
      }
      renderManageCatTable(null, true);
    }

    // ==========================================
    // TAB 5: USER MASTER (PERSIS FORMAT DOKTER)
    // ==========================================
    function renderManageUserTable(filteredList, resetPage = true) {
      if (resetPage) managePagination.user.page = 1;
      const allUsers = filteredList || getDefaultListForTab('user');
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
          if (role === 'Admin POGI' || role === 'Verifikator') return '<span class="px-2.5 py-1 rounded-md bg-emerald-50 text-[#16b3ac] border border-emerald-200 font-extrabold text-[10px]">Admin POGI</span>';
          if (role === 'Admin Cabang') return '<span class="px-2.5 py-1 rounded-md bg-sky-50 text-sky-700 border border-sky-200 font-extrabold text-[10px]">Admin Cabang</span>';
          return '<span class="px-2.5 py-1 rounded-md bg-teal-50 text-teal-700 border border-teal-200 font-extrabold text-[10px]">User/Dokter</span>';
        };

        // Desktop / Tablet Rows
        if (tbody) {
          tbody.innerHTML = pageUsers.map(usr => `
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-3 px-4">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0">
                    ${(usr.name || 'US').substring(0, 2).toUpperCase()}
                  </div>
                  <div class="min-w-0">
                    <div class="font-bold text-slate-900 text-xs truncate max-w-[200px]">${usr.name}</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate max-w-[200px]">${usr.email}</div>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4 text-center">
                ${getRoleBadge(usr.role)}
              </td>
              <td class="py-3 px-4 text-xs font-semibold text-slate-700">
                ${usr.branch_name || usr.branch || 'Pusat / Nasional'}
              </td>
              <td class="py-3 px-4 text-[11px] text-slate-500">${usr.created || '-'}</td>
              <td class="py-3 px-4 text-center">
                ${(() => {
                  const st = (usr.account_status || (usr.is_active === false ? 'INACTIVE' : 'ACTIVE')).toUpperCase();
                  if (st === 'BLOCK') return '<span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200 text-[10px] font-bold">BLOCK</span>';
                  if (st === 'INACTIVE') return '<span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold">INACTIVE</span>';
                  return '<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold">ACTIVE</span>';
                })()}
              </td>
              <td class="py-3 px-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <button type="button" onclick="openManageDetailModal('user', '${usr.id}')" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#16b3ac] font-bold text-xs transition cursor-pointer">Rincian</button>
                  <button type="button" onclick="openManageEditModal('user', '${usr.id}')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer rbac-operational">Edit</button>
                  <button type="button" onclick="deleteManageItem('user', '${usr.id}')" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition cursor-pointer rbac-operational">Hapus</button>
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
                  <div class="w-8 h-8 rounded-full bg-emerald-50 text-[#16b3ac] font-black text-xs flex items-center justify-center shrink-0">
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
                <button type="button" onclick="openManageDetailModal('user', '${usr.id}')" class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 text-[#16b3ac] text-xs font-bold transition flex items-center justify-center gap-1">
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
      const status = document.getElementById('manageUserStatusFilter')?.value || 'ALL';
      const all = getDefaultListForTab('user');
      const filtered = all.filter(u => {
        const matchQ = (u.name || '').toLowerCase().includes(q) || (u.email || '').toLowerCase().includes(q);
        const matchRole = role === 'ALL' || u.role === role;
        const uStatus = (u.account_status || (u.is_active === false ? 'INACTIVE' : 'ACTIVE')).toUpperCase();
        const matchStatus = status === 'ALL' || uStatus === status;
        return matchQ && matchRole && matchStatus;
      });
      renderManageUserTable(filtered, true);
    }

    function resetManageUserFilters() {
      document.getElementById('searchManageUserInput').value = '';
      if (window.jQuery) {
        $('#manageUserRoleFilter').val('ALL').trigger('change');
        const stEl = $('#manageUserStatusFilter');
        if (stEl.length) stEl.val('ALL').trigger('change');
      } else {
        document.getElementById('manageUserRoleFilter').value = 'ALL';
        const stEl = document.getElementById('manageUserStatusFilter');
        if (stEl) stEl.value = 'ALL';
      }
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
    function copyDetailText(val) {
      if (!val || val === '-') return;
      navigator.clipboard?.writeText(String(val)).then(() => {
        if (typeof showToast === 'function') showToast('Disalin ke clipboard: ' + val, 'info');
      }).catch(() => {});
    }

    function uiDetailCard(icon, label, value, mono = false, copyable = false) {
      const safeVal = (value !== null && value !== undefined && value !== '') ? value : '-';
      const copyBtn = (copyable && safeVal !== '-')
        ? `<button type="button" onclick="copyDetailText('${String(safeVal).replace(/'/g, "\\'")}')" class="ml-auto p-1 rounded-lg text-slate-400 hover:text-[#16b3ac] hover:bg-teal-50 transition cursor-pointer" title="Salin"><span class="material-symbols-outlined text-[14px]">content_copy</span></button>`
        : '';
      return `
        <div class="p-3.5 bg-white border border-slate-200/80 rounded-2xl shadow-2xs hover:border-slate-300/80 transition">
          <div class="flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
            <span class="material-symbols-outlined text-[14px] text-[#16b3ac]">${icon}</span>
            <span>${label}</span>
            ${copyBtn}
          </div>
          <div class="text-xs font-extrabold text-slate-800 mt-1.5 break-words ${mono ? 'font-mono' : ''}">${safeVal}</div>
        </div>
      `;
    }

    function openManageDetailModal(type, id) {
      const modal = document.getElementById('manageDetailModal');
      const title = document.getElementById('manageDetailModalTitle');
      const subtitle = document.getElementById('manageDetailModalSubtitle');
      const iconEl = document.getElementById('manageDetailModalIcon');
      const editBtn = document.getElementById('manageDetailEditBtn');
      const container = document.getElementById('manageDetailContent');
      if (!modal || !title || !container) return;

      const iconMap = { doctor: 'stethoscope', institution: 'local_hospital', event: 'event_note', category: 'category', user: 'person' };
      if (iconEl) iconEl.textContent = iconMap[type] || 'visibility';
      if (editBtn) {
        editBtn.onclick = function() {
          closeManageDetailModal();
          openManageEditModal(type, id);
        };
      }

      let item = null;
      let html = '';

      if (type === 'doctor') {
        item = getDefaultListForTab('doctor').find(d => String(d.id) === String(id));
        if (!item) return;
        title.innerText = 'Rincian Dokter: ' + item.name;
        if (subtitle) subtitle.innerText = 'Profil keanggotaan POGI, legalitas STR/SIP, dan progres SKP';
        const isComp = item.status === 'Complete' || item.raw_status === 'COMPLETE';
        html = `
          <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-gradient-to-br from-teal-50/80 via-white to-slate-50 border border-teal-100 shadow-2xs">
            <div class="w-12 h-12 rounded-2xl bg-[#16b3ac] text-white font-black text-base flex items-center justify-center shrink-0 shadow-sm">
              ${item.avatar || 'DR'}
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-black text-sm text-slate-900">${item.name}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold ${isComp ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'}">${item.raw_status || item.status}</span>
              </div>
              <div class="text-[11px] text-slate-500 font-mono mt-0.5 truncate">NPA: ${item.pogiId || item.npa_pogi || '-'} &bull; STR: ${item.str_number || '-'}</div>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            ${uiDetailCard('location_on', 'Provinsi / Cabang POGI', (item.province || '-') + (item.branch ? ' • ' + item.branch : ''))}
            ${uiDetailCard('local_hospital', 'RS / Institusi Utama', item.institution || item.hospital || '-')}
            ${uiDetailCard('badge', 'Nomor SIP & Masa Berlaku', (item.sip_number || '-') + (item.sip_expiry_date ? ' (s.d. ' + item.sip_expiry_date + ')' : ''), true)}
            ${uiDetailCard('call', 'No. WhatsApp', item.phone || '-', true, true)}
            ${uiDetailCard('mail', 'Email Dokter', item.email || '-', true, true)}
            ${uiDetailCard('trending_up', 'Progres Total SKP', item.skpDisplay || ((item.qAchieved || 0) + ' / 160'))}
          </div>
          <div class="p-4 bg-white border border-emerald-200/80 rounded-2xl shadow-2xs">
            <div class="flex items-center gap-1.5 font-extrabold text-emerald-900 text-xs mb-2.5">
              <span class="material-symbols-outlined text-[16px] text-[#16b3ac]">donut_large</span>
              <span>Rincian Ranah SKP (Standar Kemenkes 160 SKP / 5 Tahun)</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                <div class="text-[10px] text-slate-500 font-bold">1. Pelayanan</div>
                <div class="font-black text-slate-900 mt-0.5">${item.pelayanan?.achieved || 0} / 56</div>
              </div>
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                <div class="text-[10px] text-slate-500 font-bold">2. Pengabdian</div>
                <div class="font-black text-slate-900 mt-0.5">${item.pengabdian?.achieved || 0} / 8</div>
              </div>
              <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                <div class="text-[10px] text-slate-500 font-bold">3. Pembelajaran</div>
                <div class="font-black text-slate-900 mt-0.5">${item.pembelajaran?.achieved || 0} / 72</div>
              </div>
            </div>
          </div>
        `;
      } else if (type === 'institution') {
        item = getDefaultListForTab('institution').find(i => String(i.id) === String(id));
        if (!item) return;
        title.innerText = 'Rincian Faskes: ' + item.name;
        if (subtitle) subtitle.innerText = 'Informasi fasilitas kesehatan jejaring dan sebaran dokter Obgyn';
        html = `
          <div class="flex items-center justify-between gap-3 p-4 rounded-2xl bg-gradient-to-br from-teal-50/80 via-white to-slate-50 border border-teal-100 shadow-2xs">
            <div>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-100 text-teal-800 border border-teal-200">${item.institution_type || item.type || 'RS Jejaring'}${item.hospital_class ? ' • Kelas ' + item.hospital_class : ''}</span>
              <div class="font-black text-slate-900 text-sm mt-1.5">${item.name}</div>
              <div class="text-[11px] text-slate-500 font-mono mt-0.5">Kode RS: ${item.code || '-'}</div>
            </div>
            <div class="text-right shrink-0 px-3.5 py-2 rounded-xl bg-white border border-teal-100">
              <div class="text-[10px] font-bold text-slate-400 uppercase">Dokter</div>
              <div class="font-black text-lg text-[#16b3ac]">${item.doctorsCount ?? 0}</div>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            ${uiDetailCard('map', 'Provinsi', item.province || '-')}
            ${uiDetailCard('location_city', 'Kota / Kabupaten', item.city || '-')}
          </div>
          ${uiDetailCard('home_pin', 'Alamat Lengkap', item.address || (item.province + ' - RS Jejaring'))}
          ${uiDetailCard('schedule', 'Terakhir Diperbarui', item.lastActivity || '-')}
        `;
      } else if (type === 'event') {
        item = getDefaultListForTab('event').find(e => String(e.id) === String(id));
        if (!item) return;
        title.innerText = 'Rincian Event SKP';
        if (subtitle) subtitle.innerText = item.name || item.title;
        html = `
          <div class="p-4 rounded-2xl bg-gradient-to-br from-teal-50/80 via-white to-slate-50 border border-teal-100 shadow-2xs">
            <div class="flex items-center justify-between gap-2">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-100 text-teal-800 border border-teal-200">${item.type || 'Pembelajaran'}</span>
              <span class="font-black text-[#16b3ac] text-sm">${item.creditsDisplay || item.credits} SKP</span>
            </div>
            <div class="font-black text-slate-900 text-sm mt-2">${item.name || item.title}</div>
            <div class="text-xs text-slate-500 mt-0.5">${item.category || 'Kegiatan Terakreditasi'}</div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            ${uiDetailCard('corporate_fare', 'Penyelenggara', item.organizer || 'PP POGI')}
            ${uiDetailCard('calendar_month', 'Tanggal Pelaksanaan', item.date || '-')}
            ${uiDetailCard('verified', 'No. Akreditasi Kemenkes', item.kemenkes_accreditation_number || '-', true, true)}
            ${uiDetailCard('school', 'ID Course Plataran Sehat', item.plataran_sehat_course_id || '-', true, true)}
          </div>
        `;
      } else if (type === 'category') {
        item = getDefaultListForTab('category').find(c => String(c.id) === String(id));
        if (!item) return;
        title.innerText = 'Rincian Kategori: ' + item.name;
        if (subtitle) subtitle.innerText = 'Klasifikasi ranah dan nilai standar kredit SKP';
        html = `
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            ${uiDetailCard('category', 'Nama Kategori', item.name)}
            ${uiDetailCard('layers', 'Ranah SKP', item.domain || item.type || '-')}
            ${uiDetailCard('workspace_premium', 'Default Nilai SKP', item.standardSkp || ((item.default_credit_value ?? 2) + ' SKP'))}
            ${uiDetailCard('event_available', 'Event Terhubung', (item.eventsCount ?? 0) + ' Event')}
          </div>
          ${uiDetailCard('notes', 'Deskripsi Kategori', item.description || '-')}
        `;
      } else if (type === 'user') {
        item = getDefaultListForTab('user').find(u => String(u.id) === String(id));
        if (!item) return;
        title.innerText = 'Rincian Pengguna: ' + item.name;
        if (subtitle) subtitle.innerText = 'Detail akun pengelola, peran RBAC, dan riwayat aktivitas';
        const initials = (item.name || 'US').split(' ').slice(0, 2).map(w => w.charAt(0).toUpperCase()).join('');
        const accStatus = (item.account_status || (item.is_active === false ? 'INACTIVE' : 'ACTIVE')).toUpperCase();
        const badgeCls = accStatus === 'BLOCK'
          ? 'bg-rose-100 text-rose-700 border border-rose-200'
          : (accStatus === 'INACTIVE' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200');
        html = `
          <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-gradient-to-br from-teal-50/80 via-white to-slate-50 border border-teal-100 shadow-2xs">
            <div class="w-12 h-12 rounded-2xl bg-[#16b3ac] text-white font-black text-base flex items-center justify-center shrink-0 shadow-sm">
              ${initials}
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-black text-slate-900 text-sm">${item.name}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold ${badgeCls}">
                  ${accStatus}
                </span>
                ${item.email_verified ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">Email Terverifikasi</span>' : ''}
              </div>
              <div class="flex items-center gap-1.5 text-xs text-slate-600 font-mono mt-0.5 truncate">
                <span class="truncate">${item.email}</span>
                <button type="button" onclick="copyDetailText('${String(item.email).replace(/'/g, "\\'")}')" class="p-0.5 rounded text-slate-400 hover:text-[#16b3ac] cursor-pointer" title="Salin Email"><span class="material-symbols-outlined text-[13px]">content_copy</span></button>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            ${uiDetailCard('admin_panel_settings', 'Role Akses', item.role)}
            ${uiDetailCard('location_on', 'Wilayah / Cabang POGI', item.branch_name || 'Pengurus Pusat (Nasional)')}
            ${uiDetailCard('call', 'No. Telepon / WhatsApp', item.phone_number || '-', true, true)}
            ${uiDetailCard('calendar_today', 'Terdaftar Sejak', item.created || '-')}
            ${uiDetailCard('schedule', 'Login Terakhir', item.last_login || 'Belum tercatat')}
            ${uiDetailCard('analytics', 'Binaan & Audit Aktivitas', (item.assigned_doctors_count ?? 0) + ' Dokter PIC • ' + (item.audit_actions_count ?? 0) + ' Log')}
          </div>
        `;
      }

      container.innerHTML = html;
      modal.classList.remove('hidden');
      initModalSelect2(modal);
    }

    function closeManageDetailModal() {
      const modal = document.getElementById('manageDetailModal');
      if (modal) modal.classList.add('hidden');
    }

    // EDIT ITEM MODAL
    const UI_INPUT = 'w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-[#16b3ac] focus:ring-4 focus:ring-[#16b3ac]/15 outline-none transition-all';
    const UI_SELECT = 'w-full px-3 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-[#16b3ac] focus:ring-4 focus:ring-[#16b3ac]/15 outline-none transition-all cursor-pointer';
    const UI_TEXTAREA = 'w-full px-3.5 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-[#16b3ac] focus:ring-4 focus:ring-[#16b3ac]/15 outline-none transition-all resize-none';

    function uiSection(icon, title, bodyHtml) {
      return `
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-3.5 shadow-2xs">
          <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
            <span class="material-symbols-outlined text-[16px] text-[#16b3ac]">${icon}</span>
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600">${title}</span>
          </div>
          ${bodyHtml}
        </div>
      `;
    }

    function uiIconInput(icon, inputHtml) {
      return `
        <div class="relative">
          <span class="material-symbols-outlined text-[16px] text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">${icon}</span>
          ${inputHtml}
        </div>
      `;
    }

    function uiField(label, required, controlHtml, hint = '') {
      return `
        <div>
          <label class="block text-[11px] font-bold text-slate-700 mb-1.5">
            ${label} ${required ? '<span class="text-rose-500">*</span>' : ''}
          </label>
          ${controlHtml}
          ${hint ? `<p class="text-[10px] text-slate-400 mt-1 font-medium">${hint}</p>` : ''}
        </div>
      `;
    }

    function uiPasswordField(id, placeholder, onInputExpr = '', isRequired = false) {
      return `
        <div class="relative">
          <input type="password" id="${id}" ${isRequired ? 'required' : ''} autocomplete="new-password" minlength="8" ${onInputExpr ? `oninput="${onInputExpr}"` : ''} placeholder="${placeholder}" class="${UI_INPUT} pr-10" />
          <button type="button" onclick="togglePasswordVisibility('${id}', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer" title="Tampilkan/Sembunyikan Password">
            <span class="material-symbols-outlined text-[18px]">visibility</span>
          </button>
        </div>
      `;
    }

    function uiPasswordWithConfirm(passId, confirmId, passLabel, passPlaceholder, isRequired = false) {
      const handler = `evaluatePasswordSecurity('${passId}', '${confirmId}')`;
      return `
        <div class="space-y-3">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>${uiField(passLabel, isRequired, uiPasswordField(passId, passPlaceholder, handler, isRequired))}</div>
            <div>${uiField('Konfirmasi Password', isRequired, uiPasswordField(confirmId, 'Ulangi password', handler, isRequired))}</div>
          </div>
          <div id="${passId}_meterWrap" class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-bold text-slate-600">Standar Keamanan Password</span>
              <span id="${passId}_strengthLabel" class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-200 text-slate-600">Belum diisi</span>
            </div>
            <div class="grid grid-cols-4 gap-1.5">
              <div id="${passId}_bar1" class="h-1.5 rounded-full bg-slate-200 transition-all"></div>
              <div id="${passId}_bar2" class="h-1.5 rounded-full bg-slate-200 transition-all"></div>
              <div id="${passId}_bar3" class="h-1.5 rounded-full bg-slate-200 transition-all"></div>
              <div id="${passId}_bar4" class="h-1.5 rounded-full bg-slate-200 transition-all"></div>
            </div>
            <div class="grid grid-cols-2 gap-1.5 text-[10px] font-semibold text-slate-500">
              <div id="${passId}_chkLen" class="flex items-center gap-1"><span class="material-symbols-outlined text-[13px] text-slate-300">radio_button_unchecked</span>Minimal 8 karakter</div>
              <div id="${passId}_chkCase" class="flex items-center gap-1"><span class="material-symbols-outlined text-[13px] text-slate-300">radio_button_unchecked</span>Huruf besar & kecil</div>
              <div id="${passId}_chkNum" class="flex items-center gap-1"><span class="material-symbols-outlined text-[13px] text-slate-300">radio_button_unchecked</span>Mengandung angka</div>
              <div id="${passId}_chkSym" class="flex items-center gap-1"><span class="material-symbols-outlined text-[13px] text-slate-300">radio_button_unchecked</span>Karakter spesial (!@#$)</div>
            </div>
            <div id="${passId}_matchStatus" class="hidden pt-1 border-t border-slate-200/80 text-[11px] font-bold flex items-center gap-1"></div>
          </div>
        </div>
      `;
    }

    function evaluatePasswordSecurity(passId, confirmId) {
      const pass = document.getElementById(passId)?.value || '';
      const confirm = document.getElementById(confirmId)?.value || '';

      const rules = {
        chkLen: pass.length >= 8,
        chkCase: /[a-z]/.test(pass) && /[A-Z]/.test(pass),
        chkNum: /\d/.test(pass),
        chkSym: /[^A-Za-z0-9]/.test(pass)
      };

      const labels = {
        chkLen: 'Minimal 8 karakter',
        chkCase: 'Huruf besar & kecil',
        chkNum: 'Mengandung angka',
        chkSym: 'Karakter spesial (!@#$)'
      };

      let score = 0;
      Object.keys(rules).forEach(key => {
        const ok = rules[key];
        if (ok) score++;
        const el = document.getElementById(`${passId}_${key}`);
        if (el) {
          el.className = `flex items-center gap-1 ${ok ? 'text-emerald-700 font-bold' : 'text-slate-500'}`;
          el.innerHTML = `<span class="material-symbols-outlined text-[13px] ${ok ? 'text-emerald-500' : 'text-slate-300'}">${ok ? 'check_circle' : 'radio_button_unchecked'}</span>${labels[key]}`;
        }
      });

      const barColors = [
        { cls: 'bg-rose-500', text: 'Lemah', badge: 'bg-rose-100 text-rose-700' },
        { cls: 'bg-amber-500', text: 'Cukup', badge: 'bg-amber-100 text-amber-700' },
        { cls: 'bg-[#16b3ac]', text: 'Kuat', badge: 'bg-teal-100 text-teal-800' },
        { cls: 'bg-emerald-500', text: 'Sangat Kuat', badge: 'bg-emerald-100 text-emerald-700' }
      ];
      const lbl = document.getElementById(`${passId}_strengthLabel`);
      if (!pass) {
        if (lbl) {
          lbl.className = 'text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-slate-200 text-slate-600';
          lbl.textContent = 'Belum diisi';
        }
        [1, 2, 3, 4].forEach(i => {
          const b = document.getElementById(`${passId}_bar${i}`);
          if (b) b.className = 'h-1.5 rounded-full bg-slate-200 transition-all';
        });
      } else {
        const tier = barColors[Math.max(0, score - 1)];
        if (lbl) {
          lbl.className = `text-[10px] font-extrabold px-2 py-0.5 rounded-full ${tier.badge}`;
          lbl.textContent = tier.text;
        }
        [1, 2, 3, 4].forEach(i => {
          const b = document.getElementById(`${passId}_bar${i}`);
          if (b) b.className = `h-1.5 rounded-full transition-all ${i <= score ? tier.cls : 'bg-slate-200'}`;
        });
      }

      const matchEl = document.getElementById(`${passId}_matchStatus`);
      if (matchEl) {
        if (!pass && !confirm) {
          matchEl.classList.add('hidden');
        } else if (!confirm) {
          matchEl.className = 'pt-1.5 border-t border-slate-200/80 text-[11px] font-semibold text-slate-400 flex items-center gap-1';
          matchEl.innerHTML = '<span class="material-symbols-outlined text-[14px]">info</span>Masukkan konfirmasi password untuk mencocokkan';
        } else if (pass === confirm) {
          matchEl.className = 'pt-1.5 border-t border-slate-200/80 text-[11px] font-bold text-emerald-600 flex items-center gap-1';
          matchEl.innerHTML = '<span class="material-symbols-outlined text-[14px]">check_circle</span>Konfirmasi password cocok';
        } else {
          matchEl.className = 'pt-1.5 border-t border-slate-200/80 text-[11px] font-bold text-rose-600 flex items-center gap-1';
          matchEl.innerHTML = '<span class="material-symbols-outlined text-[14px]">error</span>Konfirmasi password tidak cocok';
        }
      }
    }

    function validatePasswordInput(pass, confirm, isRequired = false) {
      if (!pass && !confirm) {
        return isRequired ? 'Password wajib diisi untuk pengguna baru.' : null;
      }
      if (pass.length < 8) return 'Password minimal 8 karakter.';
      if (!/[a-z]/.test(pass) || !/[A-Z]/.test(pass) || !/\d/.test(pass)) {
        return 'Password harus mengandung huruf besar, huruf kecil, dan angka.';
      }
      if (pass !== confirm) return 'Konfirmasi password tidak cocok.';
      return null;
    }

    function togglePasswordVisibility(inputId, btn) {
      const inp = document.getElementById(inputId);
      const ic = btn?.querySelector('.material-symbols-outlined');
      if (!inp) return;
      if (inp.type === 'password') {
        inp.type = 'text';
        if (ic) ic.textContent = 'visibility_off';
      } else {
        inp.type = 'password';
        if (ic) ic.textContent = 'visibility';
      }
    }

    function uiSegmentedPills(hiddenId, currentValue, options) {
      const buttons = options.map(opt => {
        const isActive = String(currentValue) === String(opt.value);
        const activeCls = opt.activeClass || 'bg-[#16b3ac] text-white border-[#16b3ac] shadow-xs';
        const idleCls = 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100';
        return `
          <button type="button"
            data-pill-group="${hiddenId}"
            data-pill-val="${opt.value}"
            data-active-cls="${activeCls}"
            onclick="selectSegmentedPill('${hiddenId}', '${opt.value}')"
            class="flex-1 px-2.5 py-2 rounded-xl border text-[11px] font-extrabold transition flex items-center justify-center gap-1.5 cursor-pointer ${isActive ? activeCls : idleCls}">
            ${opt.dotClass ? `<span class="w-2 h-2 rounded-full ${isActive ? 'bg-white' : opt.dotClass}"></span>` : ''}
            <span>${opt.label}</span>
          </button>
        `;
      }).join('');
      return `
        <input type="hidden" id="${hiddenId}" value="${currentValue}" />
        <div class="flex flex-wrap gap-1.5">${buttons}</div>
      `;
    }

    function selectSegmentedPill(hiddenId, val) {
      const inp = document.getElementById(hiddenId);
      if (inp) inp.value = val;
      document.querySelectorAll(`[data-pill-group="${hiddenId}"]`).forEach(btn => {
        const isTarget = btn.getAttribute('data-pill-val') === String(val);
        const activeCls = btn.getAttribute('data-active-cls') || 'bg-[#16b3ac] text-white border-[#16b3ac] shadow-xs';
        const idleCls = 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100';
        btn.className = `flex-1 px-2.5 py-2 rounded-xl border text-[11px] font-extrabold transition flex items-center justify-center gap-1.5 cursor-pointer ${isTarget ? activeCls : idleCls}`;
      });
    }

    function setModalMeta(mode, type, titleText, subtitleText) {
      const iconMap = {
        doctor: 'stethoscope',
        institution: 'local_hospital',
        event: 'event_note',
        category: 'category',
        user: 'manage_accounts'
      };
      const prefix = mode === 'add' ? 'manageAddModal' : 'manageEditModal';
      const tEl = document.getElementById(prefix + 'Title');
      const sEl = document.getElementById(prefix + 'Subtitle');
      const iEl = document.getElementById(prefix + 'Icon');
      if (tEl) tEl.innerText = titleText;
      if (sEl) sEl.innerText = subtitleText;
      if (iEl) iEl.innerText = iconMap[type] || (mode === 'add' ? 'add_circle' : 'edit_square');
    }

    function buildProvinceOptions(selectedVal) {
      return (window.LIVE_MANAGE_PROVINCES || []).map(p => {
        const pName = p.name || p;
        const isSel = selectedVal && (String(selectedVal) === String(p.id) || String(selectedVal).toLowerCase() === String(pName).toLowerCase());
        return `<option value="${pName}" ${isSel ? 'selected' : ''}>${pName}</option>`;
      }).join('');
    }
    function buildInstitutionTypeOptions(selectedType) {
      const types = (window.LIVE_MANAGE_INSTITUTION_TYPES && window.LIVE_MANAGE_INSTITUTION_TYPES.length > 0)
        ? window.LIVE_MANAGE_INSTITUTION_TYPES
        : ['RSUD', 'RSUP', 'RS Swasta', 'RS Jejaring', 'Klinik Utama', 'Klinik Pratama', 'Puskesmas'];
      return types.map(t => `<option value="${t}" ${selectedType && String(selectedType).trim().toLowerCase() === String(t).trim().toLowerCase() ? 'selected' : ''}>${t}</option>`).join('');
    }
    function buildBranchOptions(selectedId) {
      const opts = (window.LIVE_MANAGE_BRANCHES || []).map(b =>
        `<option value="${b.id}" ${selectedId && String(selectedId) === String(b.id) ? 'selected' : ''}>${b.branch_name}</option>`
      ).join('');
      return `<option value="">-- Otomatis dari Provinsi --</option>` + opts;
    }
    function buildInstitutionOptions(selectedId, selectedName) {
      const opts = (window.LIVE_MANAGE_INSTITUTIONS || []).map(inst => {
        const isSel = (selectedId && String(selectedId) === String(inst.id)) || (selectedName && String(selectedName) === String(inst.name));
        return `<option value="${inst.id}" ${isSel ? 'selected' : ''}>${inst.name}</option>`;
      }).join('');
      return `<option value="">-- Pilih RS / Faskes Utama --</option>` + opts;
    }
    function buildCategoryOptions(selectedId, selectedName) {
      const opts = (window.LIVE_MANAGE_CATEGORIES || []).map(cat => {
        const isSel = (selectedId && String(selectedId) === String(cat.id)) || (selectedName && String(selectedName) === String(cat.name));
        return `<option value="${cat.id}" data-domain="${cat.domain || cat.type || 'PEMBELAJARAN'}" ${isSel ? 'selected' : ''}>${cat.name} (${cat.domain || cat.type || 'PEMBELAJARAN'})</option>`;
      }).join('');
      return opts || `<option value="">-- Belum ada kategori --</option>`;
    }

    function openManageEditModal(type, id) {
      const modal = document.getElementById('manageEditModal');
      const container = document.getElementById('manageEditFormFields');
      if (!modal || !container) return;

      activeActionContext = { type, id };

      if (type === 'doctor') {
        const item = getDefaultListForTab('doctor').find(d => String(d.id) === String(id));
        if (!item) return;
        const rawStatus = item.raw_status || (item.status === 'Complete' ? 'COMPLETE' : 'INCOMPLETE');
        const pStart = item.period_start || '2026-01-01';
        const pEnd = item.period_end || '2030-12-31';
        setModalMeta('edit', 'doctor', 'Edit Doctor: ' + item.name, 'Perbarui profil dokter, akun LMS Plataran, dan periode SKP');
        container.innerHTML =
          uiSection('stethoscope', 'Doctor info', `
            <div>${uiField('Name', true, `<input required type="text" id="editDocName" value="${item.name || ''}" placeholder="dr. Budi Santoso, Sp.PD" class="${UI_INPUT}" />`)}</div>
            <div>${uiField('STR', false, `<input type="text" id="editDocStr" value="${item.str_number || ''}" placeholder="32.73.100.2.21.0883" class="${UI_INPUT} font-mono" />`)}</div>
            <div>${uiField('Phone', true, `<input required type="text" id="editDocPhone" value="${item.phone || ''}" placeholder="08123456789" class="${UI_INPUT} font-mono" />`, 'Numbers starting with 0 are saved as +62.')}</div>
            <div>${uiField('Province', true, `<select id="editDocProv" class="${UI_SELECT}">${buildProvinceOptions(item.province)}</select>`)}</div>
            <div>${uiField('Institution', true, `<select id="editDocInstId" class="${UI_SELECT}">${buildInstitutionOptions(item.primary_institution_id, item.institution)}</select>`)}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Period start', true, `<input type="date" id="editDocPeriodStart" value="${pStart}" class="${UI_INPUT}" />`)}</div>
              <div>${uiField('Period end', true, `<input type="date" id="editDocPeriodEnd" value="${pEnd}" class="${UI_INPUT}" />`)}</div>
            </div>
          `) +
          uiSection('vpn_key', 'Credentials', `
            <div class="text-[11px] text-slate-400 font-semibold mb-2">LMS Plataran Account</div>
            <div class="space-y-3">
              <div>${uiField('Username', false, `<input type="text" id="editDocPlataranUser" value="${item.plataran_sehat_username || ''}" placeholder="super@example.com" class="${UI_INPUT} bg-sky-50/70 border-sky-100" />`)}</div>
              <div>${uiField('Password', false, `<input type="password" id="editDocPlataranPass" placeholder="•••••••• (Kosongkan jika tidak diubah)" class="${UI_INPUT} bg-sky-50/70 border-sky-100" />`)}</div>
            </div>
          `) +
          `<details class="group border border-slate-200 rounded-2xl p-3 bg-slate-50/50 mt-1" open>
            <summary class="text-xs font-bold text-slate-600 cursor-pointer flex items-center justify-between">
              <span>Keanggotaan POGI, Legalitas & Status Kepatuhan</span>
              <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
            </summary>
            <div class="pt-3 space-y-3">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>${uiField('Gelar Spesialis', false, `<input type="text" id="editDocSpec" value="${item.specialization_title || 'Sp.OG'}" class="${UI_INPUT}" />`)}</div>
                <div>${uiField('NPA POGI', false, `<input type="text" id="editDocNpa" value="${item.npa_pogi || item.pogiId || ''}" class="${UI_INPUT} font-mono" />`)}</div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>${uiField('NIK Baru (16 Digit)', false, `<input type="text" id="editDocNik" placeholder="Kosongkan jika tidak diubah" class="${UI_INPUT} font-mono" />`, 'Terenkripsi AES-256-GCM di server')}</div>
                <div>${uiField('Cabang POGI', false, `<select id="editDocBranch" class="${UI_SELECT}">${buildBranchOptions(item.branch_id)}</select>`)}</div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>${uiField('Nomor SIP', false, `<input type="text" id="editDocSip" value="${item.sip_number || ''}" class="${UI_INPUT} font-mono" />`)}</div>
                <div>${uiField('Masa Berlaku SIP', false, `<input type="date" id="editDocSipExp" value="${item.sip_expiry_date || ''}" class="${UI_INPUT}" />`)}</div>
              </div>
              <div>${uiField('Email', false, uiIconInput('mail', `<input type="email" id="editDocEmail" value="${item.email || ''}" class="${UI_INPUT} pl-9" />`))}</div>
              <div>${uiField('Status Kepatuhan SKP', false, uiSegmentedPills('editDocStatus', rawStatus, [
                { value: 'INCOMPLETE', label: 'Incomplete', dotClass: 'bg-rose-500', activeClass: 'bg-rose-600 text-white border-rose-600 shadow-xs' },
                { value: 'PENDING_REVIEW', label: 'Pending Review', dotClass: 'bg-amber-500', activeClass: 'bg-amber-500 text-white border-amber-500 shadow-xs' },
                { value: 'COMPLETE', label: 'Complete', dotClass: 'bg-emerald-500', activeClass: 'bg-emerald-600 text-white border-emerald-600 shadow-xs' }
              ]))}</div>
            </div>
          </details>`;
      } else if (type === 'institution') {
        const item = getDefaultListForTab('institution').find(i => String(i.id) === String(id));
        if (!item) return;
        const instType = item.institution_type || item.type || 'RS Jejaring';
        const isAct = (item.is_active === true || item.status === 'Aktif' || item.status === 'ACTIVE' || item.status === 'Aktif Bekerjasama');
        setModalMeta('edit', 'institution', 'Edit Faskes: ' + item.name, 'Perbarui status faskes, klasifikasi, kode Kemenkes, dan alamat wilayah');
        container.innerHTML =
          uiSection('local_hospital', 'Identitas Rumah Sakit / Faskes', `
            <div>${uiField('Nama Rumah Sakit / Faskes', true, `<input required type="text" id="editInstName" value="${item.name || ''}" class="${UI_INPUT}" />`)}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-3">
              <div>${uiField('Kode RS', false, `<input type="text" id="editInstCode" value="${item.code || ''}" placeholder="Kode Kemenkes" class="${UI_INPUT} font-mono" />`)}</div>
              <div>${uiField('Tipe Faskes', true, `<select id="editInstType" class="${UI_SELECT}">${buildInstitutionTypeOptions(instType)}</select>`)}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-3">
              <div>${uiField('Kelas RS', false, `<select id="editInstClass" class="${UI_SELECT}"><option value="">-</option><option value="A" ${item.hospital_class === 'A' ? 'selected' : ''}>Kelas A</option><option value="B" ${item.hospital_class === 'B' ? 'selected' : ''}>Kelas B</option><option value="C" ${item.hospital_class === 'C' ? 'selected' : ''}>Kelas C</option><option value="D" ${item.hospital_class === 'D' ? 'selected' : ''}>Kelas D</option></select>`)}</div>
              <div>${uiField('Status Faskes', true, `<select id="editInstStatus" class="${UI_SELECT}"><option value="ACTIVE" ${isAct ? 'selected' : ''}>Aktif</option><option value="INACTIVE" ${!isAct ? 'selected' : ''}>Nonaktif</option></select>`)}</div>
            </div>
          `) +
          uiSection('location_on', 'Lokasi & Alamat', `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Provinsi', true, `<select id="editInstProv" class="${UI_SELECT}">${buildProvinceOptions(item.province)}</select>`)}</div>
              <div>${uiField('Kota / Kabupaten', false, `<input type="text" id="editInstCity" value="${item.city || ''}" class="${UI_INPUT}" />`)}</div>
            </div>
            <div>${uiField('Alamat Lengkap', false, `<textarea id="editInstAddr" rows="2" class="${UI_TEXTAREA}">${item.address || ''}</textarea>`)}</div>
          `);
      } else if (type === 'event') {
        const item = getDefaultListForTab('event').find(e => String(e.id) === String(id));
        if (!item) return;
        setModalMeta('edit', 'event', 'Edit Event SKP: ' + (item.name || item.title), 'Perbarui detail kegiatan ilmiah, nilai kredit SKP, dan akreditasi');
        container.innerHTML =
          uiSection('event_note', 'Informasi Kegiatan Ilmiah', `
            <div>${uiField('Nama Event / Kegiatan SKP', true, `<input required type="text" id="editEvName" value="${item.name || item.title || ''}" class="${UI_INPUT}" />`)}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Kategori Event', true, `<select id="editEvCatId" class="${UI_SELECT}">${buildCategoryOptions(item.category_id, item.category)}</select>`)}</div>
              <div>${uiField('Nilai Kredit SKP', true, `<input required type="number" step="0.5" min="0.5" id="editEvCredits" value="${item.credits || item.creditsDisplay || 2}" class="${UI_INPUT}" />`)}</div>
            </div>
          `) +
          uiSection('verified', 'Jadwal & Akreditasi Kemenkes', `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Penyelenggara', true, `<input required type="text" id="editEvOrg" value="${item.organizer || 'PP POGI'}" class="${UI_INPUT}" />`)}</div>
              <div>${uiField('Tanggal Pelaksanaan', true, `<input required type="date" id="editEvDate" value="${item.date || ''}" class="${UI_INPUT}" />`)}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('No. Akreditasi Kemenkes', false, `<input type="text" id="editEvAcc" value="${item.kemenkes_accreditation_number || ''}" class="${UI_INPUT} font-mono" />`)}</div>
              <div>${uiField('ID Course Plataran Sehat', false, `<input type="text" id="editEvPlataran" value="${item.plataran_sehat_course_id || ''}" class="${UI_INPUT} font-mono" />`)}</div>
            </div>
          `);
      } else if (type === 'category') {
        const item = getDefaultListForTab('category').find(c => String(c.id) === String(id));
        if (!item) return;
        const dom = String(item.domain || item.type || 'PEMBELAJARAN').toUpperCase();
        setModalMeta('edit', 'category', 'Edit Kategori: ' + item.name, 'Sesuaikan ranah SKP dan nilai kredit standar kategori');
        container.innerHTML =
          uiSection('category', 'Detail Kategori & Ranah SKP', `
            <div>${uiField('Nama Kategori', true, `<input required type="text" id="editCatName" value="${item.name || ''}" class="${UI_INPUT}" />`)}</div>
            <div>${uiField('Ranah SKP', true, uiSegmentedPills('editCatType', dom, [
              { value: 'PEMBELAJARAN', label: 'Pembelajaran' },
              { value: 'PELAYANAN', label: 'Pelayanan' },
              { value: 'PENGABDIAN', label: 'Pengabdian' },
              { value: 'PUBLIKASI_ILMIAH', label: 'Publikasi Ilmiah' }
            ]))}</div>
            <div>${uiField('Default Nilai SKP', false, `<input type="number" step="0.5" min="0" id="editCatDefaultSkp" value="${item.default_credit_value ?? 2}" class="${UI_INPUT}" />`)}</div>
            <div>${uiField('Status Kategori', true, uiSegmentedPills('editCatStatus', (item.status || (item.is_active === false ? 'INACTIVE' : 'ACTIVE')).toUpperCase(), [
              { value: 'ACTIVE', label: 'ACTIVE', dotClass: 'bg-emerald-500', activeClass: 'bg-emerald-600 text-white border-emerald-600 shadow-xs' },
              { value: 'INACTIVE', label: 'INACTIVE', dotClass: 'bg-amber-500', activeClass: 'bg-amber-500 text-white border-amber-500 shadow-xs' }
            ]))}</div>
            <div>${uiField('Deskripsi', false, `<textarea id="editCatDesc" rows="2" class="${UI_TEXTAREA}">${item.description || ''}</textarea>`)}</div>
          `);
      } else if (type === 'user') {
        const item = getDefaultListForTab('user').find(u => String(u.id) === String(id));
        if (!item) return;
        setModalMeta('edit', 'user', 'Edit Pengguna: ' + item.name, 'Kelola profil akun, peran RBAC, cabang POGI, dan keamanan login');
        container.innerHTML =
          uiSection('person', 'Profil & Kontak Pengguna', `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Nama Pengguna', true, uiIconInput('person', `<input required type="text" id="editUsrName" value="${item.name || ''}" class="${UI_INPUT} pl-9" />`))}</div>
              <div>${uiField('No. Telepon / WhatsApp', false, uiIconInput('call', `<input type="text" id="editUsrPhone" value="${item.phone_number || ''}" placeholder="+62812..." class="${UI_INPUT} pl-9 font-mono" />`))}</div>
            </div>
            <div>${uiField('Alamat Email', true, uiIconInput('mail', `<input required type="email" id="editUsrEmail" value="${item.email || ''}" class="${UI_INPUT} pl-9 font-mono" />`))}</div>
          `) +
          uiSection('admin_panel_settings', 'Hak Akses, Status & Keamanan', `
            <div id="editUsrRoleGrid" class="${item.role === 'Admin Cabang' ? 'grid grid-cols-1 sm:grid-cols-2 gap-3' : 'grid grid-cols-1 gap-3'}">
              <div>${uiField('Role / Peran Akses', true, `<select id="editUsrRole" class="${UI_SELECT}" onchange="toggleBranchField('edit', this.value)">
                <option value="">Pilih Role / Peran Akses</option>
                <option value="Super Admin" ${item.role === 'Super Admin' ? 'selected' : ''}>Super Admin</option>
                <option value="Admin Pusat" ${item.role === 'Admin Pusat' ? 'selected' : ''}>Admin Pusat</option>
                <option value="Admin POGI" ${item.role === 'Admin POGI' || item.role === 'Verifikator' ? 'selected' : ''}>Admin POGI</option>
                <option value="Admin Cabang" ${item.role === 'Admin Cabang' ? 'selected' : ''}>Admin Cabang</option>
                <option value="User/Dokter" ${item.role === 'User/Dokter' || item.role === 'Dokter' ? 'selected' : ''}>User/Dokter</option>
              </select>`)}</div>
              <div id="editUsrBranchWrapper" style="${item.role === 'Admin Cabang' ? '' : 'display:none'}">${uiField('Cabang POGI (Opsional)', false, `<select id="editUsrBranch" class="${UI_SELECT}">${buildBranchOptions(item.branch_id)}</select>`)}</div>
            </div>
            <div>${uiField('Status Akun', true, uiSegmentedPills('editUsrStatus', item.account_status || 'ACTIVE', [
              { value: 'ACTIVE', label: 'ACTIVE', dotClass: 'bg-emerald-500', activeClass: 'bg-emerald-600 text-white border-emerald-600 shadow-xs' },
              { value: 'INACTIVE', label: 'INACTIVE', dotClass: 'bg-amber-500', activeClass: 'bg-amber-500 text-white border-amber-500 shadow-xs' },
              { value: 'BLOCK', label: 'BLOCK', dotClass: 'bg-rose-500', activeClass: 'bg-rose-600 text-white border-rose-600 shadow-xs' }
            ]))}</div>
            <div>${uiPasswordWithConfirm('editUsrPass', 'editUsrPassConfirm', 'Reset Password Baru (Opsional)', 'Kosongkan jika tidak diubah')}</div>
          `);
      }

      modal.classList.remove('hidden');
      initModalSelect2(modal);
    }

    function closeManageEditModal() {
      const modal = document.getElementById('manageEditModal');
      if (modal) modal.classList.add('hidden');
    }

    function showModalFormError(mode, msg) {
      const containerId = mode === 'add' ? 'manageAddFormFields' : 'manageEditFormFields';
      const boxId = mode === 'add' ? 'manageAddErrorBanner' : 'manageEditErrorBanner';
      const container = document.getElementById(containerId);
      if (!container) return;
      let banner = document.getElementById(boxId);
      if (!msg) {
        if (banner) banner.remove();
        return;
      }
      if (!banner) {
        banner = document.createElement('div');
        banner.id = boxId;
        banner.className = 'p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-start gap-2';
        container.prepend(banner);
      }
      banner.innerHTML = `<span class="material-symbols-outlined text-[16px] text-rose-500 shrink-0 mt-0.5">error</span><span>${msg}</span>`;
      container.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function extractValidationMessage(data, fallback) {
      if (data && data.errors && typeof data.errors === 'object') {
        const firstKey = Object.keys(data.errors)[0];
        if (firstKey && Array.isArray(data.errors[firstKey]) && data.errors[firstKey][0]) {
          return data.errors[firstKey][0];
        }
      }
      return (data && data.message) ? data.message : fallback;
    }

    function setSubmitLoading(btnId, isLoading, defaultHtml) {
      const btn = document.getElementById(btnId);
      if (!btn) return;
      btn.disabled = isLoading;
      btn.innerHTML = isLoading
        ? '<span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span><span>Menyimpan...</span>'
        : defaultHtml;
    }

    async function submitManageEdit() {
      const { type, id } = activeActionContext;
      if (!type || !id) return;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const defaultBtnHtml = '<span class="material-symbols-outlined text-[16px]">save</span><span>Simpan Perubahan</span>';
      showModalFormError('edit', null);
      setSubmitLoading('manageEditSubmitBtn', true, defaultBtnHtml);

      try {
        if (type === 'doctor') {
          const docs = window.LIVE_MANAGE_DOCTORS || [];
          const docIdx = docs.findIndex(d => String(d.id) === String(id));
          if (docIdx === -1) { showToast('Data dokter tidak ditemukan di cache.', 'error'); return; }
          const instSel = document.getElementById('editDocInstId');
          const branchSel = document.getElementById('editDocBranch');
          let phone = document.getElementById('editDocPhone')?.value || '';
          phone = phone.trim();
          if (phone.startsWith('0')) {
            phone = '+62' + phone.substring(1);
          } else if (phone && !phone.startsWith('+')) {
            phone = '+62' + phone;
          }
          const payload = {
            name: document.getElementById('editDocName')?.value || '',
            specialization_title: document.getElementById('editDocSpec')?.value || 'Sp.OG',
            npa_pogi: document.getElementById('editDocNpa')?.value || '',
            nik: document.getElementById('editDocNik')?.value || '',
            str_number: document.getElementById('editDocStr')?.value || '',
            sip_number: document.getElementById('editDocSip')?.value || '',
            sip_expiry_date: document.getElementById('editDocSipExp')?.value || '',
            phone: phone,
            email: document.getElementById('editDocEmail')?.value || '',
            province: document.getElementById('editDocProv')?.value || '',
            branch_id: branchSel?.value || null,
            institution_id: instSel?.value || null,
            period_start: document.getElementById('editDocPeriodStart')?.value || '',
            period_end: document.getElementById('editDocPeriodEnd')?.value || '',
            plataran_sehat_username: document.getElementById('editDocPlataranUser')?.value || null,
            plataran_sehat_password: document.getElementById('editDocPlataranPass')?.value || null,
            status: document.getElementById('editDocStatus')?.value || 'INCOMPLETE'
          };
          const res = await fetch('/manage/doctor/' + encodeURIComponent(id) + '/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (!res.ok || !data.success) { showToast(data.message || 'Gagal memperbarui dokter.', 'error'); return; }
          Object.assign(docs[docIdx], payload);
          docs[docIdx].pogiId = payload.npa_pogi || docs[docIdx].pogiId;
          docs[docIdx].raw_status = payload.status;
          docs[docIdx].status = payload.status === 'COMPLETE' ? 'Complete' : 'Incomplete';
          docs[docIdx].primary_institution_id = payload.institution_id;
          if (instSel && instSel.value) {
            const txt = instSel.options[instSel.selectedIndex].text;
            docs[docIdx].institution = txt;
            docs[docIdx].hospital = txt;
          }
          if (branchSel && branchSel.value) {
            docs[docIdx].branch = branchSel.options[branchSel.selectedIndex].text;
          }
          currentFilteredCache.doctor = null;
          renderManageDoctorTable(null, false);
          showToast('Data Dokter ' + docs[docIdx].name + ' berhasil diperbarui!', 'success');
        } else if (type === 'institution') {
          const instList = window.LIVE_MANAGE_INSTITUTIONS || [];
          const instIdx = instList.findIndex(i => String(i.id) === String(id));
          if (instIdx === -1) { showToast('Data institusi tidak ditemukan di cache.', 'error'); return; }
          const instSt = document.getElementById('editInstStatus')?.value || 'ACTIVE';
          const payload = {
            name: document.getElementById('editInstName').value,
            code: document.getElementById('editInstCode').value,
            institution_type: document.getElementById('editInstType').value,
            hospital_class: document.getElementById('editInstClass').value,
            province: document.getElementById('editInstProv').value,
            city: document.getElementById('editInstCity').value,
            address: document.getElementById('editInstAddr').value,
            status: instSt,
            is_active: (instSt === 'ACTIVE')
          };
          const res = await fetch('/manage/institution/' + encodeURIComponent(id) + '/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (!res.ok || !data.success) { showToast(data.message || 'Gagal memperbarui institusi.', 'error'); return; }
          Object.assign(instList[instIdx], payload);
          instList[instIdx].type = payload.institution_type;
          instList[instIdx].status = payload.status === 'ACTIVE' ? 'Aktif' : 'Nonaktif';
          instList[instIdx].is_active = (payload.status === 'ACTIVE');
          instList[instIdx].lastActivity = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace(':', '.') + ' WIB';
          currentFilteredCache.institution = null;
          renderManageInstTable(null, false);
          showToast('Data Institusi berhasil diperbarui!', 'success');
        } else if (type === 'event') {
          const evList = window.LIVE_MANAGE_EVENTS || [];
          const evIdx = evList.findIndex(e => String(e.id) === String(id));
          if (evIdx === -1) { showToast('Data event tidak ditemukan di cache.', 'error'); return; }
          const catSel = document.getElementById('editEvCatId');
          const selectedOpt = catSel?.options[catSel.selectedIndex];
          const catDomain = selectedOpt?.getAttribute('data-domain') || evList[evIdx].type || 'PEMBELAJARAN';
          const payload = {
            name: document.getElementById('editEvName').value,
            category_id: catSel?.value || null,
            type: catDomain,
            credits: document.getElementById('editEvCredits').value,
            organizer: document.getElementById('editEvOrg').value,
            date: document.getElementById('editEvDate').value,
            kemenkes_accreditation_number: document.getElementById('editEvAcc').value,
            plataran_sehat_course_id: document.getElementById('editEvPlataran').value
          };
          const res = await fetch('/manage/event/' + encodeURIComponent(id) + '/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (!res.ok || !data.success) { showToast(data.message || 'Gagal memperbarui event.', 'error'); return; }
          Object.assign(evList[evIdx], payload);
          evList[evIdx].title = payload.name;
          evList[evIdx].creditsDisplay = String(payload.credits);
          evList[evIdx].credits = parseFloat(payload.credits) || 1;
          if (selectedOpt) evList[evIdx].category = selectedOpt.text.split(' (')[0];
          currentFilteredCache.event = null;
          renderManageEventTable(null, false);
          showToast('Data SKP Event berhasil diperbarui!', 'success');
        } else if (type === 'category') {
          const catList = window.LIVE_MANAGE_CATEGORIES || [];
          const catIdx = catList.findIndex(c => String(c.id) === String(id));
          if (catIdx === -1) { showToast('Data kategori tidak ditemukan di cache.', 'error'); return; }
          const payload = {
            name: document.getElementById('editCatName').value,
            type: document.getElementById('editCatType').value,
            default_credit_value: document.getElementById('editCatDefaultSkp').value,
            description: document.getElementById('editCatDesc').value,
            status: document.getElementById('editCatStatus')?.value || 'ACTIVE'
          };
          const res = await fetch('/manage/category/' + encodeURIComponent(id) + '/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (!res.ok || !data.success) { showToast(data.message || 'Gagal memperbarui kategori.', 'error'); return; }
          catList[catIdx].name = payload.name;
          catList[catIdx].domain = payload.type;
          catList[catIdx].type = payload.type;
          catList[catIdx].default_credit_value = parseFloat(payload.default_credit_value) || 0;
          catList[catIdx].standardSkp = catList[catIdx].default_credit_value + ' SKP';
          catList[catIdx].description = payload.description;
          catList[catIdx].status = payload.status;
          currentFilteredCache.category = null;
          renderManageCatTable(null, false);
          showToast('Data Kategori berhasil diperbarui!', 'success');
        } else if (type === 'user') {
          const userList = window.LIVE_MANAGE_USERS || [];
          const usrIdx = userList.findIndex(u => String(u.id) === String(id));
          if (usrIdx === -1) { showToast('Data pengguna tidak ditemukan di cache.', 'error'); return; }
          const branchSel = document.getElementById('editUsrBranch');
          const editPass = document.getElementById('editUsrPass')?.value || '';
          const editPassConfirm = document.getElementById('editUsrPassConfirm')?.value || '';
          const passErr = validatePasswordInput(editPass, editPassConfirm);
          if (passErr) { showModalFormError('edit', passErr); showToast(passErr, 'error'); return; }
          const payload = {
            name: document.getElementById('editUsrName').value,
            email: document.getElementById('editUsrEmail').value,
            phone_number: document.getElementById('editUsrPhone').value,
            role: document.getElementById('editUsrRole').value,
            account_status: document.getElementById('editUsrStatus').value,
            branch_id: branchSel?.value || null,
            password: editPass || null,
            password_confirmation: editPassConfirm || null
          };
          const res = await fetch('/manage/user/' + encodeURIComponent(id) + '/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (!res.ok || !data.success) { const msg = extractValidationMessage(data, 'Gagal memperbarui pengguna.'); showModalFormError('edit', msg); showToast(msg, 'error'); return; }
          // Mutasi langsung di index array → dijamin merefleksikan perubahan ke window.LIVE_MANAGE_USERS
          userList[usrIdx].name = payload.name;
          userList[usrIdx].email = payload.email;
          userList[usrIdx].phone_number = payload.phone_number;
          userList[usrIdx].role = payload.role;
          userList[usrIdx].account_status = payload.account_status;
          userList[usrIdx].is_active = (payload.account_status === 'ACTIVE');
          userList[usrIdx].branch_id = payload.branch_id;
          userList[usrIdx].branch_name = (branchSel && branchSel.value) ? branchSel.options[branchSel.selectedIndex].text : null;
          currentFilteredCache.user = null; // paksa re-render dari window.LIVE_MANAGE_USERS yang sudah dimutasi
          renderManageUserTable(null, false);
          showToast('Data Pengguna berhasil diperbarui ke basis data!', 'success');
          // Jika user mengedit akunnya sendiri → update sidebar & AUTH_USER tanpa reload
          if (String(id) === String(window.AUTH_USER?.id)) {
            const sidebarName = document.getElementById('sidebarUserName');
            const sidebarEmail = document.getElementById('sidebarUserEmail');
            const sidebarAvatar = document.getElementById('sidebarUserAvatar');
            if (sidebarName) sidebarName.textContent = payload.name;
            if (sidebarEmail) sidebarEmail.textContent = payload.email;
            if (sidebarAvatar) {
              const initials = payload.name.trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase();
              sidebarAvatar.textContent = initials;
            }
            if (window.AUTH_USER) { window.AUTH_USER.name = payload.name; window.AUTH_USER.email = payload.email; }
          }
        }

        closeManageEditModal();
      } catch (err) {
        showToast('Terjadi kesalahan jaringan saat memperbarui data.', 'error');
      } finally {
        setSubmitLoading('manageEditSubmitBtn', false, defaultBtnHtml);
      }
    }

    async function deleteManageItem(type, id) {
      if (!confirm('Apakah Anda yakin ingin menghapus data ' + type + ' ini dari master data POGI? Tindakan ini tidak dapat dibatalkan.')) {
        return;
      }
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      try {
        const res = await fetch('/manage/' + encodeURIComponent(type) + '/' + encodeURIComponent(id), {
          method: 'DELETE',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
          showToast(data.message || 'Gagal menghapus data dari basis data.', 'error');
          return;
        }
      } catch (err) {
        showToast('Gagal terhubung ke server saat menghapus data.', 'error');
        return;
      }

      if (type === 'doctor') {
        window.LIVE_MANAGE_DOCTORS = (window.LIVE_MANAGE_DOCTORS || []).filter(d => String(d.id) !== String(id));
        renderManageDoctorTable(null, false);
      } else if (type === 'institution') {
        window.LIVE_MANAGE_INSTITUTIONS = (window.LIVE_MANAGE_INSTITUTIONS || []).filter(i => String(i.id) !== String(id));
        renderManageInstTable(null, false);
      } else if (type === 'event') {
        window.LIVE_MANAGE_EVENTS = (window.LIVE_MANAGE_EVENTS || []).filter(e => String(e.id) !== String(id));
        renderManageEventTable(null, false);
      } else if (type === 'category') {
        window.LIVE_MANAGE_CATEGORIES = (window.LIVE_MANAGE_CATEGORIES || []).filter(c => String(c.id) !== String(id));
        renderManageCatTable(null, false);
      } else if (type === 'user') {
        window.LIVE_MANAGE_USERS = (window.LIVE_MANAGE_USERS || []).filter(u => String(u.id) !== String(id));
        renderManageUserTable(null, false);
      }

      showToast('Data ' + type + ' berhasil dihapus dari basis data!', 'warning');
    }

    function openActiveAddModal() {
      const modal = document.getElementById('manageAddModal');
      const container = document.getElementById('manageAddFormFields');
      if (!modal || !container) return;

      const todayStr = new Date().toISOString().split('T')[0];
      const fiveYearsLater = new Date(new Date().setFullYear(new Date().getFullYear() + 5)).toISOString().split('T')[0];

      if (currentTab === 'doctor') {
        setModalMeta('add', 'doctor', 'Add Doctor', 'Daftarkan dokter baru beserta kredensial LMS Plataran dan periode SKP');
        const submitBtn = document.getElementById('manageAddSubmitBtn');
        if (submitBtn) {
          submitBtn.innerHTML = '<span class="material-symbols-outlined text-[16px]">person_add</span><span>Add doctor</span>';
        }
        container.innerHTML =
          uiSection('stethoscope', 'Doctor info', `
            <div>${uiField('Name', true, `<input required type="text" id="addDocName" placeholder="dr. Budi Santoso, Sp.PD" class="${UI_INPUT}" />`)}</div>
            <div>${uiField('STR', false, `<input type="text" id="addDocStr" placeholder="32.73.100.2.21.0883" class="${UI_INPUT} font-mono" />`)}</div>
            <div>${uiField('Phone', true, `<input required type="text" id="addDocPhone" placeholder="08123456789" class="${UI_INPUT} font-mono" />`, 'Numbers starting with 0 are saved as +62.')}</div>
            <div>${uiField('Province', true, `<select id="addDocProv" class="${UI_SELECT}">${buildProvinceOptions('DKI Jakarta')}</select>`)}</div>
            <div>${uiField('Institution', true, `<select id="addDocInstId" class="${UI_SELECT}">${buildInstitutionOptions('', '')}</select>`)}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Period start', true, `<input type="date" id="addDocPeriodStart" value="2026-01-01" class="${UI_INPUT}" />`)}</div>
              <div>${uiField('Period end', true, `<input type="date" id="addDocPeriodEnd" value="2030-12-31" class="${UI_INPUT}" />`)}</div>
            </div>
          `) +
          uiSection('vpn_key', 'Credentials', `
            <div class="text-[11px] text-slate-400 font-semibold mb-2">LMS Plataran Account</div>
            <div class="space-y-3">
              <div>${uiField('Username', false, `<input type="text" id="addDocPlataranUser" placeholder="super@example.com" class="${UI_INPUT} bg-sky-50/70 border-sky-100" />`)}</div>
              <div>${uiField('Password', false, `<input type="password" id="addDocPlataranPass" placeholder="••••••••" class="${UI_INPUT} bg-sky-50/70 border-sky-100" />`)}</div>
            </div>
          `) +
          `<details class="group border border-slate-200 rounded-2xl p-3 bg-slate-50/50 mt-1">
            <summary class="text-xs font-bold text-slate-600 cursor-pointer flex items-center justify-between">
              <span>Informasi Tambahan POGI & SIP (Opsional)</span>
              <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
            </summary>
            <div class="pt-3 space-y-3">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>${uiField('Gelar Spesialis', false, `<input type="text" id="addDocSpec" value="Sp.OG" class="${UI_INPUT}" />`)}</div>
                <div>${uiField('NPA POGI', false, `<input type="text" id="addDocNpa" placeholder="Auto jika dikosongkan" class="${UI_INPUT} font-mono" />`)}</div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>${uiField('NIK (16 Digit)', false, `<input type="text" id="addDocNik" placeholder="3171..." class="${UI_INPUT} font-mono" />`)}</div>
                <div>${uiField('Cabang POGI', false, `<select id="addDocBranch" class="${UI_SELECT}">${buildBranchOptions('')}</select>`)}</div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>${uiField('Nomor SIP', false, `<input type="text" id="addDocSip" placeholder="446/.../SIP/..." class="${UI_INPUT} font-mono" />`)}</div>
                <div>${uiField('Masa Berlaku SIP', false, `<input type="date" id="addDocSipExp" value="${fiveYearsLater}" class="${UI_INPUT}" />`)}</div>
              </div>
              <div>${uiField('Email', false, uiIconInput('mail', `<input type="email" id="addDocEmail" placeholder="dokter@pogi.or.id" class="${UI_INPUT} pl-9" />`))}</div>
            </div>
          </details>`;
      } else if (currentTab === 'institution') {
        setModalMeta('add', 'institution', 'Tambah Rumah Sakit / Faskes', 'Tambahkan fasilitas kesehatan jejaring POGI beserta klasifikasinya');
        container.innerHTML =
          uiSection('local_hospital', 'Identitas Rumah Sakit / Faskes', `
            <div>${uiField('Nama Rumah Sakit / Faskes', true, `<input required type="text" id="addInstName" placeholder="RSUP / RSUD ..." class="${UI_INPUT}" />`)}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-3">
              <div>${uiField('Kode RS', false, `<input type="text" id="addInstCode" placeholder="Kode Kemenkes" class="${UI_INPUT} font-mono" />`)}</div>
              <div>${uiField('Tipe Faskes', true, `<select id="addInstType" class="${UI_SELECT}">${buildInstitutionTypeOptions('RS Jejaring')}</select>`)}</div>
              <div>${uiField('Kelas RS', false, `<select id="addInstClass" class="${UI_SELECT}"><option value="">-</option><option value="A">Kelas A</option><option value="B">Kelas B</option><option value="C">Kelas C</option><option value="D">Kelas D</option></select>`)}</div>
              <div>${uiField('Status Faskes', true, `<select id="addInstStatus" class="${UI_SELECT}"><option value="ACTIVE" selected>Aktif</option><option value="INACTIVE">Nonaktif</option></select>`)}</div>
            </div>
          `) +
          uiSection('location_on', 'Lokasi & Alamat', `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Provinsi', true, `<select id="addInstProv" class="${UI_SELECT}">${buildProvinceOptions('DKI Jakarta')}</select>`)}</div>
              <div>${uiField('Kota / Kabupaten', false, `<input type="text" id="addInstCity" placeholder="Jakarta Pusat" class="${UI_INPUT}" />`)}</div>
            </div>
            <div>${uiField('Alamat Lengkap', false, `<textarea id="addInstAddr" rows="2" class="${UI_TEXTAREA}" placeholder="Jl. ..."></textarea>`)}</div>
          `);
      } else if (currentTab === 'event') {
        setModalMeta('add', 'event', 'Tambah Event SKP Terakreditasi', 'Buat kegiatan ilmiah baru dengan bobot SKP dan integrasi Plataran Sehat');
        container.innerHTML =
          uiSection('event_note', 'Informasi Kegiatan Ilmiah', `
            <div>${uiField('Nama Event / Kegiatan', true, `<input required type="text" id="addEvName" placeholder="Contoh: PIT POGI 2026..." class="${UI_INPUT}" />`)}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Kategori Event', true, `<select id="addEvCatId" class="${UI_SELECT}">${buildCategoryOptions('', '')}</select>`)}</div>
              <div>${uiField('Nilai Kredit SKP', true, `<input required type="number" step="0.5" min="0.5" id="addEvCredits" value="2" class="${UI_INPUT}" />`)}</div>
            </div>
          `) +
          uiSection('verified', 'Jadwal & Akreditasi Kemenkes', `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Penyelenggara', true, `<input required type="text" id="addEvOrg" value="PP POGI" class="${UI_INPUT}" />`)}</div>
              <div>${uiField('Tanggal Pelaksanaan', true, `<input required type="date" id="addEvDate" value="${todayStr}" class="${UI_INPUT}" />`)}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('No. Akreditasi Kemenkes', false, `<input type="text" id="addEvAcc" placeholder="Nomor SKP Kemenkes" class="${UI_INPUT} font-mono" />`)}</div>
              <div>${uiField('ID Course Plataran Sehat', false, `<input type="text" id="addEvPlataran" placeholder="PS-..." class="${UI_INPUT} font-mono" />`)}</div>
            </div>
          `);
      } else if (currentTab === 'category') {
        setModalMeta('add', 'category', 'Tambah Kategori Kegiatan SKP', 'Kelompokkan aktivitas SKP berdasarkan ranah resmi Kemenkes');
        container.innerHTML =
          uiSection('category', 'Detail Kategori & Ranah SKP', `
            <div>${uiField('Nama Kategori', true, `<input required type="text" id="addCatName" placeholder="Contoh: Workshop Ultrasonografi..." class="${UI_INPUT}" />`)}</div>
            <div>${uiField('Ranah SKP', true, uiSegmentedPills('addCatType', 'PEMBELAJARAN', [
              { value: 'PEMBELAJARAN', label: 'Pembelajaran' },
              { value: 'PELAYANAN', label: 'Pelayanan' },
              { value: 'PENGABDIAN', label: 'Pengabdian' },
              { value: 'PUBLIKASI_ILMIAH', label: 'Publikasi Ilmiah' }
            ]))}</div>
            <div>${uiField('Default Nilai SKP', false, `<input type="number" step="0.5" min="0" id="addCatDefaultSkp" value="2" class="${UI_INPUT}" />`)}</div>
            <div>${uiField('Status Kategori', true, uiSegmentedPills('addCatStatus', 'ACTIVE', [
              { value: 'ACTIVE', label: 'ACTIVE', dotClass: 'bg-emerald-500', activeClass: 'bg-emerald-600 text-white border-emerald-600 shadow-xs' },
              { value: 'INACTIVE', label: 'INACTIVE', dotClass: 'bg-amber-500', activeClass: 'bg-amber-500 text-white border-amber-500 shadow-xs' }
            ]))}</div>
            <div>${uiField('Deskripsi', false, `<textarea id="addCatDesc" rows="2" placeholder="Keterangan singkat kategori..." class="${UI_TEXTAREA}"></textarea>`)}</div>
          `);
      } else if (currentTab === 'user') {
        setModalMeta('add', 'user', 'Tambah Pengguna & Hak Akses', 'Buat akun pengelola atau anggota baru dengan peran RBAC dan status akun');
        container.innerHTML =
          uiSection('person', 'Profil & Kontak Pengguna', `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>${uiField('Nama Lengkap', true, uiIconInput('person', `<input required type="text" id="addUsrName" placeholder="Nama lengkap..." class="${UI_INPUT} pl-9" />`))}</div>
              <div>${uiField('No. Telepon / WhatsApp', false, uiIconInput('call', `<input type="text" id="addUsrPhone" placeholder="+62812..." class="${UI_INPUT} pl-9 font-mono" />`))}</div>
            </div>
            <div>${uiField('Alamat Email', true, uiIconInput('mail', `<input required type="email" id="addUsrEmail" placeholder="user@pogi.or.id" class="${UI_INPUT} pl-9 font-mono" />`))}</div>
          `) +
          uiSection('admin_panel_settings', 'Hak Akses, Status & Keamanan', `
            <div id="addUsrRoleGrid" class="grid grid-cols-1 gap-3">
              <div>${uiField('Role / Peran Akses', true, `<select id="addUsrRole" class="${UI_SELECT}" onchange="toggleBranchField('add', this.value)"><option value="">Pilih Role / Peran Akses</option><option value="Super Admin">Super Admin</option><option value="Admin Pusat">Admin Pusat</option><option value="Admin POGI">Admin POGI</option><option value="Admin Cabang">Admin Cabang</option><option value="User/Dokter">User/Dokter</option></select>`)}</div>
              <div id="addUsrBranchWrapper" style="display:none">${uiField('Cabang POGI (Opsional)', false, `<select id="addUsrBranch" class="${UI_SELECT}">${buildBranchOptions('')}</select>`)}</div>
            </div>
            <div>${uiField('Status Akun', true, uiSegmentedPills('addUsrStatus', 'ACTIVE', [
              { value: 'ACTIVE', label: 'ACTIVE', dotClass: 'bg-emerald-500', activeClass: 'bg-emerald-600 text-white border-emerald-600 shadow-xs' },
              { value: 'INACTIVE', label: 'INACTIVE', dotClass: 'bg-amber-500', activeClass: 'bg-amber-500 text-white border-amber-500 shadow-xs' },
              { value: 'BLOCK', label: 'BLOCK', dotClass: 'bg-rose-500', activeClass: 'bg-rose-600 text-white border-rose-600 shadow-xs' }
            ]))}</div>
            <div>${uiPasswordWithConfirm('addUsrPass', 'addUsrPassConfirm', 'Password', 'Minimal 8 karakter', true)}</div>
          `);
      }

      modal.classList.remove('hidden');
      initModalSelect2(modal);
    }

    function closeManageAddModal() {
      const modal = document.getElementById('manageAddModal');
      if (modal) modal.classList.add('hidden');
    }

    // Tampilkan/sembunyikan field Cabang POGI sesuai role
    function toggleBranchField(mode, roleVal) {
      const wrapperId = mode === 'add' ? 'addUsrBranchWrapper' : 'editUsrBranchWrapper';
      const gridId   = mode === 'add' ? 'addUsrRoleGrid'      : 'editUsrRoleGrid';
      const wrapper  = document.getElementById(wrapperId);
      const grid     = document.getElementById(gridId);
      if (!wrapper) return;
      const show = roleVal === 'Admin Cabang';
      wrapper.style.display = show ? '' : 'none';
      if (grid) grid.className = show
        ? 'grid grid-cols-1 sm:grid-cols-2 gap-3'
        : 'grid grid-cols-1 gap-3';
      
      const branchSelId = mode === 'add' ? 'addUsrBranch' : 'editUsrBranch';
      const branchSel = document.getElementById(branchSelId);
      if (show) {
        if (window.jQuery && branchSel) {
          const $b = $(branchSel);
          const modalId = mode === 'add' ? '#manageAddModal' : '#manageEditModal';
          if ($b.hasClass('select2-hidden-accessible')) {
            $b.select2('destroy');
          }
          $b.select2({
            dropdownParent: $(modalId),
            width: '100%'
          });
        }
      } else {
        // Reset nilai branch jika disembunyikan
        if (branchSel) {
          branchSel.value = '';
          if (window.jQuery && $(branchSel).hasClass('select2-hidden-accessible')) {
            $(branchSel).val('').trigger('change.select2');
          }
        }
      }
    }

    async function submitManageAdd() {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      let payload = { entity_type: currentTab };

      if (currentTab === 'doctor') {
        let phone = document.getElementById('addDocPhone')?.value || '';
        phone = phone.trim();
        if (phone.startsWith('0')) {
          phone = '+62' + phone.substring(1);
        } else if (phone && !phone.startsWith('+')) {
          phone = '+62' + phone;
        }
        payload.name = document.getElementById('addDocName')?.value || '';
        payload.str_number = document.getElementById('addDocStr')?.value || '';
        payload.phone = phone;
        payload.province = document.getElementById('addDocProv')?.value || '';
        payload.institution_id = document.getElementById('addDocInstId')?.value || null;
        payload.period_start = document.getElementById('addDocPeriodStart')?.value || '2026-01-01';
        payload.period_end = document.getElementById('addDocPeriodEnd')?.value || '2030-12-31';
        payload.plataran_sehat_username = document.getElementById('addDocPlataranUser')?.value || null;
        payload.plataran_sehat_password = document.getElementById('addDocPlataranPass')?.value || null;
        payload.specialization_title = document.getElementById('addDocSpec')?.value || 'Sp.OG';
        payload.npa_pogi = document.getElementById('addDocNpa')?.value || '';
        payload.nik = document.getElementById('addDocNik')?.value || '';
        payload.sip_number = document.getElementById('addDocSip')?.value || '';
        payload.sip_expiry_date = document.getElementById('addDocSipExp')?.value || '';
        payload.email = document.getElementById('addDocEmail')?.value || '';
        payload.branch_id = document.getElementById('addDocBranch')?.value || null;
      } else if (currentTab === 'institution') {
        payload.name = document.getElementById('addInstName').value;
        payload.code = document.getElementById('addInstCode').value;
        payload.institution_type = document.getElementById('addInstType').value;
        payload.hospital_class = document.getElementById('addInstClass').value;
        payload.province = document.getElementById('addInstProv').value;
        payload.city = document.getElementById('addInstCity').value;
        payload.address = document.getElementById('addInstAddr').value;
        payload.status = document.getElementById('addInstStatus')?.value || 'ACTIVE';
        payload.is_active = (payload.status === 'ACTIVE');
      } else if (currentTab === 'event') {
        const catSel = document.getElementById('addEvCatId');
        const selectedOpt = catSel?.options[catSel.selectedIndex];
        payload.name = document.getElementById('addEvName').value;
        payload.category_id = catSel?.value || null;
        payload.type = selectedOpt?.getAttribute('data-domain') || 'PEMBELAJARAN';
        payload.credits = document.getElementById('addEvCredits').value;
        payload.organizer = document.getElementById('addEvOrg').value;
        payload.date = document.getElementById('addEvDate').value;
        payload.kemenkes_accreditation_number = document.getElementById('addEvAcc').value;
        payload.plataran_sehat_course_id = document.getElementById('addEvPlataran').value;
      } else if (currentTab === 'category') {
        payload.name = document.getElementById('addCatName').value;
        payload.type = document.getElementById('addCatType').value;
        payload.default_credit_value = document.getElementById('addCatDefaultSkp').value;
        payload.description = document.getElementById('addCatDesc').value;
        payload.status = document.getElementById('addCatStatus')?.value || 'ACTIVE';
      } else if (currentTab === 'user') {
        const addPass = document.getElementById('addUsrPass')?.value || '';
        const addPassConfirm = document.getElementById('addUsrPassConfirm')?.value || '';
        const passErr = validatePasswordInput(addPass, addPassConfirm, true);
        if (passErr) { showModalFormError('add', passErr); showToast(passErr, 'error'); return; }
        payload.name = document.getElementById('addUsrName').value;
        payload.phone_number = document.getElementById('addUsrPhone').value;
        payload.email = document.getElementById('addUsrEmail').value;
        payload.role = document.getElementById('addUsrRole').value;
        payload.account_status = document.getElementById('addUsrStatus')?.value || 'ACTIVE';
        payload.branch_id = document.getElementById('addUsrBranch')?.value || null;
        payload.password = addPass || null;
        payload.password_confirmation = addPassConfirm || null;
      }

      const defaultBtnHtml = '<span class="material-symbols-outlined text-[16px]">check_circle</span><span>Simpan Data</span>';
      showModalFormError('add', null);
      setSubmitLoading('manageAddSubmitBtn', true, defaultBtnHtml);

      try {
        const res = await fetch('/manage/store', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
          const msg = extractValidationMessage(data, 'Gagal menyimpan data ke basis data.');
          showModalFormError('add', msg);
          showToast(msg, 'error');
          return;
        }

        const list = getDefaultListForTab(currentTab);
        if (data.item && !data.item.lastActivity) {
          data.item.lastActivity = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace(':', '.') + ' WIB';
        }
        list.unshift(data.item);

        if (currentTab === 'doctor') {
          renderManageDoctorTable(null, true);
          showToast('Dokter baru berhasil disimpan ke basis data!', 'success');
        } else if (currentTab === 'institution') {
          renderManageInstTable(null, true);
          showToast('Institusi baru berhasil disimpan ke basis data!', 'success');
        } else if (currentTab === 'event') {
          renderManageEventTable(null, true);
          showToast('SKP Event baru berhasil disimpan ke basis data!', 'success');
        } else if (currentTab === 'category') {
          renderManageCatTable(null, true);
          showToast('Kategori baru berhasil disimpan ke basis data!', 'success');
        } else if (currentTab === 'user') {
          renderManageUserTable(null, true);
          showToast('Pengguna baru berhasil disimpan ke basis data!', 'success');
        }

        closeManageAddModal();
      } catch (err) {
        showToast('Terjadi kesalahan jaringan saat menyimpan data.', 'error');
      } finally {
        setSubmitLoading('manageAddSubmitBtn', false, defaultBtnHtml);
      }
    }
  </script>
@endsection