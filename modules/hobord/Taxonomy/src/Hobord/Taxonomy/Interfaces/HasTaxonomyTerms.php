<?php

namespace Hobord\Taxonomy\Interfaces;


interface HasTaxonomyTerms
{
    public function addToTaxonomyTerm($term_id);
    public function removeFromTaxonomyTerm($term_id);
    public function taxonomy_terms();
}