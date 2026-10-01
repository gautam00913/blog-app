<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {}; ?>

<div>
    {{-- Hero --}}
    <section class="bg-zinc-950 py-20 sm:py-28">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-sm font-semibold tracking-wider uppercase text-primary">Confidentialité</span>
            <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight text-white">Politique de confidentialité</h1>
            <p class="mt-4 text-lg text-zinc-400">Comment nous protégeons vos données.</p>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16 sm:py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 prose prose-zinc dark:prose-invert prose-headings:font-bold prose-headings:text-zinc-900 dark:prose-headings:text-white prose-p:text-zinc-600 dark:prose-p:text-zinc-400 prose-a:text-primary hover:prose-a:text-primary-dark">
            <h2>1. Collecte des données</h2>
            <p>
                Nous collectons les données suivantes lorsque vous utilisez {{ config('app.name', 'Blog') }} :
            </p>
            <ul>
                <li><strong>Données d'inscription :</strong> nom, adresse email, mot de passe (hashé).</li>
                <li><strong>Données de navigation :</strong> adresse IP, type de navigateur, pages visitées, durée de la visite.</li>
                <li><strong>Données de commentaire :</strong> contenu du commentaire, date et heure de publication.</li>
            </ul>

            <h2>2. Utilisation des données</h2>
            <p>
                Vos données sont utilisées pour :
            </p>
            <ul>
                <li>La gestion de votre compte utilisateur.</li>
                <li>L'envoi de notifications liées à votre activité (nouveaux commentaires, changement de rôle, etc.).</li>
                <li>L'amélioration de nos services et de l'expérience utilisateur.</li>
                <li>La prévention des abus et la sécurité du site.</li>
            </ul>

            <h2>3. Partage des données</h2>
            <p>
                Nous ne vendons pas vos données personnelles. Vos données peuvent être partagées uniquement dans les cas suivants :
            </p>
            <ul>
                <li>Avec votre consentement explicite.</li>
                <li>Pour répondre à une obligation légale.</li>
                <li>Avec nos prestataires techniques (hébergeur, service d'envoi d'emails) dans le strict cadre de leurs missions.</li>
            </ul>

            <h2>4. Cookies</h2>
            <p>
                Ce site utilise des cookies essentiels au fonctionnement du site (session, préférences). Aucun cookie de tracking ou publicitaire n'est utilisé sans votre consentement.
            </p>

            <h2>5. Vos droits</h2>
            <p>
                Conformément au RGPD, vous disposez des droits suivants :
            </p>
            <ul>
                <li><strong>Droit d'accès :</strong> obtenir une copie de vos données personnelles.</li>
                <li><strong>Droit de rectification :</strong> corriger des données inexactes.</li>
                <li><strong>Droit à l'effacement :</strong> demander la suppression de vos données.</li>
                <li><strong>Droit à la portabilité :</strong> recevoir vos données dans un format structuré.</li>
                <li><strong>Droit d'opposition :</strong> vous opposer au traitement de vos données.</li>
            </ul>
            <p>
                Pour exercer ces droits, contactez-nous à <a href="mailto:hello@blog.com">hello@blog.com</a>.
            </p>

            <h2>6. Sécurité</h2>
            <p>
                Nous mettons en œuvre les mesures techniques et organisationnelles nécessaires pour protéger vos données contre tout accès non autorisé, perte ou altération.
            </p>

            <h2>7. Modifications</h2>
            <p>
                Cette politique de confidentialité peut être mise à jour à tout moment. La date de dernière mise à jour est indiquée ci-dessous.
            </p>
            <p><em>Dernière mise à jour : {{ now()->format('d/m/Y') }}</em></p>
        </div>
    </section>
</div>
