<?php

namespace Database\Seeders;

use App\Models\WhatsappToken;
use Illuminate\Database\Seeder;

class WhatsappTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WhatsappToken::factory(5)->create();
        WhatsappToken::whereNotNull('id')->update(['used' => false, 'active' => true]);
        WhatsappToken::first()->update(['used' => true]);
    }
}
