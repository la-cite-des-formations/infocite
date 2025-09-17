<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Models\Rubric;
use App\Statistics\Users;

class UsingManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    public $statsPage = 'using';
    public $statsCollection = [
        'mostActiveEditors' => [
            'filter' => [
                'editorType' => 'all',
                'rubric_id' => NULL,
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


    public function updatedStatsCollectionMostActiveEditorsFilterEditorType() {
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
    }

    public function updatedStatsCollectionMostActiveEditorsFilterRubricId() {
        if (empty($this->statsCollection['mostActiveEditors']['filter']['rubric_id'])) {
            $this->statsCollection['mostActiveEditors']['filter']['rubric_id'] = NULL;
        }
        $this->drawCharts(
            $this->statsCollection['mostActiveEditors']['charts'],
            $this->statsCollection['mostActiveEditors']['filter']
        );

        $this->resetPage('mostActiveEditorsPage');
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
        $activeEditors = Users::activeEditors($this->statsCollection['mostActiveEditors']['filter']);
        $activeCommentators = Users::activeCommentators($this->statsCollection['mostActiveCommentators']['filter']);
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
