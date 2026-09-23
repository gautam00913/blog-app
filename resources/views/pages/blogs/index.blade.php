<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    #[Url]
    public ?string $category = null;

    #[Url]
    public ?string $author = null;

    #[Url]
    public ?string $search = null;

    public function getPosts()
    {
        return Post::published()
            ->with(['author', 'category', 'tags'])
            ->when($this->category, fn ($q) => $q->whereHas('category', fn ($cq) => $cq->where('slug', $this->category)))
            ->when($this->author, fn ($q) => $q->where('user_id', $this->author))
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('excerpt', 'like', "%{$this->search}%"))
            ->latest('published_at')
            ->paginate(9);
    }

    public function getAuthorName(): ?string
    {
        if (!$this->author) {
            return null;
        }

        return User::find($this->author)?->name;
    }

    public function getCategories()
    {
        return Category::withCount('posts')->orderBy('name')->get();
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="relative bg-zinc-950 py-20 sm:py-28 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <span class="animate-fade-in text-sm font-semibold tracking-wider uppercase text-primary">Blog</span>
            <h1 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-white animate-slide-up stagger-1">
                Tous nos <span class="text-primary">articles</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-zinc-400 max-w-2xl mx-auto animate-slide-up stagger-2">
                Explorez notre collection d'articles, guides et tutoriels.
            </p>
        </div>
    </section>

    {{-- Active Author Filter --}}
    @if ($author && $this->getAuthorName())
        <div class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Articles de</span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary">
                        {{ $this->getAuthorName() }}
                    </span>
                    <a href="{{ route('blogs.index', array_filter(['category' => $category, 'search' => $search])) }}" wire:navigate
                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <section class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950 sticky top-16 z-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4 py-4 overflow-x-auto">
                <a href="{{ route('blogs.index', array_filter(['author' => $author, 'search' => $search])) }}" wire:navigate
                    class="shrink-0 rounded-full px-4 py-2 text-sm font-medium transition-all {{ !$category ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    Tous
                </a>
                @foreach ($this->getCategories() as $cat)
                    <a href="{{ route('blogs.index', array_filter(['category' => $cat->slug, 'author' => $author, 'search' => $search])) }}" wire:navigate
                        class="shrink-0 rounded-full px-4 py-2 text-sm font-medium transition-all {{ $category === $cat->slug ? 'text-white shadow-lg' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700' }}"
                        @if ($category === $cat->slug) style="background-color: {{ $cat->color }}; box-shadow: 0 10px 15px -3px {{ $cat->color }}40" @endif>
                        {{ $cat->name }}
                        <span class="ml-1 text-xs opacity-70">{{ $cat->posts_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Posts Grid --}}
    <section class="py-16 sm:py-20 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @php $posts = $this->getPosts(); @endphp
            @if ($posts->count())
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <a href="{{ route('blogs.show', $post->slug) }}" wire:navigate
                            class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm hover-lift dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="aspect-[16/10] overflow-hidden">
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    @if ($post->category)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-zinc-500">{{ $post->published_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                                    {{ $post->title }}
                                </h3>
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-3 flex-1">
                                    {{ $post->excerpt }}
                                </p>
                                <div class="mt-4 flex items-center justify-between border-t border-zinc-100 pt-4 dark:border-zinc-800">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white" style="background-color: {{ $post->category->color ?? '#737373' }}">
                                            {{ strtoupper(mb_substr($post->author->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $post->author->name }}</span>
                                    </div>
                                    <span class="text-xs text-zinc-500">{{ ceil(str_word_count($post->body) / 200) }} min</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->withQueryString()->links() }}
                </div>
            @else
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-16 text-center dark:border-zinc-800 dark:bg-zinc-900">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">Aucun article trouvé</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Essayez de modifier vos filtres ou revenez plus tard.</p>
                </div>
            @endif
        </div>
    </section>
</div>
