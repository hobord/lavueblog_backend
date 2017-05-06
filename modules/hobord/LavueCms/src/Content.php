<?php

namespace Hobord\LavueCms;

use Illuminate\Database\Eloquent\Model;
use \Dimsav\Translatable\Translatable;
use Hobord\Taxonomy\TaxomyTermTrait;

use Hobord\LavueCms\ContentTranslation;

class Content extends Model
{
    use Translatable;
    use TaxomyTermTrait;

    public $translationModel = ContentTranslation::class;

    public $translatedAttributes = [
        'slug',
        'title',
        'metatags',
        'document',
        'translation_status',
        'edited_by',
        'primary_locale'
    ];

    protected $fillable = [
        'type_id'
    ];


}