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
  
  <!-- Core Styles -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
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
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 page-fade-in">
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Global Application Modals (Outside Main Stacking Context for Full Viewport Overlay) -->
  @yield('modals')

  <!-- Core Scripts -->
  <script src="{{ asset('js/navigation.js') }}"></script>
  @yield('scripts')
</body>
</html>
