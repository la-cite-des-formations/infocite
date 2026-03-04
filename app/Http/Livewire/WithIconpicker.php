<?php

namespace App\Http\Livewire;

use App\CustomFacades\AP;
use Illuminate\Support\Facades\Cookie;

/**
 * Trait pour la recherche et la sélection d'icônes Material Icons dans les composants Livewire.
 */
trait WithIconpicker

{
    /**
     * Mot-clé de recherche pour les icônes.
     *
     * @var string
     */
    public $searchIcons = '';

    /**
     * Récupère les codes Material Icons filtrés par la recherche.
     *
     * @return \Illuminate\Support\Collection Collection des codes Material Icons.
     */
    public function getMiCodes() {
        $searchIcons = $this->searchIcons;

        return AP::getMiCodes()
            ->when($searchIcons, function ($icons) use ($searchIcons) {
                return $icons->filter(function ($miCode, $miName) use ($searchIcons) {
                    return str_contains($miName, $searchIcons) || str_contains($miCode, $searchIcons);
                });
            });
    }

    /**
     * Sélectionne une icône et la stocke dans le modèle spécifié, puis met à jour les icônes récentes.
     *
     * @param string $miName Le nom de l'icône Material.
     * @param string $model Le nom de la propriété du modèle Livewire à mettre à jour.
     */
    public function choiceIcon(string $miName, string $model) {
        if(isset($this->$model)) {
            $this->$model->icon = $miName;
            Cookie::queue(
                'recentMiCodes',
                AP::getRecentMiCodes()
                    ->where('name', '!=', $miName)
                    ->prepend([
                        'name' => $miName,
                        'code' => AP::getMiCode($miName),
                        'last_used_at' => now()->toDateTimeString(),
                    ])
                    ->sortByDesc('last_used_at')
                    ->take(20)
                    ->values(),
                AP::COOKIE_LIFETIME
            );
        }
    }
}
