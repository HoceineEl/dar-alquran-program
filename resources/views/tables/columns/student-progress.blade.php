@php
    $progress = $getState(); // Get the student's progress
$borderColor = 'border-gray-300'; // Default border color

// Change border color based on progress
if ($progress >= 80) {
    $borderColor = 'border-green-500';
} elseif ($progress >= 50) {
    $borderColor = 'border-yellow-500';
} elseif ($progress >= 20) {
    $borderColor = 'border-orange-500';
} else {
    $borderColor = 'border-red-500';
}

// Calculate the border width (for example purposes)
$borderWidth = $progress / 10 . 'px';
@endphp

<div class="w-10 h-10 flex justify-center items-center border-4 {{ $borderColor }}"
    style="border-width: {{ $borderWidth }}">
    <span> {{ $progress }}%</span>
</div>
