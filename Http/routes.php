<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\AppCustom\Http\Controllers'], function()
{
    Route::get('/custom', 'AppCustomController@index');
});
