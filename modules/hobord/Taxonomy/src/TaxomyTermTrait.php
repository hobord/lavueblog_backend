<?php

namespace Hobord\Taxonomy;


trait TaxomyTermTrait
{
    /**
     * @param int|array $term_id
     * @return array
     */
    public function addToTaxonomyTerm($term_id)
    {
        $result = [];
        if(is_array($term_id)) {
            foreach ($term_id as $id){
                $result[] = TaxonomyTermable::create([
                    'taxonomy_termable_type' => get_class($this),
                    'taxonomy_termable_id' => $this->id,
                    'taxonomy_term_id' => $id
                ]);
            }
        }
        else {
            $result[] = TaxonomyTermable::create([
                'taxonomy_termable_type' => get_class($this),
                'taxonomy_termable_id' => $this->id,
                'taxonomy_term_id' => $term_id
            ]);
        }

        return $result;
    }

    /**
     * @param int|array $term_id
     */
    public function removeFromTaxonomyTerm($term_id)
    {
        $result = [];
        if(is_array($term_id)) {
            foreach ($term_id as $id) {
                $node = TaxonomyTermable::where([
                    'taxonomy_termable_type' => get_class($this),
                    'taxonomy_termable_id' => $this->id,
                    'taxonomy_term_id' => $id
                ])->first();
                $node->destroy();
            }
        }
        else {
            $node = TaxonomyTermable::where([
                'taxonomy_termable_type' => get_class($this),
                'taxonomy_termable_id' => $this->id,
                'taxonomy_term_id' => $term_id
            ])->first();
            if($node) {
                $node->destroy();
            }
        }
    }

    public function taxonomy_terms()
    {
        return $this->morphToMany(TaxonomyTerm::class, 'taxonomy_termable');
    }
}