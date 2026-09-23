<?php

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public $post = null;
    public $relatedPosts = [];
    public $approvedComments = [];
    public int $commentCount = 0;

    public function mount(string $slug): void
    {
        $this->post = Post::published()
            ->with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->post->incrementViews();

        $this->relatedPosts = Post::published()
            ->with(['author', 'category'])
            ->where('category_id', $this->post->category_id)
            ->where('id', '!=', $this->post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $this->approvedComments = $this->post->comments()
            ->approved()
            ->latest()
            ->take(10)
            ->get();

        $this->commentCount = $this->post->comments()->approved()->count();
    }
}; ?>

<div>
    @if ($post)
        {{-- Hero --}}
        <section class="relative bg-zinc-950 py-20 sm:py-28 overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ $post->featured_image }}" alt="" class="h-full w-full object-cover opacity-20">
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/80 to-zinc-950/60"></div>
            </div>
            <div class="absolute top-20 right-10 w-72 h-72 bg-primary/10 rounded-full blur-3xl"></div>

            <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
                <div class="animate-fade-in">
                    @if ($post->category)
                        <a href="{{ route('blogs.index', ['category' => $post->category->slug]) }}" wire:navigate
                            class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-semibold text-white transition-colors hover:opacity-80"
                            style="background-color: {{ $post->category->color }}">
                            {{ $post->category->name }}
                        </a>
                    @endif
                </div>

                <h1 class="mt-6 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white leading-tight animate-slide-up stagger-1">
                    {{ $post->title }}
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-zinc-300 max-w-2xl mx-auto animate-slide-up stagger-2">
                    {{ $post->excerpt }}
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-6 animate-slide-up stagger-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold text-white" style="background-color: {{ $post->category->color ?? '#737373' }}">
                            {{ strtoupper(mb_substr($post->author->name, 0, 2)) }}
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-white">{{ $post->author->name }}</p>
                            <p class="text-xs text-zinc-400">Auteur</p>
                        </div>
                    </div>
                    <span class="text-zinc-600">·</span>
                    <span class="text-sm text-zinc-400">{{ $post->published_at->translatedFormat('d M Y') }}</span>
                    <span class="text-zinc-600">·</span>
                    <span class="text-sm text-zinc-400">{{ ceil(str_word_count($post->body) / 200) }} min de lecture</span>
                    <span class="text-zinc-600">·</span>
                    <span class="text-sm text-zinc-400">{{ number_format($post->views_count) }} vue{{ $post->views_count > 1 ? 's' : '' }}</span>
                </div>

                @if ($post->tags->count())
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2 animate-slide-up stagger-4">
                        @foreach ($post->tags as $tag)
                            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-zinc-300 border border-white/10">
                                #{{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Article Content --}}
        <section class="py-16 sm:py-20 bg-white dark:bg-zinc-950">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <article class="prose prose-lg prose-zinc dark:prose-invert max-w-none prose-headings:scroll-mt-20 prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-img:rounded-2xl">
                    {!! $post->body !!}
                </article>
            </div>
        </section>

        {{-- Related Posts --}}
        @if ($relatedPosts->count())
            <section class="py-16 sm:py-20 bg-zinc-50 dark:bg-zinc-900/50">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Articles similaires</h2>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($relatedPosts as $related)
                            <a href="{{ route('blogs.show', $related->slug) }}" wire:navigate
                                class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm hover-lift dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="aspect-[16/10] overflow-hidden">
                                    <img src="{{ $related->featured_image }}" alt="{{ $related->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </div>
                                <div class="flex flex-1 flex-col p-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        @if ($related->category)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background-color: {{ $related->category->color }}20; color: {{ $related->category->color }}">
                                                {{ $related->category->name }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-zinc-500">{{ $related->published_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                                        {{ $related->title }}
                                    </h3>
                                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2 flex-1">
                                        {{ $related->excerpt }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Comments --}}
        <section class="py-16 sm:py-20 bg-white dark:bg-zinc-950">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                    Commentaires
                    <span class="text-lg font-normal text-zinc-500 dark:text-zinc-400">({{ $commentCount }})</span>
                </h2>

                @if ($approvedComments->count())
                    <div class="mt-8 space-y-6">
                        @foreach ($approvedComments as $comment)
                            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                                        {{ strtoupper(mb_substr($comment->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-zinc-900 dark:text-white">{{ $comment->name }}</p>
                                            <span class="text-xs text-zinc-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $comment->body }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-8 rounded-2xl border border-zinc-200 bg-zinc-50 p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
