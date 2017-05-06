<?php

namespace Hobord\LavueCms;

use Illuminate\Database\Eloquent\Model;
use Hobord\LavueCms\Content;

class ContentType extends Model
{

    protected $casts = [
        'config' => 'array'
    ];

    protected $fillable = [
        'type',
        'config'
    ];

    public function contents()
    {
        return $this->hasMany(Content::class, 'type_id', 'id');
    }


}