@props(['onlyIcon' => false])

@if($onlyIcon)
    <img src="{{ asset('images/logo-icone.png') }}" alt="Dose em Dia" {{ $attributes->merge(['class' => 'h-9 w-auto object-contain']) }}>
@else
    <img src="{{ asset('images/logo.png') }}" alt="Dose em Dia" {{ $attributes->merge(['class' => 'w-32 h-32 object-contain']) }}>
@endif