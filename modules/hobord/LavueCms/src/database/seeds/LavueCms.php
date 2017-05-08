<?php

use Illuminate\Database\Seeder;

use Hobord\LavueCms\ContentType;

class LavueCms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $typePage = ContentType::updateOrCreate(['type' => 'Page'], [
            'type' => 'Page',
            'config' => [

            ]
        ]);
        $typePagePart = ContentType::updateOrCreate(['type' => 'PagePart'], [
            'type' => 'PagePart',
            'config' => []
        ]);
        $typePost = ContentType::updateOrCreate(['type' => 'Post'], [
            'name' => 'Post',
            'config' => [
                'indexes' => [
                    [
                        'index_name' => 'index_votes',
                        'type' => 'integer',
                        'field' => 'fields->"$.five_star.votes"'
                    ]
                ]
            ]
        ]);

        $taxonomy = Hobord\Taxonomy\Taxonomy::create(['name'=>'Catalog']);

        $term1 = Hobord\Taxonomy\TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'term1'
        ]);
        $term2 = Hobord\Taxonomy\TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'term2'
        ]);
        $term3 = Hobord\Taxonomy\TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'term3'
        ]);
        $term2_1 = Hobord\Taxonomy\TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'parent_id' => $term2->id,
            'name' => 'term2_1'
        ]);

        $post1 = Hobord\LavueCms\Content::create([
            'type_id' => $typePost->id,
            'title' => 'first post',
            'document' => [
                'lead' => [
                    'plain' => 'lead text',
                    'deltas_formated' => "{ insert: 'lead text', attributes: { bold: true } }",
                    'html_formated' => '<b>lead text</b>'
                ],
                'body' => [
                    'plain' => 'body text',
                    'deltas_formated' => "{ insert: 'body text', attributes: { bold: true } }",
                    'html_formated' => '<b>body text</b>'
                ],
            ],
            'fields' => [
                'five_star' => [
                    1=>0,2=>1,3=>4,4=>3,5=>8,'avarge'=>4.125, 'votes'=>16
                ]
            ],
            'properties' => [
                'published'=>true,
                'sticky'=>true,
                'promoted'=>true,
                'start_promotion' => '2017-05-03 00:00:00',
                'end_of_promotion' => '2017-05-04 00:00:00',
                'delta'=>2
            ]
        ]);
        $post1->addToTaxonomyTerm([$term2->id, $term2_1->id, $term3->id]);

        $post2 = Hobord\LavueCms\Content::create([
            'type_id' => $typePost->id,
            'title' => 'second post',
            'document' => [
                'lead' => [
                    'plain' => 'second post lead text',
                    'deltas_formated' => "{ insert: 'second post lead text', attributes: { bold: true } }",
                    'html_formated' => '<b>second post lead text</b>'
                ],
                'body' => [
                    'plain' => 'second post body text',
                    'deltas_formated' => "{ insert: 'second post body text', attributes: { bold: true } }",
                    'html_formated' => '<b>second post body text</b>'
                ],
            ],
            'fields' => [
                'five_star' => [
                    1=>0,2=>0,3=>0,4=>0,5=>0,'avarge'=>0, 'votes'=> 0
                ]
            ],
            'properties' => [
                'published'=>true,
                'sticky'=>false,
                'promoted'=>false,
                'delta'=>1
            ]
        ]);
        $post2->addToTaxonomyTerm([$term1->id, $term3->id]);

        $typePost->addJsonIndex('fields->"$.five_star.votes"', 'integer', 'index_votes');
//        $typePost->deleteJsonIndex('index_votes');

        /*
         http://.../admin/api/content?nocache=1&filters[document->body->plain][comp]=like&filters[document->body->plain][value]=second

http://192.168.99.100:94/admin/api/content?nocache=1&filters[fields-%3Efive_star-%3Evotes][comp]=eq&filters[fields-%3Efive_star-%3Evotes][value]=0
         */
    }
}
