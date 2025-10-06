<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Traits\Spreadsheet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SyncSpreadsheet extends Command
{
    use Spreadsheet;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sheet:sync';

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
        $today = now()->toDateString();
        $have = Customer::where('date', $today)->count();
        if ($have) {
            return $this->info('Hari ini sudah');
        }
        $config = explode(':', DB::table('config')->whereKey('sync_time')->first()->value);
        $hour = $config[0];
        $minute = $config[1];

        $nowHour = now()->hour;
        $nowMinute = now()->minute;
        if ($hour != $nowHour || $minute > $nowMinute) {
            return $this->info('Belom nanti jam '.$hour.'.'.$minute);
        }

        $spreadsheetId = DB::table('config')->whereKey('spreadsheet_id')->first()->value;
        $sheetName = $this->getSheetName($spreadsheetId, 0);
        $data = $this->getByName($spreadsheetId, $sheetName);

        foreach ($data as $item) {
            Customer::create($item);
        }
        $this->info('Berhasil memasukkan '.count($data).' data customer');
        Artisan::call('sheet:decode');
    }
}
