<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public $featuredPost = null;
    public $latestPosts = [];
    public $categories = [];
    public int $postsCount = 0;
    public int $categoriesCount = 0;
    public int $authorsCount = 0;
    public int $subscribersCount = 0;
    public $authors = [];
    public $authorColors = ['primary', 'secondary', 'accent-violet', 'accent-emerald', 'primary-light', 'secondary-light'];

    public function mount(): void
    {
        $this->authorsCount = User::has('posts')->count();
        $this->subscribersCount = Subscriber::verified()->count();

        $this->featuredPost = Post::published()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->first();

        $this->postsCount = Post::published()->count();

        $this->latestPosts = Post::published()
            ->with(['author', 'category'])
            ->where('id', '!=', $this->featuredPost?->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $this->categories = Category::withCount([
            'posts' => fn($query) => $query->where('status', 'published')->whereNotNull('published_at')
        ])->orderBy('name')->where('posts_count', '>', 0)->inRandomOrder()->take(6)->get();
        $this->categoriesCount = $this->categories->count();

        $this->authors = User::has('posts')
            ->inRandomOrder()
            ->take(4)
            ->get();
    }
     
    protected function formatNumber(int $number): string
    {
        if ($number >= 1_000_000) {
            return sprintf('%.1dM', $number / 1_000_000);
        }

        if ($number >= 1_000) {
            return sprintf('%.1dk', $number / 1_000);
        }

        return sprintf('%d', $number);
    }
}; ?>

