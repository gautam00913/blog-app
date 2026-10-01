<?php

use App\Models\Subscriber;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('components.layouts.public')] class extends Component
{
    public function mount(string $token)
    {
        if(strlen($token) != 32)
            return abort(404);

        $subscriber = Subscriber::where('token', $token)->firstOrFail();
        $subscriber->delete();
    }
};
?>

<div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
    <div class="flex w-full max-w-md flex-col gap-6 bg-white shadow-2xl p-3 md:p-10 rounded-lg border border-primary">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10">
            <flux:icon icon="check-circle" class="h-8 w-8 text-primary" />
        </div>
        <div class="mt-2 text-zinc-500 leading-relaxed">
            Votre désabonnement a été effectué avec succès. Vous ne faites plus parti de notre boîte aux lettres.
            Vous allez manquer nos prochaines publications.
        </div>
       
        <div class=" flex justify-center items-center gap-4 animate-slide-up stagger-3">
            <a href="{{ route('blogs.index') }}" wire:navigate
                class="group inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-4 text-base font-semibold text-white shadow-xl shadow-primary/30 hover:bg-primary-dark hover:shadow-2xl hover:shadow-primary/40 transition-all">
                Retour au Blog
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-5 w-5 group-hover:translate-x-1 transition-transform">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>
</div>