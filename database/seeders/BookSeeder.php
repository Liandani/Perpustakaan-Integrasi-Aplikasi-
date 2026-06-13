<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            ['title' => 'Laravel for Beginners', 'available' => true],
            ['title' => 'Clean Code', 'available' => true],
            ['title' => 'Design Patterns', 'available' => true],
            ['title' => 'The Pragmatic Programmer', 'available' => true],
            ['title' => 'Refactoring', 'available' => true],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(
                ['title' => $book['title']],
                ['available' => $book['available']]
            );
        }
    }
}
