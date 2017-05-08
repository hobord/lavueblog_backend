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


class Content extends Model implements HasMedia, HasTaxonomyTerms
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
            $query->where($this->getTranslationsTable().'.'.$key, $comp, $value);
            if ($locale) {
                $query->where($this->getTranslationsTable().'.'.$this->getLocaleKey(), $locale);
            }
        });
    }


//    public function related_contents()
//    {
//        return $this->belongsToMany(Content::class, 'contents_related_contents', 'content_id', 'related_content_id');
//    }

    //TODO
    public function related_contents()
    {
        return $this->morphedByMany(get_class($this), 'content_related_object');
    }
}