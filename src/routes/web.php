<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(MpesaController::class)
->prefix('payments')
->as('payments')
->group(function(){
    Route::post('/initiatepush','initiateStkPush')->name('initiatepush');
    Route::post('/stkcallback','stkCallback')->name('stkcallback');
    //GET STATUS
    Route::get('/stkquery','stkQuery')->name('stkquery');
    //C2B ROUTES
    Route::get('/registerurl','registerUrl')->name('registerurl');
    Route::post('/validation','Validation')->name('validation');
    Route::post('/confirmation','Confirmation')->name('confirmation');
    Route::get('/simulate','Simulate')->name('simulate');
    Route::get('/qrcode','qrcode')->name('qrcode');
    Route::get('/b2c','b2c')->name('b2c');
    Route::post('/b2cresult','b2cResult')->name('b2cresult');
    Route::post('/b2ctimeout','b2cTimeout')->name('b2ctimeout');
    Route::get('/reversal','Reversal')->name('reversal');
    Route::post('/reversalresult','reversalResult')->name('reversalresult');
    Route::post('/reversaltimeout','reversalTimeout')->name('reversaltimeout');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
