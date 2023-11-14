<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Actor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'manager_id'];

        /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = FALSE;

    public function user() {
        return $this
            ->belongsTo('App\User', 'id');
    }

    public function getIdentityAttribute() {
        return $this->user->identity;
    }

    public function getFunctionsListAttribute() {
        return $this->user->functionsList(['P']);
    }

    public function getManagerBoxFormatAttribute() {
        $format = Format::find($this->format_id);

        return
            "<p class='fw-bold {$format->title_color}'>{$this->identity}</p>".
            "<p class='{$format->subtitle_color}'>{$this->functionsList}</p>";
    }

    public function manager() {
        return $this
            ->hasOne('App/Actor', 'manager_id');
    }

    public function processes() {
        return $this->user->processes();
    }

    public function getFullSubordinatesListBoxFormatAttribute() {
        $formatedFullSubordinates = new Collection();

        $this->user->subordinates->filter(function ($subordinate) {
            return !$subordinate->isManager();
        })->each(function ($subordinate) use ($formatedFullSubordinates) {
            $formatedFullSubordinates->add(
                '<p>'.
                    '<div class="text-danger">'.$subordinate->identity.'</div>'.
                    $subordinate->functionsList(['P']).
                '</p>'
            );
        });

        return $formatedFullSubordinates->implode('');
    }

    public function getIsManagerAttribute() {
        return $this->user->isManager();
    }

    public static function getManagers() {
        return static::query()
            ->distinct()
            ->join('processes', 'actors.id', '=', 'processes.manager_id')
            ->whereNotNull('processes.manager_id')
            ->orderBy('rank')
            ->get(['actors.*', 'processes.format_id']);
    }

    public static function getOrgChart() {
        $orgChartBoxes = new Collection();

        self::getManagers()->each(function ($manager) use ($orgChartBoxes) {
            $orgChartBoxes->add([
                'data' => [
                    [
                        'v' => (string) $manager->id,
                        'f' => $manager->managerBoxFormat
                    ],
                    (string) $manager->manager_id,
                    '',
                ],
                'style' => Format::find($manager->format_id)->style
            ]);

            $orgChartBoxes->add([
                'data' => [
                    $manager->fullSubordinatesListBoxFormat,
                    (string) $manager->id,
                    '',
                ],
                'style' => Format::firstWhere('name', 'Default')->style,
            ]);
        });

        return $orgChartBoxes;
    }

    public static function saveOrgChartData() {
        Storage::put(
            'public/orgchart/actors.json',
            json_encode(self::getOrgChart()->pluck('data'), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
        );
    }
}
