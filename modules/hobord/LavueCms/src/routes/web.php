<?php
DB::enableQueryLog();
DB::listen(
    function ($sql, $bindings = null, $time = null) {
        foreach ($sql->bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else {
                if (is_string($binding)) {
                    $sql->bindings[$i] = "'$binding'";
                }
            }
        }

        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

        $query = vsprintf($query, $sql->bindings);

        // Save the query to file
        $logFile = fopen(
            storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
            'a+'
        );
        fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
        fclose($logFile);
    }
);
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

            Route::get('/{id}/terms', 'ContentController@terms')->name('api/content/terms');
            Route::post('/{id}/terms', 'ContentController@update_terms')->name('api/content/update_terms');
            Route::get('/{id}/add_term/{term_id}', 'ContentController@add_term')->name('api/content/add_term');
            Route::get('/{id}/remove_term/{term_id}', 'ContentController@remove_term')->name('api/content/remove_term');
        });
    });
});
