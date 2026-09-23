<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-950 antialiased">
        <div class="flex min-h-screen">
            {{-- Left Panel - Decorative --}}
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-zinc-950">
                {{-- Background Image --}}
                <div class="absolute inset-0">
                    <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200&q=80" alt="" class="h-full w-full object-cover opacity-30">
                    <div class="absolute inset-0 bg-gradient-to-br from-zinc-950 via-zinc-950/80 to-zinc-950/60"></div>
                </div>

                {{-- Decorative Elements --}}
                <div class="absolute top-20 left-20 w-72 h-72 bg-primary/20 rounded-full blur-3xl float"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-secondary/10 rounded-full blur-3xl float" style="animation-delay: -3s;"></div>

                {{-- Content --}}
                <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary text-white shadow-lg shadow-primary/25">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                                <path fill-rule="evenodd" d="M4.125 3C3.079 3 2.25 3.829 2.25 4.875v16.5c0 1.046.829 1.875 1.875 1.875h15.75c1.046 0 1.875-.829 1.875-1.875V4.875C21 3.829 20.171 3 19.125 3H4.125ZM5.25 7.5a.75.75 0 0 0-.75.75v9a.75.75 0 0 0 1.5 0v-9a.75.75 0 0 0-.75-.75Zm6.75.75a.75.75 0 0 1 1.5 0v9a.75.75 0 0 1-1.5 0v-9Zm1.5-3.75a.75.75 0 0 0-.75.75v12a.75.75 0 0 0 1.5 0v-12a.75.75 0 0 0-.75-.75Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-white">{{ config('app.name', 'Blog') }}</span>
                    </a>

                    {{-- Quote --}}
                    <div class="max-w-md">
                        <blockquote class="text-xl text-zinc-300 leading-relaxed font-display italic">
                            "Le code est comme l'humour. Quand on doit l'expliquer, c'est mauvais."
                        </blockquote>
                        <div class="mt-6 flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">CD</div>
                            <div>
                                <p class="font-semibold text-white">Cory House</p>
                                <p class="text-sm text-zinc-400">Software Developer</p>
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex items-center gap-8">
                        <div>
                            <p class="text-3xl font-bold text-white">24+</p>
                            <p class="text-sm text-zinc-400">Articles</p>
                        </div>
                        <div class="h-12 w-px bg-zinc-700"></div>
                        <div>
                            <p class="text-3xl font-bold text-white">4</p>
                            <p class="text-sm text-zinc-400">Auteurs</p>
                        </div>
                        <div class="h-12 w-px bg-zinc-700"></div>
                        <div>
                            <p class="text-3xl font-bold text-white">12K+</p>
                            <p class="text-sm text-zinc-400">Lecteurs</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Panel - Form --}}
            <div class="flex w-full lg:w-1/2 items-center justify-center p-6 sm:p-12">
                <div class="w-full max-w-md animate-fade-in">
                    {{-- Mobile Logo --}}
                    <div class="lg:hidden flex justify-center mb-8">
                        <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary text-white shadow-lg shadow-primary/25">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                                    <path fill-rule="evenodd" d="M4.125 3C3.079 3 2.25 3.829 2.25 4.875v16.5c0 1.046.829 1.875 1.875 1.875h15.75c1.046 0 1.875-.829 1.875-1.875V4.875C21 3.829 20.171 3 19.125 3H4.125ZM5.25 7.5a.75.75 0 0 0-.75.75v9a.75.75 0 0 0 1.5 0v-9a.75.75 0 0 0-.75-.75Zm6.75.75a.75.75 0 0 1 1.5 0v9a.75.75 0 0 1-1.5 0v-9Zm1.5-3.75a.75.75 0 0 0-.75.75v12a.75.75 0 0 0 1.5 0v-12a.75.75 0 0 0-.75-.75Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ config('app.name', 'Blog') }}</span>
                        </a>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
