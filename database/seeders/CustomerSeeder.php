<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Message;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($daysAgo = 7; $daysAgo >= 1; $daysAgo--) {
            Customer::factory(fake()->numberBetween(5, 30))->create([
                'date' => now()->subDays($daysAgo)->toDateString(),
            ]);
        }
        $messages = Message::count();

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
            $customer->update(['status' => fake()->randomElement(['sended', 'failed', 'waiting']), 'message_value' => $messageValue]);
        }
    }
}
