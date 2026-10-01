<?php

use App\Models\Subscriber;
use App\Notifications\VerifySubscriberEmail;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate('required|email|unique:subscribers,email|max:255')]
    public string $email;

    public function subscribe()
    {
        $this->validate();
        $subscriber = Subscriber::create([
            'email' => $this->email
        ]);
        $subscriber->notify(new VerifySubscriberEmail());
        $this->email = "";
    }
};
?>

<div>
    <form wire:submit='subscribe' class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
        <div class="relative flex-1 max-w-md">
            <input type="email" placeholder="Votre adresse e-mail" wire:model='email'
                class="w-full rounded-xl border @error('email')border-primary @else border-zinc-800 @enderror bg-zinc-900 px-5 py-4 text-white placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
            @error('email')
                <p class="text-primary text-left italic text-sm">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit"
            class="rounded-xl bg-primary px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary/30 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/40 transition-all">
            S'inscrire
        </button>
    </form>
</div>