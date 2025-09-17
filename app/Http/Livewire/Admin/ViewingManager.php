<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Models\Rubric;
use App\Statistics\Posts;

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
                'readerType' => 'all',
                'rubric_id' => NULL,
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
                'readerType' => 'all',
                'rubric_id' => NULL,
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
        $allViewedPosts = Posts::allViewed($this->statsCollection['mostViewedPosts']['filter']);
        $allCommentedPosts = Posts::allCommented($this->statsCollection['mostCommentedPosts']['filter']);

        return view('livewire.admin.stats-viewer', [
            'rubrics' => Rubric::allWithPosts(),
            'gcColors' => AP::getGcColors(),
            'viewedPostsTop3' => $allViewedPosts->take(3)->get(),
            'mostViewedPosts' => $allViewedPosts->paginate($this->statsCollection['mostViewedPosts']['perPage'], ['*'], 'mostViewedPostsPage'),
            'commentedPostsTop3' => $allCommentedPosts->take(3)->get(),
            'mostCommentedPosts' => $allCommentedPosts->paginate($this->statsCollection['mostCommentedPosts']['perPage'], ['*'], 'mostCommentedPostsPage'),
            'dashboard' => 'stats',
        ]);
    }
}
