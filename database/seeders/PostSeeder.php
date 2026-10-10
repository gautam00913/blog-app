<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Database\Factories\CommentFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'editor', 'author']))->get();
        $categories = Category::all();
        $tags = Tag::all();

        $posts = [
            [
                'title' => 'Les tendances du web en 2026 : ce qui va tout changer',
                'excerpt' => 'Découvrez les nouvelles technologies et frameworks qui vont dominer le développement web cette année.',
                'body' => $this->generateBody('Le web évolue à une vitesse fulgurante. En 2026, plusieurs tendances majeures s\'imposent et transforment la façon dont nous créons et interagissons avec les applications web.'),
                'category' => 'Technologie',
                'status' => 'published',
                'views' => 2450,
            ],
            [
                'title' => 'Guide complet Laravel 12 : tout ce que vous devez savoir',
                'excerpt' => 'Apprenez à maîtriser Laravel 12 avec ce guide complet couvrant les nouvelles fonctionnalités et best practices.',
                'body' => $this->generateBody('Laravel 12 apporte de nombreuses améliorations et nouvelles fonctionnalités qui rendent le framework encore plus puissant et agréable à utiliser.'),
                'category' => 'Technologie',
                'status' => 'published',
                'views' => 1820,
            ],
            [
                'title' => 'Créer des interfaces modernes avec Tailwind CSS',
                'excerpt' => 'Maîtrisez Tailwind CSS pour concevoir des interfaces utilisateur élégantes et responsives.',
                'body' => $this->generateBody('Tailwind CSS a révolutionné la façon dont nous styles nos applications web. Avec son approche utility-first, il permet de créer des designs uniques rapidement.'),
                'category' => 'Design',
                'status' => 'published',
                'views' => 1540,
            ],
            [
                'title' => 'Performance et optimisation PHP : les bonnes pratiques',
                'excerpt' => 'Les meilleures techniques pour améliorer la vitesse et l\'efficacité de vos applications PHP.',
                'body' => $this->generateBody('La performance est un aspect crucial de toute application web. En PHP, il existe de nombreuses techniques pour optimiser le code et améliorer les temps de réponse.'),
                'category' => 'Technologie',
                'status' => 'published',
                'views' => 980,
            ],
            [
                'title' => 'Sécuriser votre application Laravel : guide pratique',
                'excerpt' => 'Les bonnes pratiques de sécurité pour protéger votre application Laravel contre les attaques courantes.',
                'body' => $this->generateBody('La sécurité ne doit jamais être un après-pensée. Dans cet article, nous explorerons les meilleures pratiques pour sécuriser votre application Laravel.'),
                'category' => 'Sécurité',
                'status' => 'published',
                'views' => 1230,
            ],
            [
                'title' => 'Introduction à Docker pour les développeurs PHP',
                'excerpt' => 'Apprenez à utiliser Docker pour simplifier votre développement et votre déploiement.',
                'body' => $this->generateBody('Docker a transformé la façon dont nous développons et déployons nos applications. Pour les développeurs PHP, il offre une manière cohérente de gérer les environnements.'),
                'category' => 'DevOps',
                'status' => 'published',
                'views' => 870,
            ],
            [
                'title' => 'Les bases de JavaScript moderne en 2026',
                'excerpt' => 'Un tour d\'horizon des fonctionnalités modernes de JavaScript que tout développeur doit connaître.',
                'body' => $this->generateBody('JavaScript continue d\'évoluer avec de nouvelles fonctionnalités passionnantes. En 2026, le langage est plus puissant et plus agréable que jamais à utiliser.'),
                'category' => 'Technologie',
                'status' => 'published',
                'views' => 1650,
            ],
            [
                'title' => 'Productivité : les outils qui ont changé ma vie',
                'excerpt' => 'Découvrez les applications et techniques qui m\'ont aidé à devenir plus productif au quotidien.',
                'body' => $this->generateBody('La productivité est un sujet qui préoccupe beaucoup de développeurs. Voici les outils et méthodes qui ont transformé ma façon de travailler.'),
                'category' => 'Lifestyle',
                'status' => 'published',
                'views' => 720,
            ],
            [
                'title' => 'Construire une API RESTful avec Laravel',
                'excerpt' => 'Guide étape par étape pour créer une API RESTful robuste et bien documentée.',
                'body' => $this->generateBody('Les API RESTful sont au cœur de nombreuses applications modernes. Laravel facilite grandement leur création avec des outils puissants.'),
                'category' => 'Technologie',
                'status' => 'draft',
                'views' => 0,
            ],
            [
                'title' => 'L\'art du code clean : écrire du code maintenable',
                'excerpt' => 'Les principes et pratiques pour écrire du code propre, lisible et facile à maintenir.',
                'body' => $this->generateBody('Écrire du code propre est une compétence essentielle pour tout développeur professionnel. Le code lisible et bien structuré facilite la maintenance et la collaboration.'),
                'category' => 'Éducation',
                'status' => 'published',
                'views' => 1100,
            ],
            [
                'title' => 'React vs Vue.js en 2026 : le comparatif complet',
                'excerpt' => 'Une comparaison détaillée des deux frameworks JavaScript les plus populaires.',
                'body' => $this->generateBody('Le choix entre React et Vue.js reste l\'un des débats les plus fréquents dans la communauté JavaScript. Chacun a ses forces et ses faiblesses.'),
                'category' => 'Technologie',
                'status' => 'published',
                'views' => 2100,
            ],
            [
                'title' => 'Design system : créer et maintenir une bibliothèque de composants',
                'excerpt' => 'Comment construire un design system efficace pour votre équipe et vos projets.',
                'body' => $this->generateBody('Un design system bien conçu est un atout majeur pour les équipes produit. Il assure la cohérence visuelle et accélère le développement.'),
                'category' => 'Design',
                'status' => 'draft',
                'views' => 0,
            ],
        ];

        foreach ($posts as $postData) {
            $category = $categories->firstWhere('name', $postData['category']);
            $author = $users->random();

            $post = Post::create([
                'user_id' => $author->id,
                'category_id' => $category->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'body' => $postData['body'],
                'featured_image' => fake()->imageUrl(1200, 630, 'technology', true),
                'status' => $postData['status'],
                'published_at' => $postData['status'] === 'published' ? fake()->dateTimeBetween('-6 months', 'now') : null,
                'views_count' => $postData['views'],
            ]);

            // Assign random tags
            $post->tags()->attach($tags->random(rand(2, 4)));
        }

        // Create comments with nested replies for published posts
        $publishedPosts = Post::published()->get();

        foreach ($publishedPosts as $post) {
            $rootComments = collect();

            $commentCount = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $commentCount; $i++) {
                $comment = Comment::factory()
                    ->for($post)
                    ->when($i % 2 === 1, fn (CommentFactory $factory) => $factory->forUser($users->random()))
                    ->create(['approved' => fake()->boolean(80)]);

                if ($comment->approved) {
                    $rootComments->push($comment);
                }
            }

            foreach ($rootComments as $rootComment) {
                $replyCount = fake()->numberBetween(0, 3);

                for ($i = 0; $i < $replyCount; $i++) {
                    Comment::factory()
                        ->replyTo($rootComment)
                        ->when($i === 0, fn (CommentFactory $factory) => $factory->forUser($users->random()))
                        ->create(['approved' => fake()->boolean(85)]);
                }
            }
        }
    }

    private function generateBody(string $intro): string
    {
        $paragraphs = [$intro];
        $count = fake()->numberBetween(3, 6);

        for ($i = 0; $i < $count; $i++) {
            $paragraphs[] = fake()->paragraph(3);
        }

        return implode("\n\n", array_map(fn ($p) => '<p>'.$p.'</p>', $paragraphs));
    }
}
