<?php

namespace App;

use App\Casts\NullableField;
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

    public function getActorsListAttribute() {
        return $this->actors->pluck('identity')->implode(', ');
    }

    public function getBoxFormatAttribute() {
        return is_object($this->format) ?
            "<p class='fw-bold {$this->format->title_color}'>{$this->name}</p>".
            "<p class='{$this->format->subtitle_font_style} {$this->format->subtitle_color}'>".
                ($this->manager ?
                    $this->manager->identity :
                    $this->actors_list
                ).
            "</p>" :
            "<p class='fw-bold'>{$this->name}</p>".
            "<p>".
                ($this->manager ?
                    $this->manager->identity :
                    $this->actors_list
                ).
            "</p>";
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
        return $this->group->actors();
    }

    public function parent() {
        return $this
            ->belongsTo('App\Process', 'parent_id');
    }

    public function childs() {
        return $this
            ->hasMany('App\Process', 'parent_id');
    }

    public static function getOrgChart() {
        $orgChartBoxes = new Collection();

        self::query()
            ->orderBy('rank')
            ->get()
            ->each(function ($process) use ($orgChartBoxes) {
                $orgChartBoxes->add([
                    'data' => [
                        [
                            'v' => (string) $process->id,
                            'f' => $process->boxFormat
                        ],
                        (string) $process->parent_id,
                        '',
                    ],
                    'style' => is_object($process->format) ? $process->format->style : '',
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
