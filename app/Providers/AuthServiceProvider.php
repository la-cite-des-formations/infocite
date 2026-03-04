<?php

namespace App\Providers;

use App\CustomFacades\AP;
use App\Models as Models;
use App\Policies as Policies;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Fournisseur de services gérant l'authentification et l'autorisation.
 * Déclare les Gate et fait le lien entre modèles et politiques (Policies) de sécurité.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les correspondances entre modèles et politiques (Policies) de l'application.
     *
     * @var array
     */
    protected $policies = [
        Models\App::class => Policies\AppPolicy::class,
        Models\Comment::class => Policies\CommentPolicy::class,
        Models\Post::class => Policies\PostPolicy::class,
        Models\Rubric::class => Policies\RubricPolicy::class,
        Models\Group::class => Policies\GroupPolicy::class,
    ];

    /**
     * Enregistre les services d'authentification et d'autorisation (Gates & Policies).
     * Définit les accès au tableau de bord et les droits de gestion des modèles.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if ($this->app->runningInConsole()) {
            return;
        }

        Gate::define('access-dashboard', function ($user, $dashboard = '') {
            foreach (AP::getModels($dashboard) as $model) {
                if ($user->can("manage-{$model}")) return TRUE;
            }
            return FALSE;
        });

        Gate::define('receiveDesktopNotifs', function ($user) {
            return $user->employee?->desktop_notifications_granted;
        });

        foreach(AP::getModels() as $model) {
            $right = AP::getModelRight($model);

            Gate::define("manage-{$model}", function ($user) use ($right) {
                if (isset($right->others)) {
                    foreach ($right->others as $otherRight) {
                        if ($user->hasStrictRole($otherRight['name'], $otherRight['roles'])) {
                            return TRUE;
                        }
                    }
                }

                return $user->hasRole($right->name, $right->roles);
            });
        }
    }
}
