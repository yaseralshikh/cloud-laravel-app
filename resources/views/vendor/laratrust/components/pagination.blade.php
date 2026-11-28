{{-- Previous/next links for a simple paginator. --}}
@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="flex items-center justify-between border-t border-gray-200 px-4 py-3 dark:border-white/10" aria-label="{{ __('messages.pagination') }}">
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.page-number', ['page' => $paginator->currentPage()]) }}</p>
        <div class="flex gap-2">
            @if ($paginator->onFirstPage())
                <x-laratrust::button variant="secondary" disabled>{{ __('messages.previous') }}</x-laratrust::button>
            @else
                <x-laratrust::button variant="secondary" :href="$paginator->previousPageUrl()" rel="prev">{{ __('messages.previous') }}</x-laratrust::button>
            @endif

            @if ($paginator->hasMorePages())
                <x-laratrust::button variant="secondary" :href="$paginator->nextPageUrl()" rel="next">{{ __('messages.next') }}</x-laratrust::button>
            @else
                <x-laratrust::button variant="secondary" disabled>{{ __('messages.next') }}</x-laratrust::button>
            @endif
        </div>
    </nav>
@endif
