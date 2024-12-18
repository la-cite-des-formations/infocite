<?php

namespace App\Http\Livewire\Admin;

use App\CustomFacades\AP;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithCharts;
use App\Rubric;
use App\Post;

class ViewingManager extends Component
{
    use WithPagination;
    use WithCharts;

    protected $paginationTheme = 'bootstrap';

    public $statsPage = 'viewing';
    public $statsCollection = [
        'mostViewedPosts' => [
            'filter' => [
                'readerType' => 'all',
                'rubric_id' => NULL,
            ],
            'charts' => [
                'topTenViewedPosts' => [
                    'target' => 'topTenViewedPostsChart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Voir plus...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
        'mostCommentedPosts' => [
            'filter' => [
                'readerType' => 'all',
                'rubric_id' => NULL,
            ],
            'charts' => [
                'topTenCommentedPosts' => [
                    'target' => 'topTenCommentedPostsChart',
                    'event' => 'drawRingChart',
                ],
            ],
            'buttonLabel' => 'Voir plus...',
            'perPageOptions' => [10, 15, 25],
            'perPage' => 10,
        ],
    ];

    public function updatedStatsCollectionMostViewedPostsFilterReaderType() {
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );
    }

    public function updatedStatsCollectionMostViewedPostsFilterRubricId() {
        if (empty($this->statsCollection['mostViewedPosts']['filter']['rubric_id'])) {
            $this->statsCollection['mostViewedPosts']['filter']['rubric_id'] = NULL;
        }
        $this->drawCharts(
            $this->statsCollection['mostViewedPosts']['charts'],
            $this->statsCollection['mostViewedPosts']['filter']
        );
    }

    public function updatedStatsCollectionMostCommentedPostsFilterReaderType() {
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );
    }

    public function updatedStatsCollectionMostCommentedPostsFilterRubricId() {
        if (empty($this->statsCollection['mostCommentedPosts']['filter']['rubric_id'])) {
            $this->statsCollection['mostCommentedPosts']['filter']['rubric_id'] = NULL;
        }
        $this->drawCharts(
            $this->statsCollection['mostCommentedPosts']['charts'],
            $this->statsCollection['mostCommentedPosts']['filter']
        );
    }

    public function render()
    {
        $allViewedPosts = Post::allViewed($this->statsCollection['mostViewedPosts']['filter']);
        $allCommentedPosts = Post::allCommented($this->statsCollection['mostCommentedPosts']['filter']);

        return view('livewire.admin.stats-viewer', [
            'rubrics' => Rubric::allWithPosts(),
            'gcColors' => AP::getGcColors(),
            'mostViewedPosts' => $allViewedPosts->paginate($this->statsCollection['mostViewedPosts']['perPage']),
            'viewedPostsTop3' => $allViewedPosts->take(3)->get(),
            'mostCommentedPosts' => $allCommentedPosts->paginate($this->statsCollection['mostCommentedPosts']['perPage']),
            'commentedPostsTop3' => $allCommentedPosts->take(3)->get(),
            'dashboard' => 'stats',
        ]);
    }
}
