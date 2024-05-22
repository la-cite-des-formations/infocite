<?php

namespace App;

use App\Casts\NullableField;
use App\CustomFacades\AP;
use App\Http\Livewire\WithSearching;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    use WithSearching;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'bg_color', 'border_style', 'title_color', 'subtitle_color'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'bg_color' => NullableField::class,
        'border_style' => NullableField::class,
        'title_color' => NullableField::class,
        'subtitle_color' => NullableField::class,
    ];

    public function chartnodes() {
        return $this
            ->hasMany('App\Chartnode');
    }

    public function getStyleAttribute() {
        return
            'border-radius : 0.375rem; '.
            'border-style : '.($this->border_style ?: 'none').'; '.
            ($this->bg_color ? AP::getFormatBgColors()[$this->bg_color] : '');
    }

    public static function filter(array $filter) {
        extract($filter);

        $formats = static::query()
            ->get()
            ->when($search, function ($formats) use ($search) {
                return $formats->filter(function ($format) use ($search) {
                    return static::tableContains([
                        $format->name,
                    ], $search);
                });
            });

        return $formats->isEmpty() ? static::whereNull('id') : $formats->toQuery();
    }
}
