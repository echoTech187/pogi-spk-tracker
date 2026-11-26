<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f4f7f6]">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'POGI SKP Tracker - Dashboard Pemantauan SKP Nasional')</title>
  
  <!-- Tailwind CSS & CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Leaflet.js CDN for Interactive Vector Map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <!-- jQuery & Select2 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- SheetJS (Excel XLSX Import & Export) -->
  <script src="/js/vendor/xlsx.full.min.js"></script>
  <script>
    if (typeof XLSX === 'undefined') {
      document.write('<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"><\/script>');
    }
  </script>
  
  <!-- Core Styles -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    /* =========================================================
       Select2 Custom Tailwind Theme (Matching POGI SKP Tracker)
       ========================================================= */
    .select2-container {
      width: 100% !important;
      display: block !important;
    }
    .select2-container .select2-selection--single {
      height: 38px !important;
      background-color: #f8fafc !important; /* bg-slate-50 */
      border: 1px solid #e2e8f0 !important; /* border-slate-200 */
      border-radius: 0.75rem !important; /* rounded-xl (12px) */
      display: flex !important;
      align-items: center !important;
      padding: 0 4px !important;
      outline: none !important;
      transition: all 0.15s ease-in-out !important;
      box-sizing: border-box !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      color: #1e293b !important; /* text-slate-800 */
      font-size: 0.75rem !important; /* text-xs (12px) */
      font-weight: 600 !important; /* font-semibold */
      line-height: 36px !important;
      padding-left: 8px !important;
      padding-right: 28px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
      color: #94a3b8 !important; /* text-slate-400 */
      font-weight: 500 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 100% !important;
      right: 8px !important;
      top: 0 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
      border-color: #64748b transparent transparent transparent !important;
      border-width: 5px 4px 0 4px !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
      border-color: transparent transparent #64748b transparent !important;
      border-width: 0 4px 5px 4px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
      background-color: #ffffff !important;
      border-color: #16b3ac !important;
      box-shadow: 0 0 0 3px rgba(22, 179, 172, 0.15) !important;
    }
    .select2-dropdown {
      background-color: #ffffff !important;
      border: 1px solid #e2e8f0 !important;
      border-radius: 0.75rem !important;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
      overflow: hidden !important;
      z-index: 9999999 !important;
      font-family: inherit !important;
      padding-top: 4px !important;
      padding-bottom: 4px !important;
    }
    .select2-container--default .select2-search--dropdown {
      padding: 6px 8px !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
      border: 1px solid #cbd5e1 !important;
      border-radius: 0.5rem !important;
      padding: 6px 10px !important;
      font-size: 0.75rem !important;
      outline: none !important;
      color: #1e293b !important;
      box-sizing: border-box !important;
      width: 100% !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
      border-color: #16b3ac !important;
      box-shadow: 0 0 0 2px rgba(22, 179, 172, 0.15) !important;
    }
    .select2-results__option {
      padding: 7px 12px !important;
      font-size: 0.75rem !important;
      font-weight: 500 !important;
      color: #334155 !important;
      cursor: pointer !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: #16b3ac !important;
      color: #ffffff !important;
    }
    .select2-container--default .select2-results__option[aria-selected="true"] {
      background-color: #f0fdf4 !important;
      color: #0d9488 !important;
      font-weight: 700 !important;
    }
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

    /* Modal Sticky Header & Footer, Scrollable Body */
    .modal-dialog-scrollable {
      max-height: 90vh !important;
      display: flex !important;
      flex-direction: column !important;
      overflow: hidden !important;
    }
    .modal-header-sticky {
      flex-shrink: 0 !important;
      position: sticky !important;
      top: 0 !important;
      z-index: 20 !important;
    }
    .modal-body-scrollable {
      flex: 1 1 auto !important;
      overflow-y: auto !important;
      min-height: 0 !important;
    }
    .modal-footer-sticky {
      flex-shrink: 0 !important;
      position: sticky !important;
      bottom: 0 !important;
      z-index: 20 !important;
    }
  </style>
  @yield('styles')
</head>
<body class="h-full bg-[#f4f7f6] text-slate-800 antialiased font-sans">
  <!-- Mobile Backdrop Overlay -->
  <div id="sidebarOverlay" onclick="toggleMobileSidebar(false)" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden pointer-events-none transition-opacity lg:hidden"></div>

  <div class="flex h-screen overflow-hidden">
    <!-- APP SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Sticky Header -->
      @include('layouts.header')

      <!-- Main Body Scrollable View -->
      <main class="flex-1 overflow-y-auto bg-white  p-4 sm:p-6 lg:p-8 space-y-6 page-fade-in">
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Global Application Modals (Outside Main Stacking Context for Full Viewport Overlay) -->
  @yield('modals')

  <script>
    window.AUTH_USER = {!! json_encode([
      'id'     => Auth::id() ?? 'auth-user',
      'name'   => Auth::user()?->full_name ?? Auth::user()?->name ?? 'Super Admin',
      'email'  => Auth::user()?->email ?? '',
      'role'   => Auth::user()?->role ?? 'SUPER_ADMIN',
      'branch' => Auth::user()?->branch ?? 'Pusat'
    ]) !!};
  </script>
  <!-- Core Scripts -->
  <script src="{{ asset('js/navigation.js') }}"></script>
  @yield('scripts')
</body>
</html>
