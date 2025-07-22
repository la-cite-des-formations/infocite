<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['default_fcm_token_id']);
            $table->dropColumn([
                'birthday',
                'gender',
                'language',
                'status',
                'quality',
                'desktop_notifications_granted',
                'notify_only_favorites',
                'default_fcm_token_id',
            ]);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Colonnes extraites vers learners
            $table->date('birthday')->nullable();
            $table->enum('gender', ['F', 'M'])->nullable();
            $table->string('language', 2)->default('FR');
            $table->string('status')->nullable();
            $table->enum('quality', ['D', 'E', 'I'])->nullable();

            // Colonnes extraites vers employees
            $table->boolean('desktop_notifications_granted')->default(1)->after('is_staff');
            $table->boolean('notify_only_favorites')->default(0)->after('desktop_notifications_granted');
            $table->unsignedInteger('default_fcm_token_id')->nullable()->after('remember_token');
            $table->foreign('default_fcm_token_id')->references('id')->on('fcm_tokens')->onDelete('set null');
        });
    }
};
