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
                try {
                    $result[] = TaxonomyTermable::create([
                        'taxonomy_termable_type' => get_class($this),
                        'taxonomy_termable_id' => $this->id,
                        'taxonomy_term_id' => $id
                    ]);
                } catch (\Exception $e) {}
            }
        }
        else {
            try {
                $result[] = TaxonomyTermable::create([
                    'taxonomy_termable_type' => get_class($this),
                    'taxonomy_termable_id' => $this->id,
                    'taxonomy_term_id' => $term_id
                ]);
            } catch (\Exception $e) {}
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
                try {
                    $node = TaxonomyTermable::where([
                        'taxonomy_termable_type' => get_class($this),
                        'taxonomy_termable_id' => $this->id,
                        'taxonomy_term_id' => $id
                    ])->first();
                    $node->destroy();
                } catch (\Exception $e) {}
            }
        }
        else {
            try {
                $node = TaxonomyTermable::where([
                    'taxonomy_termable_type' => get_class($this),
                    'taxonomy_termable_id' => $this->id,
                    'taxonomy_term_id' => $term_id
                ])->first();
                if($node) {
                    $node->destroy();
                }
            } catch (\Exception $e) {}
        }
    }

    public function taxonomy_terms()
    {
        return $this->morphToMany(TaxonomyTerm::class, 'taxonomy_termable');
    }
}