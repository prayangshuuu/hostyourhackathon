@props(['id', 'title', 'size' => 'sm'])
<div id="{{ $id }}" class="fixed inset-0 bg-slate-900/40 z-50 hidden items-center justify-center p-6" x-data="{}" @keydown.escape.window="document.getElementById('{{ $id }}').classList.add('hidden'); document.getElementById('{{ $id }}').classList.remove('flex')">
  <div class="bg-white border border-slate-200 rounded-2xl w-full {{ $size === 'lg' ? 'max-w-lg' : 'max-w-md' }}">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
      <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
      <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden'); document.getElementById('{{ $id }}').classList.remove('flex')" class="w-[30px] h-[30px] inline-flex items-center justify-center rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
        <x-heroicon-o-x-mark class="w-4 h-4" />
      </button>
    </div>
    <div class="px-6 py-5 text-sm text-slate-600 leading-relaxed">{{ $slot }}</div>
    @isset($footer)
      <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-slate-100">{{ $footer }}</div>
    @endisset
  </div>
</div>
