<x-mail::message>
# Bienvenue sur {{ config('app.name', 'Blog') }}

Bonjour **{{ $user->name }}**,

Un compte a été créé pour vous sur **{{ config('app.name', 'Blog') }}**. Voici vos identifiants de connexion :

**Email :** {{ $user->email }}
**Mot de passe :** {{ $password }}

<x-mail::button :url="route('login')">
Se connecter
</x-mail::button>

Ce mot de passe a été généré pour vous. Nous vous recommandons de le modifier dès votre première connexion depuis votre espace **Paramètres > Mot de passe**.

Si vous avez des questions, n'hésitez pas à contacter l'équipe.

Cordialement,<br>
L'équipe {{ config('app.name', 'Blog') }}
</x-mail::message>
