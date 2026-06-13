<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FineController;

// =====================
// TEST ROUTE
// =====================
Route::get('/ping', function () {
    return 'OK';
});

// =====================
// USERS
// =====================
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::get('/users/{id}/loans', [UserController::class, 'getUserWithLoans']);

// =====================
// BOOKS
// =====================
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
Route::get('/books/{id}/status', [BookController::class, 'status']);

// =====================
// LOANS
// =====================
Route::get('/loans', [LoanController::class, 'index']);
Route::post('/loans', [LoanController::class, 'store']);
Route::get('/loans/history', [LoanController::class, 'history']);
Route::post('/loans/return', [LoanController::class, 'returnBook']);
Route::get('/loans/{id}', [LoanController::class, 'show']);

// =====================
// FINES
// =====================
Route::get('/fines', [FineController::class, 'index']);
Route::post('/fines/check', [FineController::class, 'checkFine']);
Route::get('/fines/loan/{loan_id}', [FineController::class, 'getByLoan']);
Route::get('/fines/{id}', [FineController::class, 'show']);