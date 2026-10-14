<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentCreated;
use App\Notifications\NewCommentReply;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public $post = null;
    public $relatedPosts = [];
    public $approvedComments = [];
    public int $commentCount = 0;

    public string $name = '';
    public string $email = '';
    public string $body = '';

    public ?int $replyingTo = null;
    public string $replyBody = '';

    public function mount(string $slug): void
    {
        $this->post = Post::published()
            ->with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->post->incrementViews();

        $this->relatedPosts = Post::published()
            ->with(['author', 'category'])
            ->where('category_id', $this->post->category_id)
            ->where('id', '!=', $this->post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $this->refreshComments();

        if ($user = auth()->user()) {
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function saveComment(): void
    {
        $this->ensureCommentingIsNotRateLimited();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $comment = $this->post->comments()->create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'body' => $validated['body'],
            'approved' => true,
        ]);

        $this->notifyModerators($comment);

        RateLimiter::hit($this->commentRateLimiterKey());

        $this->reset('body');
        $this->refreshComments();
    }

    public function startReply(int $commentId): void
    {
        $comment = $this->findReplyableComment($commentId);

        $this->replyingTo = $comment->id;
        $this->replyBody = '';
    }

    public function cancelReply(): void
    {
        $this->replyingTo = null;
        $this->replyBody = '';
        $this->resetErrorBag('replyBody');
    }

    public function saveReply(): void
    {
        $this->ensureCommentingIsNotRateLimited();

        $parent = $this->findReplyableComment($this->replyingTo);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'replyBody' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $reply = $this->post->comments()->create([
            'user_id' => auth()->id(),
            'parent_id' => $this->rootCommentId($parent),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'body' => $validated['replyBody'],
            'approved' => true,
        ]);

        $this->notifyModerators($reply);

        if ($parent->user && $parent->user->id !== auth()->id()) {
            $parent->user->notify(new NewCommentReply($reply));
        }

        RateLimiter::hit($this->commentRateLimiterKey());

        $this->cancelReply();
        $this->refreshComments();
    }

    /**
     * Replies are flat: replying to a reply attaches to the root comment instead of nesting deeper.
     */
    private function rootCommentId(Comment $comment): int
    {
        return $comment->parent_id ?? $comment->id;
    }

    private function findReplyableComment(?int $commentId): Comment
    {
        $comment = $this->post->comments()
            ->approved()
            ->find($commentId);

        if (! $comment) {
            throw ValidationException::withMessages([
                'replyBody' => 'Ce commentaire n\'existe pas ou n\'est pas encore publié.',
            ]);
        }

        return $comment;
    }

    private function refreshComments(): void
    {
        $this->approvedComments = $this->post->comments()
            ->approved()
            ->topLevel()
            ->with(['replies' => fn ($query) => $query->approved()->oldest()])
            ->latest()
            ->take(10)
            ->get();

        $this->commentCount = $this->post->comments()->approved()->count();
    }

    private function notifyModerators(Comment $comment): void
    {
        $recipients = User::whereHas('permissions', fn ($query) => $query->where('name', 'comment.manage'))
            ->orWhereHas('roles.permissions', fn ($query) => $query->where('name', 'comment.manage'))
            ->get();

        if ($this->post->author && ! $recipients->contains('id', $this->post->author->id)) {
            $recipients->push($this->post->author);
        }

        try{
            $recipients->each(fn (User $user) => $user->notify(new NewCommentCreated($comment)));
        }catch(\Exception $e){
            logger('error sending notification : '. $e->getMessage());
        }
    }

    private function commentRateLimiterKey(): string
    {
        return 'comment:'.$this->post->id.':'.request()->ip();
    }

    private function ensureCommentingIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->commentRateLimiterKey(), 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->commentRateLimiterKey());

        throw ValidationException::withMessages([
            'body' => "Trop de commentaires envoyés. Réessayez dans {$seconds} secondes.",
        ]);
    }
}; ?>

