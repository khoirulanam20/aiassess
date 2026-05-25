@props(['assessment'])

<div onclick="window.location='{{ route('assessments.show', $assessment->slug) }}'"
     role="link"
     tabindex="0"
     onkeydown="if(event.key==='Enter')window.location='{{ route('assessments.show', $assessment->slug) }}'"
     class="assessment-card rounded-xl border border-gray-200 bg-white p-5 shadow-card transition-all duration-200 hover:shadow-card-hover dark:border-gray-700 dark:bg-navy-800"
     aria-label="{{ $assessment->name }}: {{ $assessment->description }}">
    <div class="mb-4 flex items-start justify-between">
        <div class="ac-icon" style="background-color: {{ $assessment->color }}15; color: {{ $assessment->color }}">
            {{ substr($assessment->name, 0, 2) }}
        </div>
        @if ($assessment->type === 'premium')
            <span class="badge-amber dark:bg-amber-900/40 dark:text-amber-300">Premium</span>
        @else
            <span class="badge-green dark:bg-green-900/40 dark:text-green-300">Gratis</span>
        @endif
    </div>

    <h4 class="mb-1.5 font-semibold text-ink dark:text-white">{{ $assessment->name }}</h4>
    <p class="mb-4 text-sm leading-relaxed text-gray-500 line-clamp-2 dark:text-gray-400">{{ $assessment->description }}</p>

    <div class="flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-700">
        <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
            <span class="flex items-center gap-1">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ $assessment->question_count }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $assessment->cooldown_days }}h
            </span>
        </div>
        @if ($assessment->has_completed)
            @if ($assessment->can_retake)
                <span class="badge-blue text-[10px] dark:bg-sky-900/40 dark:text-sky-300">Ulang</span>
            @else
                <span class="badge-gray text-[10px] dark:bg-gray-700 dark:text-gray-400">Selesai</span>
            @endif
        @else
            <span class="text-xs font-medium text-sky-500 dark:text-sky-400">Mulai &rarr;</span>
        @endif
    </div>
</div>
