<?php

Route::group(['prefix'=>'admin'], function (){
    Route::get('/', function (){
        return view('welcome');
    });
});
