<?php

namespace Hobord\Taxonomy;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    protected $table = 'taxonomies';

    public $timestamps = false;

    protected $casts = [
        'config' => 'array'
    ];

    protected $fillable = [
        'name',
        'locale',
        'config'
    ];

    public function terms()
    {
        return $this->hasMany(TaxonomyTerm::class, 'taxonomy_id', 'id');
    }
}