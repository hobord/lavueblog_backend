<?php

namespace LavueCms\Http\Controller;

use Illuminate\Http\Request;
use LavueCms\Content;

class ContentTranslationController
{
    public function ls(Request $request)
    {
        App::getLocale();
        $pages = Content::withTranslation()->paginate();
        return $pages;
    }

    public function get(Request $request, $id)
    {
        App::getLocale();
        $model = Content::withTranslation()->where('id', $id)->firstOrFail();
    }

    public function updateOrCreate(Request $request)
    {
        App::getLocale();
        $content = Content::updateOrCreate(
            ['id' => $request->get('id')],
            $request->all()
        );
        return $content;
    }

    public function delete(Request $request, $id)
    {
        Content::destroy($id);
    }
}