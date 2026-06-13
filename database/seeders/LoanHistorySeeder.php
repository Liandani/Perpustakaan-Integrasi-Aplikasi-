<?php

namespace Database\Seeders;

use App\Models\LoanHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $histories = [
            [
                'book_id' => 1,
                'user_id' => 1,
                'borrow_date' => '2026-04-10',
                'return_date' => '2026-04-17',
                'status' => 'completed'
            ],
            [
                'book_id' => 2,
                'user_id' => 1,
                'borrow_date' => '2026-04-05',
                'return_date' => '2026-04-12',
                'status' => 'completed'
            ],
            [
                'book_id' => 3,
                'user_id' => 1,
                'borrow_date' => '2026-04-20',
                'return_date' => null,
                'status' => 'active'
            ],
        ];

        foreach ($histories as $history) {
            LoanHistory::firstOrCreate(
                [
                    'book_id' => $history['book_id'],
                    'user_id' => $history['user_id'],
                    'borrow_date' => $history['borrow_date'],
                ],
                $history
            );
        }
    }
}
