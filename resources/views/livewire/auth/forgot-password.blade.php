<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('A reset link will be sent if the account exists.'));
    }
}; ?>

<div class="flex flex-col gap-8">
    <div class="text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Mot de passe oublié ?</h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
            Pas de souci ! Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-5">
        <!-- Email Address -->
        <div class="space-y-2">
            <flux:input wire:model="email" label="Adresse e-mail" type="email" name="email" required autofocus placeholder="vous@exemple.com" />
        </div>

        <button type="submit" class="w-full rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-950">
            Envoyer le lien de réinitialisation
        </button>
    </form>

    <p class="text-center text-sm text-zinc-500 dark:text-zinc-400">
        Vous vous souvenez de votre mot de passe ?
                        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-primary hover:text-primary-dark transition-colors">
            Retour à la connexion
        </a>
    </p>
</div>
