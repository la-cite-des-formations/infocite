<?php

namespace App\Http\Livewire;

/**
 * Trait pour la gestion et l'affichage des fenêtres modales via un ModalManager centralisé.
 */
trait WithModal
{
    /**
     * Indique si l'ajout est autorisé.
     *
     * @var bool
     */
    public $canAdd = TRUE;

    /**
     * Callback appelé lors de la fermeture d'une modale.
     */
    public function modalClosed() {
        if (isset($this->closedModalCallback)) {
            foreach($this->closedModalCallback as $function) {
                $this->$function();
            }
        }
        $this->emit('render')->self();
    }

    /**
     * Affiche une modale spécifique avec des données.
     *
     * @param string $modal Type de modale (confirm, notify, guidelines, etc.).
     * @param mixed $data Données à passer à la modale.
     */
    public function showModal($modal, $data = NULL) {
        switch ($modal) {
            case 'confirm' :
                $component = "usage.confirm";
                break;

            case 'notify' :
                $component = "usage.notifications-manager";
                break;

            case 'guidelines':
                $component = "usage.guidelines-manager";
                break;

            case 'rubric-info':
                $component = "usage.rubric-info";
                break;

            case 'post-acknowledgers':
                $component = "usage.post-acknowledgers";
                break;

            case 'post-templates-manager':
                $component = "usage.post-templates-manager";
                break;

            case 'apply-template':
                $component = "usage.apply-template-manager";
                break;

            case 'extract-template':
                $component = "usage.extract-template-manager";
                break;

            case 'duplicate-template':
                $component = "usage.duplicate-template-manager";
                break;

            case 'focal-point-picker':
                $component = "focal-point-picker";
                break;

            default :
                $component = "admin.{$this->models}.$modal";
        }

        $this->emit('show', [
            'component' => $component,
            'data' => $data,
            'filter' => $this->filter ?? NULL,
        ])->to('modal-manager');
    }

    /**
     * Tente d'ouvrir automatiquement la modale de guide pour un contexte donné
     * si configuré en auto_open et non encore lu par l'utilisateur.
     *
     * @param string $contextKey Clé de contexte du guide.
     */
    public function autoOpenGuideline(string $contextKey) {
        if (!auth()->check()) return;

        $guideline = \App\Models\Guideline::forContext($contextKey);
        if ($guideline && $guideline->auto_open && $guideline->post) {
            $user = auth()->user();
            $isRead = $guideline->post->readers()
                ->where('user_id', $user->id)
                ->where('post_user.is_read', true)
                ->exists();

            if (!$isRead) {
                $this->showModal('guideline-post', ['contextKey' => $contextKey]);
            }
        }
    }
}
