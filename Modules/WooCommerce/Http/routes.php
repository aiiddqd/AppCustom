<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\WooCommerce\Http\Controllers'], function()
{
    Route::post('/woocommerce/ajax', ['uses' => 'WooCommerceController@ajax', 'laroute' => true])->name('woocommerce.ajax');

    Route::get('/mailbox/woocommerce/{id}', ['uses' => 'WooCommerceController@mailboxSettings', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.woocommerce');
    Route::post('/mailbox/woocommerce/{id}', ['uses' => 'WooCommerceController@mailboxSettingsSave', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.woocommerce.save');
});
