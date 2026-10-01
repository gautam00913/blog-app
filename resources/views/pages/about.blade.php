<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public $authors = [];
    public int $postsCount = 0;
    public int $categoriesCount = 0;

    public function mount(): void
    {
        $this->categoriesCount = Category::count();
        $this->authors = User::has('posts')->withCount([
                                'posts' => fn($query) => $query->where('status', 'published')->whereNotNull('published_at')
                            ])->get()->each(fn($author) => $this->postsCount += $author->posts_count);
    }
}; ?>

<div>
    {{-- Hero --}}
    <section class="relative bg-zinc-950 py-20 sm:py-32 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1920&q=80" alt=""
                class="h-full w-full object-cover opacity-20">
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/80 to-zinc-950/60"></div>
        </div>
        <div class="absolute top-20 left-10 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-72 h-72 bg-secondary/10 rounded-full blur-3xl"></div>

        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <span class="animate-fade-in text-sm font-semibold tracking-wider uppercase text-primary">À propos</span>
            <h1 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-white leading-tight animate-slide-up stagger-1">
                Notre <span class="text-primary">mission</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-zinc-300 max-w-2xl mx-auto leading-relaxed animate-slide-up stagger-2">
                Découvrez l'équipe passionnée derrière {{ config('app.name', 'Blog') }} et notre engagement envers la qualité.
            </p>
        </div>
    </section>

    {{-- Story --}}
    <section class="py-20 sm:py-28 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                <div class="reveal">
                    <span class="text-sm font-semibold tracking-wider uppercase text-primary">Notre histoire</span>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        Créer du contenu qui inspire
                    </h2>
                    <div class="mt-6 space-y-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        <p>
                            {{ config('app.name', 'Blog') }} est né d'une passion pour la technologie et le design. Notre équipe de rédacteurs experts partage quotidiennement des articles, guides et tutoriels pour aider les développeurs et créatifs à rester à la pointe de l'innovation.
                        </p>
                        <p>
                            Nous croyons que le partage de connaissances est la clé du progrès. Chaque article est soigneusement rédigé et vérifié pour garantir une information de qualité, accessible à tous les niveaux d'expérience.
                        </p>
                        <p>
                            Que vous soyez débutant ou expert, vous trouverez ici du contenu adapté à votre parcours et à vos ambitions professionnelles.
                        </p>
                    </div>
                </div>
                <div class="reveal relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-primary/10 to-secondary/10 rounded-3xl blur-xl"></div>
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&q=80" alt="Équipe au travail"
                        class="relative rounded-2xl shadow-2xl w-full">
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="py-16 sm:py-20 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal grid grid-cols-2 gap-8 lg:grid-cols-4">
                <div class="text-center">
                    <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">{{ $postsCount }}@if($postsCount)+@endif</p>
                    <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Articles publiés</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">{{ $authors->count() }}</p>
                    <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Rédacteurs experts</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">{{ $categoriesCount }}</p>
                    <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Catégories</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">100%</p>
                    <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Contenu gratuit</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Team --}}
    @if ($authors->count())
    <section class="py-20 sm:py-28 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal text-center max-w-2xl mx-auto">
                <span class="text-sm font-semibold tracking-wider uppercase text-primary">L'équipe</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">Nos rédacteurs</h2>
                <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">Des experts passionnés qui partagent leur savoir.</p>
            </div>

            <div class="reveal mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($authors as $author)
                    <a href="{{ route('blogs.index', ['author' => $author->id]) }}" wire:navigate class="group text-center hover-lift block">
                        <div class="relative mx-auto h-32 w-32">
                            <div class="flex h-full w-full items-center justify-center rounded-full bg-primary/10 text-3xl font-bold text-primary group-hover:bg-primary group-hover:text-white transition-all">
                                {{ strtoupper(mb_substr($author->name, 0, 2)) }}
                            </div>
                        </div>
                        <h3 class="mt-6 text-lg font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">{{ $author->name }}</h3>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $author->posts_count }} article{{ $author->posts_count > 1 ? 's' : '' }}
                        </p>
                        <span class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                            Voir les articles
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Values --}}
    <section class="py-20 sm:py-28 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="reveal text-center max-w-2xl mx-auto">
                <span class="text-sm font-semibold tracking-wider uppercase text-primary">Nos valeurs</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">Ce en quoi nous croyons</h2>
            </div>

            <div class="reveal mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-bold text-zinc-900 dark:text-white">Excellence</h3>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Chaque article est rédigé avec soin, testé et relu pour garantir la plus haute qualité de contenu.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-emerald/10 text-accent-emerald">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-bold text-zinc-900 dark:text-white">Innovation</h3>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Nous explorons les dernières tendances et technologies pour vous offrir le contenu le plus pertinent.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-violet/10 text-accent-violet">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-bold text-zinc-900 dark:text-white">Communauté</h3>
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Nous créons un espace d'échange où chaque lecteur peut apprendre, partager et grandir ensemble.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
