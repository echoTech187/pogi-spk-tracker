@props([
    'id',
    'title' => '',
    'subtitle' => '',
    'icon' => 'info',
    'iconBg' => 'bg-emerald-100 text-[#16b3ac]',
    'maxWidth' => 'max-w-2xl',
    'closeAction' => '',
])

<div id="{{ $id }}" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
  <div class="pogi-modal-card bg-white rounded-3xl {{ $maxWidth }} w-full max-h-[92vh] flex flex-col shadow-2xl overflow-hidden relative z-10 border border-slate-100">
    <!-- Modal Header (Sticky) -->
    <div class="p-5 sm:p-6 pb-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
      <div class="flex items-center gap-3 min-w-0">
        <div id="{{ $id }}IconWrap" class="w-10 h-10 rounded-2xl {{ $iconBg }} flex items-center justify-center font-bold shrink-0">
          <span id="{{ $id }}Icon" class="material-symbols-outlined text-xl">{{ $icon }}</span>
        </div>
        <div class="min-w-0">
          <h3 id="{{ $id }}Title" class="font-black text-slate-900 text-sm sm:text-base leading-snug tracking-tight truncate">{{ $title }}</h3>
          @if($subtitle)
            <p id="{{ $id }}Subtitle" class="text-xs text-slate-500 truncate">{{ $subtitle }}</p>
          @endif
        </div>
      </div>
      <button type="button" onclick="{{ $closeAction ?: "document.getElementById('{$id}').classList.add('hidden')" }}" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer shrink-0">
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>

    <!-- Modal Body (Scrollable) -->
    <div id="{{ $id }}Body" class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4 text-xs">
      {{ $slot }}
    </div>

    <!-- Modal Footer (Sticky) -->
    @if(isset($footer))
      <div id="{{ $id }}Footer" class="p-4 px-6 flex items-center justify-between border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
        {{ $footer }}
      </div>
    @endif
  </div>
</div>
