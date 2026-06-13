<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed user (idempotent)
        $user = User::firstOrCreate(
            ['email' => 'citra@gmail.com'],
            ['name' => 'Citra', 'phone' => '08123456789']
        );

        // Seed books
        $this->call(BookSeeder::class);

        // Seed active loan (Book ID 1 borrowed by User ID 1) if not exists
        $existingLoan = Loan::where('book_id', 1)
            ->where('status', 'borrowed')
            ->first();

        if (!$existingLoan) {
            Loan::create([
                'user_id' => $user->id,
                'book_id' => 1,
                'loan_date' => now(),
                'due_date' => now()->addDays(7),
                'status' => 'borrowed'
            ]);

            Book::where('id', 1)->update(['available' => false]);
        }

        // Seed loan histories
        $this->call(LoanHistorySeeder::class);
    }
}
