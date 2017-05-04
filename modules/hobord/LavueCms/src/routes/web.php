<?php

//Route::group(['prefix'=>'admin', 'middleware' => ['auth']], function (){
Route::group(['prefix'=>'admin'], function (){
    Route::get('/', function (){
        return view('welcome');
    });

    Route::group(['prefix'=>'api'], function (){
        Route::group(['prefix'=>'content'], function (){
            Route::get('/',  'ContentController@ls')->name('api/content/list');
            Route::get('/{id}',  'ContentController@get')->name('api/content/get');
            Route::post('/{content}',  'ContentController@updateOrCreate')->name('api/content/update');
            Route::get('/{id}/delete',  'ContentController@delete')->name('api/content/delete');

            Route::group(['prefix'=>'translation'], function (){
                Route::get('/',  'ContentTranslationController@ls')->name('api/content_trans/list');
                Route::get('/{id}',  'ContentTranslationController@get')->name('api/content_trans/get');
                Route::post('/{content}',  'ContentTranslationController@updateOrCreate')->name('api/content_trans/update');
                Route::get('/{id}/delete',  'ContentTranslationController@delete')->name('api/content_trans/delete');
            });
        });
    });
});