<div>
    {{-- Hero Section --}}
    <section class="relative min-h-[90vh] flex items-center overflow-hidden bg-zinc-950">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1920&q=80" alt=""
                class="h-full w-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-r from-zinc-950 via-zinc-950/80 to-zinc-950/40"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-zinc-950/50"></div>
        </div>

        <div class="absolute top-20 right-10 w-72 h-72 bg-primary/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-32">
            <div class="max-w-3xl">
                <div class="animate-fade-in">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-sm font-medium text-white border border-white/10">
                        <span class="h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                        Nouveaux articles chaque semaine
                    </span>
                </div>

                <h1 class="mt-8 text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-white leading-[1.1] animate-slide-up stagger-1">
                    L'art du
                    <span class="relative inline-block">
                        <span class="relative z-10 text-primary">code</span>
                        <span class="absolute bottom-2 left-0 right-0 h-3 bg-primary/20 -rotate-1"></span>
                    </span>
                    &
                    <span class="font-display italic text-secondary-dark">design</span>
                </h1>

                <p class="mt-6 text-xl sm:text-2xl text-zinc-300 leading-relaxed max-w-2xl animate-slide-up stagger-2">
                    Découvrez nos derniers articles, guides et tutoriels. Restez à jour avec les dernières tendances
                    et insights de nos experts.
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-4 animate-slide-up stagger-3">
                    <a href="{{ route('blogs.index') }}" wire:navigate
                        class="group inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-4 text-base font-semibold text-white shadow-xl shadow-primary/30 hover:bg-primary-dark hover:shadow-2xl hover:shadow-primary/40 transition-all">
                        Explorer les articles
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-5 w-5 group-hover:translate-x-1 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <a href="{{ route('about') }}" wire:navigate
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 backdrop-blur-sm px-8 py-4 text-base font-semibold text-white hover:bg-white/10 transition-all">
                        En savoir plus
                    </a>
                </div>

                @if ($authorsCount)
                <div class="mt-16 flex items-center gap-4 animate-slide-up stagger-4">
                    <div class="flex -space-x-3">
                        @foreach ($authors as $key => $author)
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-zinc-950 bg-{{ $authorColors[$key] ?? 'primary' }} text-xs font-bold text-white">
                                {{ strtoupper(mb_substr($author->name, 0, 2)) }}
                            </div>
                        @endforeach
                    </div>
                    <div class="text-sm text-zinc-400">
                        <span class="font-semibold text-white">{{ $authorsCount }} auteur{{ $authorsCount > 1 ? 's' : '' }} passionné{{ $authorsCount > 1 ? 's' : '' }}</span> partagent leur expertise
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-zinc-400">
            <span class="text-xs font-medium tracking-wider uppercase">Scroll</span>
            <div class="h-6 w-5 rounded-full border-2 border-zinc-400 flex justify-center pt-1.5">
                <div class="h-1.5 w-1 rounded-full bg-zinc-400 animate-bounce"></div>
            </div>
        </div>
    </section>

    {{-- Featured Articles --}}
    <section id="articles" class="py-20 sm:py-28 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal flex items-end justify-between">
                <div>
                    <span class="text-sm font-semibold tracking-wider uppercase text-primary">Sélection de la rédaction</span>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        Articles à la une</h2>
                </div>
                <a href="{{ route('blogs.index') }}" wire:navigate
                    class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-dark transition-colors group">
                    Voir tous les articles
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-4 w-4 group-hover:translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            @if ($featuredPost)
            <div class="reveal mt-12 grid gap-8 lg:grid-cols-2">
                {{-- Main Featured Article --}}
                <a href="{{ route('blogs.show', $featuredPost->slug) }}" wire:navigate class="group relative overflow-hidden rounded-3xl bg-zinc-900 hover-lift block">
                    <div class="aspect-[16/10] overflow-hidden">
                        <img src="{{ $featuredPost->featured_image }}" alt="{{ $featuredPost->title }}"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-zinc-900/50 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8">
                        <div class="flex items-center gap-3 mb-4">
                            @if ($featuredPost->category)
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white" style="background-color: {{ $featuredPost->category->color }}">
                                    {{ $featuredPost->category->name }}
                                </span>
                            @endif
                            <span class="text-sm text-zinc-400">{{ $featuredPost->published_at->translatedFormat('d M Y') }}</span>
                            <span class="text-sm text-zinc-400">·</span>
                            <span class="text-sm text-zinc-400">{{ ceil(str_word_count($featuredPost->body) / 200) }} min de lecture</span>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-bold text-white group-hover:text-primary transition-colors">
                            {{ $featuredPost->title }}
                        </h3>
                        <p class="mt-3 text-zinc-300 line-clamp-2 text-lg">
                            {{ $featuredPost->excerpt }}
                        </p>
                        <div class="mt-6 flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/20 text-sm font-bold text-primary ring-2 ring-primary/30">
                                {{ strtoupper(mb_substr($featuredPost->author->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-white">{{ $featuredPost->author->name }}</p>
                            </div>
                        </div>
                    </div>
                </a>

                {{-- Secondary Articles --}}
                <div class="flex flex-col gap-8">
                    @forelse ($latestPosts as $post)
                        <a href="{{ route('blogs.show', $post->slug) }}" wire:navigate
                            class="group relative flex flex-col sm:flex-row overflow-hidden rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover-lift">
                            <div class="sm:w-48 lg:w-56 shrink-0 overflow-hidden">
                                <div class="aspect-[4/3] sm:aspect-auto sm:h-full">
                                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </div>
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
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                    {{ $post->excerpt }}
                                </p>
                                <div class="mt-auto pt-4 flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white" style="background-color: {{ $post->category->color ?? '#737373' }}">
                                        {{ strtoupper(mb_substr($post->author->name, 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $post->author->name }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucun article pour le moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            <div class="mt-8 text-center sm:hidden">
                <a href="{{ route('blogs.index') }}" wire:navigate
                    class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-dark transition-colors">
                    Voir tous les articles
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    @if ($categories->count())
    <section class="py-20 sm:py-28 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal text-center max-w-2xl mx-auto">
                <span class="text-sm font-semibold tracking-wider uppercase text-primary">Explorez par thème</span>
                <h2 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">Catégories populaires</h2>
                <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">Trouvez l'inspiration parmi nos domaines d'expertise.</p>
            </div>

            <div class="reveal mt-14 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($categories as $category)
                    <a href="{{ route('blogs.index') }}?category={{ $category->slug }}" wire:navigate
                        class="group relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 p-6 shadow-sm border border-zinc-200 dark:border-zinc-700 hover-lift text-center">
                        <div class="absolute inset-0 bg-gradient-to-br to-transparent opacity-0 group-hover:opacity-100 transition-opacity" style="background: linear-gradient(to bottom right, {{ $category->color }}10, transparent)"></div>
                        <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl group-hover:text-white transition-all mx-auto text-white" style="background-color: {{ $category->color }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                        </div>
                        <span class="relative mt-4 block text-sm font-semibold text-zinc-900 dark:text-white">{{ $category->name }}</span>
                        <span class="relative mt-1 block text-xs text-zinc-500">{{ $category->posts_count }} article{{ $category->posts_count > 1 ? 's' : '' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Stats Section --}}
    <section class="py-20 sm:py-28 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal grid grid-cols-2 gap-8 lg:grid-cols-4">
                <div class="text-center">
                    <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">{{ $this->formatNumber($postsCount) }}</p>
                    <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Articles publiés</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">{{ $this->formatNumber($authorsCount) }}</p>
                    <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Auteur{{ $authorsCount > 1 ? 's' : '' }} expert{{ $authorsCount > 1 ? 's' : '' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">{{ $this->formatNumber($categoriesCount) }}</p>
                    <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Catégorie{{ $categoriesCount > 1 ? 's' : '' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">{{ $this->formatNumber($subscribersCount) }}</p>
                    <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Abonnés newsletter</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="py-20 sm:py-28 bg-zinc-950 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal mx-auto max-w-2xl text-center">
                <span class="text-sm font-semibold tracking-wider uppercase text-primary">Newsletter</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-white">Restez informé</h2>
                <p class="mt-4 text-lg text-zinc-400">
                    Inscrivez-vous à notre newsletter pour recevoir les derniers articles et actualités directement dans
                    votre boîte mail.
                </p>

                <livewire:newsletter.subscribe />

                @if($subscribersCount)
                    <p class="mt-4 text-sm text-zinc-500">
                        Rejoignez <span class="font-semibold text-white">{{ $subscribersCount }}</span> abonné{{ $subscribersCount > 1 ? 's' : '' }}. Désabonnement en un clic.
                    </p>
                @endif
            </div>
        </div>
    </section>
</div>
