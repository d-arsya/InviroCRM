<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($daysAgo = 7; $daysAgo >= 1; $daysAgo--) {
            Customer::factory(4)->create([
                'date' => now()->subDays($daysAgo)->toDateString(),
            ]);
        }
        $messages = Message::count();
        $default = (int) DB::table('config')->where('key', 'days_after')->value('value');

        $customers = Customer::all();

        foreach ($customers as $customer) {
            $customer->message_id = fake()->numberBetween(1, $messages);
            $customer->save();
        }
        foreach ($customers as $customer) {
            $message = $customer->message;

            $messageValue = $message ? [
                'text' => $message->text,
                'title' => $message->title,
            ] : [];
            $customer->update(['status' => fake()->randomElement(['sended', 'failed']), 'message_value' => $messageValue]);
        }
    }
}
