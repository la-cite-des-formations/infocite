<?php

namespace App\Http\Livewire;

/**
 * Trait permettant de basculer entre le mode 'consultation' (view) et 'édition' (edition) via la session.
 */
trait WithUsageMode
{
    /**
     * Mode actuel de l'interface ('view' ou 'edition').
     *
     * @var string
     */
    public $mode;

    /**
     * Initialise le mode à partir de la session (défaut: view).
     */
    public function setMode() {
        $this->mode = session('mode', 'view');
        session(['mode' => $this->mode]);
    }

    /**
     * Alterne entre le mode consultation et édition.
     */
    public function switchMode() {
        $this->mode = $this->mode == 'view' ? 'edition' : 'view';
        session(['mode' => $this->mode]);
    }
}
