<?php

use App\Models\AuthorRequest;
use App\Models\User;
use App\Notifications\NewAuthorRequest;
use Flux\Flux;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public string $last_degree = '';
    public $cv = null;
    public string $motivation_letter = '';

    public ?AuthorRequest $existingRequest = null;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->can('post.create')) {
            $this->redirect(route('dashboard'), navigate: true);

            return;
        }

        $this->existingRequest = AuthorRequest::where('user_id', $user->id)->latest()->first();
    }

    public function submit(): void
    {
        $user = auth()->user();

        if (AuthorRequest::where('user_id', $user->id)->pending()->exists()) {
            Flux::toast(text: 'Vous avez déjà une demande en attente.', variant: 'danger');

            return;
        }

        $validated = $this->validate([
            'last_degree' => ['required', 'string', 'max:255'],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'motivation_letter' => ['required', 'string', 'min:50', 'max:5000'],
        ]);

        $cvPath = $this->cv->store('author-requests/'.$user->id, 'local');

        $authorRequest = AuthorRequest::create([
            'user_id' => $user->id,
            'last_degree' => $validated['last_degree'],
            'cv_path' => $cvPath,
            'cv_original_name' => $this->cv->getClientOriginalName(),
            'motivation_letter' => $validated['motivation_letter'],
            'status' => 'pending',
        ]);

        try {
            $admins = User::whereHas('roles', fn ($query) => $query->where('name', 'admin'))->get();
            Notification::send($admins, new NewAuthorRequest($authorRequest));
        } catch (\Exception $e) {
            logger('error sending notification : '.$e->getMessage());
        }

        $this->reset('last_degree', 'cv', 'motivation_letter');
        $this->existingRequest = $authorRequest;

        Flux::toast(text: 'Votre demande a été envoyée aux administrateurs.', variant: 'success');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Devenir auteur</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Soumettez votre candidature pour publier vos articles sur le blog.</p>
        </div>
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Retour
        </a>
    </div>

    @if ($existingRequest && ! $existingRequest->isRejected())
        {{-- Status panel --}}
        <div class="max-w-2xl rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            @if ($existingRequest->isAccepted())
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <h2 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">Demande acceptée</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Félicitations, vous êtes désormais auteur. Vous pouvez créer des articles depuis le tableau de bord.</p>
            @else
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <h2 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">Demande en attente</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Votre demande a été transmise aux administrateurs. Vous recevrez une réponse prochainement.</p>
            @endif
        </div>
    @else
        @if ($existingRequest?->isRejected())
            <div class="max-w-2xl rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-400">
                Votre précédente demande a été rejetée. Vous pouvez soumettre une nouvelle candidature.
            </div>
        @endif

        {{-- Form --}}
        <div class="max-w-2xl">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <form wire:submit="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Dernier diplôme obtenu</label>
                        <input type="text" wire:model="last_degree" placeholder="Ex : Master en journalisme" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        @error('last_degree') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">CV (PDF)</label>
                        <input type="file" wire:model="cv" accept="application/pdf" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        <div wire:loading wire:target="cv" class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Téléversement en cours...</div>
                        @if ($cv)
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $cv->getClientOriginalName() }}</p>
                        @endif
                        @error('cv') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Lettre de motivation</label>
                        <textarea wire:model="motivation_letter" rows="6" placeholder="Expliquez votre motivation et votre expérience..." class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                        @error('motivation_letter') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('dashboard') }}" wire:navigate class="flex-1 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-all text-center">
                            Annuler
                        </a>
                        <button type="submit" class="flex-1 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark transition-all">
                            Envoyer ma demande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
