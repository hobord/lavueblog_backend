<?php

namespace LavueCms\Http\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LavueCms\ContentTranslation;
use Illuminate\Support\Facades\Cache;

class ContentTranslationController
{
    public function ls(Request $request)
    {
        $per_page = ($request->get('per_page')) ? $request->get('per_page'):15;

        $result = [];
        if(Auth::check() || $request->get('nocache')) {
            $result = ContentTranslation::paginate($per_page);
        }
        else {
            $key = 'content_translation' . $request->get('page') . '_' . $per_page;
            Cache::remember($key, env('CACHE_LIVETIME', 60), function() use ($per_page) {
                $result = ContentTranslation::paginate($per_page);
            });
        }

        return $result;
    }

    public function get(Request $request, $id)
    {
        $model = null;
        if(Auth::check() || $request->get('nocache')) {
            $model = ContentTranslation::where('id', $id)->firstOrFail();
        }
        else {
            Cache::remember('content_translation_'.$id, env('CACHE_LIVETIME', 60), function () use ($id) {
                $model = ContentTranslation::where('id', $id)->firstOrFail();
            });
        }
        return $model;
    }

    public function updateOrCreate(Request $request)
    {
        $content = ContentTranslation::updateOrCreate(
            ['id' => $request->get('id')],
            $request->all()
        );
        return $content;
    }

    public function delete(Request $request, $id)
    {
        ContentTranslation::destroy($id);
    }
}