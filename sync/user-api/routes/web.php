<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users/{id}', function ($id) {
    return response()->json([
        'id' => $id,
        'name' => 'Citra'
    ]);
});
