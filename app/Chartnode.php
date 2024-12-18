<?php

namespace App;

use App\Casts\NullableField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\WithSearching;

class Chartnode extends Model
{
    use WithSearching;
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
        // dump($this->actors->pluck('chartnode_identity')->implode(', '));
        return $this->actors->pluck('chartnode_identity')->implode(', ');
    }

    public function getBoxFormatAttribute() {
        return is_object($this->format) ?

            "<p class='fw-bold {$this->format->title_color}'>{$this->name}</p>".
            "<p class='{$this->format->subtitle_color}'>".$this->actors_list."</p>" :

            "<p class='fw-bold'>{$this->name}</p>".
            "<p>".$this->actors_list."</p>";
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
            ->belongsTo('App\Chartnode', 'parent_id');
    }

    public function childs() {
        return $this
            ->hasMany('App\Chartnode', 'parent_id');
    }

    public static function getOrgChart($node = NULL) {
        switch (TRUE) {
            case is_object($node) :
                $chartnodes = new Collection();
                if (is_object($node->parent)) {
                    $node->parent->parent_id = NULL;
                    $chartnodes->add($node->parent);
                }
                else {
                    $node->parent_id = NULL;
                }
                $chartnodes->add($node);
                $chartnodes = $chartnodes->merge($node->childs->sortBy('rank'));
            break;

            default :
                $chartnodes = static::all()
                    ->sortBy('rank');
        }

        $orgChartBoxes = new Collection();

        $chartnodes
            ->each(function ($chartnode) use ($orgChartBoxes) {
                $orgChartBoxes->add([
                    'c' => [
                        [
                            'v' => (string) $chartnode->id,
                            'f' => $chartnode->boxFormat
                        ],
                        ['v' => (string) $chartnode->parent_id],
                        ['v' => ''],
                    ],
                    'p' => ['style' => is_object($chartnode->format) ? $chartnode->format->style : ''],
                ]);
            });

        return [
            'cols' => [
                ['label' => 'NodeId', 'type' => 'string'],
                ['label' => 'NodeParentId', 'type' => 'string'],
                ['label' => 'ToolTip', 'type' => 'string'],
            ],
            'rows' => $orgChartBoxes
        ];
    }

    public static function saveOrgChartData() {
        Storage::put(
            'public/orgchart/chartnodes.json',
            json_encode(self::getOrgChart()->pluck('data'), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
        );
    }

    public static function filter(array $filter) {
        extract($filter);

        $chartnodes = static::all()
            ->when($search, function ($chartnodes) use ($search) {
                return $chartnodes->filter(function ($chartnode) use ($search) {
                    $columns = [$chartnode->name];
                    if (is_object($chartnode->parent)) $columns[] = $chartnode->parent->name;
                    if (is_object($chartnode->group)) $columns[] = $chartnode->group->name;

                    return static::tableContains($columns, $search);
                });
            });

        return $chartnodes->isEmpty() ? static::whereNull('id') : $chartnodes->toQuery();
    }
}
