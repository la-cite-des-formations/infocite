<?php

namespace App\Http\Livewire;

use Livewire\Component;

/**
 * Composant central de gestion des fenêtres modales.
 * Permet l'affichage dynamique et le déchargement de composants modaux.
 */
class ModalManager extends Component
{
    protected $listeners = ['unload', 'show'];

    /**
     * Identifiant du composant parent.
     *
     * @var string
     */
    public $parent;

    /**
     * Nom du composant modal à afficher.
     *
     * @var string|null
     */
    public $modal = NULL;

    /**
     * Données à passer au composant modal.
     *
     * @var mixed
     */
    public $data;

    /**
     * Filtre éventuel pour le composant modal.
     *
     * @var mixed
     */
    public $filter;

    /**
     * Identifiant du client ayant ouvert la modale.
     *
     * @var string|null
     */
    public $client = NULL;

    /**
     * Initialisation du composant.
     *
     * @param string $parent Identifiant du composant parent.
     */
    public function mount($parent) {
        $this->parent = $parent;
    }

    /**
     * Décharge la modale actuelle et notifie le composant parent ou client.
     */
    public function unload() {
        if ($this->client) {
            $this->emit('modalClosed', $this->modal)->to($this->client);
        }
        $this->emit('modalClosed', $this->modal)->to($this->parent);
        $this->reset('modal', 'data', 'client');
    }

    /**
     * Affiche une modale spécifique avec ses données.
     *
     * @param array $modalBag Sac de données contenant le composant, les données et le client cible.
     */
    public function show($modalBag) {
        $this->client = $modalBag['client'] ?? NULL;
        $this->modal = $modalBag['component'] ?? 'default-modal';
        $this->data = $modalBag['data'] ?? NULL;
        $this->filter = $modalBag['filter'] ?? NULL;

        $this->dispatchBrowserEvent('showModal');
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        return view('livewire.modal-manager');
    }
}
