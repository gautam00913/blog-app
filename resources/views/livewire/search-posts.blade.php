<div>
    {{-- Search Button --}}
    <button
        wire:click="openModal"
        class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700 transition-all"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <span class="max-lg:hidden">Rechercher...</span>
        <kbd class="hidden lg:inline-flex items-center gap-0.5 rounded-md border border-zinc-200 bg-zinc-100 px-1.5 py-0.5 text-[10px] font-medium text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
            <span class="text-xs">Ctrl</span>K
        </kbd>
    </button>

    {{-- Modal --}}
    @if ($showModal)
        <div
            class="fixed inset-0 z-50 flex items-start justify-center pt-[8vh] p-4"
            x-data
            x-on:keydown.escape.window="$wire.closeModal()"
        >
            <div class="fixed inset-0 bg-zinc-950/50 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl dark:bg-zinc-900 overflow-hidden">
                {{-- Search Input --}}
                <div class="flex items-center gap-3 border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0 text-zinc-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="query"
                        placeholder="Rechercher un article..."
                        class="flex-1 bg-transparent text-sm text-zinc-900 placeholder-zinc-500 focus:outline-none dark:text-white dark:placeholder-zinc-400"
                        x-ref="searchInput"
                        autofocus
                    >
                    <button wire:click="closeModal" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Results --}}
                <div class="max-h-[400px] overflow-y-auto">
                    @if (strlen($query) < 2)
                        <div class="px-6 py-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Tapez au moins 2 caractères pour rechercher.</p>
                        </div>
                    @elseif (empty($results))
                        <div class="px-6 py-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                            <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Aucun résultat pour "{{ $query }}".</p>
                        </div>
                    @else
                        <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($results as $result)
                                <li>
                                    <a
                                        href="{{ $result['url'] }}"
                                        wire:navigate
                                        wire:click="closeModal"
                                        class="flex items-start gap-4 px-6 py-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $result['title'] }}</p>
                                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-1">{{ $result['excerpt'] }}</p>
                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold" style="background-color: {{ $result['category_color'] }}20; color: {{ $result['category_color'] }}">
                                                    {{ $result['category'] }}
                                                </span>
                                                <span class="text-[10px] text-zinc-400 dark:text-zinc-500">par {{ $result['author'] }}</span>
                                                <span class="text-[10px] text-zinc-400 dark:text-zinc-500">{{ $result['date'] }}</span>
                                            </div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 h-4 w-4 shrink-0 text-zinc-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="border-t border-zinc-200 px-6 py-3 dark:border-zinc-800">
                    <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                        <span>{{ count($results) }} résultat{{ count($results) > 1 ? 's' : '' }}</span>
                        <div class="flex items-center gap-2">
                            <kbd class="rounded border border-zinc-200 bg-zinc-100 px-1.5 py-0.5 text-[10px] font-medium dark:border-zinc-700 dark:bg-zinc-800">Esc</kbd>
                            <span>pour fermer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
