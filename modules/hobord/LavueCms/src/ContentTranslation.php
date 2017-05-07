<?php

namespace Hobord\LavueCms;

use Illuminate\Database\Eloquent\Model;

class ContentTranslation extends Model
{
    protected $table = 'contents_translations';

    public $timestamps = false;

    protected $fillable = [
        'slug',
        'title',
        'metatags',
        'document',
        'properties',
        'translation_status',
        'edited_by'
    ];

    protected $casts = [
        'metatags' => 'array',
        'document' => 'array',
        'properties' => 'array'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }
}