@php
    $progress = $getState(); // Get the student's progress
$borderColor = '#D1D5DB'; // Default border color (gray-300 in Tailwind)
    // Calculate the stroke-dashoffset based on progress
    $strokeDashoffset = 100 - $progress;
@endphp

<!-- Circular Progress -->
<div class="relative w-10 h-10">
    <svg class="w-full h-full" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
        <!-- Background Circle -->
        <circle cx="18" cy="18" r="16" fill="none" stroke="#D1D5DB" stroke-width="2"></circle>
        <!-- Progress Circle inside a group with rotation -->
        <g class="origin-center -rotate-90 transform">
            <circle cx="18" cy="18" r="16" fill="none" stroke="#10B981" stroke-width="2"
                stroke-dasharray="100" stroke-dashoffset="{{ $strokeDashoffset }}"></circle>
        </g>
    </svg>
    <!-- Percentage Text -->
    <div class="absolute inset-0 flex items-center justify-center">
        <span class="text-center text-xs font-semibold text-gray-800">{{ $progress }}%</span>
    </div>
</div>
<!-- End Circular Progress -->
