<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white antialiased">
        <flux:header container :sticky="true" class="bg-white/70 h-16 backdrop-blur-xl border-b border-zinc-200/50 dark:bg-zinc-950/70 dark:border-zinc-800/50">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('home') }}" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="home" href="{{ route('home') }}" :current="request()->routeIs('home')" wire:navigate class="{{ request()->routeIs('home') ? 'text-primary font-semibold' : 'font-medium' }}">
                    {{ __('Accueil') }}
                </flux:navbar.item>
                <flux:navbar.item icon="document-text" href="{{ route('blogs.index') }}" :current="request()->routeIs('blogs.index')" wire:navigate class="{{ request()->routeIs('blogs.index') ? 'text-primary font-semibold' : 'font-medium' }}">
                    {{ __('Blog') }}
                </flux:navbar.item>
                <flux:navbar.item icon="folder" href="{{ route('categories') }}" :current="request()->routeIs('categories')" wire:navigate class="{{ request()->routeIs('categories') ? 'text-primary font-semibold' : 'font-medium' }}">
                    {{ __('Catégories') }}
                </flux:navbar.item>
                <flux:navbar.item icon="information-circle" href="{{ route('about') }}" :current="request()->routeIs('about')" wire:navigate class="{{ request()->routeIs('about') ? 'text-primary font-semibold' : 'font-medium' }}">
                    {{ __('À propos') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <div class="flex items-center gap-3">
                <livewire:search-posts />
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all" wire:navigate>
                            <span class="max-lg:hidden">{{ __('Dashboard') }}</span>
                            <flux:icon icon="chart-bar-square" class="lg:hidden size-5" />
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-600 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors" wire:navigate>
                            <span class="max-lg:hidden">{{ __('Log in') }}</span>
                            <flux:icon icon="arrow-right-end-on-rectangle" class="lg:hidden size-5" />
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all" wire:navigate>
                                <span class="max-lg:hidden">{{ __('Register') }}</span>
                                <flux:icon icon="user-plus" class="lg:hidden size-5" />
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ml-1 flex items-center space-x-2" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group heading="{{ __('Navigation') }}">
                    <flux:navlist.item icon="home" href="{{ route('home') }}" :current="request()->routeIs('home')" wire:navigate class="{{ request()->routeIs('home') ? 'text-primary font-semibold' : 'font-medium' }}">
                        {{ __('Accueil') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="document-text" href="{{ route('blogs.index') }}" :current="request()->routeIs('blogs.index')" wire:navigate class="{{ request()->routeIs('blogs.index') ? 'text-primary font-semibold' : 'font-medium' }}">
                        {{ __('Blog') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="folder" href="{{ route('categories') }}" :current="request()->routeIs('categories')" wire:navigate class="{{ request()->routeIs('categories') ? 'text-primary font-semibold' : 'font-medium' }}">
                        {{ __('Catégories') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="information-circle" href="{{ route('about') }}" :current="request()->routeIs('about')" wire:navigate class="{{ request()->routeIs('about') ? 'text-primary font-semibold' : 'font-medium' }}">
                        {{ __('À propos') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <div>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold w-full text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all" wire:navigate>
                            <flux:icon icon="chart-bar-square" class="size-5" />{{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="mb-4 inline-flex items-center gap-2 rounded-xl font-medium text-zinc-600 text-sm border px-5 py-2.5 w-full border-zinc-200 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors" wire:navigate>
                            <flux:icon icon="arrow-right-end-on-rectangle" class="size-5" />{{ __('Log in') }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold w-full text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all" wire:navigate>
                                <flux:icon icon="user-plus" class="size-5" />{{ __('Register') }}
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </flux:sidebar>

        <main>
            {{ $slot }}
        </main>

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
                            <li><a href="{{ route('home') }}" wire:navigate class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Accueil</a></li>
                            <li><a href="{{ route('blogs.index') }}" wire:navigate class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Blog</a></li>
                            <li><a href="{{ route('categories') }}" wire:navigate class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Catégories</a></li>
                            <li><a href="{{ route('about') }}" wire:navigate class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">À propos</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">Légal</h3>
                        <ul class="mt-4 space-y-3">
                            <li><a href="{{ route('mentions-legales') }}" wire:navigate class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Mentions légales</a></li>
                            <li><a href="{{ route('confidentialite') }}" wire:navigate class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">Confidentialité</a></li>
                            <li><a href="{{ route('cgu') }}" wire:navigate class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">CGU</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">Contact</h3>
                        <ul class="mt-4 space-y-3">
                            <li><a href="mailto:hello@blog.com" class="text-sm text-zinc-500 hover:text-primary dark:text-zinc-400 dark:hover:text-primary transition-colors">hello@blog.com</a></li>
                            <li><span class="text-sm text-zinc-500 dark:text-zinc-400">Cotonou, Bénin</span></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 border-t border-zinc-200 pt-8 dark:border-zinc-800">
                    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Blog') }}. Tous droits réservés.
                        </p>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500">
                            Fait avec <span class="text-primary">❤</span> au Bénin
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <flux:toast />

        @if (session('success'))
            <div
                data-toast="{{ session('success') }}"
                x-data
                x-init="$flux.toast($el.dataset.toast, { variant: 'success' })"
                class="hidden"
            ></div>
        @endif

        @fluxScripts

        <script>
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    console.log("open-search-modal")
                    Livewire.dispatch('open-search-modal');
                }
            });
        </script>
    </body>
</html>
