@props([
    'size' => 'text-3xl'
])

<i {{ $attributes->merge(['class' => "ph-fill ph-game-controller {$size} text-purple-500"]) }}></i>

