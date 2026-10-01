<?php

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;

new #[Layout('components.layouts.app')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $selectedRoles = ['author'];

    public function getRoles()
    {
        return Role::all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'selectedRoles' => ['required', 'array'],
            'selectedRoles.*' => ['exists:roles,name'],
        ]);

        $plainPassword = $this->password;

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
        ]);

        $user->syncRoles($this->selectedRoles);

        Mail::to($user->email)->send(new WelcomeMail($user, $plainPassword));

        session()->flash('success', 'Utilisateur créé avec succès. Un email de bienvenue a été envoyé.');

        return $this->redirect(route('dashboard.users'));
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Nouvel utilisateur</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Créez un nouveau compte utilisateur.</p>
        </div>
        <a href="{{ route('dashboard.users') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Retour
        </a>
    </div>

    {{-- Form --}}
    <div class="max-w-2xl">
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <form wire:submit="save" class="space-y-6">
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
                    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Mot de passe</label>
                    <input type="password" wire:model="password" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Confirmer le mot de passe</label>
                    <input type="password" wire:model="password_confirmation" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-3">Rôle</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->getRoles() as $role)
                            <label class="cursor-pointer">
                                <input type="checkbox" value="{{ $role->name }}" wire:model="selectedRoles" class="sr-only peer">
                                <span class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition-all
                                    border-zinc-200 bg-white text-zinc-600
                                    peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary
                                    dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400
                                    dark:peer-checked:border-primary dark:peer-checked:bg-primary/10 dark:peer-checked:text-primary">
                                    {{ ucfirst($role->name) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedRoles') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('dashboard.users') }}" wire:navigate class="flex-1 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-all text-center">
                        Annuler
                    </a>
                    <button type="submit" class="flex-1 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark transition-all">
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
