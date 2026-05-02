@props(['variant' => 'neutral', 'dot' => false])
@php
$variants = [
  'neutral' => 'bg-slate-100 text-slate-600 border-slate-200',
  'indigo'  => 'bg-accent-50 text-accent-600 border-accent-100',
  'success' => 'bg-green-50 text-green-700 border-green-200',
  'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
  'danger'  => 'bg-red-50 text-red-600 border-red-200',
  'violet'  => 'bg-purple-50 text-purple-700 border-purple-200',
  'teal'    => 'bg-teal-50 text-teal-700 border-teal-200',
  'amber'   => 'bg-amber-50 text-amber-700 border-amber-200',
];
@endphp
<span class="inline-flex items-center gap-1 h-5 px-2 text-2xs font-semibold rounded-full border whitespace-nowrap {{ $variants[$variant] }}">
  @if($dot)<span class="w-1.5 h-1.5 rounded-full bg-current flex-shrink-0"></span>@endif
  {{ $slot }}
</span>
