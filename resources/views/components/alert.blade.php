@props(['type' => 'info'])
@php
$icons = ['success'=>'check-circle','danger'=>'x-circle','warning'=>'exclamation-triangle','info'=>'information-circle'];
$styles = [
  'success' => 'bg-green-50 border-green-200 text-green-800',
  'danger'  => 'bg-red-50 border-red-200 text-red-700',
  'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
  'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
];
@endphp
<div class="flex items-start gap-2.5 p-3.5 rounded-lg border text-sm mb-5 {{ $styles[$type] }}">
  <x-dynamic-component :component="'heroicon-o-'.$icons[$type]" class="w-4 h-4 flex-shrink-0 mt-px" />
  <div>{{ $slot }}</div>
</div>
