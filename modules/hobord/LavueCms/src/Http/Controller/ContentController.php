<?php

namespace Hobord\LavueCms\Http\Controller;

use Hobord\LavueCms\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ContentController
{
    public function ls(Request $request)
    {
        \App::getLocale();
        $per_page = ($request->get('per_page')) ? $request->get('per_page'):15;

        $result = [];
        if (Auth::check() || $request->get('nocache')) {
            $result = Content::withTranslation()->paginate($per_page);
        } else {
            $key = 'contents_page_' . $request->get('page') . '_' . $per_page;
            Cache::remember($key ,  env('CACHE_LIVETIME', 60), function () use($per_page) {
                $result = Content::withTranslation()->paginate($per_page);
            });
        }

        return $result;
    }

    public function get(Request $request, $id)
    {
        \App::getLocale();
        $model = null;
        if(Auth::check() || $request->get('nocache')) {
            $model = Content::withTranslation()->where('id', $id)->firstOrFail();
        }
        else {
            Cache::remember('content_'.$id, env('CACHE_LIVETIME', 60), function () use ($id) {
                $model = Content::withTranslation()->where('id', $id)->firstOrFail();
            });
        }
        return $model;
    }

    public function updateOrCreate(Request $request)
    {
        \App::getLocale();
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