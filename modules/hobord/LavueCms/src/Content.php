<?php

namespace Hobord\LavueCms;

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
        'properties',
        'translation_status',
        'edited_by',
        'primary_locale'
    ];

    protected $fillable = [
        'type_id'
    ];
}