<?php

namespace App\Console\Commands;

use App\Models\Notification as PostNotification;
use App\Models\Rubric;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

/**
 * Commande trimestrielle de sensibilisation à l'usage des modèles d'articles.
 * Envoie un rappel pédagogique (interne + push Firebase FCM) à l'ensemble des rédacteurs
 * pour les inciter à utiliser les templates lors de la rédaction ou révision de contenus.
 */
class RemindArticleTemplates extends Command
{
    /**
     * Le nom et la signature de la commande.
     *
     * @var string
     */
    protected $signature = 'posts:remind-templates';

    /**
     * La description de la commande console.
     *
     * @var string
     */
    protected $description = 'Envoie un rappel trimestriel incitant les rédacteurs à utiliser les modèles d\'articles.';

    /**
     * Exécute la commande.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Début de la campagne trimestrielle de rappel des modèles d\'articles...');

        // 1. Cibler l'ensemble des rédacteurs (utilisateurs habilités ou ayant déjà rédigé)
        $editors = User::allWho('can-edit-posts')
            ->merge(User::allWho('have-edited-posts'))
            ->unique('id');

        if ($editors->isEmpty()) {
            $this->info('Aucun rédacteur identifié. Opération terminée.');
            return 0;
        }

        $this->info("{$editors->count()} rédacteur(s) ciblé(s).");

        $uneRubric = Rubric::firstWhere('segment', 'une') ?? Rubric::first();
        $bodyMessage = "Pensez aux modèles d'articles ! Pour faciliter votre rédaction et harmoniser la présentation de vos contenus sur l'intranet, profitez des modèles prédéfinis mis à votre disposition lors de la création ou de la révision de vos articles.";

        // 2. Création de la notification système interne (type 'DF')
        $notification = PostNotification::create([
            'content_type' => 'DF',
            'object_type'  => Rubric::class,
            'object_id'    => $uneRubric ? $uneRubric->id : 1,
            'release_at'   => now(),
        ]);

        // Attachement à chaque rédacteur
        $notification->users()->syncWithoutDetaching($editors->pluck('id'));

        // 3. Envoi du push Firebase FCM (actif si configuré en production)
        Notification::send($editors, new AppNotification([
            'type'  => 'DF',
            'title' => config('app.name') . " — Modèles d'articles",
            'body'  => $bodyMessage,
            'url'   => route('templates.reminder'),
        ]));

        $this->info("Rappel envoyé avec succès à {$editors->count()} rédacteur(s).");

        return 0;
    }
}
