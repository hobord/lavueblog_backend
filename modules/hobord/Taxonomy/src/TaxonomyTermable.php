<?php

namespace Hobord\Taxonomy;

use Illuminate\Database\Eloquent\Model;

class TaxonomyTermable extends Model
{
    protected $table = 'taxonomy_termables';

    protected $fillable = [
        'taxonomy_term_id',
        'taxonomy_termable_type',
        'taxonomy_termable_id'
    ];

    public function term()
    {
        return $this->belongsTo(TaxonomyTerm::class, 'taxonomy_term_id', 'id');
    }

    public function termable()
    {
        return $this->belongsTo($this->termable_type, 'taxonomy_termable_id', 'id');
    }
}