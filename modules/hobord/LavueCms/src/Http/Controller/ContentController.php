<?php

namespace Hobord\LavueCms\Http\Controller;

use Hobord\LavueCms\Content;
use Hobord\Taxonomy\TaxonomyTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ContentController
{

    private function filter_by_term($request)
    {
        if($request->get('term_filter')) {
            $result = Content::whereHas('taxonomy_terms', function($q) use ($request) {
                $q->whereIn('taxonomy_term_id', $request->get('term_filter'));
            });
        }
        else {
            $result = Content::withTranslation()->with('taxonomy_terms');
        }
        return $result;
    }

    private function make_filter($request, $query) {
        if($request->get('filters')) {
            $filters = $request->get('filters');
            foreach ($filters as $key => $filter) {
                if($key != 'type_id'
                    && $key != 'status'
                    && $key != 'primary_locale'
                    && $key != 'created_at'
                    && $key != 'updated_at'
                ) {
                    if ($filter['comp'] == 'like')
                        $query = $query->whereTranslationLike($key, "%".$filter['value']."%");
                    elseif ($filter['comp'] == '=')
                        $query = $query->whereTranslation($key, $filter['value']);
                    elseif ($filter['comp'] == '!=')
                        $query = $query->whereTranslation($key, '!=', "%".$filter['value']."%");
//                        elseif ($filter['comp'] == 'notLike')
//                            $query = $query->whereTranslation($key, "%".$filter['value']."%");
                }
                else {
                    $query = $query->where($key, $filter['comp'], $filter['value']);
                }
            }
        }
        return $query;
    }

    private function make_order($request, $query)
    {
        if($request->get('order_by')) {
            $query = $query->orderBy($request->get('order_by'),
                ($request->get('order_direction')) ? $request->get('order_direction') : 'desc'
            );
        }
        return $query;
    }

    public function ls(Request $request)
    {
        \App::getLocale();
        $per_page = ($request->get('per_page')) ? $request->get('per_page'):15;

        $result = [];

        if (Auth::check() || $request->get('nocache')) {
//            $result = Content::withTranslation()->with('taxonomy_terms');
            $result = $this->filter_by_term($request);
            $result = $this->make_filter($request, $result);
            $result = $this->make_order($request, $result);
            $result = $result->paginate($per_page);
        }
        else {
            $key = 'contents_page_' . $request->get('page') . '_' . $per_page;
            Cache::remember($key ,  env('CACHE_LIVETIME', 60), function () use($request, $per_page) {
//                $result = Content::withTranslation()->with('taxonomy_terms');
                $result = $this->filter_by_term($request);
                $result = $this->make_filter($request, $result);
                $result = $this->make_order($request, $result);
                $result = $result->paginate($per_page);
                return $result;
            });
        }

        return $result;
    }

    public function get(Request $request, $id)
    {
        \App::getLocale();
        $model = null;

        if(Auth::check() || $request->get('nocache')) {
            $model = Content::withTranslation()->with('taxonomy_terms')->where('id', $id)->firstOrFail();
        }
        else {
            Cache::remember('content_'.$id, env('CACHE_LIVETIME', 60), function () use ($id) {
                $model = Content::withTranslation()->with('taxonomy_terms')->where('id', $id)->firstOrFail();
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


    // Taxonomy
    public function terms(Request $request, $id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        return $content->taxonomy_terms;
    }

    public function add_term(Request $request, $id, $term_id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        $content->addToTaxonomyTerm($term_id);
        return $content->taxonomy_terms;
    }

    public function remove_term(Request $request, $id, $term_id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        $content->removeFromTaxonomyTerm($term_id);
        return $content->taxonomy_terms;
    }

    public function update_terms(Request $request, $id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        $terms_ids = $request->get('terms_ids');

        foreach ($content->taxonomy_terms as $term) {
            if(!in_array($term->id, $terms_ids)) {
                $content->removeFromTaxonomyTerm($term->id);
            }
            if (($key = array_search($term->id, $terms_ids)) !== false) {
                unset($terms_ids[$key]);
            }
        }
        foreach ($terms_ids as $term_id) {
            $content->addToTaxonomyTerm($term_id);
        }
        return $content->taxonomy_terms;
    }


}