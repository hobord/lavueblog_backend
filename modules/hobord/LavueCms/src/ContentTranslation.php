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
        'metatags', // Document meta information rendering to header
        'document', // You can store the leads, plaintext, full document etc
        'fields', // Other fields what will be rendering on frontend
        'properties', // Other properties, you can store here de system level properties
        'edited_by' //last editor id
    ];

    protected $casts = [
        'metatags' => 'array',
        'document' => 'array',
        'fields' => 'array',
        'properties' => 'array'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }
}