@props([
    'id',
    'title' => '',
    'subtitle' => '',
    'icon' => 'info',
    'iconBg' => 'bg-emerald-100 text-[#16b3ac]',
    'avatarText' => null,
    'avatarId' => null,
    'titleId' => null,
    'subtitleId' => null,
    'maxWidth' => 'max-w-2xl',
    'closeAction' => '',
    'formId' => null,
    'formOnsubmit' => null,
])

<div id="{{ $id }}" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
  <div class="pogi-modal-card bg-white rounded-3xl {{ $maxWidth }} w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative z-10 animate-scale-in">
    <!-- Modal Header (Sticky) -->
    <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100 bg-white shrink-0 sticky top-0 z-20">
      <div class="flex items-center gap-2.5 min-w-0">
        @if($avatarText !== null || $avatarId !== null)
          <div id="{{ $avatarId ?? $id . 'Avatar' }}" class="w-10 h-10 rounded-xl {{ $iconBg }} font-bold text-sm flex items-center justify-center shrink-0">
            {{ $avatarText ?? 'DR' }}
          </div>
        @else
          <div id="{{ $id }}IconWrap" class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center font-bold shrink-0">
            <span id="{{ $id }}Icon" class="material-symbols-outlined text-xl">{{ $icon }}</span>
          </div>
        @endif
        <div class="min-w-0">
          <h3 id="{{ $titleId ?? $id . 'Title' }}" class="font-black text-slate-900 text-sm leading-snug tracking-tight truncate">{{ $title }}</h3>
          <p id="{{ $subtitleId ?? $id . 'Subtitle' }}" class="text-xs text-slate-500 truncate" @if(empty($subtitle)) style="display:none;" @endif>{{ $subtitle }}</p>
        </div>
      </div>
      <button type="button" onclick="{{ $closeAction ?: "document.getElementById('{$id}').classList.add('hidden')" }}" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer shrink-0">
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>

    @if($formId)
      <form id="{{ $formId }}" @if($formOnsubmit) onsubmit="{{ $formOnsubmit }}" @endif autocomplete="off" class="flex flex-col flex-1 min-h-0 overflow-hidden">
        <!-- Modal Body (Single Scrollable Container) -->
        <div id="{{ $id }}Body" class="p-6 space-y-4 text-xs flex-1 overflow-y-auto bg-white min-h-0">
          {{ $slot }}
        </div>

        <!-- Modal Footer (Sticky) -->
        @if(isset($footer))
          <div id="{{ $id }}Footer" class="p-4 px-6 flex items-center justify-between border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
            {{ $footer }}
          </div>
        @endif
      </form>
    @else
      <!-- Modal Body (Single Scrollable Container) -->
      <div id="{{ $id }}Body" class="p-6 space-y-4 text-xs flex-1 overflow-y-auto bg-white min-h-0">
        {{ $slot }}
      </div>

      <!-- Modal Footer (Sticky) -->
      @if(isset($footer))
        <div id="{{ $id }}Footer" class="p-4 px-6 flex items-center justify-between border-t border-slate-100 bg-slate-50/80 shrink-0 sticky bottom-0 z-20">
          {{ $footer }}
        </div>
      @endif
    @endif
  </div>
</div>
