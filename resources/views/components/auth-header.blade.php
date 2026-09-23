@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-3 text-center">
    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">{{ $title }}</h1>
    <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">{{ $description }}</p>
</div>
