<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use App\Models\Rubric;
use App\Models\Post;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Models\Interaction;
use App\Statistics\Users;
use Carbon\Carbon;

/**
 * Composant Livewire pour la visualisation des statistiques d'usage (éditeurs, commentateurs, applications personnelles) dans l'interface d'administration.
 */
class UsingManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    /**
     * Identifiant de la page de statistiques.
     *
     * @var string
     */
    public $statsPage = 'using';
    /**
     * Configuration des collections de statistiques d'usage.
     *
     * @var array
     */
    public $statsCollection = [
        'mostActiveEditors' => [
            'filter' => [
                'schoolYear' => NULL,
                'month' => NULL,
                'editorType' => 'all',
                'rubricId' => [],
            ],
            'charts' => [
                'activeEditorsTop10' => [
                    'target' => 'activeEditorsTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Détailler...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
        'mostActiveCommentators' => [
            'filter' => [
                'schoolYear' => NULL,
                'month' => NULL,
                'commentatorType' => 'all',
            ],
            'charts' => [
                'activeCommentatorsTop10' => [
                    'target' => 'activeCommentatorsTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Détailler...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
        'personalAppsUsers' => [
            'filter' => [
                'userType' => 'all',
            ],
            'charts' => [
                'personalAppsUsersTop10' => [
                    'target' => 'personalAppsUsersTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Détailler...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
        'notificationsUse' => [
            'filter' => [],
            'charts' => [
                'notificationsUse' => [
                    'target' => 'notificationsUseChart',
                    'event' => 'drawBarChart',
                ],
            ],
        ],
    ];
    /**
     * Liste des années scolaires.
     *
     * @var array
     */
    public $schoolYears = [];

    /**
     * Correspondance des noms de mois en français et leurs numéros.
     *
     * @var array
     */
    public $schoolYearMonths = [
        'Septembre' => 9,
        'Octobre'   => 10,
        'Novembre'  => 11,
        'Décembre'  => 12,
        'Janvier'   => 1,
        'Février'   => 2,
        'Mars'      => 3,
        'Avril'     => 4,
        'Mai'       => 5,
        'Juin'      => 6,
        'Juillet'   => 7,
        'Août'      => 8,
    ];

    /**
     * Libellés pour les types d'éditeurs.
     *
     * @var array
     */
    public $editorLabel = [
        'all' => 'rédacteurs',
        'authors' => 'auteurs',
        'correctors' => 'correcteurs',
    ];
    /**
     * Configuration des onglets de graphiques.
     *
     * @var array
     */
    public $chartTabs = [
        'name' => 'chartTabs',
        'currentTab' => 'most-active-editors',
        'panesPath' => 'includes.admin.stats.use',
        'withMarge' => TRUE,
        'tabs' => [
            'most-active-editors' => [
                'icon' => 'history_edu',
                'title' => "Éditeurs",
                'hidden' => FALSE,
            ],
            'most-active-commentators' => [
                'icon' => '3p',
                'title' => "Commentateurs",
                'hidden' => FALSE,
            ],
            'personal-apps-users' => [
                'icon' => 'apps',
                'title' => "applications personnelles",
                'hidden' => FALSE,
            ],
            'notifications-use' => [
                'icon' => 'notifications',
                'title' => "notifications de bureau",
                'hidden' => FALSE,
            ],
        ],
    ];

    /**
     * Initialise la liste des années scolaires disponibles pour les statistiques.
     * Se base sur la date de la plus ancienne interaction enregistrée.
     */
    protected function initSchoolYears() {
        // Récupérer la plus ancienne interaction
        $oldestOccurredAt = Interaction::where('target_type', Post::class)
            ->orderBy('occurred_at')
            ->value('occurred_at');

        $currentStartYear = now()->month >= 9 ? now()->year : now()->year - 1;

        if ($oldestOccurredAt) {
            $oldestOccuredAtCarbon = Carbon::parse($oldestOccurredAt);
            $startYear = $oldestOccuredAtCarbon->month >= 9
                ? $oldestOccuredAtCarbon->year
                : $oldestOccuredAtCarbon->year - 1;

            for ($year = $currentStartYear; $year >= $startYear; $year--) {
                $this->schoolYears[] = $year;
            }
        }
        else {
            // Au moins l'année en cours si aucune interaction
            $this->schoolYears[] = $currentStartYear;
        }
    }

    /**
     * Initialisation du composant.
     */
    /**
     * Initialisation du composant.
     */
    public function mount() {
        $this->initSchoolYears();
    }

    /**
     * Change l'onglet actif et déclenche le rafraîchissement des graphiques correspondants.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet à activer.
     */
    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;

        switch ($tab) {
            case 'most-active-editors':
                $this->drawCharts(
                    $this->statsCollection['mostActiveEditors']['charts'],
                    $this->statsCollection['mostActiveEditors']['filter']
                );

                $this->resetPage('mostActiveEditorsPage');
            break;
            case 'most-active-commentators':
                $this->drawCharts(
                    $this->statsCollection['mostActiveCommentators']['charts'],
                    $this->statsCollection['mostActiveCommentators']['filter']
                );

                $this->resetPage('mostActiveCommentatorsPage');
            break;
            case 'personal-apps-users':
                $this->drawCharts(
                    $this->statsCollection['personalAppsUsers']['charts'],
                    $this->statsCollection['personalAppsUsers']['filter']
                );

                $this->resetPage('personalAppsUsersPage');
            break;
            case 'notifications-use':
                $this->drawCharts(
                    $this->statsCollection['notificationsUse']['charts'],
                    $this->statsCollection['notificationsUse']['filter']
                );
        }
    }

    /**
     * Met à jour les graphiques lors du changement de l'année scolaire du filtre éditeurs.
     */
    public function updatedStatsCollectionMostActiveEditorsFilterSchoolYear() {
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    /**
     * Met à jour les graphiques lors du changement du mois du filtre éditeurs.
     */
    public function updatedStatsCollectionMostActiveEditorsFilterMonth() {
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    /**
     * Met à jour les graphiques lors du changement du type d'éditeur.
     */
    public function updatedStatsCollectionMostActiveEditorsFilterEditorType() {
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    /**
     * Met à jour les graphiques lors du changement de la rubrique du filtre éditeurs.
     */
    public function updatedStatsCollectionMostActiveEditorsFilterRubricId() {
        if (empty($this->statsCollection['mostActiveEditors']['filter']['rubricId'])) {
            $this->statsCollection['mostActiveEditors']['filter']['rubricId'] = [];
        }
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    /**
     * Bascule la sélection de toutes les rubriques enfants d'une rubrique parente.
     *
     * @param string $collectionKey Nom de la collection ('mostActiveEditors')
     * @param int $parentId Identifiant de la rubrique parente
     */
    public function toggleParentRubric($collectionKey, $parentId) {
        $childIds = Rubric::where('parent_id', $parentId)
            ->where('contains_posts', true)
            ->pluck('id')
            ->toArray();

        if (empty($childIds)) {
            return;
        }

        $currentSelected = $this->statsCollection[$collectionKey]['filter']['rubricId'] ?? [];

        $allSelected = true;
        foreach ($childIds as $id) {
            if (!in_array($id, $currentSelected)) {
                $allSelected = false;
                break;
            }
        }

        if ($allSelected) {
            $this->statsCollection[$collectionKey]['filter']['rubricId'] = array_values(array_diff($currentSelected, $childIds));
        } else {
            $this->statsCollection[$collectionKey]['filter']['rubricId'] = array_values(array_unique(array_merge($currentSelected, $childIds)));
        }

        $this->drawCharts(
            $this->statsCollection[$collectionKey]['charts'],
            $this->statsCollection[$collectionKey]['filter']
        );
        $this->resetPage($collectionKey . 'Page');
    }

    /**
     * Met à jour les graphiques lors du changement de l'année scolaire du filtre commentateurs.
     */
    public function updatedStatsCollectionMostActiveCommentatorsFilterSchoolYear() {
        $this->drawCharts(
            $this->statsCollection['mostActiveCommentators']['charts'],
            $this->statsCollection['mostActiveCommentators']['filter']
        );

        $this->resetPage('mostActiveCommentatorsPage');
    }

    /**
     * Met à jour les graphiques lors du changement du mois du filtre commentateurs.
     */
    public function updatedStatsCollectionMostActiveCommentatorsFilterMonth() {
        $this->drawCharts(
            $this->statsCollection['mostActiveCommentators']['charts'],
            $this->statsCollection['mostActiveCommentators']['filter']
        );

        $this->resetPage('mostActiveCommentatorsPage');
    }

    /**
     * Met à jour les graphiques lors du changement du type de commentateur.
     */
    public function updatedStatsCollectionMostActiveCommentatorsFilterCommentatorType() {
        $this->drawCharts(
            $this->statsCollection['mostActiveCommentators']['charts'],
            $this->statsCollection['mostActiveCommentators']['filter']
        );

        $this->resetPage('mostActiveCommentatorsPage');
    }

    /**
     * Met à jour les graphiques lors du changement du type d'utilisateur pour les applications personnelles.
     */
    public function updatedStatsCollectionPersonalAppsUsersFilterUserType() {
        $this->drawCharts(
            $this->statsCollection['personalAppsUsers']['charts'],
            $this->statsCollection['personalAppsUsers']['filter']
        );

        $this->resetPage('personalAppsUsersPage');
    }

    /**
     * Rendu du composant.
     * Calcule les statistiques d'éditeurs, commentateurs et utilisateurs d'apps pour la vue.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $activeEditors = Users::getActiveEditors($this->statsCollection['mostActiveEditors']['filter']);
        $activeCommentators = Users::getActiveCommentators($this->statsCollection['mostActiveCommentators']['filter']);
        $personalAppsUsers = Users::personalAppsUsers($this->statsCollection['personalAppsUsers']['filter']);

        return view('livewire.admin.stats-viewer', [
            'rubricFamilies' => Rubric::with(['childs' => function($q) {
                $q->where('contains_posts', true)->orderBy('rank');
            }])
            ->whereNull('parent_id')
            ->where('name', '!=', 'Archives')
            ->orderBy('position')
            ->orderBy('rank')
            ->get(),
            'gcColors' => AP::getGcColors(),
            'activeEditors' => $activeEditors->get(),
            'activeEditorsTop3' => $activeEditors->take(3)->get(),
            'mostActiveEditors' => $activeEditors->paginate($this->statsCollection['mostActiveEditors']['perPage'], ['*'], 'mostActiveEditorsPage'),
            'activeCommentators' => $activeCommentators->get(),
            'activeCommentatorsTop3' => $activeCommentators->take(3)->get(),
            'mostActiveCommentators' => $activeCommentators->paginate($this->statsCollection['mostActiveCommentators']['perPage'], ['*'], 'mostActiveCommentatorsPage'),
            'allPersonnalAppsUsers' => $personalAppsUsers->get(),
            'personalAppsUsersTop3' => $personalAppsUsers->take(3)->get(),
            'personalAppsUsers' => $personalAppsUsers->paginate($this->statsCollection['personalAppsUsers']['perPage'], ['*'], 'personalAppsUsersPage'),
            'dashboard' => 'stats',
        ]);
    }
}
