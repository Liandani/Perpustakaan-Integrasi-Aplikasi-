<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;

class BookController extends Controller
{
    // GET ALL BOOKS
    public function index()
    {
        return response()->json(Book::all());
    }

    // GET BOOK BY ID
    public function show($id)
    {
        $book = Book::with(['loans.user'])->find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        return response()->json($book);
    }

    // CHECK BOOK STATUS (AVAILABLE OR BORROWED)
    public function status($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        $activeLoan = Loan::where('book_id', $id)
            ->where('status', 'borrowed')
            ->first();

        if ($activeLoan) {
            return response()->json([
                'book_id' => $book->id,
                'title' => $book->title,
                'available' => false,
                'message' => 'Buku sedang dipinjam',
                'borrowed_by' => $activeLoan->user_id,
                'loan_id' => $activeLoan->id,
            ]);
        }

        return response()->json([
            'book_id' => $book->id,
            'title' => $book->title,
            'available' => true,
            'message' => 'Buku tersedia untuk dipinjam'
        ]);
    }
}
