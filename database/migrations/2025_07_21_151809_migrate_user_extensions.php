<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                // Learner
                if (!$user->is_staff) {
                    DB::table('learners')->insert([
                        'user_id' => $user->id,
                        'birthday' => $user->birthday ?: NULL,
                        'gender' => $user->gender ?: NULL,
                        'language' => $user->language,
                        'status' => $user->status ?: NULL,
                        'quality' => $user->quality ?: NULL,
                    ]);
                }
                // Employee
                if ($user->is_staff) {
                    DB::table('employees')->insert([
                        'user_id' => $user->id,
                        'desktop_notifications_granted' => $user->desktop_notifications_granted,
                        'notify_only_favorites' => $user->notify_only_favorites,
                        'default_fcm_token_id' => $user->default_fcm_token_id,
                        'position' => NULL,
                        'building' => NULL,
                        'office' => NULL,
                    ]);
                }
            }
        });
    }

    public function down()
    {
        DB::table('learners')->truncate();
        DB::table('employees')->truncate();
    }
};
