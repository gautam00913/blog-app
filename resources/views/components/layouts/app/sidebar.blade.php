<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-950">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('home') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                <x-app-logo class="size-8" href="#"></x-app-logo>
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group heading="{{ __('Navigation') }}" class="grid space-y-4">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="{{ request()->routeIs('dashboard') ? 'text-primary font-semibold bg-primary/5' : '' }} rounded-xl">{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('dashboard.posts')" :current="request()->routeIs('dashboard.posts*')" wire:navigate class="{{ request()->routeIs('dashboard.posts*') ? 'text-primary font-semibold bg-primary/5' : '' }} rounded-xl">{{ __('Articles') }}</flux:navlist.item>
                    <flux:navlist.item icon="folder" :href="route('dashboard.categories')" :current="request()->routeIs('dashboard.categories')" wire:navigate class="{{ request()->routeIs('dashboard.categories') ? 'text-primary font-semibold bg-primary/5' : '' }} rounded-xl">{{ __('Catégories') }}</flux:navlist.item>
                    <flux:navlist.item icon="chat-bubble-left" :href="route('dashboard.comments')" :current="request()->routeIs('dashboard.comments')" wire:navigate class="{{ request()->routeIs('dashboard.comments') ? 'text-primary font-semibold bg-primary/5' : '' }} rounded-xl">{{ __('Commentaires') }}</flux:navlist.item>
                    <flux:navlist.item icon="users" :href="route('dashboard.users')" :current="request()->routeIs('dashboard.users*')" wire:navigate class="{{ request()->routeIs('dashboard.users*') ? 'text-primary font-semibold bg-primary/5' : '' }} rounded-xl">{{ __('Utilisateurs') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                    class="rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-xl">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-xl bg-primary/10 text-primary text-xs font-bold"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-xl">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-xl bg-primary/10 text-primary text-xs font-bold"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
