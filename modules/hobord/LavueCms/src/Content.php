<?php

namespace Hobord\LavueCms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

use Hobord\Taxonomy\Interfaces\HasTaxonomyTerms;
use Hobord\Taxonomy\TaxomyTermTrait;


class Content extends Model implements HasMedia, HasTaxonomyTerms, HasMediaConversions
{
    use Translatable;
    use TaxomyTermTrait;
    use HasMediaTrait;

    public $translationModel = ContentTranslation::class;

    public $translatedAttributes = [
        'slug',
        'title',
        'metatags',
        'document',
        'fields',
        'properties',
        'edited_by',
        'primary_locale'
    ];

    protected $fillable = [
        'type_id'
    ];

    /**
     * Register the conversions that should be performed.
     *
     * @return array
     */
    public function registerMediaConversions()
    {
//        $this->addMediaConversion('thumbnail')
//            ->width(300)
//            ->height(200)
//            ->extractVideoFrameAtSecond(5) // If it's a video; grab the still frame from the 5th second in the video
//            ->sharpen(10);
//        $this->addMediaConversion('banner')
//            ->fit(Manipulations::FIT_CROP, 800, 200)
//            ->apply()
//            ->blur(40);
    }

    /**
     * This scope filters results by checking the translation fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $key
     * @param string                                $value
     * @param string                                $comp
     * @param string                                $locale
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeWhereCompTranslation(Builder $query, $key, $value, $comp='=', $locale = null)
    {
        return $query->whereHas('translations', function (Builder $query) use ($key, $comp, $value, $locale) {
            $query->where($this->getTranslationsTable() . '.' . $key, $comp, $value);
            if ($locale) {
                $query->where($this->getTranslationsTable() . '.' . $this->getLocaleKey(), $locale);
            }
        });
    }

    //TODO
    public function related_contents()
    {
        return $this->morphedByMany(get_class($this), 'content_related_object');
    }
}