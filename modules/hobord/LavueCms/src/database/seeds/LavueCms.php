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
        $typePage = ContentType::updateOrCreate(['name' => 'Page'], [
            'name' => 'Page',
            'config' => [

            ]
        ]);
        $typePagePart = ContentType::updateOrCreate(['name' => 'PagePart'], [
            'name' => 'PagePart',
            'config' => []
        ]);
        $typePost = ContentType::updateOrCreate(['name' => 'Post'], [
            'name' => 'Post',
            'config' => [
                'indexes' => [
                    [
                        'index_name' => 'index_votes',
                        'type' => 'integer',
                        'field' => 'fields->"$.five_star.votes"'
                    ],
                    [
                        'index_name' => 'index_published',
                        'type' => 'int(1)',
                        'field' => 'properties->"$.published"'
                    ],
                    [
                        'index_name' => 'index_promoted',
                        'type' => 'int(1)',
                        'field' => 'properties->"$.promoted"'
                    ],

                ]
            ]
        ]);
//        $typePost->addJsonIndex('fields->"$.five_star.votes"', 'integer', 'index_votes');
//        $typePost->addJsonIndex('properties->"$.published"', 'int(1)', 'index_published');
//        $typePost->addJsonIndex('published->"$.promoted"', 'int(1)', 'index_promoted');
//        $typePost->deleteJsonIndex('index_votes');


        $taxonomy = Hobord\Taxonomy\Taxonomy::create(['name'=>'Catalog']);
        $terms=[];
        $terms[1] = Hobord\Taxonomy\TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'term1'
        ]);
        $terms[2] = Hobord\Taxonomy\TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'term2'
        ]);
        $terms[3] = Hobord\Taxonomy\TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'term3'
        ]);
        $terms[4] = Hobord\Taxonomy\TaxonomyTerm::create([
            'taxonomy_id' => $taxonomy->id,
            'parent_id' => $terms[2]->id,
            'name' => 'term2_1'
        ]);

        $terms = Hobord\Taxonomy\TaxonomyTerm::all();
        $typePost = ContentType::find(3);
        $generator = new Badcow\LoremIpsum\Generator();
        for($i=0; $i<100; $i++) {
            $text = implode(' ', $generator->getRandomWords(50));
            $stars = [
                1=>rand(1,100),
                2=>rand(1,100),
                3=>rand(1,100),
                4=>rand(1,100),
                5=>rand(1,100)
            ];
            $stars['avarge'] = rand(1,100);
            $stars['votes'] = rand(1,100);

            $post = Hobord\LavueCms\Content::create([
                'type_id' => $typePost->id,
                'title' => implode(' ', $generator->getRandomWords(4)),
                'document' => [
                    'lead' => [
                        'plain' => $text,
                        'deltas_formated' => "{ insert: '$text'}",
                        'html_formated' => "<p>$text</p>"
                    ],
                    'body' => [
                        'plain' => "body ".$text,
                        'deltas_formated' => "{ insert: 'body $text'}",
                        'html_formated' => "<p>body $text</p>"
                    ],
                ],
                'fields' => [
                    'five_star' => $stars
                ],
                'properties' => [
                    'published'=>1,
                    'promoted'=>1,
                    'delta'=>rand(1,100)
                ]
            ]);
            for($j=1; $j<=4; $j++) {
                $post->addToTaxonomyTerm([$terms[rand(0,3)]->id]);
            }
        }
        /*
         http://.../admin/api/content?nocache=1&filters[document->body->plain][comp]=like&filters[document->body->plain][value]=second

http://192.168.99.100:94/admin/api/content?nocache=1&filters[fields-%3Efive_star-%3Evotes][comp]=eq&filters[fields-%3Efive_star-%3Evotes][value]=0
         */
    }
}
