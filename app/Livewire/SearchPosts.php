<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchPosts extends Component
{
    public string $query = '';

    public bool $showModal = false;

    public array $results = [];

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        $this->results = Post::published()
            ->with('author', 'category')
            ->where(function ($q) {
                $q->where('title', 'like', "%{$this->query}%")
                    ->orWhere('excerpt', 'like', "%{$this->query}%");
            })
            ->orWhereHas('author', fn ($cq) => $cq->where('name', 'like', "%{$this->query}%"))
            ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$this->query}%"))
            ->orWhereHas('tags', fn ($cq) => $cq->where('name', 'like', "%{$this->query}%"))
            ->limit(8)
            ->get()
            ->map(fn ($post) => [
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'author' => $post->author->name,
                'category' => $post->category->name,
                'category_color' => $post->category->color,
                'date' => $post->published_at->format('d M Y'),
                'url' => route('blogs.show', $post->slug),
            ])
            ->toArray();
    }

    #[On('open-search-modal')]
    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->query = '';
        $this->results = [];
    }

    public function render()
    {
        return view('livewire.search-posts');
    }
}
