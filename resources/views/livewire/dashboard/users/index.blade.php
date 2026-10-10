<?php

use App\Mail\RoleChangedMail;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getUsers()
    {
        return User::with('roles')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function toggleRole(User $user, string $roleName): void
    {
        $oldRoles = $user->roles->pluck('name')->toArray();

        if ($user->hasRole($roleName)) {
            $user->removeRole($roleName);
        } else {
            $user->assignRole($roleName);
        }

        $user->load('roles');
        $newRoles = $user->roles->pluck('name')->toArray();

        if ($oldRoles !== $newRoles) {
            Mail::to($user->email)->send(new RoleChangedMail($user, $oldRoles[0] ?? null, $newRoles[0] ?? ''));
        }

        Flux::toast(text: 'Rôle mis à jour pour ' . $user->name . '.', variant: 'success');
    }

    public function deleteUser(User $user): void
    {
        if ($user->id === auth()->id()) {
            Flux::toast(text: 'Vous ne pouvez pas supprimer votre propre compte.', variant: 'danger');
            return;
        }

        $user->delete();
        Flux::toast(text: 'Utilisateur supprimé avec succès.', variant: 'success');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Utilisateurs</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Gérez les comptes et les rôles de vos utilisateurs.</p>
        </div>
        @if (auth()->user()->can('user.manage'))
            <a href="{{ route('dashboard.users.create') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nouvel utilisateur
            </a>
        @endif
    </div>

    {{-- Filters --}}
    <div>
        <input
            type="text"
            wire:model.live="search"
            placeholder="Rechercher par nom ou email..."
            class="w-full max-w-md rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
        >
    </div>

    {{-- Users Table --}}
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Utilisateur</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Rôles</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Inscrit le</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($this->getUsers() as $user)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white" style="background-color: {{ $user->hasRole('admin') ? 'var(--color-primary)' : ($user->hasRole('editor') ? 'var(--color-secondary)' : ($user->hasRole('author') ? 'var(--color-accent-violet)' : 'var(--color-zinc-400)')) }}">
                                        {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($user->roles as $role)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                            {{ match($role->name) {
                                                'admin' => 'bg-primary/10 text-primary',
                                                'editor' => 'bg-secondary/10 text-secondary',
                                                'author' => 'bg-accent-violet/10 text-accent-violet',
                                                'subscriber' => 'bg-accent-emerald/10 text-accent-emerald',
                                                default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                            } }}">
                                            {{ match($role->name) {
                                                'admin' => 'Administrateur',
                                                'editor' => 'Editeur',
                                                'author' => 'Auteur',
                                                'subscriber' => 'Abonné',
                                                default => '...',
                                            } }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-zinc-400">Aucun rôle</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('dashboard.users.edit', $user) }}" wire:navigate class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                        Éditer
                                    </a>
                                    <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Supprimer cet utilisateur ?" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucun utilisateur trouvé.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 dark:border-zinc-800 px-6 py-4">
            {{ $this->getUsers()->links() }}
        </div>
    </div>
</div>
