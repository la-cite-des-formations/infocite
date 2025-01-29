<?php

namespace App\Http\Livewire\Modals\Admin\Comments;

use App\Models\Comment;
use Livewire\Component;

class Delete extends Component
{
    public $commentsIDs;
    public $deletionPerformed = FALSE;

    public function mount($commentsIDs) {
        $this->commentsIDs = $commentsIDs;
    }

    public function delete() {
        Comment::whereIn('id', $this->commentsIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->commentsIDs) > 1 ? 'Commentaires concernés' : 'Commentaire concerné',
            'models' => Comment::query()
                ->whereIn('id', $this->commentsIDs)
                ->orderByRaw('user_id, created_at DESC')
                ->get(),
            'modelInfo' => ['field' => 'content'],
        ]);
    }
}
