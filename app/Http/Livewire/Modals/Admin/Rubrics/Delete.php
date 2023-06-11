<?php

namespace App\Http\Livewire\Modals\Admin\Rubrics;

use App\Right;
use App\Rubric;
use Livewire\Component;

class Delete extends Component
{
    public $rubricsIDs;
    public $deletionPerformed = FALSE;
    public $alertMessage = '';

    public function mount($rubricsIDs) {
        $this->rubricsIDs = $rubricsIDs;
    }

    public function delete() {
        foreach(Rubric::query()
            ->whereIn('id', $this->rubricsIDs)
            ->get() as $rubric) {
                if (!$rubric->havePosts()) {
                    Right::each(function ($right) use($rubric) {
                        $right
                            ->users()
                            ->newPivotQuery()
                            ->where('resource_type', 'Rubric')
                            ->where('resource_id', $rubric->id)
                            ->delete();

                        $right
                            ->groups()
                            ->newPivotQuery()
                            ->where('resource_type', 'Rubric')
                            ->where('resource_id', $rubric->id)
                            ->delete();
                    });
                    $rubric->delete();
                }
                else {
                    $this->alertMessage .= "<p>Suppression impossible pour la rubrique '{$rubric->name}' car elle contient des articles !</p>\n";
                }
            }

        if (!$this->alertMessage) {
            $this->deletionPerformed = TRUE;
        }
    }

    public function render()
    {
        if ($this->alertMessage) {
            session()->flash('alertClass', 'warning');
            session()->flash('message', $this->alertMessage);
        }

        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->rubricsIDs) > 1 ? 'Rubriques concernées' : 'Rubrique concernée',
            'models' => Rubric::query()
                ->whereIn('id', $this->rubricsIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
