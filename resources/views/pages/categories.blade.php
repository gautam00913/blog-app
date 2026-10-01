<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.public')] class extends Component {

    use WithPagination;

    public function with(): array
    {
        return [
            'categories' => Category::withCount([
                                'posts' => fn($query) => $query->where('status', 'published')->whereNotNull('published_at')
                            ])
                            ->orderBy('name')
                            ->paginate(9)
        ];
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="relative bg-zinc-950 py-20 sm:py-28 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <span class="animate-fade-in text-sm font-semibold tracking-wider uppercase text-primary">Explorez</span>
            <h1 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-white animate-slide-up stagger-1">
                Nos <span class="text-primary">catégories</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-zinc-400 max-w-2xl mx-auto animate-slide-up stagger-2">
                Parcourez nos articles par thème et trouvez le contenu qui vous intéresse.
            </p>
        </div>
    </section>

    {{-- Categories Grid --}}
    <section class="py-20 sm:py-28 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($categories as $category)
                    <a href="{{ route('blogs.index') }}?category={{ $category->slug }}" wire:navigate
                        class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm hover-lift dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="absolute top-0 right-0 h-32 w-32 -translate-y-8 translate-x-8 rounded-full transition-colors" style="background-color: {{ $category->color }}10"></div>
                        <div class="relative">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl text-white" style="background-color: {{ $category->color }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="h-7 w-7">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                </svg>
                            </div>
                            <h3 class="mt-6 text-xl font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">
                                {{ $category->name }}
                            </h3>
                            @if ($category->description)
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                    {{ $category->description }}
                                </p>
                            @endif
                            <div class="mt-4 flex items-center gap-2">
                                <span class="text-sm font-semibold" style="color: {{ $category->color }}">
                                    {{ $category->posts_count }}
                                </span>
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                    article{{ $category->posts_count > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-zinc-200 bg-white p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucune catégorie disponible pour le moment.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-12">
                {{ $categories->links() }}
            </div>
        </div>
    </section>
</div>
