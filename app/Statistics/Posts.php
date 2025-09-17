<?php

namespace App\Statistics;

use App\Models\Post;
use App\Models\Interaction;
use Illuminate\Support\Facades\DB;

class Posts
{
    public static function allViewed($filter = []) {
        extract($filter);
        $byStaff = !isset($readerType) || ($readerType == 'all') ? NULL : $readerType == 'staff';
        $rubric_id = isset($rubric_id) ? $rubric_id : NULL;

        return DB::table('posts')
            ->join('rubrics', 'posts.rubric_id', '=', 'rubrics.id')
            ->join('post_user', 'posts.id', '=', 'post_user.post_id')
            ->selectRaw('posts.title AS title, rubrics.name AS rubric, COUNT(*) AS views_nb')
            ->when($byStaff !== NULL, function ($query) use ($byStaff) {
                $query
                    ->join('users', 'users.id', '=', 'post_user.user_id')
                    ->where('users.is_staff', $byStaff);
            })
            ->when($rubric_id !== NULL, function ($query) use ($rubric_id) {
                $query->where('rubrics.id', $rubric_id);
            })
            ->where('post_user.is_read', TRUE)
            ->groupBy('title', 'rubric')
            ->orderByRaw('views_nb desc, rubric, title');
    }

    public static function allCommented($filter = []) {
        extract($filter);
        $byStaff = !isset($readerType) || ($readerType == 'all') ? NULL : $readerType == 'staff';
        $rubric_id = isset($rubric_id) ? $rubric_id : NULL;

        return DB::table('posts')
            ->join('rubrics', 'posts.rubric_id', '=', 'rubrics.id')
            ->join('comments', 'posts.id', '=', 'comments.post_id')
            ->selectRaw('posts.title AS title, rubrics.name AS rubric, COUNT(*) AS comments_nb')
            ->when($byStaff !== NULL, function ($query) use ($byStaff) {
                $query
                    ->join('users', 'users.id', '=', 'comments.user_id')
                    ->where('users.is_staff', $byStaff);
            })
            ->when($rubric_id !== NULL, function ($query) use ($rubric_id) {
                $query->where('rubrics.id', $rubric_id);
            })
            ->groupBy('title', 'rubric')
            ->orderByRaw('comments_nb desc, rubric, title');
    }
}
