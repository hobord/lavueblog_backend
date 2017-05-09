<?php

namespace Hobord\LavueCms\Http\Controller;

use Hobord\LavueCms\Content;
use Hobord\LavueCms\ContentTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\Media;

class ContentController
{

    /**
     * Generate term_filter if is 'term_filter' in request exists
     *
     * @param $request
     * @return mixed
     */
    private function filter_by_term($request)
    {
        if($request->get('term_filter')) {
            $result = Content::withTranslation()->whereHas('taxonomy_terms', function($q) use ($request) {
                $q->whereIn('taxonomy_term_id', $request->get('term_filter'));
            });
        }
        else {
            $result = Content::withTranslation()->with('taxonomy_terms');
        }
        return $result;
    }

    /**
     * Generate filter if 'filters' array is exists in the requests
     *
     * @param $request
     * @param $query
     * @return mixed
     */
    private function make_filter($request, $query) {
        if($request->get('filters')) {
            $filters = $request->get('filters');
            if(is_array($filters)) {
                foreach ($filters as $key => $filter) {
                    switch ($filter['comp']){
                        case 'like':
                            $comp = 'like';
                            $filter['value'] = "%".$filter['value']."%";
                            break;
                        case 'eq':
                            $comp = '=';
                            break;
                        case 'not_eq':
                            $comp = '!=';
                            break;
                        case 'gt':
                            $comp = '>';
                            break;
                        case 'lt':
                            $comp = '<';
                            break;
                        default:
                            $comp = '=';
                    }

                    if($key != 'type_id'
                        && $key != 'status'
                        && $key != 'primary_locale'
                        && $key != 'created_at'
                        && $key != 'updated_at'
                    ) {
                        if(is_numeric($filter['value']))
                            $filter['value'] =(float) $filter['value'];
                        $query = $query->whereCompTranslation($key, $filter['value'], $comp);
                    }
                    else {
                        $query = $query->where($key, $comp, $filter['value']);
                    }
                }
            }
        }
        return $query;
    }

    /**
     * Make order into the query by  'order_by' and 'order_direction' request parameters
     *
     * @param $request
     * @param $query
     * @return mixed
     */
    private function make_order($request, $query)
    {
        if($request->get('order_by')) {
            $query = $query->orderBy($request->get('order_by'),
                ($request->get('order_direction')) ? $request->get('order_direction') : 'desc'
            );
        }
        return $query;
    }


    // Content API

