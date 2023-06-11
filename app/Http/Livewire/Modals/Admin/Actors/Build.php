<?php

namespace App\Http\Livewire\Modals\Admin\Actors;

use App\Actor;
use App\User;
use Livewire\Component;
use App\Http\Livewire\WithAlert;
use Illuminate\Support\Collection;

class Build extends Component
{
    use WithAlert;

    public $autoGenerationPerformed = FALSE;
    public $employees;
    public $filter;

    protected $listeners = ['render'];

    public function mount($selectionIds, $filter) {
        $this->employees = User::query()
            ->whereIn('id', $selectionIds)
            ->orderByRaw('name ASC, first_name ASC')
            ->get();

        $this->filter = $filter;
    }

    public function build($filteredActors = FALSE) {
        if($filteredActors == 'all') {
            $this->employees = User::filter($this->filter)->get();
        }
        $alerts = [
            'parent' => (object) [
                'enable' => FALSE,
                'message' =>
                   "<p>Traitement terminé. Les liens de certains responsables n'ont pu être générés car les processus parents ".
                   "dont ils dépendent n'ont pas été définis.</p>
                    <p>Utiliser le formulaire de gestion des processus pour les définir.</p>
                    <dt>Processus concernés :</dt>",
                'items' => new Collection(),
            ],
            'processes' => (object) [
                'enable' => FALSE,
                'message' =>
                   "<p>Traitement terminé. Certains liens n'ont pu être générés car plusieurs choix sont possibles.</p>
                    <p>Utiliser le bouton modifier des lignes concernées pour les définir.</p>
                    <p>Acteurs concernés :</p>",
                'items' => new Collection(),
            ],
            'manager' => (object) [
                'enable' => FALSE,
                'message' =>
                   "<p>Traitement terminé. Certains liens n'ont pu être générés car aucun responsable n'est défini ".
                   "pour le processus fonctionnel de rattachement de l'acteur ou pour son processus parent.</p>
                    <p>Utiliser le formulaire de gestion des processus pour définir les responsables manquants.</p>
                    <p>Processus concernés :</p>",
                'items' => new Collection(),
            ],
        ];

        foreach($this->employees as $employee) {
            if ($employee->processes()->count() === 1) {
                $process = $employee->processes()->first();

                if (is_object($process->manager)) {
                    // le manager du processus est défini
                    if ($employee->id !== $process->manager_id) {
                        // l'acteur n'est pas le manager du processus auquel il est rattaché :
                        // son manager correspond donc au manager de ce processus
                        $actor = Actor::findOrNew($employee->id);

                        $actor->id = $actor->id ?? $employee->id;
                        $actor->manager_id = $process->manager_id;

                        $actor->save();
                    }
                    elseif($process->parent_id) {
                        // l'acteur est le manager du processus auquel il est rattaché :
                        // son manager correspond donc au manager du processus parent existant
                        if (is_object($process->parent->manager)) {
                            // le manager du processus parent est défini
                            $actor = Actor::findOrNew($employee->id);

                            $actor->id = $actor->id ?? $employee->id;
                            $actor->manager_id = $process->parent->manager_id;

                            $actor->save();
                        }
                        else {
                            // manager non défini pour le processus concerné
                            $alerts['manager']->enable = TRUE;
                            $alerts['manager']->items->add($process->parent->name);
                        }
                    }
                    else {
                        // processus parent non défini
                        $alerts['parent']->enable = TRUE;
                        $alerts['parent']->items->add($process->name);
                    }
                }
                else {
                    // manager non défini pour le processus concerné
                    $alerts['manager']->enable = TRUE;
                    $alerts['manager']->items->add($process->name);
                }
            }
            elseif ($employee->processes()->count()) {
                // plusieurs choix sont possibles
                $alerts['processes']->enable = TRUE;
                $alerts['processes']->items->add($employee->identity());
            }
        }

        $this->autoGenerationPerformed = TRUE;

        $failed = FALSE;

        foreach($alerts as $alert) {
            if ($alert->enable) {
                $failed = TRUE;

                $this->sendAlert([
                    'alertClass' => 'danger',
                    'message' =>
                        $alert->message.
                        "<ul>
                            <li>".$alert->items->unique()->implode('</li>
                            <li>')."</li>
                        </ul>",
                ]);
            }
        }

        if (!$failed) {
            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Génération automatique des liens hiérarchiques terminée avec succès.",
            ]);
        }
    }

    public function render() {
        return view('livewire.modals.admin.actors.build');
    }
}
