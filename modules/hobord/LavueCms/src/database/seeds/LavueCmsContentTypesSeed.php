<?php

use Illuminate\Database\Seeder;

use LavueCms\ContentType;

class LavueCmsContentTypesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContentType::updateOrCreate(['type' => 'Page'], [
            'type' => 'Page',
            'config' => [

            ]
        ]);

        ContentType::updateOrCreate(['type' => 'PagePart'], [
            'type' => 'PagePart',
            'config' => []
        ]);

        ContentType::updateOrCreate(['type' => 'Post'], [
            'type' => 'Post',
            'config' => []
        ]);
    }
}
