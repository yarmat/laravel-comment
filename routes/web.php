<?php

Route::group([
    'prefix' => config('comment.prefix'),
    'as' => 'comment.',
    'middleware' => ['web', 'comment']
], function() {
    Route::post('store', config('comment.controller') . '@store')->name('store')
        ->middleware(config('comment.middleware.store'));

    Route::post('get', config('comment.controller') . '@get')->name('get')
        ->middleware(config('comment.middleware.get'));

    Route::post('destroy', config('comment.controller') . '@destroy')->name('destroy')
        ->middleware(config('comment.middleware.destroy'));

    Route::post('update', config('comment.controller') . '@update')->name('update')
        ->middleware(config('comment.middleware.update'));

    Route::post('count', config('comment.controller') . '@count')->name('count')
        ->middleware(config('comment.middleware.count'));

});