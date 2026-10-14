<?php

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $role = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
    {
        $this->resetPage();
    }

    public function getUsers()
    {
        return User::with(['roles.permissions', 'permissions'])
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->role, fn ($q) => $q->whereHas('roles', fn ($role) => $role->where('name', $this->role)))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function permissions()
    {
        return Permission::orderBy('name')->get();
    }

    #[Computed]
    public function roles()
    {
        return Role::orderBy('name')->get();
    }

    public function togglePermission(User $user, string $permission): void
    {
        if (! $this->permissions->pluck('name')->contains($permission)) {
            return;
        }

        if ($user->id === auth()->id() && $permission === 'user.manage') {
            Flux::toast(text: 'Vous ne pouvez pas retirer votre propre accès à la gestion des permissions.', variant: 'danger');

            return;
        }

        $label = $this->permissionLabel($permission);

        if ($user->hasDirectPermission($permission)) {
            $user->revokePermissionTo($permission);
            $message = "Permission « {$label} » retirée à {$user->name}.";
        } else {
            $user->givePermissionTo($permission);
            $message = "Permission « {$label} » accordée à {$user->name}.";
        }

        Flux::toast(text: $message, variant: 'success');
    }

    public function permissionLabel(string $permission): string
    {
        return match ($permission) {
            'post.create' => 'Créer des articles',
            'post.edit' => 'Modifier des articles',
            'post.delete' => 'Supprimer des articles',
            'post.publish' => 'Publier des articles',
            'category.manage' => 'Gérer les catégories',
            'comment.manage' => 'Gérer les commentaires',
            'user.manage' => 'Gérer les utilisateurs',
            default => $permission,
        };
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Permissions</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Accordez ou retirez des permissions directement à un utilisateur, en complément de ses rôles.</p>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-zinc-500 dark:text-zinc-400">
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-flex h-4 w-4 items-center justify-center rounded bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            </span>
            Accordée directement — cliquez pour retirer
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-flex h-4 w-4 items-center justify-center rounded bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
            </span>
            Accordée via un rôle
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-flex h-4 w-4 items-center justify-center rounded text-zinc-300 dark:text-zinc-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            </span>
            Non accordée — cliquez pour accorder
        </span>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Rechercher par nom ou email..."
                class="w-full max-w-md rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
            >
        </div>
        <div>
            <select
                wire:model.live="role"
                class="rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            >
                <option value="">Tous les rôles</option>
                @foreach ($this->roles as $role)
                    <option value="{{ $role->name }}">
                        {{ match($role->name) {
                            'admin' => 'Administrateur',
                            'editor' => 'Editeur',
                            'author' => 'Auteur',
                            'subscriber' => 'Abonné',
                            default => $role->name,
                        } }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Permissions Table --}}
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Utilisateur</th>
                        @foreach ($this->permissions as $permission)
                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400" title="{{ $permission->name }}">
                                {{ $this->permissionLabel($permission->name) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($this->getUsers() as $user)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white" style="background-color: {{ $user->hasRole('admin') ? 'var(--color-primary)' : ($user->hasRole('editor') ? 'var(--color-secondary)' : ($user->hasRole('author') ? 'var(--color-accent-violet)' : 'var(--color-zinc-400)')) }}">
                                        {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                                        @if ($user->roles->isNotEmpty())
                                            <div class="mt-0.5 flex flex-wrap gap-1">
                                                @foreach ($user->roles as $role)
                                                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-1.5 py-0.5 text-[10px] font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                                        {{ match($role->name) {
                                                            'admin' => 'Administrateur',
                                                            'editor' => 'Editeur',
                                                            'author' => 'Auteur',
                                                            'subscriber' => 'Abonné',
                                                            default => '...',
                                                        } }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            @foreach ($this->permissions as $permission)
                                <td class="px-4 py-4">
                                    @if ($user->hasDirectPermission($permission->name))
                                        <button
                                            wire:click="togglePermission({{ $user->id }}, '{{ $permission->name }}')"
                                            wire:confirm="Retirer cette permission ?"
                                            title="Retirer « {{ $this->permissionLabel($permission->name) }} »"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition-colors hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        </button>
                                    @elseif ($user->can($permission->name))
                                        <span
                                            title="Accordée via un rôle"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-500"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                        </span>
                                    @else
                                        <button
                                            wire:click="togglePermission({{ $user->id }}, '{{ $permission->name }}')"
                                            title="Accorder « {{ $this->permissionLabel($permission->name) }} »"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-zinc-300 transition-colors hover:bg-emerald-50 hover:text-emerald-600 dark:text-zinc-600 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                        </button>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $this->permissions->count() + 1 }}" class="px-6 py-12 text-center">
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