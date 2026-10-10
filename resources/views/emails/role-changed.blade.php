<x-mail::message>
# Votre rôle a été mis à jour

Bonjour **{{ $user->name }}**,

Votre rôle sur **{{ config('app.name', 'Blog') }}** a été modifié par un administrateur.

**Ancien rôle :** {{ match($oldRole) {
                'admin' => 'Administrateur',
                'editor' => 'Editeur',
                'author' => 'Auteur',
                'subscriber' => 'Abonné',
                default => 'Auncun',
            } }}
**Nouveau rôle :** {{ match($newRole) {
                'admin' => 'Administrateur',
                'editor' => 'Editeur',
                'author' => 'Auteur',
                'subscriber' => 'Abonné',
                default => 'Aucun',
            } }}

<x-mail::button :url="route('dashboard')">
Accéder au tableau de bord
</x-mail::button>

Si vous pensez qu'il s'agit d'une erreur, veuillez contacter un administrateur.

Cordialement,<br>
L'équipe {{ config('app.name', 'Blog') }}
</x-mail::message>
