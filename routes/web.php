<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    // return Socialite::driver('ipatco')->redirect();
    return ['Laravel' => app()->version()];
});

require __DIR__ . '/auth.php';
