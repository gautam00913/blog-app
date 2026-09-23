<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public ?Post $post = null;

    public string $title = '';
    public string $excerpt = '';
    public string $body = '';
    public ?int $category_id = null;
    public array $selectedTags = [];
    public string $status = 'draft';

    public array $categories = [];
    public array $tags = [];

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = $post->excerpt ?? '';
        $this->body = $post->body;
        $this->category_id = $post->category_id;
        $this->status = $post->status;
        $this->selectedTags = $post->tags->pluck('id')->toArray();

        $this->categories = Category::orderBy('name')->pluck('name', 'id')->toArray();
        $this->tags = Tag::orderBy('name')->get()->toArray();
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:draft,published,scheduled'],
        ]);

        $this->post->update([
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'status' => $this->status,
            'published_at' => $this->status === 'published' && !$this->post->published_at ? now() : $this->post->published_at,
        ]);

        $this->post->tags()->sync($this->selectedTags);

        session()->flash('success', 'Article mis à jour avec succès.');
        $this->redirectRoute('dashboard.posts', navigate: true);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex items-center gap-4">
            <a href="{{ route('dashboard.posts') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Retour
        </a>
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Éditer l'article</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Modifiez les détails de votre article.</p>
        </div>
    </div>

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Titre</label>
                        <input
                            type="text"
                            wire:model="title"
                            placeholder="Titre de l'article"
                            class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                        >
                        @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Extrait</label>
                        <textarea
                            wire:model="excerpt"
                            rows="3"
                            placeholder="Courte description de l'article..."
                            class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                        ></textarea>
                        @error('excerpt') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Contenu</label>
                        <textarea
                            wire:model="body"
                            rows="15"
                            placeholder="Contenu de l'article en HTML..."
                            class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400 font-mono"
                        ></textarea>
                        @error('body') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Statut</h3>
                <select
                    wire:model="status"
                    class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                    <option value="draft">Brouillon</option>
                    <option value="published">Publié</option>
                    <option value="scheduled">Programmé</option>
                </select>
            </div>

            {{-- Category --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Catégorie</h3>
                <select
                    wire:model="category_id"
                    class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                    <option value="">Sélectionner une catégorie</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Tags --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        <label class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 cursor-pointer transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/10 has-[:checked]:text-primary">
                            <input type="checkbox" wire:model="selectedTags" value="{{ $tag['id'] }}" class="sr-only">
                            {{ $tag['name'] }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <button type="submit" class="w-full rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all">
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
