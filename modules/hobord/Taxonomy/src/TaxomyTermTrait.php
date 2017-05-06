<?php

namespace Hobord\Taxonomy;


trait TaxomyTermTrait
{
    public function addToTaxonomyTerm($term_id)
    {
        return TaxonomyTermable::create([
            'taxonomy_termable_type' => get_class($this),
            'taxonomy_termable_id' => $this->id,
            'taxonomy_term_id' => $term_id
        ]);
    }

    public function removeFromTaxonomyTerm($term_id)
    {
        $node = TaxonomyTermable::where([
            'taxonomy_termable_type' => get_class($this),
            'taxonomy_termable_id' => $this->id,
            'taxonomy_term_id' => $term_id
        ])->first();

        if($node) {
           $node->destroy();
        }
    }

    public function taxonomy_terms()
    {
        return $this->morphToMany(TaxonomyTerm::class, 'taxonomy_termable');
    }
}