<?php

namespace App\Http\Livewire\Usage;

use App\Models\App;
use App\Http\Livewire\WithModal;
use Livewire\Component;
use Livewire\WithPagination;

class AppsManager extends Component
{
    use WithPagination;
    use WithModal;

    public $rubricSegment;
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $blockRedirection = FALSE;

    protected $listeners = ['deleteApp', 'render', 'displayUpdated'=>'render'];

    public function mount($viewBag) {
        $this->rubricSegment = $viewBag->rubricSegment;
    }

    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    public function switchFavoriteApp($appId) {
        $app = App::find($appId);
        $user = auth()->user();
        $updatedFavoritesApps = $user->myFavoritesApps->pluck('pivot.rank', 'id');

        if ($app->isFavorite) {
            $currentRank = $updatedFavoritesApps->pull($appId);
            $updatedFavoritesApps = $updatedFavoritesApps->map(function ($rank, $id) use ($currentRank) {
                return ['rank' => $rank < $currentRank ? $rank : $rank - 1];
            });
        }
        else {
            $updatedFavoritesApps->put($appId, 0);
            $updatedFavoritesApps = $updatedFavoritesApps->map(function ($rank, $id) {
                return ['rank' => $rank + 1];
            });
        }

        $user->myFavoritesApps()->sync($updatedFavoritesApps);

        $this->emitSelf('render');
    }

    public function deleteApp($appId) {
        App::find($appId)->delete();
    }

    public function redirectToApp($appUrl) {
        if (!$this->blockRedirection) {
            $this->emit('newTabRedirection', $appUrl);
        }
        else {
            $this->blockRedirection = FALSE;
        }
    }

    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.apps-manager');
    }
}
