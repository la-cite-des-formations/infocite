<?php

namespace App\Http\Livewire\Modals\Admin\Rubrics;

use App\Models\Right;
use App\Models\Rubric;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de suppression de rubriques.
 */
class Delete extends Component
{
    /**
     * Liste des identifiants des rubriques à supprimer.
     *
     * @var array
     */
    public $rubricsIDs;

    /**
     * Indique si la suppression a été effectuée.
     *
     * @var bool
     */
    public $deletionPerformed = FALSE;

    /**
     * Message d'alerte en cas d'erreur lors de la suppression.
     *
     * @var string
     */
    public $alertMessage = '';

    /**
     * Initialisation du composant.
     *
     * @param array $rubricsIDs Identifiants des rubriques à supprimer.
     */
    public function mount($rubricsIDs) {
        $this->rubricsIDs = $rubricsIDs;
    }

    /**
     * Supprime les rubriques sélectionnées si elles ne contiennent pas d'articles.
     */
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

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
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
