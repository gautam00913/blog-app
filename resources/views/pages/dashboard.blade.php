<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Subscription;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public int $postsCount = 0;
    public int $publishedCount = 0;
    public int $draftCount = 0;
    public int $commentsCount = 0;
    public int $pendingCommentsCount = 0;
    public int $viewsCount = 0;
    public int $subscribersCount = 0;
    public int $categoriesCount = 0;

    public $recentPosts = [];
    public $recentComments = [];

    public function mount(): void
    {
        $this->postsCount = Post::count();
        $this->publishedCount = Post::published()->count();
        $this->draftCount = Post::draft()->count();
        $this->commentsCount = Comment::count();
        $this->pendingCommentsCount = Comment::pending()->count();
        $this->viewsCount = Post::sum('views_count');
        $this->subscribersCount = Subscription::count();
        $this->categoriesCount = Category::count();

        $this->recentPosts = Post::with(['author', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $this->recentComments = Comment::with(['post', 'user'])
            ->latest()
            ->take(5)
            ->get();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-8 p-6 lg:p-8">
    {{-- Welcome Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                @php
                    $hour = now()->hour;
                    $greeting = $hour < 12 ? 'Bonjour' : ($hour < 18 ? 'Bon après-midi' : 'Bonsoir');
                @endphp
                {{ $greeting }}, {{ auth()->user()->name }} 👋
            </h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Voici un aperçu de votre blog aujourd'hui.</p>
        </div>
        <a href="{{ route('dashboard.posts.create') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nouvel article
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Articles --}}
        <a href="{{ route('dashboard.posts') }}" wire:navigate class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 hover-lift">
            <div class="absolute top-0 right-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-primary/5 group-hover:bg-primary/10 transition-colors"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $publishedCount }} publiés</span>
                </div>
                <p class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">{{ $postsCount }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Articles</p>
            </div>
        </a>

        {{-- Commentaires --}}
        <a href="{{ route('dashboard.comments') }}" wire:navigate class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 hover-lift">
            <div class="absolute top-0 right-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-secondary/5 group-hover:bg-secondary/10 transition-colors"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-secondary/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-secondary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                        </svg>
                    </div>
                    @if ($pendingCommentsCount > 0)
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">{{ $pendingCommentsCount }} en attente</span>
                    @endif
                </div>
                <p class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">{{ $commentsCount }}</p>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Commentaires</p>
            </div>
        </a>

        {{-- Vues --}}
        <div class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 hover-lift">
            <div class="absolute top-0 right-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-accent-violet/5 group-hover:bg-accent-violet/10 transition-colors"></div>
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-violet/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-accent-violet">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($viewsCount) }}</p>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Vues totales</p>
        </div>

        {{-- Abonnés --}}
        <div class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 hover-lift">
            <div class="absolute top-0 right-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-accent-emerald/5 group-hover:bg-accent-emerald/10 transition-colors"></div>
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-emerald/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-accent-emerald">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">{{ $subscribersCount }}</p>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Abonnés</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Recent Posts --}}
        <div class="lg:col-span-2 rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Derniers articles</h2>
                <a href="{{ route('dashboard.posts') }}" wire:navigate class="text-sm font-semibold text-primary hover:text-primary-dark transition-colors">Voir tout</a>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($recentPosts as $post)
                    <a href="{{ route('dashboard.posts.edit', $post) }}" wire:navigate class="group flex items-center gap-4 px-6 py-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $post->featured_image }}" alt="" class="h-full w-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white group-hover:text-primary transition-colors truncate">{{ $post->title }}</p>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">{{ $post->author->name }} · {{ $post->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="hidden sm:block">
                            @if ($post->status === 'published')
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Publié</span>
                            @elseif ($post->status === 'draft')
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Brouillon</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-semibold text-secondary">Programmé</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucun article pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Derniers commentaires</h2>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($recentComments as $comment)
                    <div class="flex items-start gap-4 px-6 py-4">
                        <div class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ strtoupper(mb_substr($comment->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-zinc-900 dark:text-white">
                                <span class="font-semibold">{{ $comment->name }}</span> a commenté
                            </p>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ $comment->body }}</p>
                            <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                        @if (!$comment->approved)
                            <span class="shrink-0 h-2 w-2 rounded-full bg-amber-400"></span>
                        @endif
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucun commentaire.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
