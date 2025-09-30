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

class UsingManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    public $statsPage = 'using';
    public $statsCollection = [
        'mostActiveEditors' => [
            'filter' => [
                'schoolYear' => NULL,
                'month' => NULL,
                'editorType' => 'all',
                'rubricId' => NULL,
            ],
            'charts' => [
                'activeEditorsTop10' => [
                    'target' => 'activeEditorsTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Voir plus...',
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
            'buttonLabel' => 'Voir plus...',
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
            'buttonLabel' => 'Voir plus...',
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
    public $schoolYears = [];
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

    public function mount() {
        $this->initSchoolYears();
    }

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

    public function updatedStatsCollectionMostActiveEditorsFilterSchoolYear() {
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    public function updatedStatsCollectionMostActiveEditorsFilterMonth() {
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    public function updatedStatsCollectionMostActiveEditorsFilterEditorType() {
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    public function updatedStatsCollectionMostActiveEditorsFilterRubricId() {
        if (empty($this->statsCollection['mostActiveEditors']['filter']['rubricId'])) {
            $this->statsCollection['mostActiveEditors']['filter']['rubricId'] = NULL;
        }
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    public function updatedStatsCollectionMostActiveCommentatorsFilterSchoolYear() {
        $this->drawCharts(
            $this->statsCollection['mostActiveCommentators']['charts'],
            $this->statsCollection['mostActiveCommentators']['filter']
        );

        $this->resetPage('mostActiveCommentatorsPage');
    }

    public function updatedStatsCollectionMostActiveCommentatorsFilterMonth() {
        $this->drawCharts(
            $this->statsCollection['mostActiveCommentators']['charts'],
            $this->statsCollection['mostActiveCommentators']['filter']
        );

        $this->resetPage('mostActiveCommentatorsPage');
    }

    public function updatedStatsCollectionMostActiveCommentatorsFilterCommentatorType() {
        $this->drawCharts(
            $this->statsCollection['mostActiveCommentators']['charts'],
            $this->statsCollection['mostActiveCommentators']['filter']
        );

        $this->resetPage('mostActiveCommentatorsPage');
    }

    public function updatedStatsCollectionPersonalAppsUsersFilterUserType() {
        $this->drawCharts(
            $this->statsCollection['personalAppsUsers']['charts'],
            $this->statsCollection['personalAppsUsers']['filter']
        );

        $this->resetPage('personalAppsUsersPage');
    }

    public function render()
    {
        $activeEditors = Users::getActiveEditors($this->statsCollection['mostActiveEditors']['filter']);
        $activeCommentators = Users::getActiveCommentators($this->statsCollection['mostActiveCommentators']['filter']);
        $personalAppsUsers = Users::personalAppsUsers($this->statsCollection['personalAppsUsers']['filter']);

        return view('livewire.admin.stats-viewer', [
            'rubrics' => Rubric::allWithPosts(),
            'gcColors' => AP::getGcColors(),
            'activeEditorsTop3' => $activeEditors->take(3)->get(),
            'mostActiveEditors' => $activeEditors->paginate($this->statsCollection['mostActiveEditors']['perPage'], ['*'], 'mostActiveEditorsPage'),
            'activeCommentatorsTop3' => $activeCommentators->take(3)->get(),
            'mostActiveCommentators' => $activeCommentators->paginate($this->statsCollection['mostActiveCommentators']['perPage'], ['*'], 'mostActiveCommentatorsPage'),
            'personalAppsUsersTop3' => $personalAppsUsers->take(3)->get(),
            'personalAppsUsers' => $personalAppsUsers->paginate($this->statsCollection['personalAppsUsers']['perPage'], ['*'], 'personalAppsUsersPage'),
            'dashboard' => 'stats',
        ]);
    }
}
