<?php

use App\Models\Comment;
use App\Notifications\CommentApproved;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public string $status = '';
    #[Url]
    public ?int $id = null;

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function getComments()
    {
        return Comment::with(['post', 'user', 'parent'])
            ->when($this->id, fn ($q) => $q->where('id', $this->id))
            ->when($this->status === 'approved', fn ($q) => $q->approved())
            ->when($this->status === 'pending', fn ($q) => $q->pending())
            ->latest()
            ->paginate(15);
    }

    public function approve(Comment $comment): void
    {
        $this->authorize('comment.manage');

        $comment->update(['approved' => true]);

        if ($comment->user) {
            $comment->user->notify(new CommentApproved($comment));
        }

        Flux::toast(text: 'Commentaire approuvé.', variant: 'success');
    }

    public function reject(Comment $comment): void
    {
        $this->authorize('comment.manage');

        $comment->update(['approved' => false]);
        Flux::toast(text: 'Commentaire rejeté.', variant: 'success');
    }

    public function delete(Comment $comment): void
    {
        $this->authorize('comment.manage');

        $comment->delete();
        Flux::toast(text: 'Commentaire supprimé.', variant: 'success');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Commentaires</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Modérez les commentaires de vos articles.</p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <select
            wire:model.live="status"
            class="rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
        >
            <option value="">Tous les commentaires</option>
            <option value="approved">Approuvés</option>
            <option value="pending">En attente</option>
        </select>
        @if ($id)
            <a wire:click="$wire.set('id', null)"
                class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary transition-colors cursor-pointer">
                <span>Annuler le filtre</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </a>
        @endif
    </div>

    {{-- Comments List --}}
    <div class="space-y-4">
        @forelse ($this->getComments() as $comment)
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-sm font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ strtoupper(mb_substr($comment->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $comment->name }}</p>
                                @if ($comment->isReply())
                                    <span class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">Réponse</span>
                                @endif
                                @if ($comment->approved)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Approuvé</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">En attente</span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $comment->email }} · {{ $comment->created_at->diffForHumans() }}
                                · sur <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $comment->post->title ?? 'Article supprimé' }}</span>
                            </p>
                            @if ($comment->isReply() && $comment->parent)
                                <p class="mt-2 border-l-2 border-primary/30 pl-3 text-xs text-zinc-500 dark:text-zinc-400">
                                    En réponse à <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $comment->parent->name }}</span> :
                                    « {{ Str::limit($comment->parent->body, 100) }} »
                                </p>
                            @endif
                            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $comment->body }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                    @if (!$comment->approved)
                        <button wire:click="approve({{ $comment->id }})" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            Approuver
                        </button>
                    @else
                        <button wire:click="reject({{ $comment->id }})" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/20 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            Rejeter
                        </button>
                    @endif
                    <button wire:click="delete({{ $comment->id }})" wire:confirm="Supprimer ce commentaire ?" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        Supprimer
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-zinc-200 bg-white p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucun commentaire trouvé.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $this->getComments()->links() }}
    </div>
</div>
