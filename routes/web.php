<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $preloadStats = opcache_get_status()['preload_statistics'];

    $memory = $preloadStats['memory_consumption'];

    $base = log($memory, 1024);
    $suffixes = array('', 'KB', 'MB', 'G', 'T');

    $preloadStats['memory'] = round(pow(1024, $base - floor($base)), 2) .' '. $suffixes[floor($base)];

    return view('welcome', ['preloadStats' => $preloadStats]);
});
