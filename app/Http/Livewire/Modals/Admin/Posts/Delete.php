<?php

namespace App\Http\Livewire\Modals\Admin\Posts;

use App\Post;
use App\Right;
use Livewire\Component;

class Delete extends Component
{
    public $postsIDs;
    public $deletionPerformed = FALSE;

    public function mount($postsIDs) {
        $this->postsIDs = $postsIDs;
    }

    public function delete() {
        Right::each(function ($right) {
            $right
                ->users()
                ->newPivotQuery()
                ->where('resource_type', 'Post')
                ->whereIn('resource_id', $this->postsIDs)
                ->delete();

            $right
                ->groups()
                ->newPivotQuery()
                ->where('resource_type', 'Post')
                ->whereIn('resource_id', $this->postsIDs)
                ->delete();
        });
        Post::whereIn('id', $this->postsIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->postsIDs) > 1 ? 'Contenus concernés' : 'Contenu concerné',
            'models' => Post::query()
                ->whereIn('id', $this->postsIDs)
                ->orderByRaw('rubric_id, title ASC')
                ->get(),
            'modelInfo' => ['field' => 'title'],
        ]);
    }
}
