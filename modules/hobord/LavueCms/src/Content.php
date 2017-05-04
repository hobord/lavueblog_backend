<?php

namespace LavueCms;

use Illuminate\Database\Eloquent\Model;
use \Dimsav\Translatable\Translatable;

use LavueCms\ContentTranslation;

class Content extends Model
{
    use Translatable;

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
        'type'
    ];


}