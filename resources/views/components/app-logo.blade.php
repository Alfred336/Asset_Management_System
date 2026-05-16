@props([
    'sidebar' => false,
])

@if($sidebar)
    <a href="{{ route('dashboard') }}" wire:navigate {{ $attributes->merge(['class' => 'flex items-center px-3 py-3']) }}>
        <img src="{{ asset('wemanage-logo.png') }}" alt="WeManage" class="h-8 w-auto" />
    </a>
@else
    <a href="{{ route('dashboard') }}" {{ $attributes->merge(['class' => 'flex items-center']) }}>
        <img src="{{ asset('wemanage-logo.png') }}" alt="WeManage" class="h-7 w-auto" />
    </a>
@endif
