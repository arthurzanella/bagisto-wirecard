<?php

const WIRECARD_CONTROLER = 'ArthurZanella\Wirecard\Http\Controllers\WirecardController@';

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::prefix('wirecard')->group(function () {
        Route::get('/', WIRECARD_CONTROLER . 'index')->name('wirecard.index');
        //Route::get('/pay', WIRECARD_CONTROLER . 'pay')->name('wirecard.pay');
        Route::post('/pay', WIRECARD_CONTROLER . 'pay')->name('wirecard.pay');
        Route::post('/notify', WIRECARD_CONTROLER . 'notify')->name('wirecard.notify');
        Route::get('/success', WIRECARD_CONTROLER . 'success')->name('wirecard.success');
        Route::get('/cancel', WIRECARD_CONTROLER . 'cancel')->name('wirecard.cancel');
    });
});