@props(['messages', 'id' => null])

@if ($messages)
    <ul
        {{ $attributes->merge(['class' => 'mt-2 flex items-start gap-1.5 text-sm text-red-600 dark:text-red-400']) }}
        @if ($id) id="{{ $id }}" @endif
        role="alert"
        aria-live="polite"
    >
        @foreach ((array) $messages as $message)
            <li class="flex items-start gap-1.5">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
