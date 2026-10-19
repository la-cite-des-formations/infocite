<?php

namespace App\Console\Commands;

use App\Models\Notification as PostNotification;
use App\Models\Post;
use App\Models\Roles;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Commande hebdomadaire de détection des articles obsolètes et d'envoi d'alertes.
 * Identifie les articles inactifs depuis plus d'un an, regroupe les responsables
 * (auteur, correcteur ou gestionnaire pour les articles orphelins) et leur envoie
 * une notification unique (interne + push Firebase FCM) pour les inviter à arbitrer
 * dans la section "Mes articles à réviser" de la page Mes infos.
 */
class CheckObsoletePosts extends Command
{
    /**
     * Le nom et la signature de la commande.
     *
     * @var string
     */
    protected $signature = 'posts:check-obsolescence';

    /**
     * La description de la commande console.
     *
     * @var string
     */
    protected $description = 'Détecte les articles inactifs depuis plus d\'un an et notifie leurs responsables pour arbitrage.';

    /**
     * Exécute la commande.
     * 1. Récupère tous les articles nécessitant une révision.
     * 2. Construit la carte utilisateurs → articles dont ils sont responsables.
     * 3. Envoie une notification unique par utilisateur (anti-doublon).
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Vérification des articles obsolètes...');

        $obsoletePosts = Post::needsReview()->with(['rubric'])->get();

        if ($obsoletePosts->isEmpty()) {
            $this->info('Aucun article à réviser. Opération terminée.');
            return 0;
        }

        $this->info("{$obsoletePosts->count()} article(s) à réviser détecté(s).");

        // Construction du mapping utilisateur → articles dont il est responsable
        $userIds = $this->buildUserMap($obsoletePosts);

        if ($userIds->isEmpty()) {
            $this->info('Aucun responsable identifié. Opération terminée.');
            return 0;
        }

        $users = User::whereIn('id', $userIds->keys())->get()->keyBy('id');

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($userIds as $userId => $postCount) {
            $user = $users->get($userId);
            if (!$user) {
                $bar->advance();
                continue;
            }

            $this->notifyUser($user, $postCount);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Notifications envoyées à {$users->count()} utilisateur(s).");

        return 0;
    }

    /**
     * Construit la carte userId → nombre d'articles à réviser.
     * Gère 3 cas : auteur renseigné, correcteur seul renseigné, orphelin total.
     *
     * @param  \Illuminate\Support\Collection  $posts
     * @return \Illuminate\Support\Collection  [userId => count]
     */
    protected function buildUserMap(Collection $posts): Collection
    {
        $map = collect();

        // Bitmask des droits complets : Éditeur + Modérateur + Administrateur
        $fullRolesMask = Roles::IS_EDITR | Roles::IS_MODER | Roles::IS_ADMIN;

        // Pré-calcul des gestionnaires pour les rubriques d'articles orphelins (pour éviter les boucles imbriquées coûteuses)
        $orphanRubricIds = $posts->whereNull('author_id')->whereNull('corrector_id')->pluck('rubric_id')->filter()->unique();
        $managersByRubric = [];

        if ($orphanRubricIds->isNotEmpty()) {
            $realUsers = User::where('name', '<>', \App\CustomFacades\AP::PROFILE)->get();
            foreach ($orphanRubricIds as $rubricId) {
                $managersByRubric[$rubricId] = $realUsers->filter(
                    fn($u) => $u->hasStrictRole('posts', $fullRolesMask, 'Rubric', $rubricId)
                )->pluck('id');
            }
        }

        foreach ($posts as $post) {
            if ($post->author_id) {
                // Cas 1 : auteur renseigné
                $map[$post->author_id] = ($map[$post->author_id] ?? 0) + 1;
            } elseif ($post->corrector_id) {
                // Cas 2 : correcteur seul renseigné, auteur absent
                $map[$post->corrector_id] = ($map[$post->corrector_id] ?? 0) + 1;
            } else {
                // Cas 3 : article complètement orphelin
                // → notifier les utilisateurs réels ayant les droits complets sur la rubrique
                if (!$post->rubric_id || empty($managersByRubric[$post->rubric_id])) continue;

                foreach ($managersByRubric[$post->rubric_id] as $managerId) {
                    $map[$managerId] = ($map[$managerId] ?? 0) + 1;
                }
            }
        }

        return $map;
    }

    /**
     * Envoie ou rafraîchit la notification RP pour un utilisateur.
     * Anti-doublon : si une notification RP non lue existe déjà, on rafraîchit
     * simplement sa date release_at sans créer de doublon.
     *
     * @param  \App\Models\User  $user
     * @param  int  $postCount  Nombre d'articles à réviser pour cet utilisateur
     * @return void
     */
    protected function notifyUser(User $user, int $postCount): void
    {
        // Vérifier si une notification RP non lue existe déjà pour cet utilisateur
        $existingNotification = $user->newNotifications()
            ->where('content_type', 'RP')
            ->first();

        if ($existingNotification) {
            // Anti-spam : rafraîchir la date de diffusion seulement
            $existingNotification->update(['release_at' => now()]);
            return;
        }

        // Créer une nouvelle notification interne RP
        $notification = PostNotification::create([
            'content_type' => 'RP',
            'object_type'  => User::class,
            'object_id'    => $user->id,
            'release_at'   => now(),
        ]);
        $notification->users()->attach($user->id);

        // Envoi du push Firebase FCM (actif uniquement si firebase.enabled = true)
        $suffix = $postCount > 1
            ? "{$postCount} articles nécessitent votre arbitrage"
            : "1 article nécessite votre arbitrage";

        Notification::send($user, new AppNotification([
            'type'  => 'RP',
            'title' => config('app.name') . ' — Fraîcheur des contenus',
            'body'  => $suffix . " dans votre espace « Mes infos ».",
        ]));
    }
}
