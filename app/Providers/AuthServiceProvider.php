<?php

namespace App\Providers;

use App\CustomFacades\AP;
use App\Models as Models;
use App\Policies as Policies;
use App\Models\Roles;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
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
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-dashboard', function ($user, $dashboard = '') {
            foreach (AP::getModels($dashboard) as $model) {
                if ($user->can("manage-{$model}")) return TRUE;
            }
            return FALSE;
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
