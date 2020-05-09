<?php

const WIRECARD_CONTROLER = 'ArthurZanella\Wirecard\Http\Controllers\WirecardController@';

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::prefix('wirecard')->group(function () {
        Route::get('/', WIRECARD_CONTROLER . 'index')->name('wirecard.index');
        //Route::get('/pay', WIRECARD_CONTROLER . 'pay')->name('wirecard.pay');
        Route::post('/pay', WIRECARD_CONTROLER . 'pay')->name('wirecard.pay');
        Route::post('/notify', WIRECARD_CONTROLER . 'notify')->name('wirecard.notify');
        Route::get('/success/{reference}', WIRECARD_CONTROLER . 'success')->name('wirecard.success');
        Route::get('/cancel', WIRECARD_CONTROLER . 'cancel')->name('wirecard.cancel');
        Route::get('/createwebhook', WIRECARD_CONTROLER . 'createWebhook')->name('wirecard.createwebhook');
        Route::get('/listwebhook', WIRECARD_CONTROLER . 'listWebhook')->name('wirecard.listwebhook');
        Route::get('/deletewebhook/{notification_id}', WIRECARD_CONTROLER . 'deleteWebhook')->name('wirecard.deletewebhook');
    });
});