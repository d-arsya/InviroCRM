<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Message::factory(5)->create();
        Message::whereNotNull('id')->update(['default' => false]);
        Message::first()->update(['default' => true]);
    }
}
