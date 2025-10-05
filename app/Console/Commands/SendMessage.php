<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Traits\Whatsapp;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendMessage extends Command
{
    use Whatsapp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:send';

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
        $delay = 0;
        $days_after = (int) DB::table('config')->whereKey('days_after')->first()->value;
        $send_interval = (int) DB::table('config')->whereKey('send_interval')->first()->value;
        $cutoffDate = Carbon::now()->subDays($days_after)->toDateString();
        $customers = Customer::whereStatus('waiting')->whereDate('date', '<=', $cutoffDate)->get();
        $phones = implode(',', $customers->pluck('phone')->toArray());
        $isValid = $this->isWhatsappBulk($phones);
        $valid = $isValid->registered;

        foreach ($customers as $customer) {
            $isvalid = in_array($customer->phone, $valid);
            if ($isvalid) {
                $message = $customer->message_value['text'];
                $message = str_replace('{nama}', $customer->name, $message);
                $message = str_replace('\n', "\n", $message);
                $sended = $this->send($customer->phone, $message);
                $this->send($customer->phone, $message, $delay);
                $delay += $send_interval;
                if ($sended) {
                    $customer->update(['status' => 'sended']);
                } else {
                    $customer->update(['status' => 'failed']);
                }
            } else {
                $customer->update(['status' => 'failed']);
            }
        }
    }
}
