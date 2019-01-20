<?php

Route::group([
    'prefix' => config('comment.prefix'),
    'as' => 'comment.',
    'middleware' => ['web', 'comment']
], function() {
     Route::post('store', config('comment.controller') . '@store')
         ->middleware(config('comment.middleware.store'))
         ->name('store');

     Route::post('get', config('comment.controller') . '@get')
         ->middleware(config('comment.middleware.get'))
         ->name('get');

     Route::post('destroy', config('comment.controller') . '@destroy')
         ->middleware(config('comment.middleware.destroy'))
         ->name('destroy');

     Route::post('update', config('comment.controller') . '@update')
         ->middleware(config('comment.middleware.update'))
         ->name('update');

    Route::post('count', config('comment.controller') . '@count')
        ->middleware(config('comment.middleware.count'))
        ->name('count');
});