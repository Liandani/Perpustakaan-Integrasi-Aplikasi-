<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/books/{id}', function ($id) {
    return response()->json([
        'id' => $id,
        'title' => 'Clean Code',
        'stock' => 5
    ]);
});
