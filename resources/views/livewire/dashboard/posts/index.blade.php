<?php

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function getPosts()
    {
        return Post::with(['author', 'category'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);
    }

    public function delete(Post $post): void
    {
        $this->authorize('delete', $post);
        $post->delete();
        session()->flash('success', 'Article supprimé avec succès.');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Articles</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Gérez vos articles de blog.</p>
        </div>
        <a href="{{ route('dashboard.posts.create') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nouvel article
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Rechercher un article..."
                class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
            >
        </div>
        <div>
            <select
                wire:model.live="status"
                class="rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            >
                <option value="">Tous les statuts</option>
                <option value="published">Publié</option>
                <option value="draft">Brouillon</option>
                <option value="scheduled">Programmé</option>
            </select>
        </div>
    </div>

    {{-- Posts Table --}}
    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Article</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Auteur</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Vues</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($this->getPosts() as $post)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                        <img src="{{ $post->featured_image }}" alt="" class="h-full w-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">{{ $post->title }}</p>
                                        <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $post->category->name ?? 'Sans catégorie' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $post->author->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($post->status === 'published')
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Publié</span>
                                @elseif ($post->status === 'draft')
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Brouillon</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-semibold text-secondary">Programmé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ number_format($post->views_count) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $post->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('dashboard.posts.edit', $post) }}" wire:navigate class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                        Éditer
                                    </a>
                                    <button wire:click="delete({{ $post->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer cet article ?" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucun article trouvé.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200 dark:border-zinc-800 px-6 py-4">
            {{ $this->getPosts()->links() }}
        </div>
    </div>
</div>
