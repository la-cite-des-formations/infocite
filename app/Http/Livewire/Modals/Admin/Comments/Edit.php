<?php

namespace App\Http\Livewire\Modals\Admin\Comments;

use App\Comment;
use Livewire\Component;

class Edit extends Component
{
    public $comment;

    public function setComment($id = NULL) {
        $this->comment = $this->comment ?? Comment::findOrNew($id);
    }

    public function mount($data) {
        extract($data);

        $this->setComment($id ?? NULL);
    }


    public function render(){
        return view('livewire.modals.admin.comments.sheet');
    }
}
