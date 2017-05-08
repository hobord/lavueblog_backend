<?php

namespace Hobord\Taxonomy;

use Illuminate\Database\Eloquent\Model;

class TaxonomyTerm extends Model
{
    protected $table = 'taxonomy_terms';

    protected $casts = [
        'properties' => 'array'
    ];

    protected $fillable = [
        'taxonomy_id',
        'parent_id',
        'name',
        'locale',
        'properties',
        'delta'
    ];

    public function taxonomy()
    {
        return $this->belongsTo(Taxonomy::class, 'taxonomy_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(TaxonomyTerm::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(TaxonomyTerm::class, 'parent_id', 'id');
    }

    public function objects($class_name)
    {
        return $this->morphedByMany($class_name, 'taxonomy_termable');
    }
}
