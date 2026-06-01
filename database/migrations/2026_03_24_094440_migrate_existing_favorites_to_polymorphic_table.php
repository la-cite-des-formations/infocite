<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Migration des favoris de Rubriques (rubric_user)
        DB::table('rubric_user')->orderBy('user_id')->chunk(100, function ($rows) {
            $favorites = [];
            foreach ($rows as $row) {
                $favorites[] = [
                    'user_id' => $row->user_id,
                    'favoriteable_id' => $row->rubric_id,
                    'favoriteable_type' => 'App\Models\Rubric',
                    'rank' => 0
                ];
            }
            DB::table('favorites')->insert($favorites);
        });

        // 2. Migration des favoris d'Applications (favorites_apps)
        if (Schema::hasTable('favorites_apps')) {
            DB::table('favorites_apps')->orderBy('user_id')->chunk(100, function ($rows) {
                $favorites = [];
                foreach ($rows as $row) {
                    $favorites[] = [
                        'user_id' => $row->user_id,
                        'favoriteable_id' => $row->app_id,
                        'favoriteable_type' => 'App\Models\App',
                        'rank' => $row->rank
                    ];
                }
                DB::table('favorites')->insert($favorites);
            });
        }

        // 3. Migration des favoris d'Articles (post_user)
        DB::table('post_user')->where('is_favorite', 1)->orderBy('user_id')->chunk(100, function ($rows) {
            $favorites = [];
            foreach ($rows as $row) {
                $favorites[] = [
                    'user_id' => $row->user_id,
                    'favoriteable_id' => $row->post_id,
                    'favoriteable_type' => 'App\Models\Post',
                    'rank' => 0
                ];
            }
            DB::table('favorites')->insert($favorites);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('favorites')->truncate();
    }
};
