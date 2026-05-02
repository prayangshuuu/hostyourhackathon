@props([
  'variant' => 'secondary',
  'size' => 'md',
  'type' => 'button',
  'href' => null,
  'icon' => null,
  'iconRight' => null,
  'disabled' => false,
  'fullWidth' => false,
])
@php
$heights = ['sm' => 'h-[30px]', 'md' => 'h-[36px]', 'lg' => 'h-[42px]'];
$paddings = ['sm' => 'px-3', 'md' => 'px-3.5', 'lg' => 'px-4'];
$textSizes = ['sm' => 'text-2xs', 'md' => 'text-sm', 'lg' => 'text-base'];
$fontWeights = ['sm' => 'font-medium', 'md' => 'font-medium', 'lg' => 'font-semibold'];
$variants = [
  'primary'   => 'text-white bg-accent-500 border border-accent-500 hover:bg-accent-600 hover:border-accent-600',
  'secondary' => 'text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300',
  'ghost'     => 'text-slate-500 bg-transparent border border-transparent hover:bg-slate-100 hover:text-slate-700',
  'danger'    => 'text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 hover:border-red-300',
];
$iconSizes = ['sm' => 'w-3.5 h-3.5', 'md' => 'w-4 h-4', 'lg' => 'w-[18px] h-[18px]'];
$base = 'inline-flex items-center justify-center gap-1.5 rounded-md transition-colors duration-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-500/25 disabled:opacity-40 disabled:cursor-not-allowed disabled:pointer-events-none whitespace-nowrap select-none';
$classes = implode(' ', [
  $base,
  $heights[$size],
  $paddings[$size],
  $textSizes[$size],
  $fontWeights[$size],
  $variants[$variant],
  $fullWidth ? 'w-full' : '',
]);
@endphp
@if($href)
  <a href="{{ $href }}" class="{{ $classes }}" {{ $attributes }}>
    @if($icon)<x-dynamic-component :component="'heroicon-o-'.$icon" class="{{ $iconSizes[$size] }}" />@endif
    {{ $slot }}
    @if($iconRight)<x-dynamic-component :component="'heroicon-o-'.$iconRight" class="{{ $iconSizes[$size] }}" />@endif
  </a>
@else
  <button type="{{ $type }}" class="{{ $classes }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes }}>
    @if($icon)<x-dynamic-component :component="'heroicon-o-'.$icon" class="{{ $iconSizes[$size] }}" />@endif
    {{ $slot }}
    @if($iconRight)<x-dynamic-component :component="'heroicon-o-'.$iconRight" class="{{ $iconSizes[$size] }}" />@endif
  </button>
@endif
