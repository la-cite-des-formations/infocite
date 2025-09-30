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

class ViewingManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    public $statsPage = 'viewing';
    public $statsCollection = [
        'mostViewedPosts' => [
            'bladeFilePath' => 'posts.most-viewed-posts',
            'filter' => [
                'schoolYear' => NULL,
                'month' => NULL,
                'readerType' => 'all',
                'rubricId' => NULL,
            ],
            'charts' => [
                'viewedPostsTop10' => [
                    'target' => 'viewedPostsTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Voir plus...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
        'mostCommentedPosts' => [
            'bladeFilePath' => 'posts.most-commented-posts',
            'filter' => [
                'schoolYear' => NULL,
                'month' => NULL,
                'readerType' => 'all',
                'rubricId' => NULL,
            ],
            'charts' => [
                'commentedPostsTop10' => [
                    'target' => 'commentedPostsTop10Chart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Voir plus...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
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
        }
    }

    public function updatedStatsCollectionMostViewedPostsFilterSchoolYear() {
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );
        $this->resetPage('mostViewedPostsPage');
    }

    public function updatedStatsCollectionMostViewedPostsFilterMonth() {
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );
        $this->resetPage('mostViewedPostsPage');
    }

    public function updatedStatsCollectionMostViewedPostsFilterReaderType() {
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );

        $this->resetPage('mostViewedPostsPage');
    }

    public function updatedStatsCollectionMostViewedPostsFilterRubricId() {
        if (empty($this->statsCollection['mostViewedPosts']['filter']['rubric_id'])) {
            $this->statsCollection['mostViewedPosts']['filter']['rubric_id'] = NULL;
        }
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );

        $this->resetPage('mostViewedPostsPage');
    }

    public function updatedStatsCollectionMostCommentedPostsFilterSchoolYear() {
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );
        $this->resetPage('mostCommentedPostsPage');
    }

    public function updatedStatsCollectionMostCommentedPostsFilterMonth() {
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );
        $this->resetPage('mostCommentedPostsPage');
    }

    public function updatedStatsCollectionMostCommentedPostsFilterReaderType() {
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );

        $this->resetPage('mostCommentedPostsPage');
    }

    public function updatedStatsCollectionMostCommentedPostsFilterRubricId() {
        if (empty($this->statsCollection['mostCommentedPosts']['filter']['rubric_id'])) {
            $this->statsCollection['mostCommentedPosts']['filter']['rubric_id'] = NULL;
        }
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );

        $this->resetPage('mostCommentedPostsPage');
    }

    public function render()
    {
        $viewedPosts = Posts::getViewed($this->statsCollection['mostViewedPosts']['filter']);
        $commentedPosts = Posts::getCommented($this->statsCollection['mostCommentedPosts']['filter']);

        return view('livewire.admin.stats-viewer', [
            'rubrics' => Rubric::allWithPosts(),
            'gcColors' => AP::getGcColors(),
            'viewedPostsTop3' => $viewedPosts->take(3)->get(),
            'mostViewedPosts' => $viewedPosts->paginate($this->statsCollection['mostViewedPosts']['perPage'], ['*'], 'mostViewedPostsPage'),
            'commentedPostsTop3' => $commentedPosts->take(3)->get(),
            'mostCommentedPosts' => $commentedPosts->paginate($this->statsCollection['mostCommentedPosts']['perPage'], ['*'], 'mostCommentedPostsPage'),
            'dashboard' => 'stats',
        ]);
    }
}
