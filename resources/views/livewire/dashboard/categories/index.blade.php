<?php

use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $name = '';
    public string $description = '';
    public string $color = '#FF584D';

    public ?int $editId = null;
    public bool $showModal = false;

    public function getCategories()
    {
        return Category::withCount('posts')->orderBy('name')->get();
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->reset(['name', 'description', 'color', 'editId']);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'description', 'color', 'editId']);
    }

    public function edit(Category $category): void
    {
        $this->editId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->color = $category->color;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        if ($this->editId) {
            $category = Category::findOrFail($this->editId);
            $category->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'color' => $this->color,
            ]);
            Flux::toast(text: 'Catégorie mise à jour avec succès.', variant: 'success');
        } else {
            Category::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'color' => $this->color,
            ]);
            Flux::toast(text: 'Catégorie créée avec succès.', variant: 'success');
        }

        $this->closeModal();
    }

    public function delete(Category $category): void
    {
        $category->delete();
        Flux::toast(text: 'Catégorie supprimée avec succès.', variant: 'success');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 lg:p-8">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Catégories</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Organisez vos articles par catégorie.</p>
        </div>
        @if (auth()->user()->can('category.manage'))
            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nouvelle catégorie
            </button>
        @endif
    </div>

    {{-- Categories Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($this->getCategories() as $category)
            <div class="group rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm hover-lift dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl" style="background-color: {{ $category->color }}20">
                            <div class="h-full w-full rounded-xl flex items-center justify-center">
                                <div class="h-4 w-4 rounded-full" style="background-color: {{ $category->color }}"></div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $category->name }}</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $category->posts_count }} article{{ $category->posts_count > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if (auth()->user()->can('category.manage'))
                            <button wire:click="edit({{ $category->id }})" class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-zinc-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                </svg>
                            </button>
                            <button wire:click="delete({{ $category->id }})" wire:confirm="Êtes-vous sûr ?" class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-red-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                @if ($category->description)
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">{{ $category->description }}</p>
                @endif
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-zinc-200 bg-white p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucune catégorie trouvée.</p>
            </div>
        @endforelse
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-zinc-950/50 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-zinc-900">
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">
                    {{ $editId ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
                </h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Nom</label>
                        <input type="text" wire:model="name" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Description</label>
                        <textarea wire:model="description" rows="2" class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">Couleur</label>
                        <input type="color" wire:model="color" class="h-10 w-20 rounded-xl border border-zinc-200 bg-white cursor-pointer dark:border-zinc-700 dark:bg-zinc-800">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="closeModal" class="flex-1 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-all">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark transition-all">
                            {{ $editId ? 'Enregistrer' : 'Créer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
