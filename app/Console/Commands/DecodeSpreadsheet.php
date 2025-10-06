<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class DecodeSpreadsheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sheet:decode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customers = Customer::where('status', 'waiting')->get();
        foreach ($customers as $customer) {
            $prod = $customer->products;
            $prod = json_encode($prod);
            $prod = str_replace('"[', '[', $prod);
            $prod = str_replace(']"', ']', $prod);
            $prod = str_replace('\\', '', $prod);
            $prod = json_decode($prod);
            $customer->update(['products' => $prod]);
        }
    }
}
