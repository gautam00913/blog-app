<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public ?Post $post = null;

    public string $title = '';
    public string $excerpt = '';
    public string $body = '';
    public ?int $category_id = null;
    public array $selectedTags = [];
    public string $status = 'draft';
    public ?string $published_at = null;
    public $featured_image = null;
    public ?string $originalFeaturedImage = null;

    public array $categories = [];
    public array $tags = [];

    public function mount(Post $post): void
    {
        if (! auth()->user()->can('post.edit') && $post->user_id !== auth()->id()) {
            abort(403);
        }

        $this->post = $post;
        $this->title = $post->title;
        $this->excerpt = $post->excerpt ?? '';
        $this->body = $post->body;
        $this->category_id = $post->category_id;
        $this->status = $post->status;
        $this->selectedTags = $post->tags->pluck('id')->toArray();
        $this->featured_image = $post->featured_image;
        $this->originalFeaturedImage = $post->featured_image;
        $this->published_at = $post->status === 'scheduled' && $post->published_at
            ? $post->published_at->format('Y-m-d\TH:i')
            : null;

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
            'featured_image' => $this->featured_image instanceof UploadedFile
                ? ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048']
                : ['nullable', 'string'],
        ]);

        if ($this->status !== 'draft' && ! auth()->user()->can('post.publish')) {
            $this->status = $this->post->status;
        }

        if ($this->status === 'scheduled') {
            $this->validate([
                'published_at' => ['required', 'date', 'after:now'],
            ]);
        }

        $data = [
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'status' => $this->status,
            'published_at' => match (true) {
                $this->status === 'scheduled' => Carbon::parse($this->published_at),
                $this->status === 'published' && ! $this->post->published_at => now(),
                default => $this->post->published_at,
            },
        ];

        if ($this->featured_image instanceof UploadedFile) {
            $data['featured_image'] = $this->featured_image->store('posts', 'public');
            $this->deleteStoredImage($this->originalFeaturedImage);
        } elseif (blank($this->featured_image) && filled($this->originalFeaturedImage)) {
            $data['featured_image'] = null;
            $this->deleteStoredImage($this->originalFeaturedImage);
        }

        $this->post->update($data);

        $this->post->tags()->sync($this->selectedTags);

        session()->flash('success', 'Article mis à jour avec succès.');
        $this->redirectRoute('dashboard.posts', navigate: true);
    }

    public function cancelImageChange(): void
    {
        $this->featured_image = $this->originalFeaturedImage;
    }

    private function deleteStoredImage(?string $path): void
    {
        if (filled($path) && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
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
                        <x-rich-text-editor
                            property="body"
                            :value="$body"
                            placeholder="Rédigez votre article..."
                        />
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
                    @if (auth()->user()->can('post.publish'))
                        <option value="published">Publié</option>
                        <option value="scheduled">Programmé</option>
                    @endif
                </select>
                @if ($status === 'scheduled')
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Date de publication</label>
                        <input
                            type="datetime-local"
                            wire:model="published_at"
                            class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                        >
                        @error('published_at') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>

            {{-- Image à la une --}}
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Image à la une</h3>
                @if ($featured_image instanceof UploadedFile)
                    <div class="mb-3 overflow-hidden rounded-xl">
                        <img src="{{ $featured_image->temporaryUrl() }}" alt="Aperçu de la nouvelle image" class="h-40 w-full object-cover">
                    </div>
                    <button type="button" wire:click="cancelImageChange" class="mb-3 text-xs font-medium text-red-600 hover:underline dark:text-red-400">Annuler le choix</button>
                @elseif (filled($featured_image))
                    <div class="mb-3 overflow-hidden rounded-xl">
                        <img src="{{ $post->featuredImageUrl() }}" alt="Image à la une actuelle" class="h-40 w-full object-cover">
                    </div>
                    <button type="button" wire:click="$set('featured_image', null)" class="mb-3 text-xs font-medium text-red-600 hover:underline dark:text-red-400">Supprimer l'image</button>
                @else
                    <p class="mb-3 text-xs text-zinc-500 dark:text-zinc-400">Aucune image. Formats acceptés : JPG, PNG, WebP, GIF (2 Mo max).</p>
                @endif
                <input type="file" wire:model="featured_image" accept="image/jpeg,image/png,image/webp,image/gif" class="block w-full text-sm text-zinc-600 dark:text-zinc-400 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-zinc-700 dark:file:bg-zinc-800 dark:file:text-zinc-300 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700">
                @error('featured_image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
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
