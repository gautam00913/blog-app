<?php

use App\Models\AuthorRequest;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public string $status = '';
    public string $search = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getRequests()
    {
        return AuthorRequest::with('user')
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->search, fn ($q) => $q->whereHas('user', function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(15);
    }

    public function counts(): array
    {
        return [
            'all' => AuthorRequest::count(),
            'pending' => AuthorRequest::pending()->count(),
            'accepted' => AuthorRequest::accepted()->count(),
            'rejected' => AuthorRequest::rejected()->count(),
        ];
    }

    public function accept(AuthorRequest $authorRequest): void
    {
        $this->authorize('user.manage');

        if (! $authorRequest->isPending()) {
            return;
        }

        $authorRequest->update(['status' => 'accepted', 'reviewed_at' => now()]);
        $authorRequest->user->assignRole('author');

        Flux::toast(text: "{$authorRequest->user->name} est désormais auteur.", variant: 'success');
    }

    public function reject(AuthorRequest $authorRequest): void
    {
        $this->authorize('user.manage');

        if (! $authorRequest->isPending()) {
            return;
        }

        $authorRequest->update(['status' => 'rejected', 'reviewed_at' => now()]);

        Flux::toast(text: "La demande de {$authorRequest->user->name} a été rejetée.", variant: 'success');
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'En attente',
            'accepted' => 'Acceptée',
            'rejected' => 'Rejetée',
            default => $status,
        };
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Demandes d'auteur</h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Examinez les candidatures et accordez le rôle d'auteur.</p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap gap-2">
            @foreach (['' => 'Toutes', 'pending' => 'En attente', 'accepted' => 'Acceptées', 'rejected' => 'Rejetées'] as $value => $label)
                <button
                    wire:click="$set('status', '{{ $value }}')"
                    class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold transition-all {{ $status === $value ? 'border-primary bg-primary/10 text-primary' : 'border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}"
                >
                    {{ $label }}
                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-bold text-zinc-500 dark:bg-zinc-700 dark:text-zinc-300">
                        {{ $this->counts()[$value === '' ? 'all' : $value] }}
                    </span>
                </button>
            @endforeach
        </div>
        <div>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Rechercher un candidat..."
                class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 sm:w-72 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
            >
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Candidat</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Dernier diplôme</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">CV</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Motivation</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Statut</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($this->getRequests() as $authorRequest)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                                        {{ strtoupper(mb_substr($authorRequest->user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $authorRequest->user->name }}</p>
                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $authorRequest->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-300">{{ $authorRequest->last_degree }}</td>
                            <td class="px-4 py-4">
                                <a href="{{ route('dashboard.author-requests.cv', $authorRequest) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                    CV
                                </a>
                            </td>
                            <td class="px-4 py-4">
                                <details class="max-w-xs">
                                    <summary class="cursor-pointer text-xs font-semibold text-primary hover:text-primary-dark transition-colors">Voir la lettre</summary>
                                    <p class="mt-2 whitespace-pre-line text-xs text-zinc-600 dark:text-zinc-300">{{ $authorRequest->motivation_letter }}</p>
                                </details>
                            </td>
                            <td class="px-4 py-4">
                                @if ($authorRequest->isAccepted())
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">{{ $this->statusLabel($authorRequest->status) }}</span>
                                @elseif ($authorRequest->isRejected())
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ $this->statusLabel($authorRequest->status) }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">{{ $this->statusLabel($authorRequest->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs text-zinc-500 dark:text-zinc-400">{{ $authorRequest->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right">
                                @if ($authorRequest->isPending())
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="accept({{ $authorRequest->id }})" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition-all">
                                            Accepter
                                        </button>
                                        <button wire:click="reject({{ $authorRequest->id }})" wire:confirm="Rejeter cette demande ?" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 dark:border-red-900/40 dark:text-red-400 dark:hover:bg-red-900/20 transition-all">
                                            Rejeter
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400 dark:text-zinc-600">{{ $authorRequest->reviewed_at?->diffForHumans() ?? '—' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucune demande trouvée.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 dark:border-zinc-800 px-6 py-4">
            {{ $this->getRequests()->links() }}
        </div>
    </div>
</div>
