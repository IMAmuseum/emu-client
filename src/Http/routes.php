<?php

/*
|--------------------------------------------------------------------------
| Emu-Client Package Routes
|--------------------------------------------------------------------------
*/

use Imamuseum\EmuClient\EmuController as Emu;

Route::group(['prefix' => 'emu'], function() {

    Route::get('/getSpecificObject/{id}', function($id) {
        $emu = new Emu(config('emu-client'));
        return $emu->getSpecificObject($id);
    });

});