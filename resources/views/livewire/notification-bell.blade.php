<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public int $unreadCount = 0;
    public array $notifications = [];
    public bool $showDropdown = false;

    public function mount(): void
    {
        if (auth()->check()) {
            $this->unreadCount = auth()->user()->unreadNotifications()->count();
            $this->notifications = auth()->user()
                ->notifications()
                ->latest()
                ->take(10)
                ->get()
                ->toArray();
        }
    }

    public function getListeners() 
    {
        return [
            "echo-private:App.Models.User." . auth()->id() . ",.notification.created" => 'newNotification'
        ];
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    public function newNotification($event): void
    {
        $this->unreadCount++;
        $this->notifications = collect($this->notifications)
            ->prepend($event)
            ->take(10)
            ->toArray();
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->unreadCount = max(0, $this->unreadCount - 1);
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->unreadCount = 0;
    }
}; ?>

<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    <button
        wire:click="toggleDropdown"
        class="relative rounded-xl p-2 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-300 transition-colors"
    >
        <flux:icon icon="bell" class="size-5" />
        @if ($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white animate-pulse">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    @if ($showDropdown)
        <div
            class="absolute -left-52 md:right-0 z-50 mt-2 w-80 rounded-2xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-800 dark:bg-zinc-900"
            @click.outside="$wire.set('showDropdown', false)"
        >
            <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Notifications</h3>
                @if ($unreadCount > 0)
                    <button
                        wire:click="markAllAsRead"
                        class="text-xs font-medium text-primary hover:text-primary-dark transition-colors"
                    >
                        Tout marquer comme lu
                    </button>
                @endif
            </div>

            <div class="max-h-80 overflow-y-auto">
                @forelse ($notifications as $notification)
                    <a
                        href="{{ $notification['data']['url'] ?? '#' }}"
                        wire:click="markAsRead('{{ $notification['id'] }}')"
                        wire:navigate
                        class="flex items-start gap-3 border-b border-zinc-100 px-4 py-3 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/50 transition-colors {{ !isset($notification['read_at']) ? 'bg-primary/5' : '' }}"
                    >
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <flux:icon icon="bell" class="size-4" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-zinc-700 dark:text-zinc-300 line-clamp-2">
                                {{ $notification['data']['message'] ?? 'Nouvelle notification' }}
                            </p>
                            <p class="mt-1 text-xs text-zinc-400">
                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                            </p>
                        </div>
                        @if (!isset($notification['read_at']))
                            <div class="mt-2 h-2 w-2 shrink-0 rounded-full bg-primary"></div>
                        @endif
                    </a>
                @empty
                    <div class="px-4 py-8 text-center">
                        <flux:icon icon="bell-slash" class="mx-auto size-8 text-zinc-300 dark:text-zinc-600" />
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Aucune notification</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
