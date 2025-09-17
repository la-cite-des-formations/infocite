<?php

namespace App\Statistics;

use App\Models\User;
use App\Models\Employee;
use App\Models\Interaction;

class Users
{
    public static function allRefuseDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', FALSE);
    }

    public static function allGrantAllDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', TRUE)
            ->where('notify_only_favorites', FALSE);
    }

    public static function allGrantOnlyFavoritesDesktopNotifications() {
        return Employee::query()
            ->where('desktop_notifications_granted', TRUE)
            ->where('notify_only_favorites', TRUE);
    }

    public static function activeEditors($filter = []) {
        extract($filter);
        $editorType = isset($editorType) ? $editorType : 'all';
        $rubric_id = isset($rubric_id) ? $rubric_id : NULL;

        return User::query()
            ->join('posts', function ($query) use ($editorType) {
                $query->when($editorType == 'all' || $editorType == 'authors', function ($join) {
                    $join->on('posts.author_id', '=', 'users.id');
                })->when($editorType == 'all' || $editorType == 'correctors', function ($join) {
                    $join->orOn('posts.corrector_id', '=', 'users.id');
                });
            })
            ->selectRaw('users.name, users.first_name, COUNT(*) AS posts_nb')
            ->when($rubric_id !== NULL, function ($query) use ($rubric_id) {
                $query->where('posts.rubric_id', $rubric_id);
            })
            ->groupByRaw('users.name, users.first_name')
            ->orderByRaw('posts_nb DESC, users.name, users.first_name');
    }

    public static function activeCommentators($filter = []) {
        extract($filter);
        $byStaff = !isset($commentatorType) || ($commentatorType == 'all') ? NULL : $commentatorType == 'staff';

        return User::query()
            ->join('comments', 'comments.user_id', '=', 'users.id')
            ->selectRaw('users.name, users.first_name, COUNT(*) AS comments_nb')
            ->when($byStaff !== NULL, function ($query) use ($byStaff) {
                $query->where('users.is_staff', $byStaff);
            })
            ->groupByRaw('users.name, users.first_name')
            ->orderByRaw('comments_nb DESC, users.name, users.first_name');
    }

    public static function personalAppsUsers($filter = []) {
        extract($filter);
        $byStaff = !isset($userType) || ($userType == 'all') ? NULL : $userType == 'staff';

        return User::query()
            ->join('apps', 'apps.owner_id', '=', 'users.id')
            ->selectRaw('users.name, users.first_name, COUNT(*) AS apps_nb')
            ->when($byStaff !== NULL, function ($query) use ($byStaff) {
                $query->where('users.is_staff', $byStaff);
            })
            ->groupByRaw('users.name, users.first_name')
            ->orderByRaw('apps_nb DESC, users.name, users.first_name');
    }
}
