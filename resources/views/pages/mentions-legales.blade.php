<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {}; ?>

<div>
    {{-- Hero --}}
    <section class="bg-zinc-950 py-20 sm:py-28">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-sm font-semibold tracking-wider uppercase text-primary">Légal</span>
            <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight text-white">Mentions légales</h1>
            <p class="mt-4 text-lg text-zinc-400">Informations légales relatives au site.</p>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16 sm:py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 prose prose-zinc dark:prose-invert prose-headings:font-bold prose-headings:text-zinc-900 dark:prose-headings:text-white prose-p:text-zinc-600 dark:prose-p:text-zinc-400 prose-a:text-primary hover:prose-a:text-primary-dark">
            <h2>1. Éditeur du site</h2>
            <p>
                Le site {{ config('app.name', 'Blog') }} est édité par :
            </p>
            <ul>
                <li><strong>Raison sociale :</strong> {{ config('app.name', 'Blog') }}</li>
                <li><strong>Adresse :</strong> Paris, France</li>
                <li><strong>Email :</strong> <a href="mailto:hello@blog.com">hello@blog.com</a></li>
            </ul>

            <h2>2. Directeur de la publication</h2>
            <p>
                Le directeur de la publication est le représentant légal de {{ config('app.name', 'Blog') }}.
            </p>

            <h2>3. Hébergeur</h2>
            <p>
                Ce site est hébergé par :
            </p>
            <ul>
                <li><strong>Hébergeur :</strong> Laravel Cloud</li>
                <li><strong>Site web :</strong> <a href="https://cloud.laravel.com" target="_blank" rel="noopener">cloud.laravel.com</a></li>
            </ul>

            <h2>4. Propriété intellectuelle</h2>
            <p>
                L'ensemble du contenu de ce site (textes, images, graphismes, logo, icônes, sons, logiciels) est la propriété exclusive de {{ config('app.name', 'Blog') }} ou de ses partenaires et est protégé par les lois françaises et internationales relatives à la propriété intellectuelle.
            </p>
            <p>
                Toute reproduction totale ou partielle de ce contenu est strictement interdite sans autorisation préalable écrite.
            </p>

            <h2>5. Données personnelles</h2>
            <p>
                Pour en savoir plus sur la gestion de vos données personnelles, veuillez consulter notre <a href="{{ route('confidentialite') }}">politique de confidentialité</a>.
            </p>

            <h2>6. Contact</h2>
            <p>
                Pour toute question relative aux mentions légales, vous pouvez nous contacter à l'adresse suivante : <a href="mailto:hello@blog.com">hello@blog.com</a>.
            </p>
        </div>
    </section>
</div>
