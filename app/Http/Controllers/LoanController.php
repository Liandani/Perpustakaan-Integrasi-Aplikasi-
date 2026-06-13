<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use App\Models\LoanHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    // GET ALL LOANS
    public function index()
    {
        return response()->json(
            Loan::with(['user', 'book'])->get()
        );
    }

    // CREATE LOAN
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'book_id' => 'required|integer',
            'loan_date' => 'nullable|date',
            'due_date' => 'nullable|date'
        ]);

        $user = User::find($request->user_id);
        $book = Book::find($request->book_id);

        if (!$user || !$book) {
            return response()->json([
                'message' => 'User atau Book tidak ditemukan'
            ], 404);
        }

        $activeLoan = Loan::where('book_id', $book->id)
            ->where('status', 'borrowed')
            ->first();

        if ($activeLoan) {
            return response()->json([
                'message' => 'Book sedang dipinjam dan tidak tersedia'
            ], 400);
        }

        $loanDate = $request->loan_date
            ? Carbon::parse($request->loan_date)
            : now();

        $dueDate = $request->due_date
            ? Carbon::parse($request->due_date)
            : $loanDate->copy()->addDays(7);

        $loan = Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => $loanDate,
            'due_date' => $dueDate,
            'status' => 'borrowed'
        ]);

        $book->update([
            'available' => false
        ]);

        return response()->json([
            'message' => 'Loan berhasil dibuat',
            'loan' => $loan->load(['user', 'book'])
        ]);
    }

    // GET LOAN HISTORY
    public function history()
    {
        $histories = LoanHistory::with(['user', 'book'])->get();

        return response()->json($histories);
    }

    // PENGEMBALIAN BUKU
    public function returnBook(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|integer',
            'return_date' => 'nullable|date'
        ]);

        $loan = Loan::find($request->loan_id);

        if (!$loan) {
            return response()->json([
                'message' => 'Data peminjaman tidak ditemukan'
            ], 404);
        }

        if ($loan->status === 'returned') {
            return response()->json([
                'message' => 'Buku sudah dikembalikan sebelumnya'
            ], 400);
        }

        $returnDate = $request->return_date
            ? Carbon::parse($request->return_date)
            : now();

        $dueDate = Carbon::parse($loan->due_date);

        $daysLate = 0;
        $fine = 0;

        if ($returnDate->gt($dueDate)) {
            $daysLate = $dueDate->diffInDays($returnDate);
            $fine = $daysLate * 2000;
        }

        $loan->update([
            'status' => 'returned',
            'return_date' => $returnDate
        ]);

        $book = Book::find($loan->book_id);

        if ($book) {
            $book->update([
                'available' => true
            ]);
        }

        return response()->json([
            'message' => 'Buku berhasil dikembalikan',
            'detail_denda' => [
                'hari_terlambat' => $daysLate,
                'total_denda' => 'Rp ' . number_format($fine, 0, ',', '.')
            ],
            'data' => $loan->load(['user', 'book'])
        ]);
    }

    // GET DETAIL LOAN BY ID
    public function show($id)
    {
        $loan = Loan::with(['user', 'book'])->find($id);

        if (!$loan) {
            return response()->json([
                'message' => 'Loan not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Loan detail retrieved successfully',
            'data' => $loan
        ]);
    }
}