<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {}; ?>

<div>
    {{-- Hero --}}
    <section class="bg-zinc-950 py-20 sm:py-28">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-sm font-semibold tracking-wider uppercase text-primary">Conditions</span>
            <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight text-white">Conditions Générales d'Utilisation</h1>
            <p class="mt-4 text-lg text-zinc-400">Les règles encadrant l'utilisation du site.</p>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16 sm:py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 prose prose-zinc dark:prose-invert prose-headings:font-bold prose-headings:text-zinc-900 dark:prose-headings:text-white prose-p:text-zinc-600 dark:prose-p:text-zinc-400 prose-a:text-primary hover:prose-a:text-primary-dark">
            <h2>1. Objet</h2>
            <p>
                Les présentes Conditions Générales d'Utilisation (CGU) ont pour objet de définir les modalités et conditions dans lesquelles {{ config('app.name', 'Blog') }} met à disposition de ses utilisateurs le site et ses services, ainsi que de définir les droits et obligations des parties dans ce cadre.
            </p>

            <h2>2. Acceptation des CGU</h2>
            <p>
                L'utilisation du site implique l'acceptation pleine et entière des présentes CGU. Tout utilisateur qui n'accepte pas ces conditions est invité à ne pas utiliser le site.
            </p>

            <h2>3. Inscription</h2>
            <p>
                L'inscription au site est ouverte à toute personne physique majeure ou émancipée. L'utilisateur s'engage à fournir des informations exactes lors de son inscription et à les mettre à jour en cas de modification.
            </p>
            <p>
                L'utilisateur est responsable de la confidentialité de ses identifiants de connexion. En cas de suspicion d'accès non autorisé, il doit en informer immédiatement l'administration.
            </p>

            <h2>4. Contenu utilisateur</h2>
            <p>
                Les utilisateurs peuvent publier des commentaires sur le site. En publiant du contenu, l'utilisateur garantit qu'il :
            </p>
            <ul>
                <li>En est l'auteur ou dispose des droits nécessaires.</li>
                <li>Ne contrevient à aucun droit de tiers.</li>
                <li>Ne contient pas de contenu illicite, diffamatoire ou injurieux.</li>
            </ul>
            <p>
                {{ config('app.name', 'Blog') }} se réserve le droit de supprimer tout contenu sans préavis en cas de violation de ces conditions.
            </p>

            <h2>5. Propriété intellectuelle</h2>
            <p>
                Le contenu publié par {{ config('app.name', 'Blog') }} (articles, images, etc.) est protégé par le droit d'auteur. Toute reproduction non autorisée est interdite.
            </p>

            <h2>6. Responsabilité</h2>
            <p>
                {{ config('app.name', 'Blog') }} s'efforce de fournir des informations fiables et à jour. Cependant, il ne peut garantir l'exactitude, la complétude ou l'actualité du contenu publié.
            </p>
            <p>
                Le site ne saurait être tenu responsable des dommages résultant de l'utilisation du site ou du contenu publié par les utilisateurs.
            </p>

            <h2>7. Modification des CGU</h2>
            <p>
                {{ config('app.name', 'Blog') }} se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés de toute modification substantielle.
            </p>

            <h2>8. Droit applicable</h2>
            <p>
                Les présentes CGU sont soumises au droit français. En cas de litige, les tribunaux compétents seront ceux du ressort du siège social de {{ config('app.name', 'Blog') }}.
            </p>

            <h2>9. Contact</h2>
            <p>
                Pour toute question relative aux CGU, contactez-nous à <a href="mailto:hello@blog.com">hello@blog.com</a>.
            </p>
        </div>
    </section>
</div>
