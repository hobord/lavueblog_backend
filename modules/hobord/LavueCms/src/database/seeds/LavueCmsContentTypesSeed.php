<?php

use Illuminate\Database\Seeder;

use Hobord\LavueCms\ContentType;

class LavueCmsContentTypesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContentType::updateOrCreate(['type' => 'Layout'], [
            'type' => 'Layout',
            'config' => [
                'properties'=>[
                    'blocks'=>[

                    ]
                ]
            ]
        ]);

        ContentType::updateOrCreate(['type' => 'Page'], [
            'type' => 'Page',
            'config' => [
                'form_settings' => [

                ],
                'properties'=>[
                    'layout'=>'default',
                    'blocks'=>[

                    ]
                ]
            ]
        ]);

        ContentType::updateOrCreate(['type' => 'Block'], [
            'type' => 'Block',
            'config' => []
        ]);

        ContentType::updateOrCreate(['type' => 'Post'], [
            'type' => 'Post',
            'config' => []
        ]);
    }
}
