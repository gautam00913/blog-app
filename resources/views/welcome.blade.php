<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Blog') }}</title>
        @include('partials.head')
    </head>
    <body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white antialiased">

        {{-- Header --}}
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/70 backdrop-blur-xl border-b border-zinc-200/50 dark:bg-zinc-950/70 dark:border-zinc-800/50">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-lg shadow-primary/25">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd" d="M4.125 3C3.079 3 2.25 3.829 2.25 4.875v16.5c0 1.046.829 1.875 1.875 1.875h15.75c1.046 0 1.875-.829 1.875-1.875V4.875C21 3.829 20.171 3 19.125 3H4.125ZM5.25 7.5a.75.75 0 0 0-.75.75v9a.75.75 0 0 0 1.5 0v-9a.75.75 0 0 0-.75-.75Zm6.75.75a.75.75 0 0 1 1.5 0v9a.75.75 0 0 1-1.5 0v-9Zm1.5-3.75a.75.75 0 0 0-.75.75v12a.75.75 0 0 0 1.5 0v-12a.75.75 0 0 0-.75-.75Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ config('app.name', 'Blog') }}</span>
                </a>

                <nav class="hidden md:flex items-center gap-1">
                    <a href="#" class="px-4 py-2 text-sm font-medium text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-primary rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">Accueil</a>
                    <a href="#" class="px-4 py-2 text-sm font-medium text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-primary rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">Blog</a>
                    <a href="#" class="px-4 py-2 text-sm font-medium text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-primary rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">Catégories</a>
                    <a href="#" class="px-4 py-2 text-sm font-medium text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-primary rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">À propos</a>
                </nav>

                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all">
                                {{ __('Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">
                                {{ __('Log in') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all">
                                    {{ __('Register') }}
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        {{-- Hero Section --}}
        <section class="relative min-h-[90vh] flex items-center overflow-hidden bg-zinc-950">
            {{-- Background Image --}}
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1920&q=80" alt="" class="h-full w-full object-cover opacity-40">
                <div class="absolute inset-0 bg-gradient-to-r from-zinc-950 via-zinc-950/80 to-zinc-950/40"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-zinc-950/50"></div>
            </div>

            {{-- Decorative Elements --}}
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
                        Découvrez nos derniers articles, guides et tutoriels. Restez à jour avec les dernières tendances技术和 et insights de nos experts.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center gap-4 animate-slide-up stagger-3">
                        <a href="#articles" class="group inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-4 text-base font-semibold text-white shadow-xl shadow-primary/30 hover:bg-primary-dark hover:shadow-2xl hover:shadow-primary/40 transition-all">
                            Explorer les articles
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 group-hover:translate-x-1 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                        <a href="#" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 backdrop-blur-sm px-8 py-4 text-base font-semibold text-white hover:bg-white/10 transition-all">
                            En savoir plus
                        </a>
                    </div>

                    {{-- Author Avatars --}}
                    <div class="mt-16 flex items-center gap-4 animate-slide-up stagger-4">
                        <div class="flex -space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-zinc-950 bg-primary text-xs font-bold text-white">JD</div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-zinc-950 bg-secondary text-xs font-bold text-white">ML</div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-zinc-950 bg-accent-violet text-xs font-bold text-white">PM</div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-zinc-950 bg-accent-emerald text-xs font-bold text-white">SC</div>
                        </div>
                        <div class="text-sm text-zinc-400">
                            <span class="font-semibold text-white">4 auteurs passionnés</span> partagent leur expertise
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scroll Indicator --}}
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
                        <h2 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">Articles à la une</h2>
                    </div>
                    <a href="#" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-dark transition-colors group">
                        Voir tous les articles
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 group-hover:translate-x-1 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

                <div class="reveal mt-12 grid gap-8 lg:grid-cols-2">
                    {{-- Main Featured Article --}}
                    <article class="group relative overflow-hidden rounded-3xl bg-zinc-900 hover-lift">
                        <div class="aspect-[16/10] overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&q=80" alt="" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-zinc-900/50 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="inline-flex items-center rounded-full bg-primary px-3 py-1 text-xs font-semibold text-white">Technologie</span>
                                <span class="text-sm text-zinc-400">12 Jan 2026</span>
                                <span class="text-sm text-zinc-400">·</span>
                                <span class="text-sm text-zinc-400">8 min de lecture</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-white group-hover:text-primary transition-colors">
                                Les tendances du web en 2026 : ce qui va tout changer
                            </h3>
                            <p class="mt-3 text-zinc-300 line-clamp-2 text-lg">
                                Découvrez les nouvelles technologies et frameworks qui vont dominer le développement web cette année.
                            </p>
                            <div class="mt-6 flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/20 text-sm font-bold text-primary ring-2 ring-primary/30">JD</div>
                                <div>
                                    <p class="font-semibold text-white">Jean Dupont</p>
                                    <p class="text-sm text-zinc-400">Lead Developer</p>
                                </div>
                            </div>
                        </div>
                    </article>

                    {{-- Secondary Articles --}}
                    <div class="flex flex-col gap-8">
                        <article class="group relative flex flex-col sm:flex-row overflow-hidden rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover-lift">
                            <div class="sm:w-48 lg:w-56 shrink-0 overflow-hidden">
                                <div class="aspect-[4/3] sm:aspect-auto sm:h-full">
                                    <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&q=80" alt="" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center rounded-full bg-secondary/10 px-2.5 py-0.5 text-xs font-semibold text-secondary">Tutorial</span>
                                    <span class="text-xs text-zinc-500">10 Jan 2026</span>
                                </div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                                    Guide complet Laravel 12 : tout ce que vous devez savoir
                                </h3>
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                    Apprenez à maîtriser Laravel 12 avec ce guide complet couvrant les nouvelles fonctionnalités et best practices.
                                </p>
                                <div class="mt-auto pt-4 flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary/20 text-xs font-bold text-secondary">ML</div>
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Marie Laurent</span>
                                </div>
                            </div>
                        </article>

                        <article class="group relative flex flex-col sm:flex-row overflow-hidden rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover-lift">
                            <div class="sm:w-48 lg:w-56 shrink-0 overflow-hidden">
                                <div class="aspect-[4/3] sm:aspect-auto sm:h-full">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80" alt="" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center rounded-full bg-accent-violet/10 px-2.5 py-0.5 text-xs font-semibold text-accent-violet">Design</span>
                                    <span class="text-xs text-zinc-500">8 Jan 2026</span>
                                </div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                                    Créer des interfaces modernes avec Tailwind CSS
                                </h3>
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                    Maîtrisez Tailwind CSS pour concevoir des interfaces utilisateur élégantes et responsives.
                                </p>
                                <div class="mt-auto pt-4 flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-accent-violet/20 text-xs font-bold text-accent-violet">PM</div>
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pierre Martin</span>
                                </div>
                            </div>
                        </article>

                        <article class="group relative flex flex-col sm:flex-row overflow-hidden rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover-lift">
                            <div class="sm:w-48 lg:w-56 shrink-0 overflow-hidden">
                                <div class="aspect-[4/3] sm:aspect-auto sm:h-full">
                                    <img src="https://images.unsplash.com/photo-1555949963-aa79dcee981c?w=400&q=80" alt="" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center rounded-full bg-accent-emerald/10 px-2.5 py-0.5 text-xs font-semibold text-accent-emerald">DevOps</span>
                                    <span class="text-xs text-zinc-500">5 Jan 2026</span>
                                </div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                                    Optimiser les performances de votre application
                                </h3>
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                                    Les meilleures techniques pour améliorer la vitesse et l'efficacité de vos projets.
                                </p>
                                <div class="mt-auto pt-4 flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-accent-emerald/20 text-xs font-bold text-accent-emerald">SC</div>
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Sophie Chen</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <div class="mt-8 text-center sm:hidden">
                    <a href="#" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-dark transition-colors">
                        Voir tous les articles
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        {{-- Categories --}}
        <section class="py-20 sm:py-28 bg-zinc-50 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="reveal text-center max-w-2xl mx-auto">
                    <span class="text-sm font-semibold tracking-wider uppercase text-primary">Explorez par thème</span>
                    <h2 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">Catégories populaires</h2>
                    <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">Trouvez l'inspiration parmi nos domaines d'expertise.</p>
                </div>

                <div class="reveal mt-14 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    <a href="#" class="group relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 p-6 shadow-sm border border-zinc-200 dark:border-zinc-700 hover-lift text-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-all mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <span class="relative mt-4 block text-sm font-semibold text-zinc-900 dark:text-white">Technologie</span>
                        <span class="relative mt-1 block text-xs text-zinc-500">24 articles</span>
                    </a>

                    <a href="#" class="group relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 p-6 shadow-sm border border-zinc-200 dark:border-zinc-700 hover-lift text-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-secondary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-secondary/10 text-secondary group-hover:bg-secondary group-hover:text-white transition-all mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                            </svg>
                        </div>
                        <span class="relative mt-4 block text-sm font-semibold text-zinc-900 dark:text-white">Éducation</span>
                        <span class="relative mt-1 block text-xs text-zinc-500">18 articles</span>
                    </a>

                    <a href="#" class="group relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 p-6 shadow-sm border border-zinc-200 dark:border-zinc-700 hover-lift text-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-accent-violet/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-violet/10 text-accent-violet group-hover:bg-accent-violet group-hover:text-white transition-all mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                            </svg>
                        </div>
                        <span class="relative mt-4 block text-sm font-semibold text-zinc-900 dark:text-white">Design</span>
                        <span class="relative mt-1 block text-xs text-zinc-500">15 articles</span>
                    </a>

                    <a href="#" class="group relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 p-6 shadow-sm border border-zinc-200 dark:border-zinc-700 hover-lift text-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-accent-sky/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-sky/10 text-accent-sky group-hover:bg-accent-sky group-hover:text-white transition-all mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                        <span class="relative mt-4 block text-sm font-semibold text-zinc-900 dark:text-white">Business</span>
                        <span class="relative mt-1 block text-xs text-zinc-500">12 articles</span>
                    </a>

                    <a href="#" class="group relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 p-6 shadow-sm border border-zinc-200 dark:border-zinc-700 hover-lift text-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-accent-amber/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-amber/10 text-accent-amber group-hover:bg-accent-amber group-hover:text-white transition-all mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <span class="relative mt-4 block text-sm font-semibold text-zinc-900 dark:text-white">Sécurité</span>
                        <span class="relative mt-1 block text-xs text-zinc-500">9 articles</span>
                    </a>

                    <a href="#" class="group relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-800 p-6 shadow-sm border border-zinc-200 dark:border-zinc-700 hover-lift text-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-accent-emerald/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-emerald/10 text-accent-emerald group-hover:bg-accent-emerald group-hover:text-white transition-all mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                        </div>
                        <span class="relative mt-4 block text-sm font-semibold text-zinc-900 dark:text-white">Lifestyle</span>
                        <span class="relative mt-1 block text-xs text-zinc-500">7 articles</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- Stats Section --}}
        <section class="py-20 sm:py-28 bg-white dark:bg-zinc-950">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="reveal grid grid-cols-2 gap-8 lg:grid-cols-4">
                    <div class="text-center">
                        <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">24+</p>
                        <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Articles publiés</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">4</p>
                        <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Auteurs experts</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">6</p>
                        <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Catégories</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl sm:text-5xl font-bold text-zinc-900 dark:text-white">12K+</p>
                        <p class="mt-2 text-sm font-medium text-zinc-500 dark:text-zinc-400">Lecteurs mensuels</p>
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
                        Inscrivez-vous à notre newsletter pour recevoir les derniers articles et actualités directement dans votre boîte mail.
                    </p>

                    <form class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                        <div class="relative flex-1 max-w-md">
                            <input
                                type="email"
                                placeholder="Votre adresse e-mail"
                                class="w-full rounded-xl border border-zinc-800 bg-zinc-900 px-5 py-4 text-white placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
                            >
                        </div>
                        <button
                            type="submit"
                            class="rounded-xl bg-primary px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary/30 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/40 transition-all"
                        >
                            S'inscrire
                        </button>
                    </form>

                    <p class="mt-4 text-sm text-zinc-500">
                        Rejoignez <span class="font-semibold text-white">2,847</span> abonnés. Désabonnement en un clic.
                    </p>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                    <div class="col-span-2 lg:col-span-2">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-lg shadow-primary/25">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path fill-rule="evenodd" d="M4.125 3C3.079 3 2.25 3.829 2.25 4.875v16.5c0 1.046.829 1.875 1.875 1.875h15.75c1.046 0 1.875-.829 1.875-1.875V4.875C21 3.829 20.171 3 19.125 3H4.125ZM5.25 7.5a.75.75 0 0 0-.75.75v9a.75.75 0 0 0 1.5 0v-9a.75.75 0 0 0-.75-.75Zm6.75.75a.75.75 0 0 1 1.5 0v9a.75.75 0 0 1-1.5 0v-9Zm1.5-3.75a.75.75 0 0 0-.75.75v12a.75.75 0 0 0 1.5 0v-12a.75.75 0 0 0-.75-.75Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-zinc-900 dark:text-white">{{ config('app.name', 'Blog') }}</span>
                        </div>
                        <p class="mt-4 max-w-xs text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Votre source d'inspiration pour la tech, le design et l'innovation. Des articles de qualité pour développeurs et créatifs.
                        </p>
                        <div class="mt-6 flex items-center gap-4">
                            <a href="#" class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 hover:bg-primary hover:text-white transition-all dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-primary">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 hover:bg-primary hover:text-white transition-all dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-primary">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                            </a>
                            <a href="#" class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 text-zinc-600 hover:bg-primary hover:text-white transition-all dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-primary">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">Navigation</h3>
                        <ul class="mt-4 space-y-3">
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Accueil</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Blog</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Catégories</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">À propos</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">Légal</h3>
                        <ul class="mt-4 space-y-3">
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Mentions légales</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Confidentialité</a></li>
                            <li><a href="#" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">CGU</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">Contact</h3>
                        <ul class="mt-4 space-y-3">
                            <li><a href="mailto:hello@blog.com" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">hello@blog.com</a></li>
                            <li><span class="text-sm text-zinc-500 dark:text-zinc-400">Paris, France</span></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 border-t border-zinc-200 pt-8 dark:border-zinc-800">
                    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Blog') }}. Tous droits réservés.
                        </p>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500">
                            Fait avec <span class="text-primary">❤</span> en France
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
