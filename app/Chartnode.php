<?php

namespace App;

use App\Casts\NullableField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Chartnode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'parent_id', 'format_id', 'rank', 'code_fonction'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
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

    public function getGroupAttribute() {
        return Group::query()
            ->where('type', 'P')
            ->where('code_ypareo', $this->code_fonction)
            ->first();
    }

    public function format() {
        return $this
            ->belongsTo('App\Format');
    }

    public function actors() {
        if (is_null($this->group)) {
            return (new Group)->users();
        }

        return $this->group->users();
    }

    public function parent() {
        return $this
            ->belongsTo(self::class, 'parent_id');
    }

    public function childs() {
        return $this
            ->hasMany(self::class, 'parent_id');
    }

    public static function getOrgChart() {
        $orgChartBoxes = new Collection();

        self::all()
            ->sortBy('rank')
            ->each(function ($chartnode) use ($orgChartBoxes) {
                $orgChartBoxes->add([
                    'data' => [
                        [
                            'v' => (string) $chartnode->id,
                            'f' => $chartnode->boxFormat
                        ],
                        (string) $chartnode->parent_id,
                        '',
                    ],
                    'style' => is_object($chartnode->format) ? $chartnode->format->style : '',
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
