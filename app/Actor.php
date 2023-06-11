<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
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

    public function getBoxFormatAttribute() {
        $process = $this->processes()->first();

        return
            "<p class='fw-bold {$process->format->title_color}'>{$this->identity}</p>".
            "<p class='{$process->format->subtitle_font_style} {$process->format->subtitle_color}'>{$this->functionsList}</p>";
    }

    public function manager() {
        return $this
            ->hasOne('App/Actor', 'manager_id');
    }

    public function processes() {
        return $this->user->processes();
    }

    public function getIsManagerAttribute() {
        return $this->user->isManager();
    }

    public static function getManagers() {
        return User::query()
            ->whereIn('id', static::all()
                ->filter(function($actor) {
                    return $actor->isManager;
                })
                ->pluck('id')
            )
            ->get();
    }

    public static function getOrgChart() {
        $orgChartBoxes = new Collection();

        self::all()->each(function ($actor) use ($orgChartBoxes) {

            $orgChartBoxes->add([
                'data' => [
                    [
                        'v' => (string) $actor->id,
                        'f' => $actor->boxFormat
                    ],
                    (string) $actor->manager_id,
                    '',
                ],
                'style' => $actor->processes()->first()->format->style
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