<div>
    @if ($post)
        {{-- Hero --}}
        <section class="relative bg-zinc-950 py-20 sm:py-28 overflow-hidden">
            <div class="absolute inset-0">
                @if ($image = $post->featuredImageUrl())
                    <img src="{{ $image }}" alt="" class="h-full w-full object-cover opacity-20">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/80 to-zinc-950/60"></div>
            </div>
            <div class="absolute top-20 right-10 w-72 h-72 bg-primary/10 rounded-full blur-3xl"></div>

            <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
                <div class="animate-fade-in">
                    @if ($post->category)
                        <a href="{{ route('blogs.index', ['category' => $post->category->slug]) }}" wire:navigate
                            class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-semibold text-white transition-colors hover:opacity-80"
                            style="background-color: {{ $post->category->color }}">
                            {{ $post->category->name }}
                        </a>
                    @endif
                </div>

                <h1 class="mt-6 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white leading-tight animate-slide-up stagger-1">
                    {{ $post->title }}
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-zinc-300 max-w-2xl mx-auto animate-slide-up stagger-2">
                    {{ $post->excerpt }}
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-6 animate-slide-up stagger-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold text-white" style="background-color: {{ $post->category->color ?? '#737373' }}">
                            {{ strtoupper(mb_substr($post->author->name, 0, 2)) }}
                        </div>
                        <div class="text-left">
                            <p class="font-semibold text-white">{{ $post->author->name }}</p>
                            <p class="text-xs text-zinc-400">Auteur</p>
                        </div>
                    </div>
                    <span class="text-zinc-600">·</span>
                    <span class="text-sm text-zinc-400">{{ $post->published_at->translatedFormat('d M Y') }}</span>
                    <span class="text-zinc-600">·</span>
                    <span class="text-sm text-zinc-400">{{ $post->readingMinutes() }} min de lecture</span>
                    <span class="text-zinc-600">·</span>
                    <span class="text-sm text-zinc-400">{{ number_format($post->views_count) }} vue{{ $post->views_count > 1 ? 's' : '' }}</span>
                </div>

                @if ($post->tags->count())
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2 animate-slide-up stagger-4">
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('blogs.index') }}?tag={{ $tag->name }}" wire:navigate class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-zinc-300 border border-white/10">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Article Content --}}
        <section class="py-16 sm:py-20 bg-white dark:bg-zinc-950">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <article class="prose prose-lg prose-zinc dark:prose-invert max-w-none prose-headings:scroll-mt-20 prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-img:rounded-2xl">
                    {!! $post->body !!}
                </article>
            </div>
        </section>

        {{-- Related Posts --}}
        @if ($relatedPosts->count())
            <section class="py-16 sm:py-20 bg-zinc-50 dark:bg-zinc-900/50">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Articles similaires</h2>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($relatedPosts as $related)
                            <a href="{{ route('blogs.show', $related->slug) }}" wire:navigate
                                class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm hover-lift dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="aspect-[16/10] overflow-hidden">
                                    <img src="{{ $related->featuredImageUrl() }}" alt="{{ $related->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                </div>
                                <div class="flex flex-1 flex-col p-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        @if ($related->category)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background-color: {{ $related->category->color }}20; color: {{ $related->category->color }}">
                                                {{ $related->category->name }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-zinc-500">{{ $related->published_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">
                                        {{ $related->title }}
                                    </h3>
                                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2 flex-1">
                                        {{ $related->excerpt }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Comments --}}
        <section class="py-16 sm:py-20 bg-white dark:bg-zinc-950">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">
                    Commentaires
                    <span class="text-lg font-normal text-zinc-500 dark:text-zinc-400">({{ $commentCount }})</span>
                </h2>

                @if ($approvedComments->count())
                    <div class="mt-8 space-y-6">
                        @foreach ($approvedComments as $comment)
                            <div wire:key='com-{{ $comment->id }}'
                                id="comment-{{ $comment->id }}"
                                class="rounded-2xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-800 dark:bg-zinc-900"
                            >
                                <div class="flex items-start gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                                        {{ strtoupper(mb_substr($comment->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-semibold text-zinc-900 dark:text-white">{{ $comment->name }}</p>
                                            <span class="text-xs text-zinc-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $comment->body }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-4 border-t border-zinc-200 pt-4 sm:pl-14 dark:border-zinc-800">
                                    <button
                                        type="button"
                                        wire:click="startReply({{ $comment->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 transition-colors hover:text-primary dark:text-zinc-400 dark:hover:text-primary"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 6 6v3" />
                                        </svg>
                                        Répondre
                                    </button>

                                    @if ($comment->replies->count())
                                        <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                            {{ $comment->replies->count() }} réponse{{ $comment->replies->count() > 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Replies --}}
                            @if ($comment->replies->count())
                                <div class="space-y-4 sm:pl-14">
                                    @foreach ($comment->replies as $reply)
                                        <div wire:key='reply-{{ $reply->id }}'
                                            id="comment-{{ $reply->id }}"
                                            class="rounded-2xl border-l-2 border-primary/40 bg-zinc-50 p-5 dark:border-primary/30 dark:bg-zinc-900"
                                        >
                                            <div class="flex items-start gap-3">
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary">
                                                    {{ strtoupper(mb_substr($reply->name, 0, 2)) }}
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $reply->name }}</p>
                                                        <span class="text-xs text-zinc-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="mt-1.5 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $reply->body }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Inline Reply Form --}}
                            @if ($replyingTo === $comment->id)
                                <form
                                    wire:submit="saveReply"
                                    class="rounded-2xl border border-primary/30 bg-primary/5 p-5 sm:ml-14 dark:bg-primary/5"
                                >
                                    <div class="flex items-center justify-between gap-4">
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                            Répondre à <span class="text-primary">{{ $comment->name }}</span>
                                        </p>
                                        <button
                                            type="button"
                                            wire:click="cancelReply"
                                            class="text-xs font-medium text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                                        >
                                            Annuler
                                        </button>
                                    </div>

                                    <div class="mt-4 space-y-3">
                                        @if (! auth()->check())
                                            <div class="grid gap-3 sm:grid-cols-2">
                                                <div>
                                                    <label for="reply-name" class="sr-only">Nom</label>
                                                    <input
                                                        id="reply-name"
                                                        type="text"
                                                        wire:model="name"
                                                        autocomplete="name"
                                                        placeholder="Votre nom"
                                                        class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                                                    >
                                                    @error('name')
                                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label for="reply-email" class="sr-only">Email</label>
                                                    <input
                                                        id="reply-email"
                                                        type="email"
                                                        wire:model="email"
                                                        autocomplete="email"
                                                        placeholder="vous@exemple.com"
                                                        class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                                                    >
                                                    @error('email')
                                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        <div>
                                            <label for="reply-body" class="sr-only">Votre réponse</label>
                                            <textarea
                                                id="reply-body"
                                                wire:model="replyBody"
                                                rows="3"
                                                placeholder="Écrivez votre réponse..."
                                                class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                                            ></textarea>
                                            @error('replyBody')
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex justify-end">
                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition-all hover:bg-primary-dark"
                                            >
                                                Publier la réponse
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="mt-8 rounded-2xl border border-zinc-200 bg-zinc-50 p-12 text-center dark:border-zinc-800 dark:bg-zinc-900">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                    </div>
                @endif

                {{-- Comment Form --}}
                <div class="mt-10 rounded-2xl border border-zinc-200 bg-zinc-50 p-6 sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Laisser un commentaire</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Soyez courtois dans vos commentaires s'il vous plaît.
                    </p>

                    <form wire:submit="saveComment" class="mt-6 space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="comment-name" class="block text-sm font-semibold text-zinc-900 dark:text-white">
                                    Nom
                                </label>
                                <input
                                    id="comment-name"
                                    type="text"
                                    wire:model="name"
                                    autocomplete="name"
                                    required
                                    class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                                    placeholder="Votre nom"
                                >
                                @error('name')
                                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="comment-email" class="block text-sm font-semibold text-zinc-900 dark:text-white">
                                    Email
                                </label>
                                <input
                                    id="comment-email"
                                    type="email"
                                    wire:model="email"
                                    autocomplete="email"
                                    required
                                    class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                                    placeholder="vous@exemple.com"
                                >
                                @error('email')
                                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="comment-body" class="block text-sm font-semibold text-zinc-900 dark:text-white">
                                Commentaire
                            </label>
                            <textarea
                                id="comment-body"
                                wire:model="body"
                                rows="5"
                                required
                                class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder-zinc-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-400"
                                placeholder="Partagez votre avis..."
                            ></textarea>
                            @error('body')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                Votre email ne sera pas publié.
                            </p>
                            <button
                                type="submit"
                                class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/25 transition-all hover:bg-primary-dark hover:shadow-xl hover:shadow-primary/30"
                            >
                                Publier
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    @endif
</div>
