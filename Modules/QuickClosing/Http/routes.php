<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\QuickClosing\Http\Controllers'], function()
{
    Route::get('/', 'QuickClosingController@index');
});
