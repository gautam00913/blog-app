<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth', ['title' => "Inscription"])] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-8">
    <x-auth-header title="Créer un compte" description="Rejoignez notre communauté de développeurs et créatifs." />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-5">
        <!-- Name -->
        <div class="space-y-2">
            <flux:input wire:model="name" id="name" label="Nom complet" type="text" name="name" required autofocus autocomplete="name" placeholder="Jean Dupont" />
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <flux:input wire:model="email" id="email" label="Adresse e-mail" type="email" name="email" required autocomplete="email" placeholder="vous@exemple.com" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <flux:input
                wire:model="password"
                id="password"
                label="Mot de passe"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
            />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <flux:input
                wire:model="password_confirmation"
                id="password_confirmation"
                label="Confirmer le mot de passe"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
            />
        </div>

        <div class="flex items-start gap-3">
            <input type="checkbox" id="terms" class="mt-1 h-4 w-4 rounded border-zinc-300 text-primary focus:ring-primary" required>
            <label for="terms" class="text-sm text-zinc-500 dark:text-zinc-400">
                J'accepte les <a href="{{ route('cgu') }}" class="font-medium text-primary hover:text-primary-dark transition-colors" wire:navigate>conditions d'utilisation</a> et la <a href="{{ route('confidentialite') }}" class="font-medium text-primary hover:text-primary-dark transition-colors" wire:navigate>politique de confidentialité</a>
            </label>
        </div>

        <button type="submit" class="w-full rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-950">
            Créer mon compte
        </button>
    </form>

    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-zinc-200 dark:border-zinc-700"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="bg-white px-4 text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">ou s'inscrire avec</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <button type="button" class="flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50 hover:border-zinc-300 transition-all dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Google
        </button>
        <button type="button" class="flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50 hover:border-zinc-300 transition-all dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
            GitHub
        </button>
    </div>

    <p class="text-center text-sm text-zinc-500 dark:text-zinc-400">
        Déjà un compte ?
                        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-primary hover:text-primary-dark transition-colors">
            Se connecter
        </a>
    </p>
</div>
