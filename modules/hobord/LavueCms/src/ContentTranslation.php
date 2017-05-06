<?php

namespace Hobord\LavueCms;

use Illuminate\Database\Eloquent\Model;
use \Dimsav\Translatable\Translatable;

class ContentTranslation extends Model
{
    protected $table = 'contents_translations';

    public $timestamps = false;

    protected $fillable = [
        'slug',
        'title',
        'metatags',
        'document',
        'translation_status',
        'edited_by',
        'primary_locale'
    ];

    protected $casts = [
        'document' => 'array',
        'metatags' => 'array'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }
}