    /**
     * List Contents
     * You can filtering by taxonomy terms -> 'term_filter'
     * You can search/filtering by fields  -> 'filters'
     * You can ordering the results with 'order_by' and 'order_direction'  request parameters
     * The result is paginated by default 15 item/page
     * You can disable the cache by 'nocache' request parameter (authenticated users has disabled)
     *
     * @param Request $request
     * @return array|mixed
     */
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
            $result = Cache::remember($key ,  env('CACHE_LIVETIME', 60), function () use($request, $per_page) {
                $result = $this->filter_by_term($request);
                $result = $this->make_filter($request, $result);
                $result = $this->make_order($request, $result);
                $result = $result->paginate($per_page);
                return $result;
            });
        }

        return $result;
    }

    /**
     * Get the Content by id
     * You can disable the cache by 'nocache' request parameter (authenticated users has disabled)
     *
     * @param Request $request
     * @param $id
     * @return null
     */
    public function get(Request $request, $id)
    {
        \App::getLocale();
        $model = null;

        if(Auth::check() || $request->get('nocache')) {
            $model = Content::withTranslation()
                ->with('taxonomy_terms')
                ->with('media')
                ->where('id', $id)
                ->firstOrFail();
        }
        else {
            $model = Cache::remember('content_'.$id, env('CACHE_LIVETIME', 60), function () use ($id) {
                return Content::withTranslation()
                    ->with('taxonomy_terms')
                    ->with('media')
                    ->where('id', $id)
                    ->firstOrFail();
            });
        }
        return $model;
    }

    /**
     * Update or create a Content
     *
     * @param Request $request
     * @return mixed
     */
    public function updateOrCreate(Request $request)
    {
        \App::getLocale();
        $content = Content::updateOrCreate(
            ['id' => $request->get('id')],
            $request->all()
        );
        return $content;
    }

    /**
     * Delete Content
     *
     * @param Request $request
     * @param $id
     * @return int
     */
    public function delete(Request $request, $id)
    {
        return Content::destroy($id);
    }

    // Taxonomy

    /**
     * List of Content's ($id) TaxonomyTerms
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function terms(Request $request, $id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        return $content->taxonomy_terms;
    }

    /**
     * Add Content ($id) to TaxonomyTerm ($term_id)
     * return List of Content's TaxonomyTerms
     *
     * @param Request $request
     * @param $id
     * @param $term_id
     * @return mixed
     */
    public function add_term(Request $request, $id, $term_id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        $content->addToTaxonomyTerm($term_id);
        return $content->taxonomy_terms;
    }

    /**
     * Remove Content ($id) from TaxonomyTerm ($term_id)
     * return List of Content's TaxonomyTerms
     *
     * @param Request $request
     * @param $id
     * @param $term_id
     * @return mixed
     */
    public function remove_term(Request $request, $id, $term_id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        $content->removeFromTaxonomyTerm($term_id);
        return $content->taxonomy_terms;
    }

    /**
     * Add and remove (update) Content ($id) TaxonomyTerms by 'terms_ids' request array parameter
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update_terms(Request $request, $id)
    {
        $content = Content::where('id', $id)->firstOrFail();
        $terms_ids = $request->get('terms_ids');

        if($terms_ids && is_array($terms_ids)) {
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
        }

        return $content->taxonomy_terms;
    }

    // Media

    /**
     * List Content ($id) urls of media collection (default collection is 'images')
     * The result array key is the media object id
     *
     * @param Request $request
     * @param $id
     * @param string $collection
     * @return mixed
     */
    public function ls_media(Request $request, $id, $collection='images')
    {
        $conversionName = ($request->get('conversion')) ? $request->get('conversion') : '';
        $content = Content::where('id', $id)->firstOrFail();

        $urls = $content->getMedia($collection)
            ->keyBy('id')
            ->transform(function (\Spatie\MediaLibrary\Media $media) use ($conversionName) {
            return $media->getUrl($conversionName);
        });

        return $urls;
    }

    /**
     * Get media by $mediaId
     * You can set optionally the conversion version of url by 'conversion' request parameter
     *
     * @param Request $request
     * @param $id
     * @param $mediaId
     * @return mixed
     */
    public function get_media(Request $request, $id=null, $mediaId)
    {
        $conversionName = ($request->get('conversion')) ? $request->get('conversion') : '';
        $media = Media::where('id', $mediaId)->firstOrFail();
        $media->url = $media->getUrl($conversionName);
        return $media;
    }

    /**
     * Upload file to Content ($id) media library into the ($collection='images').
     *
     * @param Request $request
     * @param $id
     * @param string $collection
     */
    public function add_media(Request $request, $id, $collection='images')
    {
        $content = Content::where('id', $id)->firstOrFail();
        if($request->get('file_name')) {
            return $content->addMedia($request->file)
            ->usingFileName($request->get('file_name'))
            ->toMediaLibrary($collection);
        }
        else {
            return $content->addMedia($request->file)
                ->toMediaLibrary($collection);
        }
    }

    /**
     * Delete media by $mediaId
     *
     * @param Request $request
     * @param $id
     * @param $mediaId
     * @return mixed
     */
    public function delete_media(Request $request, $id=null, $mediaId)
    {
        $media = Media::where('id', $mediaId)->firstOrFail();
        return $media->delete();
    }

}