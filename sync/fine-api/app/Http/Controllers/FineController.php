<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FineController extends Controller
{
    // GET ALL FINES
    public function index()
    {
        return response()->json([
            'message' => 'List data denda',
            'data' => Fine::all()
        ]);
    }

    // GET DETAIL FINE BY ID
    public function show($id)
    {
        $fine = Fine::find($id);

        if (!$fine) {
            return response()->json([
                'message' => 'Data denda tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail data denda',
            'data' => $fine
        ]);
    }

    // CHECK AND CALCULATE FINE FROM LOAN DATA
    public function checkFine(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|integer'
        ]);

        $loan = Loan::with(['user', 'book'])->find($request->loan_id);

        if (!$loan) {
            return response()->json([
                'message' => 'Data peminjaman tidak ditemukan'
            ], 404);
        }

        if (!$loan->return_date) {
            return response()->json([
                'message' => 'Buku belum dikembalikan, denda belum bisa dihitung',
                'data' => $loan
            ], 400);
        }

        $dueDate = Carbon::parse($loan->due_date)->startOfDay();
        $returnDate = Carbon::parse($loan->return_date)->startOfDay();

        $finePerDay = 2000;
        $lateDays = 0;
        $totalFine = 0;
        $status = 'no_fine';

        if ($returnDate->gt($dueDate)) {
            $lateDays = $dueDate->diffInDays($returnDate);
            $totalFine = $lateDays * $finePerDay;
            $status = 'unpaid';
        }

        $fine = Fine::updateOrCreate(
            [
                'loan_id' => $loan->id
            ],
            [
                'user_id' => $loan->user_id,
                'book_id' => $loan->book_id,
                'due_date' => $loan->due_date,
                'return_date' => $loan->return_date,
                'late_days' => $lateDays,
                'fine_per_day' => $finePerDay,
                'total_fine' => $totalFine,
                'status' => $status
            ]
        );

        return response()->json([
            'message' => $totalFine > 0
                ? 'Denda berhasil dihitung'
                : 'Tidak ada denda karena pengembalian tepat waktu',
            'data' => [
                'fine' => $fine,
                'loan' => $loan,
                'total_denda_rupiah' => 'Rp ' . number_format($totalFine, 0, ',', '.')
            ]
        ]);
    }

    // GET FINE BY LOAN ID
    public function getByLoan($loan_id)
    {
        $fine = Fine::where('loan_id', $loan_id)->first();

        if (!$fine) {
            return response()->json([
                'message' => 'Denda untuk loan ini belum ada'
            ], 404);
        }

        return response()->json([
            'message' => 'Data denda berdasarkan loan',
            'data' => $fine
        ]);
    }
}