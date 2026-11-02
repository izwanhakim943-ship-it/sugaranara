<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Halaman contoh design (sementara, akan dibuang selepas dashboard sebenar siap)
Route::view('/design-preview', 'design-preview')->name('design.preview');