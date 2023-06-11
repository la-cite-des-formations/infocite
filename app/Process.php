<?php

namespace App;

use App\Casts\NullableField;
use App\CustomFacades\AP;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Process extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'manager_id', 'parent_id', 'format_id', 'rank', 'group_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'manager_id' => NullableField::class,
        'parent_id' => NullableField::class,
    ];

    public function getBoxFormatAttribute() {
        return
            "<p class='fw-bold {$this->format->title_color}'>{$this->name}</p>".($this->manager ?
            "<p class='{$this->format->subtitle_font_style} {$this->format->subtitle_color}'>{$this->manager->identity}</p>" :
            "");
    }

    public function format() {
        return $this
            ->belongsTo('App\Format');
    }

    public function manager() {
        return $this
            ->belongsTo('App\User', 'manager_id');
    }

    public function group() {
        return $this
            ->belongsTo('App\Group');
    }

    public function actors() {
        return $this->group->users();
    }

    public function parent() {
        return $this
            ->belongsTo('App\Process', 'parent_id');
    }

    public static function getOrgChart() {
        $orgChartBoxes = new Collection();

        self::all()->each(function ($process) use ($orgChartBoxes) {
            $orgChartBoxes->add([
                'data' => [
                    [
                        'v' => (string) $process->id,
                        'f' => $process->boxFormat
                    ],
                    (string) $process->parent_id,
                    '',
                ],
                'style' => $process->format->style,
            ]);
        });

        return $orgChartBoxes;
    }

    public static function saveOrgChartData() {
        Storage::put(
            'public/orgchart/processes.json',
            json_encode(self::getOrgChart()->pluck('data'), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
        );
    }

    public static function filter(array $filter) {
        extract($filter);

        return self::query()
            ->when($search, function ($query) use ($search) {
                $query
                    ->where('name', 'like', "%$search%");
            });
    }
}
