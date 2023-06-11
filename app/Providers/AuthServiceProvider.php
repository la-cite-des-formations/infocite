<?php

namespace App\Providers;

use App\CustomFacades\AP;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Right;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        App::class => AppPolicy::class,
        Comment::class => CommentPolicy::class,
        Post::class => PostPolicy::class,
        Rubric::class => RubricPolicy::class,
        Group::class => GroupPolicy::class,
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
            $right = Right::where('name', AP::getModelRight($model))->first();

            Gate::define("manage-{$model}", function ($user) use ($right) {
                return is_object($right) ? $user->hasRole($right->name, $right->dashboard_roles) : FALSE;
            });
        }
    }
}
