<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use App\Models\Rubric;
use App\Models\Post;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Models\Interaction;
use App\Statistics\Posts;
use Carbon\Carbon;

/**
 * Composant Livewire pour la visualisation des statistiques de consultation
 * (articles lus, articles commentés, articles les mieux notés) dans l'interface d'administration.
 */
class ViewingManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    /**
     * Identifiant de la page de statistiques.
     *
     * @var string
     */
    public $statsPage = 'viewing';

    /**
     * Configuration des collections de statistiques de consultation (lus, commentés, notés).
     *
     * @var array
     */
    public $statsCollection = [
        'mostViewedPosts' => [
            'bladeFilePath' => 'posts.most-viewed-posts',
            'filter' => [
                'schoolYear' => NULL,
                'month' => NULL,
                'readerType' => 'all',
                'rubricId' => [],
            ],
            'charts' => [
                'viewedPostsTop10' => [
                    'target' => 'viewedPostsTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Détailler...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
        'mostCommentedPosts' => [
            'bladeFilePath' => 'posts.most-commented-posts',
            'filter' => [
                'schoolYear' => NULL,
                'month' => NULL,
                'readerType' => 'all',
                'rubricId' => [],
            ],
            'charts' => [
                'commentedPostsTop10' => [
                    'target' => 'commentedPostsTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Détailler...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
        'mostRatedPosts' => [
            'bladeFilePath' => 'posts.most-rated-posts',
            'filter' => [
                // Pas de filtre date : les notes ne sont pas horodatées
                'readerType' => 'all',
                'rubricId' => [],
            ],
            'charts' => [
                'ratedPostsTop10' => [
                    'target' => 'ratedPostsTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Détailler...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
    ];

    /**
     * Liste des années scolaires.
     *
     * @var array
     */
    public $schoolYears = [];

    /**
     * Correspondance des mois.
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
     * Configuration des onglets.
     *
     * @var array
     */
    public $chartTabs = [
        'name' => 'chartTabs',
        'currentTab' => 'most-viewed-posts',
        'panesPath' => 'includes.admin.stats.posts',
        'withMarge' => TRUE,
        'tabs' => [
            'most-viewed-posts' => [
                'icon' => 'local_library',
                'title' => "Articles lus",
                'hidden' => FALSE,
            ],
            'most-commented-posts' => [
                'icon' => 'comment_bank',
                'title' => "Articles commentés",
                'hidden' => FALSE,
            ],
            'most-rated-posts' => [
                'icon' => 'star',
                'title' => "Articles notés",
                'hidden' => FALSE,
            ],
        ],
    ];

    /**
     * Initialise la liste des années scolaires disponibles pour les statistiques.
     * Se base sur la date de la plus ancienne interaction sur un article.
     */
    protected function initSchoolYears() {
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
            $this->schoolYears[] = $currentStartYear;
        }
    }

    /**
     * Initialisation du composant.
     */
    public function mount() {
        $this->initSchoolYears();
    }

    /**
     * Change l'onglet actif et rafraîchit les graphiques de consultation.
     *
     * @param string $tabsSystem Système d'onglets.
     * @param string $tab Onglet cible.
     */
    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;

        switch ($tab) {
            case 'most-viewed-posts':
                $this->drawCharts(
                    $this->statsCollection['mostViewedPosts']['charts'],
                    $this->statsCollection['mostViewedPosts']['filter']
                );
                $this->resetPage('mostViewedPostsPage');
            break;
            case 'most-commented-posts':
                $this->drawCharts(
                    $this->statsCollection['mostCommentedPosts']['charts'],
                    $this->statsCollection['mostCommentedPosts']['filter']
                );
                $this->resetPage('mostCommentedPostsPage');
            break;
            case 'most-rated-posts':
                $this->drawCharts(
                    $this->statsCollection['mostRatedPosts']['charts'],
                    $this->statsCollection['mostRatedPosts']['filter']
                );
                $this->resetPage('mostRatedPostsPage');
        }
    }

    // -------------------------------------------------------------------------
    // Articles lus
    // -------------------------------------------------------------------------

    /** Met à jour les graphiques lors du changement de l'année scolaire du filtre articles lus. */
    public function updatedStatsCollectionMostViewedPostsFilterSchoolYear() {
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );
        $this->resetPage('mostViewedPostsPage');
    }

    /** Met à jour les graphiques lors du changement du mois du filtre articles lus. */
    public function updatedStatsCollectionMostViewedPostsFilterMonth() {
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );
        $this->resetPage('mostViewedPostsPage');
    }

    /** Met à jour les graphiques lors du changement du type de lecteur du filtre articles lus. */
    public function updatedStatsCollectionMostViewedPostsFilterReaderType() {
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );
        $this->resetPage('mostViewedPostsPage');
    }

    /** Met à jour les graphiques lors du changement de rubrique du filtre articles lus. */
    public function updatedStatsCollectionMostViewedPostsFilterRubricId() {
        if (empty($this->statsCollection['mostViewedPosts']['filter']['rubricId'])) {
            $this->statsCollection['mostViewedPosts']['filter']['rubricId'] = [];
        }
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );
        $this->resetPage('mostViewedPostsPage');
    }

    // -------------------------------------------------------------------------
    // Articles commentés
    // -------------------------------------------------------------------------

    /** Met à jour les graphiques lors du changement de l'année scolaire du filtre articles commentés. */
    public function updatedStatsCollectionMostCommentedPostsFilterSchoolYear() {
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );
        $this->resetPage('mostCommentedPostsPage');
    }

    /** Met à jour les graphiques lors du changement du mois du filtre articles commentés. */
    public function updatedStatsCollectionMostCommentedPostsFilterMonth() {
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );
        $this->resetPage('mostCommentedPostsPage');
    }

    /** Met à jour les graphiques lors du changement du type de lecteur du filtre articles commentés. */
    public function updatedStatsCollectionMostCommentedPostsFilterReaderType() {
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );
        $this->resetPage('mostCommentedPostsPage');
    }

    /** Met à jour les graphiques lors du changement de rubrique du filtre articles commentés. */
    public function updatedStatsCollectionMostCommentedPostsFilterRubricId() {
        if (empty($this->statsCollection['mostCommentedPosts']['filter']['rubricId'])) {
            $this->statsCollection['mostCommentedPosts']['filter']['rubricId'] = [];
        }
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );
        $this->resetPage('mostCommentedPostsPage');
    }

    // -------------------------------------------------------------------------
    // Articles notés (pas de filtre date)
    // -------------------------------------------------------------------------

    /** Met à jour les graphiques lors du changement du type de lecteur du filtre articles notés. */
    public function updatedStatsCollectionMostRatedPostsFilterReaderType() {
        $this->drawCharts(
            $this->statsCollection['mostRatedPosts']['charts'],
            $this->statsCollection['mostRatedPosts']['filter']
        );
        $this->resetPage('mostRatedPostsPage');
    }

    /** Met à jour les graphiques lors du changement de rubrique du filtre articles notés. */
    public function updatedStatsCollectionMostRatedPostsFilterRubricId() {
        if (empty($this->statsCollection['mostRatedPosts']['filter']['rubricId'])) {
            $this->statsCollection['mostRatedPosts']['filter']['rubricId'] = [];
        }
        $this->drawCharts(
            $this->statsCollection['mostRatedPosts']['charts'],
            $this->statsCollection['mostRatedPosts']['filter']
        );
        $this->resetPage('mostRatedPostsPage');
    }

    // -------------------------------------------------------------------------
    // Toggle rubriques parentes (toutes collections)
    // -------------------------------------------------------------------------

    /**
     * Bascule la sélection de toutes les rubriques enfants d'une rubrique parente.
     *
     * @param string $collectionKey 'mostViewedPosts' | 'mostCommentedPosts' | 'mostRatedPosts'
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
     * Rendu du composant.
     * Calcule les statistiques de consultation (lus, commentés, notés) pour la vue.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $viewedPosts    = Posts::getViewed($this->statsCollection['mostViewedPosts']['filter']);
        $commentedPosts = Posts::getCommented($this->statsCollection['mostCommentedPosts']['filter']);
        $ratedPosts     = Posts::getRated($this->statsCollection['mostRatedPosts']['filter']);

        return view('livewire.admin.stats-viewer', [
            'rubricFamilies' => Rubric::with(['childs' => function($q) {
                $q->where('contains_posts', true)->orderBy('rank');
            }])
            ->whereNull('parent_id')
            ->where('name', '!=', 'Archives')
            ->orderBy('position')
            ->orderBy('rank')
            ->get(),
            'gcColors'           => AP::getGcColors(),
            // Articles lus
            'viewedPosts'        => $viewedPosts->get(),
            'viewedPostsTop3'    => $viewedPosts->take(3)->get(),
            'mostViewedPosts'    => $viewedPosts->paginate($this->statsCollection['mostViewedPosts']['perPage'], ['*'], 'mostViewedPostsPage'),
            // Articles commentés
            'commentedPosts'     => $commentedPosts->get(),
            'commentedPostsTop3' => $commentedPosts->take(3)->get(),
            'mostCommentedPosts' => $commentedPosts->paginate($this->statsCollection['mostCommentedPosts']['perPage'], ['*'], 'mostCommentedPostsPage'),
            // Articles notés
            'ratedPosts'         => $ratedPosts->get(),
            'ratedPostsTop3'     => $ratedPosts->take(3)->get(),
            'mostRatedPosts'     => $ratedPosts->paginate($this->statsCollection['mostRatedPosts']['perPage'], ['*'], 'mostRatedPostsPage'),
            'dashboard'          => 'stats',
        ]);
    }
}
