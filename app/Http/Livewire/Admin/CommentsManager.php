<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Rubric;
use App\Models\User;

class CommentsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    public $models = 'comments';
    public $elements = 'comments';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    public $filter = [
        'searchOnly' => FALSE,
        'search' => '',
        'authorId' => '',
        'rubricId' => '',
        'postId' => '',
    ];

    public $perPageOptions = [10, 15, 25];
    public $perPage = 10;

    public function render()
    {
        return view('livewire.admin.models-manager', [
            'comments' => Comment::filter($this->filter)
                ->orderByRaw('user_id, created_at DESC')
                ->paginate($this->perPage),
            'authors' => User::allWho('have-commented-posts'),
            'rubrics' => Rubric::allWithPosts(),
            'posts' => Post::allCommentable()
                ->when($this->filter['rubricId'], function ($query) {
                    $query->where('rubric_id', $this->filter['rubricId']);
                })
                ->get(),
            ]);
    }
}
