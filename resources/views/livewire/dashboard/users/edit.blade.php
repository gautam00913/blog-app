<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;

new #[Layout('components.layouts.app')] class extends Component {
    public User $user;
    public string $name = '';
    public string $email = '';
    public ?string $password = null;
    public ?string $password_confirmation = null;
    public array $selectedRoles = [];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if ($this->password) {
            $validated['password'] = Hash::make($this->password);
        } else {
            unset($validated['password']);
        }

        $this->user->update($validated);

        $this->user->syncRoles($this->selectedRoles);

        session()->flash('success', 'Utilisateur mis à jour avec succès.');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Éditer l'utilisateur</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Modifiez les informations de {{ $user->name }}.</p>
        </div>
        <a href="{{ route('dashboard.users') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-5 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Retour
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Profile Form --}}
        <div class="lg:col-span-2 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-6">Informations personnelles</h2>
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Nom</label>
                    <input type="text" wire:model="name" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Email</label>
                    <input type="email" wire:model="email" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Nouveau mot de passe <span class="font-normal text-zinc-500">(laisser vide pour conserver)</span></label>
                    <input type="password" wire:model="password" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Confirmer le mot de passe</label>
                    <input type="password" wire:model="password_confirmation" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>

        {{-- Roles --}}
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-6">Rôles</h2>
            <div class="space-y-3">
                @foreach ($this->getRoles() as $role)
                    <label class="flex items-center gap-3 rounded-xl border border-zinc-200 p-4 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800 transition-colors {{ in_array($role->name, $selectedRoles) ? 'border-primary bg-primary/5' : '' }}">
                        <input type="checkbox" value="{{ $role->name }}" wire:model="selectedRoles"
                            class="h-4 w-4 rounded border-zinc-300 text-primary focus:ring-primary/50">
                        <div class="flex-1">
                            <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ ucfirst($role->name) }}</span>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                {{ match($role->name) {
                                    'admin' => 'Accès complet au système',
                                    'editor' => 'Peut publier et gérer les contenus',
                                    'author' => 'Peut créer et éditer ses articles',
                                    'subscriber' => 'Accès en lecture seule',
                                    default => '',
                                } }}
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white" style="background-color: {{ $user->hasRole('admin') ? 'var(--color-primary)' : $user->hasRole('editor') ? 'var(--color-secondary)' : $user->hasRole('author') ? 'var(--color-accent-violet)' : 'var(--color-zinc-400)' }}">
                        {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Membre depuis {{ $user->created_at->translatedFormat('M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
