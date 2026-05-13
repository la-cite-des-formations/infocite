<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Http\Livewire\WithAlert;
use App\Http\Livewire\WithModal;
use Livewire\Component;

/**
 * Composant Livewire pour la modale de confirmation d'actions (suppression, création, etc.).
 */
class Confirm extends Component
{
    use WithModal;
    use WithAlert;

    /**
     * Action à confirmer (ex: 'deletePost', 'deleteComment', etc.).
     *
     * @var string
     */
    public $handling;

    /**
     * Identifiant de l'article concerné.
     *
     * @var int|null
     */
    public $postId;

    /**
     * Identifiant du commentaire concerné.
     *
     * @var int|null
     */
    public $commentId;

    /**
     * Identifiant de l'application concernée.
     *
     * @var int|null
     */
    public $appId;

    /**
     * Route de redirection après confirmation.
     *
     * @var string|null
     */
    public $redirectionRoute;

    /**
     * Message de confirmation à afficher.
     *
     * @var string
     */
    public $message;

    /**
     * Nombre d'éléments par page.
     *
     * @var int
     */
    public $perPage = 8;

    public $event;
    public $params = [];


    /**
     * Initialisation du composant.
     * Configure le message de confirmation en fonction de l'action à valider.
     *
     * @param array $data Données contenant l'action ('handling') et les IDs nécessaires.
     */
    public function mount($data) {
        $this->handling = $data['handling'] ?? 'default';
        $this->postId = $data['postId'] ?? NULL;
        $this->commentId = $data['id'] ?? NULL;
        $this->appId = $data['appId'] ?? NULL;
        $this->event = $data['event'] ?? NULL;
        $this->params = $data['params'] ?? [];

        if (isset($data['content'])) {
            $this->message = $data['content'];
            return;
        }

        switch($this->handling){
            case 'deletePost':
            case 'deletePostFromRubric':
                    $isTemplate = $data['isTemplate'] ?? FALSE;
                    $this->message = $isTemplate ? "Êtes-vous sûr de vouloir supprimer ce modèle ?" : "Êtes-vous sûr de vouloir supprimer cet article ?";
            break;
            case 'deleteComment':
                $this->message = "Êtes-vous sûr de vouloir supprimer ce commentaire ?";
            break;
            case 'deleteApp':
                $this->message = "Êtes-vous sûr de vouloir supprimer cette application ?";
            break;
            case 'update':
                $this->message = "Êtes-vous sûr de vouloir modifier ?";
            break;
            case 'create':
                $this->message = "Êtes-vous sûr de vouloir créer ?";
            break;
            default:
                $this->message = "Confirmer l'action ?";
        }
    }

    /**
     * Confirme l'action demandée en émettant l'événement approprié vers le manager concerné.
     */
    public function confirm() {
        switch($this->handling){
            case('deletePost'):
                $this->emit('deletePost')->to('PostsManager');
            break;
            case('deletePostFromRubric'):
                $this->emit('deletePost', $this->postId)->to('PostsManager');
            break;
            case('deleteComment'):
                $this->emit('deleteComment', $this->commentId)->to('PostsManager');
            break;
            case('deleteApp'):
                $this->emit('deleteApp', $this->appId)->to('AppsManager');
            break;
            case('update'):
            case('create'):
                if (isset($this->redirectionRoute)) {
                    $this->emit('save', $this->redirectionRoute);
                }
                else {
                    $this->emit('save');
                }
            break;
            default:
                if ($this->event) {
                    $this->emit($this->event, ...$this->params);
                }
            break;
        }
        $this->dispatchBrowserEvent('close-modal');
    }

    /**
     * Rendu du composant.
     * Récupère les nouvelles et les anciennes notifications de l'utilisateur.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.usage.confirm');
    }
}
