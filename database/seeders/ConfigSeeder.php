<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('config')->insert(['key' => 'days_after', 'value' => 3]);
        DB::table('config')->insert(['key' => 'send_interval', 'value' => 15]);
    }
}
